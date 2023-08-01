<?php
/**
 * Checkpoints landing page controller
 * @author mabrigos@cambridge.org
 */
class CheckpointsController extends Controller
{
    private $pkgHandle = 'checkpoints';

    public function on_start()
    {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("checkpoints_theme"));
    }

    public function sendEmail()
    {
        if (isset($_COOKIE['CONCRETE5'])) {
            $data = $this->post();

            $recaptchaResponse = $this->post('g-recaptcha-response');
            $postData = http_build_query(
                        array(
                            'secret' => RECAPTCHA_CONTACT_US_SECRET_KEY,
                            'response' => $recaptchaResponse,
                            'remoteip' => $_SERVER['REMOTE_ADDR']
                        ));

            $options = array(
                        'http' => array(
                            'header'  => "Content-type: application/x-www-form-urlencoded",
                            'method'  => 'POST',
                            'content' => $postData
                        )
            );

            $recaptchaURL = 'https://www.google.com/recaptcha/api/siteverify';
            $context  = stream_context_create($options);
            $response = file_get_contents($recaptchaURL, false, $context);
            $responseKeys = json_decode($response, true);

            if ($responseKeys["success"]) {
                $data = $this->post();
                $mh = Loader::helper('mail');
                if (defined('EMAIL_ADDRESS_REGISTER_NOTIFICATION_FROM')) {
                    $mh->from(EMAIL_ADDRESS_REGISTER_NOTIFICATION_FROM,  t('Cambridge Checkpoints'));
                } else {
                    $adminUser = UserInfo::getByID(USER_SUPER_ID);
                    if (is_object($adminUser)) {
                        $mh->from($adminUser->getUserEmail(),  t('Cambridge Checkpoints'));
                    }
                 }
                $mh->setSubject("Re: [Inquiry] Cambridge Checkpoints");
                $mh->setBody(
                    "From: " . $data['contactName'] . " " .
                    $data['contactLastName'] . " <" . $data['contactMail'] . ">" .
                    "\n\n" . $data['contactComments'] .
                    "\n\nYou can directly reply to this email."
                );

                $mh->to(CAMBRIDGE_AU_SUPPORT_EMAIL, "Cambridge Checkpoints Support");
                $mh->from('go@cambridge.edu.au');
                $mh->replyto($data['contactMail']);
                $mh->sendMail();
                exit;
            }
        }

        http_response_code(403);
        exit;
    }
}
