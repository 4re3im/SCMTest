<?php
/**
 * Elevate Controller
 * @author Renzo Sunico <jsunico@cambridge.org>
 * Last Edited : June 2, 2017
 */

defined('C5_EXECUTE') or die(_('Access Denied.'));

class ElevateServicesCredentialsController extends Controller {

    protected $pkgHandle = 'go_elevate';
    protected $response = array("message" => "Successful. Cookie has been set.");
    protected $anonymous_user = "ANON_GOUID_G4Z97JV512LK9567";

    public function on_start()
    {
        parent::on_start();
        header('Content-Type: application/json');
        Loader::helper('elevate_encryption', $this->pkgHandle);
        Loader::model('elevate_subscription', $this->pkgHandle);
    }

    public function generate_credentials($isbn)
    {
        global $u;

        if (!$isbn) {
            $this->response['message'] = "Failed. ISBN Required.";
            $this->response['success'] = false;

            header('Content-Type: application/json');
            echo json_encode($this->response);
            exit;
        }

        try {
            $isElevateSample = ElevateSubscription::isElevateSample($isbn);

            $uid = $u->isLoggedIn() && !$isElevateSample ? $u->getUserID() : $this->anonymous_user;
            $gouid = ElevateEncryptionHelper::encrypt($uid);
            $token = ElevateEncryptionHelper::encrypt(ElevateEncryptionHelper::generate_token());
            $expires = ElevateEncryptionHelper::encrypt(str_replace('+00:00', 'Z', gmdate('c', strtotime('+2 minutes'))));
            $domain = ElevateEncryptionHelper::encrypt($_SERVER['SERVER_NAME']);
            $signature = "GOUID=$gouid&Token=$token&Expires=$expires&Domain=$domain";
            $signature = strtoupper(hash_hmac('sha1', $signature, ELEVATE_SECRET_KEY));

            $uSubscription = ElevateSubscription::getTabAccessByISBN($uid, $isbn);
            $uGroups = $u->isLoggedIn() ? $u->getUserGroups() : array();
            $adminGroups = array('Administrators', 'CUP Staff');

            if ((!$u->isLoggedIn() && !$isElevateSample) ||
                ($u->isLoggedIn() && !$uSubscription && !$isElevateSample && !array_intersect($uGroups, $adminGroups))) {
                throw new Exception('User is not entitled to this title.');
            }

            setrawcookie("EREADER_GOUID", $gouid, time() + 120, "/");
            setrawcookie("EREADER_TOKEN", $token, time() + 120, "/");
            setrawcookie("EREADER_EXPIRES", $expires, time() + 120, "/");
            setrawcookie("EREADER_DOMAIN", $domain, time() + 120, "/");
            setrawcookie("EREADER_SIGNATURE", $signature, time() + 120, "/");
            setrawcookie("EREADER_ISBN",  ElevateEncryptionHelper::encrypt($isbn), time() + 120, "/");
            setrawcookie("ELEVATE_ISBN", $isbn, time() + 120, "/");

            $this->response['success'] = true;
        } catch (Exception $e) {
            $this->response['message'] = $e->getMessage();
            $this->response['success'] = false;
        }

        echo json_encode($this->response);
        exit;
    }
}
?>
