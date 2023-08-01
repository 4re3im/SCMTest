<?php

defined('C5_EXECUTE') || die(_('Access Denied.'));
require_once DIR_BASE . '/vendor/autoload.php';
// SB-577 added by mabrigos 20200603
Loader::library('hub-sdk/autoload');
use HubEntitlement\Models\Entitlement;
use Ramsey\Uuid\Uuid;

class DashboardGlobalGoProvisioningJobController extends Controller
{
    const PROVISION_LIMIT = 1;

    private $pModel;
    private $fileId;
    private $filePath;
    private $fileRecordId;
    private $pkgHandle = 'global_go_provisioning';
    // GCAP-541 Campion added by machua/mtanada 20191004
    private $providerId;
    // SB-674 Added by mtanada 20200901
    private $queueNumber;
    // GCAP-990 added by mabrigos
    private $privileges = array();
    private $isPrivilegesExisting = false;

    // ANZGO-3654 Modified by Shane Camus, 04/24/2018
    public function __construct()
    {
        parent::__construct();
        Loader::library('FileLog/FileLogger');
        Loader::model('provisioning_hotmaths_user_class_queue', $this->pkgHandle);
        Loader::model('provisioning', $this->pkgHandle);
        Loader::model('job_list', $this->pkgHandle);
        Loader::model('subscriptions', 'go_gigya');
        $this->pModel = new ProvisioningHotmathsModel();
    }

    // ANZGO-3528 Modified by John Renzo S. Sunico
    // ANZGO-3914 Modified by Shane Camus 11/12/18
    public function on_start()
    {
        $htmlHelper = Loader::helper('html');
        $cssPath = (string)$htmlHelper->css('styles.css', $this->pkgHandle)->file . "?v=1.1";
        $jsPath = (string)$htmlHelper->javascript('scripts.js', $this->pkgHandle)->file . "?v=1.0";
        $jsJobPath = (string)$htmlHelper->javascript('jobs.js', $this->pkgHandle)->file . "?v=1.0";

        $this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . $cssPath . '">');
        $this->addFooterItem('<script type="text/javascript" src="' . $jsPath . '"></script>');
        $this->addFooterItem('<script type="text/javascript" src="' . $jsJobPath . '"></script>');
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

    // START ==========================================================

    /**
     * SB-674 Added by mtanada 20200827
     */
    public function saveProvisionJob() {
        $fileRecordID = $this->uploadFile();
        $products = [];
        if ($fileRecordID) {
            $this->fileRecordId = $fileRecordID;
            $provisioningRecord = $this->pModel->getFileRecord($this->fileRecordId);
            $subsAvailIds = $this->post('subsAvail');
            $entitlements = $this->post('subscriptionIds');
            if (!empty($subsAvailIds)) {
                $products = $subsAvailIds;
            } else if (!empty($entitlements)) {
                $products[] = $entitlements;
            }
            $isSaved = $this->pModel->saveJobQueue($provisioningRecord['FileName'], $this->fileRecordId, $products);
            if ($isSaved) {
                $this->checkProvisionJob();
            }
            die; // To not proceed with postSubmitForm.. After this method, checkProvisionJob should be called..
        }

    }

    /**
     * SB-674 Added by jdchavez 20200828
     */
    public function checkProvisionJob() {
        $result = $this->pModel->checkProvisionQueue();
        $this->updateProvisionJob($result);
    }

    /**
     * SB-674 Added by mtanada 20200901
     */
    public function updateProvisionJob($jobData) {
        $this->queueNumber = 1;
        if ($jobData[0]['Status'] === 'pending') {
            $this->pModel->updateJobQueue((int)$jobData[0]['ID'], $this->queueNumber++, 'started');
        } else if ($jobData[0]['Status'] === 'started') {
            $this->queueNumber++;
        }

        foreach ($jobData as $key => $value) {
            if ($key > 0 && $value['Status'] === 'pending') {
                $this->pModel->updateJobQueue($value['ID'], $this->queueNumber++);
            }
        }
        $this->displayJobs(1);
    }

    // END ============================================================

