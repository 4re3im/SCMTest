<?php

defined('C5_EXECUTE') || die(_('Access Denied.'));

// SB-577 added by mabrigos 20200603
Loader::library('hub-sdk/autoload');

use HubEntitlement\Models\Entitlement;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


class DashboardGlobalGoProvisioningSetupController extends Controller
{
    const PROVISION_LIMIT = 1;
    const COLUMN_EMAIL = 0;
    const FILE_ROWS_LIMIT = -1;
    const MINIMUM_FIELD_COUNT = 5;

    private $pModel;
    private $fileId;
    private $filePath;
    private $fileRecordId;
    private $registrationHelper;
    // GCAP-1181 added by mabrigos
    private $bulkActionsHelper;
    private $pkgHandle = 'global_go_provisioning';
    // GCAP-541 Campion added by machua/mtanada 20191004
    private $providerId;
    // SB-912 added by mabrigos 10/20/21
    private $accountType;
    private $status;
    // GCAP-990 added by mabrigos
    private $privileges = array();
    private $isPrivilegesExisting = false;

    // ANZGO-3654 Modified by Shane Camus, 04/24/2018
    public function __construct()
    {
        parent::__construct();
        $this->registrationHelper = Loader::helper('registration', $this->pkgHandle);
        Loader::library('FileLog/FileLogger');
        Loader::model('provisioning_hotmaths_user_class_queue', $this->pkgHandle);
        Loader::model('provisioning', $this->pkgHandle);
        Loader::model('subscriptions', 'go_gigya');
        Loader::helper('spreadsheet', $this->pkgHandle);
        // SB-912 added by mabrigos
        Loader::library('gigya/GigyaAccount');
        Loader::library('gigya/GigyaExport');
        Loader::library('gigya/GigyaDataFlow');
        Loader::library('gigya/GigyaSchedule');
        Loader::library('gigya/GigyaJobStatus');
        Loader::library('gigya/CWSExport');

        $this->pModel = new ProvisioningHotmathsModel();

        // create a log channel 20212801
        $this->log = new Logger('Provisioning');
        $this->log->pushHandler(
            new StreamHandler('logs/provisioning.' . date("Y-m-d", time()) .'.log', Logger::INFO)
        );

    }

