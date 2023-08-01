<?php
/**
 * Description of forgot_password
 *
 * @author michaelabrigos
 */
defined('C5_EXECUTE') or die("Access Denied.");
Loader::library('gigya/GigyaAccount');
class GoResendVerificationController extends Controller
{
  public function on_start()
  {
      $v = View::getInstance();
      $v->setTheme(PageTheme::getByHandle("go_theme"));
      if (!$_SESSION['expiredVerification']) {
        $this->redirect('go/login');
    }
  }
  
  public function resend($uid)
  {
      $gigyaAccount = new gigyaAccount();
      $result = $gigyaAccount->resendVerificationEmail($uid);
      echo json_encode($result);
      exit;
  }

  public function resendSucess()
  {
      unset($_SESSION['expiredVerification']);
      exit;
  }

  public function checkEmailIfExists()
  {
      $gigyaAccount = new gigyaAccount();
      $email = $gigyaAccount->getProfileByEmail($_POST['data']);
      if ($email) {
        $this->resend($email["UID"]);
      }
      $result = array('result' => false);
      echo json_encode($result);
      exit;
  }
}