    public function startProvisioning($fileRecordID)
    {
        //$fileRecordID = $this->uploadFile();
        //if ($fileRecordID) {
        $this->fileRecordId = $fileRecordID;

        Loader::helper('spreadsheet', $this->pkgHandle);
        $provisioningRecord = $this->pModel->getFileRecord($fileRecordID);
        $spreadsheetHelper = new SpreadsheetHelper($provisioningRecord['FileID']);

        // SB-626 added by mabrigos 20200708
        $records = $spreadsheetHelper->getRows(0, false);
        $emails = array();
        $emailWithPasswords = array();
        foreach ($records as $row) {
            $emails[] = $row[ProvisioningHotmathsModel::COLUMN_EMAIL];
            $emailAndPasswords[$row[ProvisioningHotmathsModel::COLUMN_EMAIL]] = $row[ProvisioningHotmathsModel::COLUMN_PASSWORD];
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
        //}
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
            $row[ProvisioningHotmathsModel::COLUMN_EMAIL] = strtolower($row[ProvisioningHotmathsModel::COLUMN_EMAIL]);
            $uuid = Uuid::uuid4();
            $row[ProvisioningHotmathsModel::COLUMN_UID] = $uuid->toString();

            $meta = array(
                'email' => $row[ProvisioningHotmathsModel::COLUMN_EMAIL],
                'name' => $row[ProvisioningHotmathsModel::COLUMN_FIRST_NAME] . ' ' . $row[ProvisioningHotmathsModel::COLUMN_LAST_NAME],
                'type' => $row[ProvisioningHotmathsModel::COLUMN_USER_ROLE]
            );

            $provisioningID = null;
            $hmProducts = null;

            try {
                $provisioningID = $this->pModel->insertProvisioningUsers($row, $fileRecordId);
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

            // SB-675 Modified by mtanada 20200814
            if ($hmProducts || $row[ProvisioningHotmathsModel::COLUMN_SCHOOL_ID] ||
                $row[ProvisioningHotmathsModel::COLUMN_CLASS_NAME_INT_MATH] ||
                $row[ProvisioningHotmathsModel::COLUMN_CLASS_NAME_HUMANITIES] ||
                $row[ProvisioningHotmathsModel::COLUMN_CLASS_NAME_ICEEM]) {
                try {
                    $this->provisionInHotMaths(
                        $provisioningID,
                        !$row[ProvisioningHotmathsModel::COLUMN_SCHOOL_ID] ? null : $row[ProvisioningHotmathsModel::COLUMN_SCHOOL_ID],
                        !$row[ProvisioningHotmathsModel::COLUMN_CLASS_NAME_INT_MATH] ? null : $row[ProvisioningHotmathsModel::COLUMN_CLASS_NAME_INT_MATH],
                        !$row[ProvisioningHotmathsModel::COLUMN_CLASS_NAME_HUMANITIES] ? null : $row[ProvisioningHotmathsModel::COLUMN_CLASS_NAME_HUMANITIES],
                        !$row[ProvisioningHotmathsModel::COLUMN_CLASS_NAME_ICEEM] ? null : $row[ProvisioningHotmathsModel::COLUMN_CLASS_NAME_ICEEM],
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

    // ANZGO-3642 Modified by John Renzo Sunico, 02/22/2018
    // SB-2 Modified by Michael Abrigos, 01/16/2019
    private function processUserSubscriptionInGo($userID)
    {
        $subsAvailIds = $this->post('subsAvail');
        $provisioningUser = $this->pModel->getProvisioningUserByID($userID);

        if (!$subsAvailIds || !$provisioningUser['uID']) {
            return false;
        }

        if (!$this->isPrivilegesExisting) {
            $this->buildPrivileges($subsAvailIds);
            $this->isPrivlegesExisting = true;
        }

        $this->pModel->updateProvisioningUserStatusByID($provisioningUser['ID'], 'Adding Subscription.');
        $meta = $this->pModel->processUserSubscription(
            $provisioningUser,
            $subsAvailIds,
            $this->post('endDate'),
            $this->privileges
        );

        if (!is_null($meta)) {
            return $meta;
        }
    }

    /**
    * @param array $subsAvailIds
    * Build an array of privileges using entitlement as key and privileges as value
    */
    public function buildPrivileges($subsAvailIds)
    {
        foreach($subsAvailIds as $saID) {
            if (!array_key_exists($saID, $this->privileges)) {
                $tabs = $this->pModel->getHubProductTabs($saID);
                $subscriptions = new Subscriptions();
                $productTabs = array();
    
                foreach ($tabs as $tab) {
                    if (isset($tab['id'])) {
                        array_push($productTabs, $tab['id']);
                    }
                }
    
                $titleIds = $subscriptions->getTitleIds($productTabs);
                $listTitletabs = array();
                if ($titleIds) {
                    foreach ($titleIds as $titleId => $value) {
                        $listTitletabs[$value['TitleID']][] = (int)$value['tabId'];
                    }
                    $titleSeriesIds = $subscriptions->getSeriesIds($titleIds);
                }
    
                if (empty($listTitletabs)) {
                    $this->privileges[$saID] = null;
                } else {
                    foreach ($titleSeriesIds as $titleSeriesId) {
                        $titles = array();
                        if (array_key_exists($titleSeriesId['titleId'], $listTitletabs)) {
                            $tabs = $listTitletabs[$titleSeriesId['titleId']];
                        }
                        $titles[] = array(
                            "id" => (int) $titleSeriesId['titleId'],
                            "tabs" => $tabs
                        );
                        $privileges[] = array(
                            "series" => array(
                                "id" => (int) $titleSeriesId['seriesId']
                            ),
                            "titles" => $titles
                        );
                    }
                    $this->privileges[$saID] = $privileges;
                }
            }
        }
    }

    /**
     * Provision a User in HotMaths with school ID, class name/s and products
     *
     * @param $provisioningID
     * @param null $schoolId
     * @param null $classNameIntMath
     * @param null $classNameHumanities
     * @param null $classNameIceem
     * @param null $products
     */
    private function provisionInHotMaths(
        $provisioningID,
        $schoolId = null,
        $classNameIntMath = null,
        $classNameHumanities = null,
        $classNameIceem = null,
        $products = null
    )
    {
        $userClassQueue = new ProvisioningHotMathsUserClassesQueue($this->pModel);
        $provisioningUser = $this->pModel->getProvisioningUserByID($provisioningID);
        $classNames = [
            'intMath'    => $classNameIntMath,
            'humanities' => $classNameHumanities,
            'iceem'      => $classNameIceem
        ];
        if ($classNameIntMath === null && $classNameHumanities === null && $classNameIceem === null) {
            $classNames = [];
        }

        $hmUser = $userClassQueue->provisionUserInHotMaths(
            $provisioningUser,
            $schoolId,
            $classNames,
            $products
        );

        if (isset($hmUser->success) && !$hmUser->success) {
            $this->provisionWhatCouldBeProvisionedInHotMaths(
                $userClassQueue,
                $hmUser,
                $provisioningUser,
                $provisioningID,
                $products,
                $userClassQueue->classCodes,
                $schoolId
            );

            // SB-71 added by jbernardez 20190219
            // add this code here so that tokens are always stored
            $hmUser = $userClassQueue->getHmUserByUsername($provisioningUser['Email']); //990
            $userClassQueue->storeTokens($hmUser);
            return;
        }

        $userClassQueue->storeTokens($hmUser);

        $status = 'Provisioned';
        $remarks = 'Provisioned successfully';

        if (!is_null($products)) {
            $remarks = 'Subscription added in HM and Go';
        }

        // SB-660 Added by mtanada 20200730
        if (!is_null($schoolId)) {
            if ($hmUser->schoolId) {
                $status = 'AddedSchool';
                $remarks = "User has been added to school $schoolId";
            } else {
                $status = 'AddSchoolError';
                $remarks = "School ID $schoolId is invalid";
            }

        }

        // SB-675 Modified by mtanada 20200814
        if (!empty($userClassQueue->classCodes)) {
            $status = 'AddedClass';
            $remarks = "User has been added to a class or classes";
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
     * @param $classCodes
     * @param $schoolId
     */
    private function provisionWhatCouldBeProvisionedInHotMaths(
        $userClassQueue,
        $hmUser,
        $provisioningUser,
        $provisioningID,
        $products,
        $classCodes,
        $schoolId
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

            // SB-675 Modified by mtanada 20200814
            if (!empty($classCodes)) {
                $userClassQueue->provisionUserToClassCodes(
                    $provisioningUser['Email'],
                    $classCodes
                );

                if (preg_match('/Class code is invalid/', $hmUser->message)) {
                    $status = 'AddClassError';
                    $remarks = "Class is invalid";
                }
            }

            // SB-660 Added by mtanada 20200728
            if (!is_null($schoolId)) {
                $userClassQueue->provisionSchoolIdInHotMaths(
                    $provisioningUser['Email'],
                    $schoolId
                );

                if (preg_match('/School id not valid/', $hmUser->message)) {
                    $status = 'AddSchoolError';
                    $remarks = "School ID $schoolId is invalid";
                }
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

        // SB-675 Added by mtanada 20200814
        if (!empty($classCodes) && $stillNeedsToAddToClass) {
            $userClassQueue->provisionUserToClassCodes(
                $provisioningUser['Email'],
                $classCodes
            );

            if (preg_match('/Class code is invalid/', $hmUser->message)) {
                $status = 'AddClassError';
                $remarks = $hmUser->message;
            }

            $this->pModel->updateProvisioningUserStatusByID($provisioningID, $status, $remarks);
            return;
        }

        if ($stillNeedsToAddToClass) {
            $this->pModel->updateProvisioningUserStatusByID($provisioningID, $status, $remarks);
            return;
        }

        if (preg_match('/Class code is invalid/', $hmUser->message)) {
            $status = 'AddClassError';
            $remarks = $hmUser->message;
            $stillNeedsToSubscribeProduct = true;
        }

        // SB-660 Added by mtanada 20200730
        if (preg_match('/School id not valid/', $hmUser->message)) {
            $status = 'AddSchoolError';
            $remarks = "School ID $schoolId is invalid";
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
            $migratedUsers = $gAccount->searchLiteUsersByGigyaUID($allUsers);
        } else {
            $migratedUsers = $gAccount->searchUsersByGigyaUID($allUsers);
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
        $encryptPasswordHelper = Loader::helper('bulk_actions', $this->pkgHandle);

        $status = ['success' => false, 'message' => null, 'errorCode' => null, 'scheduleId' => null];

        $provisioningRecord = $this->pModel->getFileRecord($fileRecordId);
        if (!$provisioningRecord) {
            echo json_encode([
                'success' => false,
                'message' => 'File record not found.'
            ]);
            exit;
        }

        // gcap-990 added by mabrigos use provisioning sheet 
        $userIds = $this->pModel->getProvisionedGlobalGoUsers($fileRecordId);

        Loader::helper('spreadsheet', $this->pkgHandle);
        $reg = Loader::helper('registration', $this->pkgHandle);

        $this->fileId = $provisioningRecord['FileID'];

        $spreadsheetHelper = new SpreadsheetHelper($this->fileId);
        $this->filePath = $spreadsheetHelper->getSpreadsheetPath();
        $records = $spreadsheetHelper->getRows($offset, 0);

        $users = array();
        foreach ($records as $row) {
            foreach ($userIds as $key => $val) {
                if ($val['Email'] === $row[ProvisioningHotmathsModel::COLUMN_EMAIL]) {
                    $uID = $val['uID'];
                }
            }
            $row = $reg->cleanData($row);
            $row[ProvisioningHotmathsModel::COLUMN_EMAIL] = strtolower($row[ProvisioningHotmathsModel::COLUMN_EMAIL]);

            $meta = array(
                'uID' => $uID,
                'firstName' => $row[ProvisioningHotmathsModel::COLUMN_FIRST_NAME],
                'lastName' => $row[ProvisioningHotmathsModel::COLUMN_LAST_NAME],
                'state' => $row[ProvisioningHotmathsModel::COLUMN_STATE],
                'postCode' => $row[ProvisioningHotmathsModel::COLUMN_POST_CODE],
                'email' => $row[ProvisioningHotmathsModel::COLUMN_EMAIL],
                'password' => $encryptPasswordHelper->encryptPassword($row[ProvisioningHotmathsModel::COLUMN_PASSWORD]),
                'role' => $row[ProvisioningHotmathsModel::COLUMN_USER_ROLE],
                "isValidated" => 1,
                "isActive" => 1
            );
            array_push($users, $meta);
        }

        // SB-626 added by mabrigos 20200708
        $selfRegisteredUsers = $_SESSION['selfRegisteredAccount'];
        
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
        $exporter->addUsers($users, $this->providerId, true);
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
            $migratedUsers = $gAccount->searchLiteUsersByGigyaUID($users);
            $migratedUsers = json_decode($migratedUsers);
            $this->pModel->markLiteUsersInGigya($migratedUsers->results);
		} else {
			$migratedUsers = $gAccount->searchUsersByGigyaUID($users);
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

    /**
     * SB-674 Added by mtanada 20200901
     * @param int | $page
     */
    public function displayJobs($page = 0)
    {
        $jobs = $this->pModel->getJobStatusByAdmin($page);
        $jobCount = $this->pModel->getJobCountByAdmin();

        $pageParams = array(
            'total' => $jobCount[0]['jobCount'],
            'activePage' => $page
        );

        ob_start();
        Loader::packageElement('jobs_pagination', $this->pkgHandle, $pageParams);
        $pageHtml = ob_get_clean();

        ob_start();
        Loader::packageElement('provisioned_jobs', $this->pkgHandle, ['jobs' => $jobs]);
        $html = ob_get_clean();

        $display = array(
            'jobs' => $html,
            'pagination' => $pageHtml
        );

        echo json_encode($display);
        exit;
    }
}
