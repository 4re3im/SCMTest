<?php 
/**
*
* Proccesses pending users to add HM products and classes
* @package Utilities
*/

defined('C5_EXECUTE') or die("Access Denied.");
ini_set('log_errors', 1);

class CupPendingProvisioningSyncNew extends Job
{
    const CLASS_ID = 'classID';
    const FUNCTION_TYPE = 'function';
    const TIMESTAMP = 'timestamp';
    const PRODUCT_ID = 'productID';
    const USER_ID = 'userID';
    const USER_IDS = 'userIDs';
    
    public function __construct() {
        Loader::library('FileLog/FileLogger');
	Loader::library('HotMaths/collections_v2/user');
        Loader::model('gousermodel', 'go_contents');
        $this->userModel = new GoUserModel();
    }
	
    public function getJobName() {
    	return t("Process Pending HM Provisions");
    }
	
    public function getJobDescription() {
    	return t("Process Pending HM Provisions");
    }
	
    public function run() {
    	$result = $this->processHMPendings();
        $msg = 'Cron Failed. Please try again';

        if ($result['success']) {
            $msg = "CupPendingProvisioningSync Finished!";
            $msg .= "\nTime started: " . $result['time_started'];
            $msg .= "\nTime ended: " . $result['time_ended'];

            shell_exec(CRON_EXPIRY_DATE_PENDING);
        }

        return t($msg);
    }


