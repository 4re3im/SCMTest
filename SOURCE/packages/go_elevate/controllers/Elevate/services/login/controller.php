<?php
/**
 * Elevate Login Controller
 * ANZGO-3408 Created by Shane Camus 06/09/2017
 */


defined('C5_EXECUTE') or die(_('Access Denied.'));

class ElevateServicesLoginController extends Controller {

    protected $pkgHandle = 'go_elevate';

    public function __construct()
    {
        parent::__construct();
        $this->methods = array(
            'post' => function () {
                $header = getallheaders();
                 // GCAP-784 modified by machua 30032020 login using Gigya API
                $authorizationString = $header['Authorization'];

                if (is_null($authorizationString)) {
                    $response = array('error' => 'Missing data! Please try again.');
                    echo json_encode($response);
                    exit;
                }

                $authorizationArray = explode(' ', $authorizationString);
                $encryptedBasic = $authorizationArray[1];
                
                $basicString = ElevateEncryptionHelper::decrypt($encryptedBasic);               
                $basicArray = explode(':', $basicString);

                if (count($basicArray) !== 2) {
                    $response = array('error' => 'Incorrect data! Please try again.');
                    echo json_encode($response);
                    exit;
                }

                $username = $basicArray[0];
                $password = $basicArray[1];

                $gigyaAccount = new GigyaAccount();
                $gigyaAccountData = $gigyaAccount->login($username, $password, "data");
                // SB-572 added by machua 20200520 for error checking of authentication
                $gigyaErrorCode = $gigyaAccountData->getErrorCode();

                if ($gigyaAccountData->isValid() && $gigyaErrorCode === 0) {
                    $gigyaUID = $gigyaAccountData->getUIDFromData();
                    $goUID = $gigyaAccountData->getSystemID();
                    $subscriptions = ElevateSubscription::getAllByUserID($goUID);
                    $response = array('goUID' => $goUID, 'entitlements' => $subscriptions);
                } else if ($gigyaErrorCode === 206001) {
                    $response = array('error' => 'User has not yet agreed to the terms of service of TNG.');
                } else {
                    $response = array('error' => 'Invalid email address and/or password! Please try again.');
                }

                echo json_encode($response);
                exit;
            }
        );
    }

    public function on_start()
    {
        parent::on_start();
        header('Content-Type: application/json');
        Loader::helper('elevate_encryption', $this->pkgHandle);
        Loader::model('elevate_validation_hashes', $this->pkgHandle);
        Loader::library('gigya/GigyaAccount');
        Loader::model('elevate_subscription', $this->pkgHandle);
    }

    public function view()
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if (!array_key_exists($method, $this->methods)) {
            http_response_code(405);
            $response['message'] = "Method not allowed in this resource";
            $response['status'] = '405 Method not allowed';
            echo json_encode($response);
            exit;
        }

        $this->methods[$method]();
        exit;
    }
}
