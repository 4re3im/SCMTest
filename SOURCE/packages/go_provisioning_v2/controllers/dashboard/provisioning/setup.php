<?php

defined('C5_EXECUTE') || die(_('Access Denied.'));

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
    private $pkgHandle = 'go_provisioning_v2';
    // GCAP-541 Campion added by machua/mtanada 20191004
    private $providerId;

    // mtanada 20200201
    private $hmUsers = array();
    private $hmProducts = array();
    private $existingHMUsers = array();
    public $countFailedSubscription = 0;

    // ANZGO-3654 Modified by Shane Camus, 04/24/2018
    public function __construct()
    {
        parent::__construct();
        Loader::library('FileLog/FileLogger');
        Loader::library('HotMaths/collections_v2/class');
        Loader::model('provisioning_hotmaths_user_class_queue', $this->pkgHandle);
        Loader::model('provisioning', $this->pkgHandle);
        $this->pModel = new ProvisioningModel();
    }

    // ANZGO-3528 Modified by John Renzo S. Sunico
    // ANZGO-3914 Modified by Shane Camus 11/12/18
    public function on_start()
    {
        $htmlHelper = Loader::helper('html');
        $cssPath = (string)$htmlHelper->css('styles.css', $this->pkgHandle)->file . "?v=14";
        $jsPath = (string)$htmlHelper->javascript('scripts.js', $this->pkgHandle)->file . "?v=27.0";

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
            $total = $spreadsheetHelper->getRowsCount();
            $total = $total > 0 ? $total : static::PROVISION_LIMIT;

            echo json_encode([
                'total' => $total,
                'pages' => $total / static::PROVISION_LIMIT,
                'pagination' => static::PROVISION_LIMIT,
                'fileRecordId' => $this->fileRecordId
            ]);

            //add the excel data in the Session
            $records = $spreadsheetHelper->getRows(0,$total);
            $_SESSION['records'][$this->fileRecordId] = array_values($records);
        }
        exit;
    }

    // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
    // ANZGO-3654 Modified by Shane Camus, 04/24/2018
    // ANZGO-3914 Modified by Shane Camus, 11/15/2018
    public function provision($fileRecordId, $offset = 0)
    {
        session_write_close();

        // $provisioningRecord = $this->pModel->getFileRecord($fileRecordId);
        // if (!$provisioningRecord) {
        if (!$_SESSION['records'][$fileRecordId]) {
            echo json_encode([
                'success' => false,
                'message' => 'File record not found.'
            ]);
            exit;
        }

        // Loader::helper('spreadsheet', $this->pkgHandle);
        $reg = Loader::helper('registration', $this->pkgHandle);

        $this->fileRecordId = $fileRecordId;
        $this->pModel->fileRecordID = $fileRecordId;
        // $this->fileId = $provisioningRecord['FileID'];

        // $spreadsheetHelper = new SpreadsheetHelper($this->fileId);
        // $this->filePath = $spreadsheetHelper->getSpreadsheetPath();
        // $records = $spreadsheetHelper->getRows($offset, static::PROVISION_LIMIT);
        // var_dump($records); die;
        // foreach ($records as $row) {
        //     $row = $reg->cleanData($row);
        //     $row[ProvisioningModel::COLUMN_EMAIL] = strtolower($row[ProvisioningModel::COLUMN_EMAIL]);

        //     try {
        //         $provisioningID = $this->pModel->insertProvisioningUsers($row, $fileRecordId);
        //         $this->registerUser($row, $reg);
        //     } catch (UnexpectedValueException $e) {
        //         $hasException1 = true;
        //         $this->log(false, 'User failed to be registered ' . $e->getMessage(), $meta);
        //     }
        // }

        // foreach ($records as $row) {
        for ($x=0; $x < static::PROVISION_LIMIT; $x++){
            $row = $_SESSION['records'][$this->fileRecordId][$offset+$x];
            $hasException1 = false;
            $hasException2 = false;
            $hasException3 = false;
            $hasException4 = false;

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
            $class = null;

            // GO CREATION OF USER
            try {
                $provisioningID = $this->pModel->insertProvisioningUsers($row, $fileRecordId);
                $this->registerUserInGo($row, $reg);
            } catch (UnexpectedValueException $e) {
                $hasException1 = true;
                $this->log(false, 'User failed to be registered in GO' . $e->getMessage(), $meta);
            }

            if (!$hasException1) {
                $this->log(true, 'User successfully provisioned in GO', $meta);
            }

            // GO ADDING OF PRODUCT
            try {
                // processUserSubscriptionInGo contains all the adding of products in GO and PEAS then return hm ids
                // $hmProducts contains the HM IDs mtanada
                $hmMeta = $this->processUserSubscriptionInGo($provisioningID);

                if ($hmMeta['countFailedSubscription'] >= 1) {
                    $this->countFailedSubscription = $hmMeta['countFailedSubscription'];
                }
            } catch (UnexpectedValueException $e) {
                $hasException2 = true;
                $this->log(false, 'Subscription failed to be added in GO' . $e->getMessage(), $meta);
            }

            if (!$hasException2) {
                $this->log(true, 'Subscription successfully provisioned in GO', $meta);
            }

            if ($row[ProvisioningModel::COLUMN_CLASS_KEY]) {
                try {
                    $class = $this->validateClassKey($provisioningID, $row[ProvisioningModel::COLUMN_CLASS_KEY]);
                } catch (UnexpectedValueException $e) {
                    $hasException3 = true;
                    $this->log(false, 'Failed to register user into HM class key' . $e->getMessage(), $meta);
                }
            }

            if (!$hasException3) {
                $this->log(true, 'HM User has valid class', $meta);
            }

            // ADD TO PENDING HM PROVISION 
            if ($hmMeta || !is_null($class)) {
                $params = array();
                $provisioningUser = $this->pModel->getProvisioningUserByID($provisioningID);
                $goUID = $provisioningUser['uID'];

                $hmUID = $this->checkUserAccountInHMIfExisting($goUID);
                $params['user'] = array_merge(
                    $row,
                    array(
                        'goUID' => $provisioningUser['uID'],
                        'hmUID' => $hmUID ?: NULL
                    )
                );

                if ($hmMeta['products']) {
                    $params['products'] = $hmMeta['products'];
                }

                if (!is_null($class)) {
                    $params['classCode'] = $class;
                }

                if ($hmMeta['hasError'] || $hasException3) {
                    $params['hasError'] = true;
                }

                try {
                    $this->queueToHMProvision($provisioningID, $params);
                } catch (UnexpectedValueException $e) {
                    $hasException4 = true;
                    $this->log(false, 'Failed to enqueue user in HM.' . $e->getMessage(), $meta);
                }

                if (!$hasException4) {
                    $this->log(true, 'Successfully enqueued user in HM', $meta);
                }       
            }
                // // COMPILE USERS FOR CREATION IN HM
                // // COMPILE PRODUCTS TO BE ADDED IN HM
                // $hmUsers[] = $meta;
                // $hmSubscriptions[] = $hmProducts; 

                // $userClassQueue = new ProvisioningHotMathsUserClassesQueue($this->pModel);
                // $provisioningUser = $this->pModel->getProvisioningUserByID($provisioningID);

                // Provision USER (ONLY) IN HM
                // try {
                //     // make sure yung user id in local mag start sa last external user id in HM test environment
                //     $this->provisionUserInHotMaths(
                //         $provisioningID,
                //         !$row[ProvisioningModel::COLUMN_CLASS_KEY] ?
                //             null : $row[ProvisioningModel::COLUMN_CLASS_KEY],
                //         $userClassQueue,
                //         $provisioningUser
                //     );
                // } catch (UnexpectedValueException $e) {
                //     $hasException3 = true;
                //     $this->log(false, 'Failed user provisioning on HotMaths ' . $e->getMessage(), $meta);
                // }
                // if (!$hasException3) {
                //     $this->log(true, 'User successfully provisioned in HOTMaths', $meta);
                // }   
            

            $this->pModel->markProvisionUserCompletedByID($provisioningID);
        }

        // $hmModel = new ProvisioningHotMaths();
        // // TRY TO PROVISION EXISTING USERS TO THEIR CLASS AGAIN
        // if (count($this->existingHMUsers) > 0) {
        //     foreach($this->existingHMUsers as $classCode => $users) {
        //         $this->provisionHMUsersToClass($hmModel, $classCode, $users);
        //     }
        // }


        // IF WE NEED TO PROVISION HM PRODUCT, FETCH ALL PRODUCTS FROM HM 
        // if (count($hmSubscriptions) > 0) {
        //     // FETCH ALL PRODUCTS IN HM
        //     $this->hmProducts = $this->getHMProducts();
        // }

        // Bulk Adding of HM Products to Users
        // try {
        //     $userClassQueue->provisionSubscriptionInHotMaths(
        //         $provisioningUser['Email'],
        //         !$hmProducts ? null : $hmProducts,
        //         $userClassQueue,
        //         $provisioningUser
        //     );
        // } catch (UnexpectedValueException $e) {
        //     $hasException3 = true;
        //     $this->log(false, 'Failed product provisioning on HotMaths ' . $e->getMessage(), $meta);
        // }
        // if (!$hasException4) {
        //     $this->log(true, 'Product successfully added in HOTMaths', $meta);
        // }
        $resultTable = $this->displayUsers(
            0,
            $this->fileRecordId,
            true,
            $totalGigyaUsers = 0,
            $providerId = null,
            $this->countFailedSubscription
        );
        echo json_encode([
            'table' => $resultTable
        ]);
        exit;
    }

    // private function setupRecordsToArray($records, $reg)
    // {
    //     $formedArray = array();

    //     foreach ($records as $row) {
    //         $row = $reg->cleanData($row);
    //         $row[ProvisioningModel::COLUMN_EMAIL] = strtolower($row[ProvisioningModel::COLUMN_EMAIL]);

    //         $meta = array(
    //             'email' => $row[ProvisioningModel::COLUMN_EMAIL],
    //             'name' => $row[ProvisioningModel::COLUMN_FIRST_NAME] . ' ' . $row[ProvisioningModel::COLUMN_LAST_NAME],
    //             'type' => $row[ProvisioningModel::COLUMN_USER_ROLE],
    //             'password' => $row[ProvisioningModel::COLUMN_PASSWORD],
    //             'schoolName' => $row[ProvisioningModel::COLUMN_SCHOOL],
    //             'state' =>  $row[ProvisioningModel::COLUMN_STATE],
    //             'postCode' =>  $row[ProvisioningModel::COLUMN_POST_CODE],
    //             'classKey' =>  $row[ProvisioningModel::COLUMN_CLASS_KEY],
    //         );

    //         array_push($formedArray, $meta);
    //     }

    //     return $formedArray;
    // }

    // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
    // ANZGO-3654 Modified by Shane Camus, 04/26/2018
    private function registerUserInGo($record, $validator)
    {
        $this->pModel->updateProvisioningUserStatusByID($provisioningID, 'Processing', 'User Registration');
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
    private function processUserSubscriptionInGo($provisioningID)
    {
        $subsAvailIds = $this->post('subsAvail');
        $provisioningUser = $this->pModel->getProvisioningUserByID($provisioningID);

        if (!$subsAvailIds || !$provisioningUser['uID']) {
            return false;
        }

        $this->pModel->updateProvisioningUserStatusByID($provisioningID, 'Processing', 'Adding Subscription.');
        // Meta contains the HM IDs mtanada
        $meta = $this->pModel->processUserSubscription($provisioningUser, $subsAvailIds, $this->post('endDate'));

        if (!is_null($meta)) {
            return $meta;
        }
    }

    private function validateClassKey($provisioningID, $classKey)
    {
        $class = $this->getClassByClassKey($classKey);

        if (count($class) === 0) {
            $hmApi = new HMClass();
            $class = $hmApi->getClassByClassKey($classKey);

            if (isset($class->success) && !$class->success) {
                $status = 'AddClassError';
                $remarks = "Class code $classKey is invalid.";
                $this->pModel->updateProvisioningUserStatusByID($provisioningID, $status, $remarks);
                throw new UnexpectedValueException('Class not found.');
            } else {
                $classCode = $class->classCode;
            }

            $this->insertHMClassInRefTable($class);
        } else {
            $classCode = $class['classCode'];
        }

        return $classCode;
    }

    public function getClassByClassKey($classKey)
    {
        $db = Loader::db();
        $sql = "SELECT * FROM HMClassKeys WHERE classCode = ?";
        return $db->GetRow($sql, array($classKey)); 
    }

    public function insertHMClassInRefTable($class)
    {
        $db = Loader::db();
        $sql = "INSERT INTO HMClassKeys (classId, classCode) VALUES (?,?)";
        $db->Execute($sql, array(
            $class->classId,
            $class->classCode
        ));
        return $db->Insert_ID('HMClassKeys');
    }

    public function getHMUserByGoID($userID)
    {
        $db = Loader::db();
        $sql = "SELECT * FROM Hotmaths WHERE UserID = ? ORDER BY ID DESC LIMIT 1";
        return $db->GetRow($sql, array($userID));
    }

    public function checkUserAccountInHMIfExisting($userID)
    {
        $user = $this->getHMUserByGoID($userID);
        
        if ($user) {
            return $user['externalID'];
        }

        return false;
    }

    public function queueToHMProvision($provisioningID, $params)
    {        
        $detailPending['goUID'] = $detailPendingProduct['goUID'] = $params['user']['goUID'];
        $detailPending['externalID'] = $detailPendingProduct['externalID'] = $params['user']['hmUID'];
        $detailPending['email'] = $params['user'][static::COLUMN_EMAIL];
        $detailPending['firstName'] = $params['user'][static::COLUMN_FIRST_NAME];
        $detailPending['lastName'] = $params['user'][static::COLUMN_LAST_NAME];
        $detailPending['subscriberType'] = $role = strtolower($params['user'][static::COLUMN_USER_ROLE]);

        $hasProduct = array_key_exists('products', $params);
        $hasClass = array_key_exists('classCode', $params);

        $hasError = false;
        $status = 'HMProvisioned';

        // FIRST CONDITION: HAS PRODUCT (CAN HANDLE W/ OR W/O CLASS)
        // SECOND CONDITION: HAS CLASS ONLY
        if ($hasProduct) {
            // HM PRODUCT SAMPLE
            // $hmProducts = array (
            //     '0' => array (
            //         'limitedProduct' => true,
            //         'productExpiryDate' => 'timestamp',
            //         'productId' => 101
            //     ),
            //     '1' => array (
            //         'limitedProduct' => false,
            //         'productExpiryDate' => 'timestamp',
            //         'productId' => 102
            //     ),
            //     '2' => array (
            //         'limitedProduct' => true,
            //         'productExpiryDate' => 'timestamp',
            //         'productId' => 103
            //     ),
            //     '3' => array (
            //         'limitedProduct' => false,
            //         'productExpiryDate' => 'timestamp',
            //         'productId' => 104
            //     )
            // );
            $remarks = 'Subscription added *';

            $sortedProduct = array();

            // INSERT RIGHT AWAY TO PENDING HM PRODUCT
            // SORT FOR PENDING HM TABLE
            foreach($params['products'] as $product) {
                $detailPendingProduct['hmPID'] = $product['productId'];
                $detailPendingProduct['limitedProduct'] = $product['limitedProduct'];
                $date = new DateTime($product['productExpiryDate'] . " 21:00:00");
                $detailPendingProduct['productExpiryDate'] = $date->format("Y-m-d\TH:i:s\Z");

                $pendingHMProduct = $this->insertToPendingHMProductProvision($detailPendingProduct);

                if (!$pendingHMProduct) {
                    $status = 'HMProvisionError';
                    $remarks = "Error adding product in HM";
                    $hasError = true;
                }

                $sortedProduct[$product['limitedProduct']][] = $product['productId'];
            }

            $flagWithMoreCount = count($sortedProduct[1]) > count($sortedProduct[0]);

            foreach($sortedProduct as $flag => $sorting) {
                if (count($sorting) > 0) {
                    $detailPending['hmPID'] = implode(',', $sorting);
                    $detailPending['limitedProduct'] = $flag;

                    // HAS CLASS AND ((LTD PRODUCT FLAG WITH MORE COUNT AND TEACHER) OR (STUDENT))
                    // GOTTA MAKE SURE WE'RE NOT INCLUDING CLASS TO THE FLAG THAT HAS NO PRODUCT
                    if (
                        $hasClass &&
                        (($flagWithMoreCount == $flag && $role === 'teacher') ||
                        ($role === 'student'))
                    ) {
                        $detailPending['classCode'] = $params['classCode'];
                        $remarks = "Subscription and class " . $params['classCode'] . " added *";
                    } else {
                        $detailPending['classCode'] = null;
                    }

                    $pendingHM = $this->insertToPendingHMProvision($detailPending);

                    if (!$pendingHM) {
                        $status = 'HMProvisionError';
                        $remarks = "Error adding product in HM";
                        $hasError = true;
                    }
                }
            }

        } elseif ($hasClass) {
            $detailPending['hmPID'] = null;
            $detailPending['classCode'] = $params['classCode'];
            $detailPending['limitedProduct'] = false;
            $remarks = "User was added to class " . $params['classCode'] . " *";

            $pendingHM = $this->insertToPendingHMProvision($detailPending);

            if (!$pendingHM) {
                $status = 'HMProvisionError';
                $remarks = "Error adding class " . $params['classCode'] . " in HM";
                $hasError = true;
            }
        }

        $status = !is_null($params['user']['hmUID']) ? 'Existing' : $status;

        if ($params['hasError'] && $status === 'Existing') {
            $this->pModel->updateProvisionedUsersStatus($provisioningID, $status);
        } elseif (!$params['hasError']) {
            $this->pModel->updateProvisioningUserStatusByID(
                    $provisioningID,
                    $status,
                    $remarks
            );
        }

        if ($hasError) {
            throw new UnexpectedValueException('Database insert failed.');
        }

        return;

        # TODO
        # INSERT TABLE FOR PRODUCTEXPIRY
        # TABLE ABOVE WILL BE USED FOR CRONJOB SCRIPT IN ORDER TO UDPATE OTHER PRODUCT DETAILS
    }

    public function insertToPendingHMProvision($params)
    {
        $keys = implode(',', array_keys($params));
        $values = array_values($params);
        $db = Loader::db();
        $sql = "INSERT INTO PendingHM ($keys) VALUES (?,?,?,?,?,?,?,?,?)";
        $db->Execute($sql, $values);

        return $db->hasAffectedRows;
    }

    public function insertToPendingHMProductProvision($params)
    {
        $keys = implode(',', array_keys($params));
        $values = array_values($params);
        $db = Loader::db();
        $sql = "INSERT INTO PendingHMProductDetails ($keys) VALUES (?,?,?,?,?)";
        $db->Execute($sql, $values);

        return $db->hasAffectedRows;
    }

    /**
     * Provision a User in HotMaths with its class code and products
     *
     * @param $provisioningID
     * @param null $classCode
     * @param $userClassQueue
     * @param $provisioningUser
     */
    private function provisionUserInHotMaths($provisioningID, $classCode = null, $userClassQueue, $provisioningUser)
    {
        // Adding of HM USER in HM with class key (if any)
        $hmUser = $userClassQueue->provisionUserInHotMaths($provisioningUser['uID'], $classCode);

        // User not created catch
        if (isset($hmUser->success) && !$hmUser->success) {
            $this->classifyCreateHMUserError(
                $userClassQueue,
                $hmUser,
                $provisioningUser,
                $provisioningID,
                $classCode
            );

            // SB-71 added by jbernardez 20190219
            // add this code here so that tokens are always stored
            // $hmUser = $userClassQueue->getHmUserOnly();
            // $userClassQueue->storeTokens($hmUser);
            return;
        }
//        else {
//            // Success adding of user in HM
//            array_map("addUserMapping", array($hmUser));
//        }

       // $userClassQueue->storeTokens($hmUser);

        $status = 'Provisioned User in HM';
        $remarks = 'Provisioned successfully';

        if (!is_null($classCode)) {
            $status = 'AddedClass';
            $remarks = "User has been added to class $classCode";
        }

        $this->pModel->updateProvisioningUserStatusByID($provisioningID, $status, $remarks);
    }

//    function addUserMapping($hmUser){
//        $this->hmUsers[] = $hmUser;
//var_export($this->hmUsers);
//    }

    // private function addProductMapping($hmProduct){
    //     $this->hmProducts[] = $hmProduct;
    // }

    // private function provisionHMUsersToClass($hmModel, $classCode, $users)
    // {
    //     $userEmails = array_keys($users);
    //     var_dump(array_map(array($hmModel, 'getUserInHotMathsByEmail'), $userEmails)); die;
    //     $temp = $hmModel->provisionUsersToClassInHotMaths($classCode, $users);
    //     var_dump($temp); die;
    //     // $status = 'AddedClass';
    //     // $remarks = "User has been added to class $classCode";
    // }

    /**
     * If user cannot be created - user exists - then add to the class
     * For invalid classKey create the user without class key
     *
     * @param $userClassQueue
     * @param $hmUser
     * @param $provisioningUser
     * @param $provisioningID
     * @param $classCode
     */
    private function classifyCreateHMUserError(
        $userClassQueue,
        $hmUser,
        $provisioningUser,
        $provisioningID,
        $classCode
    )
    {
        $status = 'CreateHMUserError';
        $remarks = 'Unable to create Hotmaths user';
        $stillNeedsToAddToClass = false;

        if ($hmUser->message === 'Username is already used') {
            $status = 'Existing';
            $remarks = 'User already exists in HM';

            if (!isset($this->existingHMUsers[$classCode])) {
                $this->existingHMUsers[$classCode] = array();
            }

            // ADD TO ARRAY TO PROVISION TO CODE LATER
            $this->existingHMUsers[$classCode] = array_merge(
                $this->existingHMUsers[$classCode],
                array(
                    $provisioningUser['Email'] => array(
                        $provisioningUser['Email'],
                        $provisioningID
                    )
            )
            );
        } elseif (preg_match('/Class code is invalid/', $hmUser->message)) {
            $hmUser = $userClassQueue->provisionUserInHotMaths($provisioningUser['uID']);
            
            // ASSUME USER IS SUCCESSFULLY CREATED
            array_push($this->hmUsers, $hmUser);

            $status = 'AddClassError';
            $remarks = "Class code $classCode is invalid";
        } else {
            $remarks .= " " . $hmUser->message;
            // CONSIDER IT DONE FOR THE USER WAS NOT CREATED SUCCESSFULLY
            $this->pModel->markProvisionUserCompletedByID($provisioningID);
        }

        $this->pModel->updateProvisioningUserStatusByID($provisioningID, $status, $remarks);
        return;
    }

    // ANZGO-3642 Modified by John Renzo Sunico, 02/22/2018
    public function displayUsers(
        $page = 0,
        $fileID = 0,
        $return = false,
        $totalGigyaUsers = 0,
        $providerId = null,
        $countFailedSubscription = 0
    )
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
            'totalTngUsers' => $usersTotal,
            'totalFailedSubscription' => $countFailedSubscription
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
        // $gIds = [];

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
        $this->displayUsers(
            0,
            $fileRecordId,
            false,
            0,
            $providerId,
            $this->countFailedSubscription
        );
        // echo json_encode([
        //     'gUIds' => $gUIds
        // ]);
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
                $gUIds = [];

                foreach ($migratedUsers->results as $gigyaUser) {
                    // array_push($gUIds, $gigyaUser->UID);
                    array_push($ids, $gigyaUser->data->systemIDs[0]->idValue);
                }
            
                $this->pModel->markSuccessfulMigrationOfUser($ids);
            }
        }

        $this->pModel->markUnsuccessfulMigrationOfUser($fileId);
        // return $gUIds;
    }

    public function autoVerifyUsersInGigya($fileRecordId, $providerId = null)
    {
        $gUIDs = $this->post('gUIds');
        if (is_null($providerId)) {
            foreach ($gUIDs as $gUID) {
                $this->autoVerifyUserInGigya($gUID);
            }
        }
        $this->displayUsers(
            0,
            $fileRecordId,
            false,
            0,
            $providerId,
            $this->countFailedSubscription
        );
        die;
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
}