    // ANZGO-3528 Modified by John Renzo S. Sunico
    // ANZGO-3914 Modified by Shane Camus 11/12/18
    public function on_start()
    {
        Loader::helper('spreadsheet', $this->pkgHandle);
        $htmlHelper = Loader::helper('html');
        $cssPath = (string)$htmlHelper->css('styles.css', $this->pkgHandle)->file . "?v=1.1";
        $jsPath = (string)$htmlHelper->javascript('scripts.js', $this->pkgHandle)->file . "?v=1.46";

        // GCAP-1181 added by mabrigos
        $this->bulkActionsHelper = Loader::helper('bulk_actions', $this->pkgHandle);

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

            $provisioningRecord = $this->pModel->getFileRecord($fileRecordID);
            $spreadsheetHelper = new SpreadsheetHelper($provisioningRecord['FileID']);
            // GCAP-1181 modified by mabrigos 20201214
            $records = $spreadsheetHelper->getRows(0, static::FILE_ROWS_LIMIT);
            // Minimum data supplied in the file
            $this->bulkActionsHelper->setRowElementCount(static::MINIMUM_FIELD_COUNT);

            // SB-886 added by mtanada 20210715
            $withSchoolOid = $_POST['accountType'] === 'institution' ? true : false;
            foreach ($records as $record) {
                $row = $this->bulkActionsHelper->sanitizeEntry($record);
                $validationMessage = $this->bulkActionsHelper->validateEntry($row, $withSchoolOid);
                if (!empty($validationMessage)) {
                    echo json_encode([
                        'error' => true,
                        'message' => $validationMessage
                    ]);
                    exit;
                }
            }
            $total = $spreadsheetHelper->getRowsCount();
            $total = $total > 0 ? $total : static::PROVISION_LIMIT;
            $pages = ceil(total / static::PROVISION_LIMIT);
            $pages = $pages > 0 ? $pages : 1;

            echo json_encode([
                'total' => $total,
                'pages' => $pages,
                'pagination' => static::PROVISION_LIMIT,
                'fileRecordId' => $this->fileRecordId,
                'error' => false
            ]);
        }
        exit;
    }

    /**
     * Save file records into database.
     *
     * @param $fileRecordId
     * @param $limit
     * @param $page
     */
    public function saveRecords($fileRecordId, $limit, $page)
    {
        session_write_close();
        $this->getFileId($fileRecordId);

        // Compute for offset
        $offset = ($limit * $page) - $limit;

        $spreadsheetHelper = new SpreadsheetHelper($this->fileId);
        $records = $spreadsheetHelper->getRows($offset, $limit);
        $this->pModel->insertProvisioningUsers($records, $this->fileId, $this->registrationHelper);

        echo json_encode([
            'success' => true,
            'message' => count($records) . ' has been saved.',
        ]);
        exit;
    }

    /**
     * Get Gigya records using email addresses uploaded.
     *
     * @param $fileRecordId
     * @param $limit
     * @param $page
     */
    public function getUsersFromGigya($fileRecordId, $limit, $page)
    {
        session_write_close();
        $this->getFileId($fileRecordId);
        $gigyaAccount = new GigyaAccount();

        $this->log->info(
            'Get Users from DB ('. $fileRecordId .') time Start: '. date(DateTime::ISO8601)
        );
        $records = $this->pModel->getProvisionedGlobalGoUsers($this->fileId, $limit, $page);
        $emailsFromRecords = array_column($records, 'Email');

        try {
            $this->log->info(
                'Search user from Gigya ('. $fileRecordId .') time Start: '. date(DateTime::ISO8601)
            );
            $results = $gigyaAccount->searchUsersByEmails($emailsFromRecords);
        } catch (Exception $e) {
            $this->log->error($e);
            return false;
        }

        if ($results === false) {
            echo json_encode([
                'success' => false,
                'message' => 'Unable to get users from Gigya'
            ]);
            exit;
        }
        $this->log->info(
            'array_map/array_diff ('. $fileRecordId .') time Start: '. date(DateTime::ISO8601)
        );
        // SB-753 Modified by mtanada 20210120
        $emailsFromGigya = array_map('strtolower', array_column($results, 'email'));
        $emailsForImport = array_diff($emailsFromRecords, $emailsFromGigya);

        $this->log->info(
            'markUsersAlreadyInGigya ('. $fileRecordId .') time Start: '. date(DateTime::ISO8601)
        );
        foreach ($results as $account) {
            $this->pModel->markUsersAlreadyInGigya($account, $this->fileId);
        }

        $this->log->info(
            'markUsersForImport ('. $fileRecordId .') time Start: '. date(DateTime::ISO8601)
        );
        $this->pModel->markUsersForImport($emailsForImport, $this->fileId);

        $this->log->info(
            'getUsersFromGigya() ('. $fileRecordId .') time End: '. date(DateTime::ISO8601)
        );

        echo json_encode([
            'success' => true,
            'message' => 'Users ready for Provisioning'
        ]);
        exit;
    }

    // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
    // ANZGO-3654 Modified by Shane Camus, 04/24/2018
    // ANZGO-3914 Modified by Shane Camus, 11/15/2018
    // SB-912 modified by mabrigos
    public function provision($fileRecordId, $offset = 0, $acctType, $skip = false)
    {
        session_write_close();
        $this->getFileId($fileRecordId);
        $options = [
            'limit' => static::PROVISION_LIMIT,
            'page' => $offset + 1
        ];

        if ($skip && $acctType === 'liteidp') {
            $gigyaAccount = new GigyaAccount();
            $users = $this->pModel->getUsersForSubscriptionUpdate($this->fileId, $options);
            $gigyaResults = json_decode($gigyaAccount->searchLiteUsersByEmail($users));
            foreach ($gigyaResults->results as $result) {
                $this->pModel->setLiteUserUid($result->UID, $result->profile->email, $this->fileId);
            }
        } else if (!$skip && $acctType === 'liteidp') {
            echo json_encode([
                'success' => true,
                'message' => 'Skipping resource provision for lite users'
            ]);
            exit;
        }

        $users = $this->pModel->getUsersForSubscriptionUpdate($this->fileId, $options);

        foreach ($users as $user) {
            $isSuccessful = true;

            $provisioningID = $user['uID'];
            $hmProducts = null;

            try {
                $hmProducts = $this->processUserSubscriptionInGo($provisioningID);
            } catch (UnexpectedValueException $e) {
                $isSuccessful = false;
                $this->log(false, 'Subscription failed to be added ' . $e->getMessage(), $user);
            }
            // GCAP-1041 added by mabrigos
            $hmProducts = !$hmProducts ? NULL : $hmProducts;

            if ($hmProducts ||
                $user['HMSchoolID'] !== '' ||
                $user['ClassNameMaths'] !== '' ||
                $user['ClassNameHumanities'] !== '' ||
                $user['ClassNameICEEM'] !== '')
            {
                try {
                    $this->provisionInHotMaths(
                        $user,
                        $user['HMSchoolID'] !== '' ? $user['HMSchoolID'] : null,
                        $user['ClassNameMaths'] !== '' ? $user['ClassNameMaths'] : null,
                        $user['ClassNameHumanities'] !== '' ? $user['ClassNameHumanities'] : null,
                        $user['ClassNameICEEM'] !== '' ? $user['ClassNameICEEM'] : null,
                        $hmProducts
                    );
                } catch (UnexpectedValueException $e) {
                    $isSuccessful = false;
                    $this->log(false, 'Failed provisioning on HotMaths ' . $e->getMessage(), $user);
                }
            }

            if ($isSuccessful) {
                $this->log(true, 'User and/or Subscription successfully provisioned', $user);
            }

            $this->pModel->markProvisionUserCompletedByUID($provisioningID);
        }

        $resultTable = $this->displayUsers(1, true);

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

        if (!$subsAvailIds) {
            return false;
        }

        if (!$this->isPrivilegesExisting) {
            $this->buildPrivileges($subsAvailIds);
            $this->isPrivlegesExisting = true;
        }

        $this->pModel->updateProvisioningUserStatusByID($provisioningUser['uID'], 'Adding Subscription.');
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
        foreach ($subsAvailIds as $saID) {
            $privileges = array();
            if (!array_key_exists($saID, $this->privileges)) {
                $tabs = $this->pModel->getHubProductTabs($saID);
                $isDemo = $tabs['isDemo'];
                $tabs = $tabs['tabs'];
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

                // GCAP-1046 modified by mabrigos 20201123
                if (empty($listTitletabs) || empty($titleSeriesIds)) {
                    $this->privileges[$saID] = array();
                } else {
                    foreach ($titleSeriesIds as $titleSeriesId) {
                        $titles = array();
                        if (array_key_exists($titleSeriesId['titleId'], $listTitletabs)) {
                            $tabs = $listTitletabs[$titleSeriesId['titleId']];
                        }
                        $titles[] = array(
                            "id" => (int)$titleSeriesId['titleId'],
                            "tabs" => $isDemo ? array() : $tabs
                        );
                        $privileges[] = array(
                            "series" => array(
                                "id" => (int)$titleSeriesId['seriesId']
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
        $user,
        $schoolId = null,
        $classNameIntMath = null,
        $classNameHumanities = null,
        $classNameIceem = null,
        $products = null
    ) {
        $userClassQueue = new ProvisioningHotMathsUserClassesQueue($this->pModel);
        $provisioningUser = $this->pModel->getProvisioningUserByID($user['uID']);
        $classNames = [
            'intMath' => $classNameIntMath,
            'humanities' => $classNameHumanities,
            'iceem' => $classNameIceem
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
                $user['uID'],
                $products,
                $userClassQueue->classCodes,
                $schoolId
            );

            // SB-71 added by jbernardez 20190219
            // add this code here so that tokens are always stored
            //$hmUser = $userClassQueue->getHmUserByUsername($provisioningUser['Email']);
            // $userClassQueue->storeTokens($hmUser);
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

        $this->pModel->updateProvisioningUserStatusByID($user['uID'], $status, $remarks);
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
    ) {
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

        $userClassQueue->provisionUserInHotMaths($provisioningUser, $schoolId, $classCodes);

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
    public function displayUsers($page = 0, $return = false, $totalGigyaUsers = 0, $providerId = null)
    {
        $gAccount = new GigyaAccount();

        $users = $this->pModel->getProvisionedUsers($this->fileId, $page);
        $usersTotal = $this->pModel->getTotalProvUsers($this->fileId);
        $allUsers = $this->pModel->getAllProvisionedUsers($this->fileId);
        // GCAP-541 Campion added by machua/mtanada 20191004
        if ($providerId) {
            $migratedUsers = $gAccount->searchLiteUsersByEmail($allUsers);
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
        $postId = intval($this->post('fileId'));
        $getId = intval($this->get('fileId'));
        $fileRecordId = !empty($postId) ? $postId : $getId;

        $this->getFileId($fileRecordId);

        $fileRecord = $this->pModel->getFileRecord($fileRecordId);
        $users = $this->pModel->getAllProvisionedUsers($this->fileId);

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
     * SB-868 modified by mtanada 2021-06-22 add param $accountType
     * SB-912 modified by mabrigos separate shared df and go df 10/20/21
     * */
    public function provisionInGigya($fileRecordId, $accountType = null, $providerId = null)
    {
        $this->setProvisioningType($accountType, $providerId);
        $this->getFileId($fileRecordId);
        $this->setStatus();

        $filename = 'dataload_go-' . date('YmdHis') . '-provision-' . $fileRecordId . '.json';

        if ($this->accountType === 'fullnoidp' || $this->accountType === 'liteidp') {
            $this->provisionWithGoDataflow($filename);
            return;
        }

        $this->provisionWithSharedDataflow($filename);
    }

    public function provisionWithSharedDataflow($filename)
    {
        // SB-868 modified by mtanada 20210622, SB-886 modified by mtanada 20210714
        // Add or create user with IdP attribution || Institution using SIP flow
        $usersForS3 = $this->pModel->getProvisioningUsersByFileId($this->fileId);
        $config = [
            'bucket' => CWS_GIGYA_S3_BUCKET,
            'filename' => $filename
        ];
        $exporter = new CWSExport($config);

        if ($this->accountType === 'institution') {
            $exporter->setMode($exporter::ATTRIBUTE_INSTITUTION);
            $exporter->attributeInstituteToUsers($usersForS3);
        } else {
            $exporter->setMode($exporter::ATTRIBUTE_IDP);
            $exporter->attributeIdP($usersForS3, $this->providerId);
        }

        $this->status['success'] = $exporter->exportToS3();
        $this->status['message'] = "File uploaded to S3.";
        echo json_encode($this->status);
        die;
    }

    /*
     * SB-912 added by mabrigos 10/20/21
     * separated logic for provisioning using Go DF vs SIP
     * @param $filename
     * */
    public function provisionWithGoDataflow($filename)
    {
        $users = $this->pModel->getUsersForGigyaImport($this->fileId);

        if (empty($users)) {
            $resp = $this->displayUsers(0, true);
            $resp['message'] = "Users are in Gigya.";
            $resp['errorCode'] = null;
            $resp['scheduleId'] = null;
            $resp['success'] = true;
            echo json_encode($resp);
            exit;
        }

        $exporter = new GigyaExport();
        $exporter->addUsers($users, $this->providerId, true);
        $exporter->setCurrentFilename($filename);
        $exportSuccess = $exporter->exportToS3($this->providerId);

        if ($exportSuccess) {
            $dataFlow = new GigyaDataFlow();
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
            $this->status['success'] = $success;
            $this->status['message'] = "Migration success!";
            $this->status['errorCode'] = $errorCode;
            $this->status['scheduleId'] = $scheduleId;
            echo json_encode($this->status);
            die;
        } else {
            $this->status['message'] = "Migration error";
            $this->status['errorCode'] = $errorCode;
            echo json_encode($this->status);
            die;
        }
    }

    public function setStatus()
    {
        $this->status = [
            'success' => false, 
            'message' => null, 
            'errorCode' => null, 
            'scheduleId' => null
        ];
    }

    public function setProvisioningType($accountType, $providerId)
    {
        $this->accountType = $accountType;
        $this->providerId = $providerId;
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
        $this->getFileId($fileRecordId);

        $pUsers = $this->pModel->getAllProvisionedUsers($this->fileId);
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

        $data = $this->displayUsers(0, true, 0, $providerId);
        $data['success'] = true;
        echo json_encode($data);
        die;
    }

    // GCAP-541 Campion modified by mtanada 20191015
    // GCAP-530 Modified by Shane Camus 11/06/19
    // GCAP-1042 Modified by mtanada 20210629 searchLiteUsersByEmail
    protected function processUpdateGigyaStatus($users, $fileRecordId, $providerId = null)
    {
        $gAccount = new GigyaAccount();

        $this->getFileId($fileRecordId);

        if ($providerId) {
            $migratedUsers = $gAccount->searchLiteUsersByEmail($users);
            $migratedUsers = json_decode($migratedUsers);
            $this->pModel->markLiteUsersInGigya($migratedUsers->results, $this->fileId);
        } else {
            $migratedUsers = $gAccount->searchUsersByGigyaUID($users);

            if (!$migratedUsers) {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'Unable to get Gigya migration status.'
                    ]
                );
                exit;
            }

            $migratedUsers = json_decode($migratedUsers);

            if ($migratedUsers->totalCount > 0) {
                $ids = [];
                foreach ($migratedUsers->results as $gigyaUser) {
                    array_push($ids, $gigyaUser->UID);
                }
                $this->pModel->markSuccessfulMigrationOfUser($ids);
            }
        }

        $this->pModel->markUnsuccessfulMigrationOfUser($this->fileId);
    }

    public function getJobStatus($scheduleId)
    {
        $job = new GigyaJobStatus();
        $status = $job->getStatusByScheduleId($scheduleId);
        echo json_encode(['status' => $status]);
        die;
    }

    // SB-577 added by mabrigos 20200603
    public function getMultipleEntitlements()
    {
        $ids = explode(" ", $_POST['entitlementIds']);
        $subscriptions = array();
        $counter = 0;
        foreach ($ids as $id) {
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

    public function getFileId ($fileRecordId)
    {
        $fileRecord = $this->pModel->getFileRecord($fileRecordId);

        if (!$fileRecord) {
            echo json_encode([
                'success' => false,
                'message' => 'File record not found.'
            ]);
            exit;
        }

        $this->fileRecordId = $fileRecordId;
        $this->fileId = $fileRecord['FileID'];
    }
}
