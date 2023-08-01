<?php

/**
 * QueensLandSenior landing page controller
 * @author jsunico@cambridge.org
 */
class QueensLandSeniorController extends Controller
{
    public function on_start()
    {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("queenslandsenior"));
    }

    public function sendEmail()
    {
        if (isset($_COOKIE['CONCRETE5'])) {
            $data = $this->post();
            // SB-579 added by mabrigos 20200709
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
                    $mh->from(EMAIL_ADDRESS_REGISTER_NOTIFICATION_FROM, t('Cambridge Senior Maths for Queensland'));
                } else {
                    $adminUser = UserInfo::getByID(USER_SUPER_ID);
                    if (is_object($adminUser)) {
                        $mh->from($adminUser->getUserEmail(), t('Cambridge Senior Maths for Queensland'));
                    }
                }
                $mh->setSubject("Re: Inquiry Cambridge Senior Maths for Queensland");
                $mh->setBody(
                    "From: " . $data['contactName'] . " " .
                    $data['contactLastName'] . " <" . $data['contactMail'] . ">\n\n" .
                    $data['contactComments'] .
                    "\n\nYou can directly reply to this email."
                );
                $mh->to('digitalsupportau@cambridge.edu.au', "Cambridge Senior Maths for Queensland Support");
                $mh->replyto($data['contactMail']);
                $mh->sendMail();
                exit;
            }
            
        }

        http_response_code(403);
        exit;
    }
}