    public function processHMPendings()
    {
        $HMUser = new HMUser();
        $AllUserHMPendings = $this->userModel->fetchAllUserHMPendings();
        $isHMUserRecentlyCreated = false;
        $updatedHMUID = null;
        $HMUIDsCollection = array();
        $GOUIDsCollection = array();
        $classCodesCollection = array();
        $HMPIDsCollection = array();

        $unsuccesfullyProcessedUIDs = array();
        $unsuccesfullyProcessedClassCodes = array();
        $unsuccesfullyProcessedHMPIDs = array();

        $result = array(
            "success" => false,
            // "goUIDs" => array(),
            "time_started" => date("Y-m-d H:i:s"),
            "time_ended" => null
        );

        // 1 user each
        foreach($AllUserHMPendings as $userHMPendings) {
            // var_dump($userHMPendings);
            $GOUID = intval($userHMPendings['goUID']);
            $HMPIDs = $userHMPendings['HMPIDs'];
            $classCode = $userHMPendings['classCode'];
            $HMUID = $userHMPendings['externalID'];
            $email = $userHMPendings['email'];
            $firstName = $userHMPendings['firstName'];
            $lastName = $userHMPendings['lastName'];
            $subscriberType = $userHMPendings['subscriberType'];
            $limitedProduct = intval($userHMPendings['limitedProduct']);
            
            if (is_null($HMUID) && !$GOUIDsCollection[$GOUID]) {
                var_dump('********* create user!'); var_dump($GOUID);
                 $userParams = array(
                    'email' => $email,
                    'username' => $email,
                    'userID' => $GOUID,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'countryCode' => '',
                    'subscriberType' => $subscriberType
                );
                $response = $HMUser->create($userParams);
                $HMUID = $response->userId;

                if (is_null($response) || $response->success === false || !empty($response->errors)) {
                    if ($response->message == 'Username is already used') {
                        $fetchedHMUID = $this->userModel->getHMUIDbyGOUID($GOUID);
                        if (empty($fetchedHMUID)) {
                            $getUserByGoID = $HMUser->getUserByGoID($GOUID);
                            $this->userModel->updateHotMaths($getUserByGoID);
                            $HMUID = $getUserByGoID->userId;
                        } else {
                            $HMUID = $fetchedHMUID['externalID'];
                        }
                    } else {
                        FileLogger::log(
                            array(
                                static::TIMESTAMP => date('r'),
                                static::USER_ID => $userInfo->uID,
                                'error' => 'HM API Function (CRON Pending HM Provisioning - createUser): ' . debug_backtrace()[1][static::FUNCTION_TYPE],
                                'meta' => $response
                            )
                        );
                        array_push($unsuccesfullyProcessedUIDs, $GOUID);
                    }
                } else {
                    $this->userModel->updateHotMaths($response);
                }
                
                $updatedHMUID = $response->userId;
                $isHMUserRecentlyCreated = true;
                // $HMUIDsColletion[$HMUID] = $HMUID;
                $GOUIDsCollection[$GOUID] = $GOUID;
            } else {
                // update HMUID using recent update
                $HMUID = $updatedHMUID;
            }            

            if (!is_null($HMUID)) {
                if (!is_null($classCode)) {
                    $classCodesCollection[$classCode][] = $HMUID;
                    // $classCodesCollectionWithGOUID[$classCode][] = $GOUID;
                }

                if (!is_null($HMPIDs)) {
                    $HMPIDsCollection[$HMPIDs][] = $HMUID;
                    // $HMPIDsCollectionWithGOUID[$HMPIDs][] = $GOUID;
                }
            }
        }

        //  1 code - many users
        foreach($classCodesCollection as $key => $value) {
            var_dump('********* Process Class Key');
            var_dump($classCodesCollection);
            $classCodesArray = explode(',', $key);

            foreach($classCodesArray as $classCode) {
                var_dump('classCode'); var_dump($classCode);
                var_dump('value'); var_dump($value);
                $classResponse = $HMUser->addUsersToClass($classCode, $value);
                // TODO: insert log here for adding class key
                var_dump($classResponse);

                if (is_null($classResponse) || $classResponse->success === false || !empty($classResponse->errors)) {
                    FileLogger::log(
                        array(
                            static::TIMESTAMP => date('r'),
                            static::CLASS_ID => $classId,
                            static::USER_IDS => $value,
                            'error' => 'HM API Function (CRON Pending HM Provisioning - addUsersToClass): ' . debug_backtrace()[1][static::FUNCTION_TYPE],
                            'meta' => $classResponse
                        )
                    );

                    array_push($unsuccesfullyProcessedClassCodes, $classCode);
                }
            }
        }

        foreach($HMPIDsCollection as $key => $value) {
            var_dump('********* Process User Products');
            $HMPIDArray = explode(',', $key);
            $HMPIDArray = array_map('intval', $HMPIDArray);
            $additionals = null;

            if (!is_null($limitedProduct)) {
                $additionals['limitedProduct'] = $limitedProduct;
            }

            // 1 product to many users
            foreach($HMPIDArray as $productId) {
                var_dump('productId'); var_dump($productId); 
                var_dump('value'); var_dump($value); 
                $productResponse = $HMUser->addProductToUser($value, $productId, $additionals);
                var_dump($productResponse);

                if (is_null($productResponse) || $productResponse->success === false || !empty($productResponse->errors)) {
                    FileLogger::log(
                        array(
                            static::TIMESTAMP => date('r'),
                            static::PRODUCT_ID => $productId,
                            static::USER_IDS => $value,
                            'error' => 'HM API Function (CRON Pending HM Provisioning - addProductToUser): ' . debug_backtrace()[1][static::FUNCTION_TYPE],
                            'meta' => $productResponse
                        )
                    );
                    array_push($unsuccesfullyProcessedHMPIDs, $productId);
                }
            }
        }

        $processedAt = $this->userModel->updateAllUserHMPendings($unsuccesfullyProcessedUIDs, $unsuccesfullyProcessedClassCodes, $unsuccesfullyProcessedHMPIDs);

        // $result['goUIDs'] = $GOUIDsCollection;
        $result['success'] = true;
        $result['time_ended'] = date("Y-m-d H:i:s");
        var_dump($result);

        // $params = array(
        //     'unsuccesfullyProcessedUIDs' => $unsuccesfullyProcessedUIDs,
        //     'unsuccesfullyProcessedClassCodes' => $unsuccesfullyProcessedClassCodes,
        //     'unsuccesfullyProcessedHMPIDs' => $unsuccesfullyProcessedHMPIDs,
        //     'processedAt' => $processedAt
        // );

        // $this->emailResult($params);
        return $result;
    }

    // public function emailResult($params) {
    //     // [$unsuccesfullyProcessedUIDs, $unsuccesfullyProcessedClassCodes, $unsuccesfullyProcessedHMPIDs, $processedAt] = $params;

    //     $succesful = '';
    //     $unsuccesful = '';

    //     $mh = Loader::helper('mail');
    //     $mh->to('cgodoy@cambridge.org');
    //     $mh->to('jsunico@cambridge.org');
    //     $mh->from('cgodoy@cambridge.org', t('Cambridge GO Server'));
    //     $mh->setSubject('Provisioning Result');
    //     $mh->setBody($result);
    //     $mh->sendMail();
    // }
}

?>