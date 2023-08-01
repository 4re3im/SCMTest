<?php

defined('C5_EXECUTE') || die(_('Access Denied.'));

// SB-577 added by mabrigos 20200603
Loader::library('hub-sdk/autoload');
use HubEntitlement\Models\Entitlement;

class DashboardProvisioningSetupController extends Controller
{
    const COLUMN_EMAIL = 0;
    const COLUMN_PASSWORD = 1;
    const COLUMN_FIRST_NAME = 2;
    const COLUMN_LAST_NAME = 3;
    const COLUMN_SCHOOL = 4;
    const COLUMN_STATE = 5;
    const COLUMN_POST_CODE = 6;
    const COLUMN_USER_ROLE = 7;
    const COLUMN_CLASS_KEY = 8;
    const PROVISION_LIMIT = 1;

    private $pModel;
    private $fileId;
    private $filePath;
    private $fileRecordId;
    private $pkgHandle = 'go_provisioning';
    // GCAP-541 Campion added by machua/mtanada 20191004
    private $providerId;

    // ANZGO-3654 Modified by Shane Camus, 04/24/2018
    public function __construct()
    {
        parent::__construct();
        Loader::library('FileLog/FileLogger');
        Loader::model('provisioning_hotmaths_user_class_queue', $this->pkgHandle);
        Loader::model('provisioning', $this->pkgHandle);
        $this->pModel = new ProvisioningModel();
    }

