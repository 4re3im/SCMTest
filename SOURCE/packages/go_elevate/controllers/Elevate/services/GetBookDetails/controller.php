<?php
/**
 * Elevate Controller
 * @author Renzo Sunico <jsunico@cambridge.org>
 * Last Edited : June 2, 2017
 */

defined('C5_EXECUTE') or die(_('Access Denied.'));

class ElevateServicesGetBookDetailsController extends Controller
{

    protected $pkgHandle = 'go_elevate';
    protected $methods = array();
    protected $userID = null;

    public function __construct()
    {
        parent::__construct();
        $this->methods = array(
            'get' => function () {
                $header = getallheaders();
                // GCAP-789 modified by machua 30032020 get GOUID using Gigya API
                $authorizationString = $header['Authorization'];

                if (is_null($authorizationString)) {
                    $response = array('error' => 'Missing data! Please try again.');
                    echo json_encode($response);
                    exit;
                }

                $authorizationArray = explode(' ', $authorizationString);

                if (count($authorizationArray) !== 2) {
                    $response = array('error' => 'Incorrect data! Please try again.');
                    echo json_encode($response);
                    exit;
                }

                $encryptedGigyaUID = $authorizationArray[1];

                $goUID = ElevateEncryptionHelper::decrypt($encryptedGigyaUID);

                $subscriptions = ElevateSubscription::getAllByUserID($goUID);
                $subscriptions = array('entitlements' => $subscriptions);
                echo json_encode($subscriptions);
                exit;
            }
        );
    }

    public function on_start()
    {
        parent::on_start();
        header('Content-Type: application/json');
        Loader::helper('elevate_encryption', $this->pkgHandle);
        Loader::model('elevate_subscription', $this->pkgHandle);
        Loader::library('gigya/GigyaAccount');
    }

    public function view()
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if (!array_key_exists($method, $this->methods)) {
            ElevateAuthenticationHelper::raise_method_not_allowed();
        }

        $this->methods[$method]();
        exit;
    }

    public function isISBNSample($isbn)
    {
        $is_sample = (ElevateSubscription::isElevateSample($isbn)) ? true : false;
        echo json_encode(array('is_sample' => $is_sample));
        exit;
    }
}
