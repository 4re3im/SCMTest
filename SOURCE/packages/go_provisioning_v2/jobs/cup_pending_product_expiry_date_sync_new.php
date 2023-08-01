<?php 
/**
*
* Setting of expiry date of products that were provisioned
* @package Utilities
*/

defined('C5_EXECUTE') or die("Access Denied.");
ini_set('log_errors', 1);

class CupPendingProductExpiryDateSyncNew extends Job
{
    const FUNCTION_TYPE = 'function';
    const TIMESTAMP = 'timestamp';
    const USER_ID = 'userID';
    const HM_ID = 'hmID';
    const PRODUCT_ID = 'productID';

    public function __construct()
    {
	Loader::library('FileLog/FileLogger');
	Loader::library('HotMaths/collections_v2/user');
	Loader::model('gousermodel', 'go_contents');
    }
	
    public function getJobName()
    {
    	return t("Process Pending Products Expiry date");
    }
	
    public function getJobDescription()
    {
    	return t("Process Pending Products Expiry date");
    }

    public function run()
    {
    	$result = $this->processPendingExpiryDateSingles();
	$msg = 'Cron Failed. Please try again';

	if ($result['success']) {
	    $msg = "CupPendingProductExpiryDateSync Finished!";
	    $msg .= "\nTime started: " . $result['time_started'];
	    $msg .= "\nTime ended: " . $result['time_ended'];
	}

	return t($msg);
    }

    public function processPendingExpiryDateSingles()
    {
        $userModel = new GoUserModel();
        $pendings = $userModel->fetchAllPendingProductExpiryDateSingles();
        $HMUser = new HMUser();
        $processedUsers = array();

        $result = array(
            "success" => false,
            "goUIDs" => array(),
            "time_started" => date("Y-m-d H:i:s"),
            "time_ended" => null
        );

        var_dump($pendings);

        foreach($pendings as $pending) {
            $goUID = $pending['goUID'];
            $externalID = $pending['externalID'];
            $hmPID = $pending['hmPID'];
            $productExpiryDate = $pending['productExpiryDate'];
            $limitedProduct = filter_var($pending['limitedProduct'], FILTER_VALIDATE_BOOLEAN);

            var_dump('goUID'); var_dump($goUID);
            
            if (is_null($externalID)) {
                $externalID = $userModel->getHMUIDbyGOUID($goUID)['externalID'];
            }

            $userIds = array($externalID);
            $productIds = array($hmPID);

            if (!is_null($productExpiryDate)) {
                $additionals['productExpiryDate'] = $productExpiryDate;
            }

            if (!is_null($limitedProduct)) {
                $additionals['limitedProduct'] = $limitedProduct;
            }
            

            var_dump('externalID'); var_dump($userIds);
            var_dump('hmPID'); var_dump($productIds);
            var_dump('additionals'); var_dump($additionals);
            $response = $HMUser->extendUsersProducts($userIds, $productIds, $additionals);
            var_dump($response);

            if (is_null($response) || $response->success === false || !empty($response->errors)) {
                FileLogger::log(
                    array(
                        static::TIMESTAMP => date('r'),
                        static::USER_ID => $goUID,
                        static::HM_ID => $externalID,
                        static::PRODUCT_ID => $hmPID,
                        'info' => 'HM API Function (CRON Pending Product Details Single): ' . debug_backtrace()[1][static::FUNCTION_TYPE],
                        'meta' => $response
                    )
                );
            } else {
                array_push($processedUsers, $goUID);
            }
        }

        $userModel->updateAllUserProductExpiryPendingsSingles($processedUsers);
        $result['goUIDs'] = $processedUsers;

        $result['success'] = true;
        $result['time_ended'] = date("Y-m-d H:i:s");

        var_dump($result);
        return $result;
    }

    // public function processPendingExpiryDate() {
    //     $userModel = new GoUserModel();
    //     $pendings = $userModel->fetchAllPendingProductExpiryDate();
    //     $HMUser = new HMUser();
    //     $processedUsers = array();

    //     $result = array(
    //         "success" => false,
    //         "goUIDs" => array(),
    //         "time_started" => date("Y-m-d H:i:s"),
    //         "time_ended" => null
    //     );

    //     var_dump($pendings);

    //     foreach($pendings as $pending) {
    //         $goUIDs = $pending['goUIDs'];
    //         $hmPIDs = $pending['hmPIDs'];
    //         $productExpiryDate = $pending['productExpiryDate'];
    //         $limitedProduct = filter_var($pending['limitedProduct'], FILTER_VALIDATE_BOOLEAN);

    //         $goUIDsArr = array_map('intval', explode(',', $goUIDs));
    //         $hmPIDsArr = array_map('intval', explode(',', $hmPIDs));
    //         $externalIDs = array();

    //         foreach($goUIDsArr as $goUID) {
    //             $externalID = $userModel->getHMUIDbyGOUID($goUID)['externalID'];
    //             if (!is_null($externalID)) {
    //                 array_push($externalIDs, $externalID);
    //             }
    //         }

    //         if (!is_null($productExpiryDate)) {
    //             $additionals['productExpiryDate'] = $productExpiryDate;
    //         }

    //         if (!is_null($limitedProduct)) {
    //             $additionals['limitedProduct'] = $limitedProduct;
    //         }

    //         var_dump('userIds'); var_dump($externalIDs);
    //         var_dump('productIds'); var_dump($hmPIDsArr);
    //         var_dump('additionals'); var_dump($additionals);
    //         $response = $HMUser->extendUsersProducts($externalIDs, $hmPIDsArr, $additionals);
    //         var_dump($response);

    //         if (!is_null($response) && $response->success) {
    //             $processedUsers = array_merge($processedUsers, $goUIDsArr);
    //         } else {
    //             FileLogger::log(
    //                 array(
    //                     static::TIMESTAMP => date('r'),
    //                     static::USER_IDS => $goUIDs,
    //                     static::HM_IDS => $externalIDs,
    //                     static::PRODUCT_IDS => $hmPIDs,
    //                     'info' => 'HM API Function (CRON Pending Product Details): ' . debug_backtrace()[1][static::FUNCTION_TYPE],
    //                     'meta' => $response
    //                 )
    //             );
    //         }
    //     }

    //     // TODO: check if all api inserts are success before updating
    //     if (count($processedUsers) > 0) {
    //         $userModel->updateAllUserProductExpiryPendings($processedUsers);
    //         $result['goUIDs'] = $processedUsers;
    //     }

    //     $result['success'] = true;
    //     $result['time_ended'] = date("Y-m-d H:i:s");

    //     var_dump($result);
    //     return $result;
    // }
}

?>