    // ANZGO-3528 Modified by John Renzo S. Sunico
    // ANZGO-3914 Modified by Shane Camus 11/12/18
    public function on_start()
    {
        $htmlHelper = Loader::helper('html');
        $cssPath = (string)$htmlHelper->css('styles.css', $this->pkgHandle)->file . "?v=10";
        $jsPath = (string)$htmlHelper->javascript('scripts.js', $this->pkgHandle)->file . "?v=19";

        $this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . $cssPath . '">');
        $this->addFooterItem('<script type="text/javascript" src="' . $jsPath . '"></script>');
        $this->addFooterItem($htmlHelper->javascript('plugins/malsup/jquery.form.min.js', $this->pkgHandle));
    }

    // ANZGO-3654 Added by Shane Camus, 04/24/2018
    public function log($isSuccessful, $message, $meta)
    {
        $u = new User();

        $data = array(
            'timestamp' => date('r'),
            'userID' => $u->getUserID(),
            'info' => 'Provisioning Function: ' . debug_backtrace()[1]['function'],
            'status' => ($isSuccessful) ? 200 : 400,
            'message' => $message
        );

        if (isset($meta)) {
            $data['meta'] = $meta;
        }

        // ANZGO-3899 Modified by Shane Camus 10/19/18
        // ANZGO-3895 POC by John Renzo Sunico 10/19/18
        FileLogger::log($data);
    }

    // ANZGO-3654 Modified by Shane Camus, 04/24/2018
    private function uploadFile()
    {
        Loader::library('file/importer');
        $fi = new FileImporter();
        $tmpName = $_FILES['excel']['tmp_name'];
        $fileName = $_FILES['excel']['name'];

        $excelFile = $fi->import($tmpName, $fileName);
        $this->fileId = $excelFile->getFileID();
        $newFile = $excelFile->getFile();
        $this->filePath = $newFile->getPath();

        $this->log(
            true,
            'Provisioning file uploaded',
            array(
                'fileID' => $this->fileId,
                'fileName' => $fileName,
                'filePath' => $this->filePath
            )
        );

        return $this->pModel->insertFileRecord($newFile->getFileID(), $fileName);
    }

    // ANZGO-3899 Modified by Shane Camus 10/19/18
    // ANZGO-3895 POC by John Renzo Sunico 10/19/18
    public function startProvisioning()
    {
        $fileRecordID = $this->uploadFile();
        if ($fileRecordID) {
            $this->fileRecordId = $fileRecordID;

            Loader::helper('spreadsheet', $this->pkgHandle);
            $provisioningRecord = $this->pModel->getFileRecord($fileRecordID);
            $spreadsheetHelper = new SpreadsheetHelper($provisioningRecord['FileID']);

            // SB-626 added by mabrigos 20200708
            $records = $spreadsheetHelper->getRows(0, false);
            $emails = array();
            $emailWithPasswords = array();
            foreach ($records as $row) {
                $emails[] = $row[ProvisioningModel::COLUMN_EMAIL]; 
                $emailAndPasswords[$row[ProvisioningModel::COLUMN_EMAIL]] = $row[ProvisioningModel::COLUMN_PASSWORD];
            }

            $selfRegisteredEmails = array_column($this->pModel->checkSelfRegisteredUsers($emails), 'uEmail');

            $_SESSION['selfRegisteredAccount'] = array_filter($emailAndPasswords, 
                function ($key) use ($selfRegisteredEmails) {
                    return in_array($key, $selfRegisteredEmails);
                }, ARRAY_FILTER_USE_KEY);

            $total = $spreadsheetHelper->getRowsCount();
            $total = $total > 0 ? $total : static::PROVISION_LIMIT;

            echo json_encode([
                'total' => $total,
                'pages' => $total / static::PROVISION_LIMIT,
                'pagination' => static::PROVISION_LIMIT,
                'fileRecordId' => $this->fileRecordId
            ]);
        }
        exit;
    }

    // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
    // ANZGO-3654 Modified by Shane Camus, 04/24/2018
    // ANZGO-3914 Modified by Shane Camus, 11/15/2018
    public function provision($fileRecordId, $offset = 0)
    {
        session_write_close();

        $provisioningRecord = $this->pModel->getFileRecord($fileRecordId);
        if (!$provisioningRecord) {
            echo json_encode([
                'success' => false,
                'message' => 'File record not found.'
            ]);
            exit;
        }

        Loader::helper('spreadsheet', $this->pkgHandle);
        $reg = Loader::helper('registration', $this->pkgHandle);

        $this->fileRecordId = $fileRecordId;
        $this->pModel->fileRecordID = $fileRecordId;
        $this->fileId = $provisioningRecord['FileID'];

        $spreadsheetHelper = new SpreadsheetHelper($this->fileId);
        $this->filePath = $spreadsheetHelper->getSpreadsheetPath();
        $records = $spreadsheetHelper->getRows($offset, static::PROVISION_LIMIT);

        foreach ($records as $row) {
            $hasException1 = false;
            $hasException2 = false;
            $hasException3 = false;

            $row = $reg->cleanData($row);
            // SB-311 added by mabrigos 20190903 update email to lowercase before processing
            $row[ProvisioningModel::COLUMN_EMAIL] = strtolower($row[ProvisioningModel::COLUMN_EMAIL]);

            $meta = array(
                'email' => $row[ProvisioningModel::COLUMN_EMAIL],
                'name' => $row[ProvisioningModel::COLUMN_FIRST_NAME] . ' ' . $row[ProvisioningModel::COLUMN_LAST_NAME],
                'type' => $row[ProvisioningModel::COLUMN_USER_ROLE]
            );

            $provisioningID = null;
            $hmProducts = null;

            try {
                $provisioningID = $this->pModel->insertProvisioningUsers($row, $fileRecordId);
                $this->registerUser($row, $reg);
            } catch (UnexpectedValueException $e) {
                $hasException1 = true;
                $this->log(false, 'User failed to be registered ' . $e->getMessage(), $meta);
            }

            try {
                $hmProducts = $this->processUserSubscriptionInGo($provisioningID);
            } catch (UnexpectedValueException $e) {
                $hasException2 = true;
                $this->log(false, 'Subscription failed to be added ' . $e->getMessage(), $meta);
            }

            if ($hmProducts || $row[ProvisioningModel::COLUMN_CLASS_KEY]) {
                try {
                    $this->provisionInHotMaths(
                        $provisioningID,
                        !$row[ProvisioningModel::COLUMN_CLASS_KEY] ? null : $row[ProvisioningModel::COLUMN_CLASS_KEY],
                        !$hmProducts ? null : $hmProducts
                    );
                } catch (UnexpectedValueException $e) {
                    $hasException3 = true;
                    $this->log(false, 'Failed provisioning on HotMaths ' . $e->getMessage(), $meta);
                }
            }

            if (!$hasException1 && !$hasException2 && !$hasException3) {
                $this->log(true, 'User and/or Subscription successfully provisioned', $meta);
            }

            $this->pModel->markProvisionUserCompletedByID($provisioningID);
        }

        $resultTable = $this->displayUsers(1, $this->fileRecordId, true);

        echo json_encode([
            'table' => $resultTable
        ]);
        exit;
    }

    // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
    // ANZGO-3654 Modified by Shane Camus, 04/26/2018
    private function registerUser($record, $validator)
    {
        if (!$validator->validateEmail($record[ProvisioningModel::COLUMN_EMAIL], $this->fileRecordId)) {
            throw new UnexpectedValueException('Invalid email.');
        }

        if (!$validator->validatePassword(
            $record[ProvisioningModel::COLUMN_EMAIL],
            $record[ProvisioningModel::COLUMN_PASSWORD],
            $this->fileRecordId
        )) {
            throw new UnexpectedValueException('Invalid password.');
        }

        if (!$validator->validateGroup(
            $record[ProvisioningModel::COLUMN_EMAIL],
            $record[ProvisioningModel::COLUMN_USER_ROLE],
            $this->fileRecordId
        )) {
            throw new UnexpectedValueException('Invalid group.');
        }

        $validator->registerUser($record, $this->fileRecordId);
    }

    // ANZGO-3642 Modified by John Renzo Sunico, 02/22/2018
    // SB-2 Modified by Michael Abrigos, 01/16/2019
    private function processUserSubscriptionInGo($userID)
    {
        $subsAvailIds = $this->post('subsAvail');
        $provisioningUser = $this->pModel->getProvisioningUserByID($userID);

        if (!$subsAvailIds || !$provisioningUser['uID']) {
            return false;
        }

        $this->pModel->updateProvisioningUserStatusByID($provisioningUser['ID'], 'Adding Subscription.');
        $meta = $this->pModel->processUserSubscription($provisioningUser, $subsAvailIds, $this->post('endDate'));

        if (!is_null($meta)) {
            return $meta;
        }
    }

    /**
     * Provision a User in HotMaths with its class code and products
     *
     * @param $provisioningID
     * @param null $classCode
     * @param null $products
     */
    private function provisionInHotMaths($provisioningID, $classCode = null, $products = null)
    {
        $userClassQueue = new ProvisioningHotMathsUserClassesQueue($this->pModel);
        $provisioningUser = $this->pModel->getProvisioningUserByID($provisioningID);

        $hmUser = $userClassQueue->provisionUserInHotMaths($provisioningUser['uID'], $classCode, $products);

        if (isset($hmUser->success) && !$hmUser->success) {
            $this->provisionWhatCouldBeProvisionedInHotMaths(
                $userClassQueue,
                $hmUser,
                $provisioningUser,
                $provisioningID,
                $products,
                $classCode
            );

            // SB-71 added by jbernardez 20190219
            // add this code here so that tokens are always stored
            $hmUser = $userClassQueue->getHmUserOnly();
            $userClassQueue->storeTokens($hmUser);
            return;
        }

        $userClassQueue->storeTokens($hmUser);

        $status = 'Provisioned';
        $remarks = 'Provisioned successfully';

        if (!is_null($products)) {
            $remarks = 'Subscription added in HM and Go';
        }

        if (!is_null($classCode)) {
            $status = 'AddedClass';
            $remarks = "User has been added to class $classCode";
        }

        $this->pModel->updateProvisioningUserStatusByID($provisioningID, $status, $remarks);
    }

    /**
     * If user cannot be created with product or classKey,
     * create the user then add to the class or subscribe the product (if available)
     *
     * @param $userClassQueue
     * @param $hmUser
     * @param $provisioningUser
     * @param $provisioningID
     * @param $products
     * @param $classCode
     */
    private function provisionWhatCouldBeProvisionedInHotMaths(
        $userClassQueue,
        $hmUser,
        $provisioningUser,
        $provisioningID,
        $products,
        $classCode
    )
    {
        $status = 'CreateHMUserError';
        $remarks = 'Unable to create Hotmaths user';
        $stillNeedsToSubscribeProduct = false;
        $stillNeedsToAddToClass = false;

        if ($hmUser->message === 'Username is already used') {
            $status = 'Existing';
            $remarks = 'User already exists in HM';

            if (!is_null($products)) {
                $userClassQueue->provisionSubscriptionInHotMaths(
                    $provisioningUser['Email'],
                    $products
                );
            }

            if (!is_null($classCode)) {
                $userClassQueue->provisionUserToClassCodeInHotMaths(
                    $provisioningUser['Email'],
                    $classCode
                );
            }

            $this->pModel->updateProvisioningUserStatusByID($provisioningID, $status, $remarks);
            return;
        }

        $userClassQueue->provisionUserInHotMaths($provisioningUser['uID']);

        if (preg_match('/Product .*? is invalid/', $hmUser->message)) {
            $status = 'HMProductProblem';
            $remarks = 'Hotmaths product is invalid';
            $stillNeedsToAddToClass = true;
        }

        if (preg_match('/Product .*? subscriber type does not match/', $hmUser->message)) {
            $status = 'HMProvisionCompatibility';
            $remarks = 'Hotmaths product is incompatible';
            $stillNeedsToAddToClass = true;
        }

        if (!is_null($classCode) && $stillNeedsToAddToClass) {
            $userClassQueue->provisionUserToClassCodeInHotMaths(
                $provisioningUser['Email'],
                $classCode
            );

            $this->pModel->updateProvisioningUserStatusByID($provisioningID, $status, $remarks);
            return;
        }

        if ($stillNeedsToAddToClass) {
            $this->pModel->updateProvisioningUserStatusByID($provisioningID, $status, $remarks);
            return;
        }

        if (preg_match('/Class code is invalid/', $hmUser->message)) {
            $status = 'AddClassError';
            $remarks = "Class code $classCode is invalid";
            $stillNeedsToSubscribeProduct = true;
        }

        if (!is_null($products) && $stillNeedsToSubscribeProduct) {
            $userClassQueue->provisionSubscriptionInHotMaths(
                $provisioningUser['Email'],
                $products
            );

            $this->pModel->updateProvisioningUserStatusByID($provisioningID, $status, $remarks);
            return;
        }

        if ($stillNeedsToSubscribeProduct) {
            $this->pModel->updateProvisioningUserStatusByID($provisioningID, $status, $remarks);
            return;
        }

        $this->pModel->updateProvisioningUserStatusByID($provisioningID, $status, $remarks);
        return;
    }

    // ANZGO-3642 Modified by John Renzo Sunico, 02/22/2018
    public function displayUsers($page = 0, $fileID = 0, $return = false, $totalGigyaUsers = 0, $providerId = null)
    {
        Loader::library('gigya/GigyaAccount');
        $gAccount = new GigyaAccount();

        if ($fileID > 0) {
            $this->fileRecordId = $fileID;
        }

        $users = $this->pModel->getProvisionedUsers($this->fileRecordId, $page);
        $usersTotal = $this->pModel->getTotalProvUsers($this->fileRecordId);
        $allUsers = $this->pModel->getAllProvisionedUsers($this->fileRecordId);
        // GCAP-541 Campion added by machua/mtanada 20191004
        if ($providerId) {
            $migratedUsers = $gAccount->searchLiteUsersBySystemId($allUsers);
        } else {
            $migratedUsers = $gAccount->searchUsersByGoID($allUsers);
        }
        $migratedUsers = json_decode($migratedUsers);
        $pageParams = array(
            'total' => $usersTotal,
            'interval' => 10,
            'activePage' => $page
        );

        ob_start();
        Loader::packageElement('pagination', $this->pkgHandle, $pageParams);
        $pageHtml = ob_get_clean();

        ob_start();
        Loader::packageElement('provisioned_users', $this->pkgHandle, ['users' => $users]);
        $html = ob_get_clean();

        // ANZGO-3258 Modified by John Renzo Sunico, October 13, 2017
        $disp = array(
            'users' => $html,
            'fileRecordId' => $this->fileRecordId,
            'pagination' => $pageHtml,
            'totalGigyaUsers' => $migratedUsers->totalCount,
            'totalTngUsers' => $usersTotal
        );

        if ($return) {
            return $disp;
        }

        echo json_encode($disp);
        exit;
    }

    /**
     * ANZGO-3258 Added by John Renzo S. Sunico, October 13, 2017
     * Exports result of provisioning
     * @return string CSV comma separated data
     */
    public function exportToSheet()
    {
        $fileID = intval($this->post('fileId'));
        $fileID = !empty($fileID) ? $fileID : $this->get('fileId');

        $fileRecord = $this->pModel->getFileRecord($fileID);
        $users = $this->pModel->getAllProvisionedUsers($fileID);

        if (!$fileRecord) {
            http_response_code(404);
            exit;
        }

        $fileName = explode('.', $fileRecord['FileName'])[0];
        $fileName .= '_results_' . date('Y-m-d-h:i:sa') . '.csv';

        header('Content-type: text/csv');
        header('Cache-Control: no-store, no-cache');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        if (!$users) {
            exit;
        }

        $csvData = implode(',', array_keys($users[0])) . "\n";
        foreach ($users as $user) {
            $csvData .= implode(",", array_values($user)) . "\n";
        }

        echo $csvData;
        exit;
    }

    /*
     * modified by mtanada 2019-07-19 Add 2 minutes delay before running schedule
     * expected UTC ISO Format: '2019-07-18T11:18:26.651Z'
     * GCAP-460 modified by scamus 2019-07-26
     * GCAP-Campion modified by machua/mtanada 2019-10-02 add param $providerId
     * */
    public function provisionInGigya($fileRecordId, $providerId = null)
    {
        $this->providerId = $providerId;
        Loader::library('gigya/GigyaExport');
        Loader::library('gigya/GigyaDataFlow');
        Loader::library('gigya/GigyaSchedule');

        $status = ['success' => false, 'message' => null, 'errorCode' => null, 'scheduleId' => null];

        $users = $this->pModel->getAllNewProvisionedUsersInfo($fileRecordId);

        // SB-626 added by mabrigos 20200708
        $selfRegisteredUsers = $_SESSION['selfRegisteredAccount'];
        $encryptPasswordHelper = Loader::helper('bulk_actions', $this->pkgHandle);
        foreach ($users as $key => $user) {
            if ($user['password'] === null || empty($user['password']) || $user['password'] == '') {
                foreach($selfRegisteredUsers as $email => $password) {
                    if ($email == $user['email']) {
                        $users[$key]['password'] = $encryptPasswordHelper->encryptPassword($password);
                    }
                }
            }
        }

        if (!$users) {
            $status['message'] = 'Users already exist in Gigya.';
            echo json_encode($status);
            exit;
        }

        $filename = "$fileRecordId.json";
        $exporter = new GigyaExport();
        $exporter->addUsers($users, $this->providerId);
        $exporter->setCurrentFilename($filename);
        $exportSuccess = $exporter->exportToS3($this->providerId);

        $dataFlow = new GigyaDataFlow();
        if ($exportSuccess) {
            $schedule = new GigyaSchedule();
            if ($this->providerId) {
                $dataFlow->name = "Import Lite Accounts from S3 - TNG";
                $dataFlow->description = "S3 > parse > injectJobId > importLiteAccount > format > S3";
                $dataFlow->steps = $dataFlow->getSteps($dataFlow::$LITE);
                $errorCode = $dataFlow->update(GIGYA_MIGRATION_MASTER_LITE_DATAFLOW_ID);
                $schedule->dataFlowId = GIGYA_MIGRATION_MASTER_LITE_DATAFLOW_ID;
            } else {
                $dataFlow->name = "Cambridge Go Provisioning";
                $dataFlow->description = "Master Provisioning for Cambridge Go.";
                $dataFlow->steps = $dataFlow->getSteps($dataFlow::$CUSTOM);
                $errorCode = $dataFlow->update(GIGYA_MIGRATION_MASTER_DATAFLOW_ID);
                $schedule->dataFlowId = GIGYA_MIGRATION_MASTER_DATAFLOW_ID;
            }
        }

        if ($errorCode === 0) {
            $schedule->name = $filename;
            $schedule->successEmailNotification = GIGYA_ADMIN_EMAILS;
            $schedule->failureEmailNotification = GIGYA_ADMIN_EMAILS;
            $schedule->nextJobStartTime = date(
                'Y-m-d\TH:i:s.000\Z',
                strtotime('+30 seconds', strtotime(gmdate("Y-m-d H:i:s")))
            );

            $scheduleId = $schedule->save();
            $success = (!empty($scheduleId));
            $status['success'] = $success;
            $status['message'] = "Migration success!";
            $status['errorCode'] = $errorCode;
            $status['scheduleId'] = $scheduleId;
            echo json_encode($status);
            die;
        } else {
            $status['message'] = "Migration error";
            $status['errorCode'] = $errorCode;
            echo json_encode($status);
            die;
        }
    }

    public function getGigyaProvisioningStatus($fileId)
    {
        $fileRecord = $this->pModel->getFileRecord($fileId);

        echo json_encode([
            'isDone' => !empty($fileRecord)
        ]);
        exit;
    }

    public function updateGigyaStatus($fileRecordId, $providerId = null)
    {
        $pUsers = $this->pModel->getAllProvisionedUsers($fileRecordId);
        $isChunked = false;

        // Chunk result to a max of 300 elements per array
        // since Gigya only returns 300 results per query.
        if (count($pUsers) > 300) {
            $pUsers = array_chunk($pUsers, 300);
            $isChunked = true;
        }

        if ($isChunked) {
            foreach ($pUsers as $pUser) {
                $this->processUpdateGigyaStatus($pUser, $fileRecordId, $providerId);
            }
        } else {
            $this->processUpdateGigyaStatus($pUsers, $fileRecordId, $providerId);
        }

        $this->displayUsers(0, $fileRecordId, false, 0, $providerId);
        die;
    }

    // GCAP-541 Campion modified by mtanada 20191015
    // GCAP-530 Modified by Shane Camus 11/06/19
    protected function processUpdateGigyaStatus($users, $fileId, $providerId = null)
    {
        Loader::library('gigya/GigyaAccount');
        $gAccount = new GigyaAccount();
	
        if ($providerId) {
            $migratedUsers = $gAccount->searchLiteUsersBySystemId($users);
            $migratedUsers = json_decode($migratedUsers);
            $this->pModel->markLiteUsersInGigya($migratedUsers->results);
		} else {
			$migratedUsers = $gAccount->searchUsersByGoID($users);
			$migratedUsers = json_decode($migratedUsers);
			
			if ($migratedUsers->totalCount > 0) {
				$ids = [];

				foreach ($migratedUsers->results as $gigyaUser) {
					$verifiedResult = $this->autoVerifyUserInGigya($gigyaUser->UID);
					if ($verifiedResult) {
						array_push($ids, $gigyaUser->data->systemIDs[0]->idValue);
					}
				}

				$this->pModel->markSuccessfulMigrationOfUser($ids);
			}
        }

		$this->pModel->markUnsuccessfulMigrationOfUser($fileId);
    }


    // GCAP-530 Added by Shane Camus 11/06/19
    public function autoVerifyUserInGigya($uID)
    {
        Loader::library('gigya/GigyaAccount');
        $gAccount = new GigyaAccount($uID);
        return $gAccount->verifyAccount();
    }

    public function getJobStatus($scheduleId)
    {
        Loader::library('gigya/GigyaJobStatus');
        $job = new GigyaJobStatus();
        $status = $job->getStatusByScheduleId($scheduleId);
        echo json_encode(['status' => $status]);
        die;
    }

    // SB-577 added by mabrigos 20200603
    public function getMultipleEntitlements() {
        $ids = explode(" ", $_POST['entitlementIds']);
        $subscriptions = array();
        $counter = 0;
        foreach($ids as $id) {
            $entitlement = Entitlement::find($id);
            if ($entitlement->Active !== 'Y') {
                continue;
            }

            $description = $entitlement->Description;
            $entitlementId = $entitlement->id;
            if ($entitlement->Type === 'duration') {
                if ($entitlement->Duration > 0) {
                    $due = $entitlement->Duration . ' days';
                } else {
                    $due = 'perpetual';
                }
            } elseif ($entitlement->Type === 'start-end') {
                $due = $entitlement->StartDate->format('Y/m/d H:i:s') .
                ' to ' . $entitlement->EndDate->format('Y/m/d H:i:s');
            } elseif ($entitlement->Type === 'end-of-year') {
                $due = 'school year';
            } else {
                $due = '';
            }

            $product = $entitlement->product()->fetch();
            $productId = $product->id;
            $cmsName = $product->CMS_Name;
            $name = $product->Name;
            $isbn13 = $product->ISBN_13;
            
            $obj = new stdclass;
            $obj->label = "$cmsName : $name : $description / $isbn13 ($due)";
            $obj->value = "$cmsName : $name : $description / $isbn13 ($due)";
            $obj->id = $entitlementId;
            $obj->s_id = $productId; 

            array_push($subscriptions, $obj);
        }

        echo json_encode($subscriptions);
        die;
    }
}
