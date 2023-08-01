<?php
/**
 * Description of resources
 *
 * @author paulbalila
 */
class GoContactController extends Controller {
    public function on_start() {
        
    }
    
    public function view() {
        $v = View::getInstance();
        // ANZGO-3919 modified by mtanada 20181120 change theme
        $v->setTheme(PageTheme::getByHandle("go_theme"));
    }
    
    public function confirmation() {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_theme"));

        if ($_POST) {
            // SB-199 added by jbernardez 20190617
            // SB-266 added by machua 20190724 to add recaptcha verification
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
            $responseKeys = json_decode($response,true);

            if($responseKeys["success"]) {
                $salutation = $this->post('salutation');
                $firstName  = $this->post('first_name');
                $lastName   = $this->post('last_name');
                $position   = $this->post('title');
                $school     = $this->post('company');
                $city       = $this->post('city');
                $country    = $this->post('country');
                $postCode   = $this->post('zip');
                $email      = $this->post('email');
                $phone      = $this->post('phone');
                $query      = $this->post('description');
                $optOut     = $this->post('HardCopyOptOut__c');
                $subject    = $this->post('00N20000000BALL');
                $level      = $this->post('00N20000000BALM');
                
                // start email sending here
                $mh = Loader::helper('mail');
                $mh->addParameter('salutation', $salutation);
                $mh->addParameter('firstName', $firstName);
                $mh->addParameter('lastName', $lastName);
                $mh->addParameter('position', $position);
                $mh->addParameter('noEmail', $optOut == 1 ? 'Yes' : 'No');
                $mh->addParameter('school', $school);
                $mh->addParameter('city', $city);
                $mh->addParameter('country', $country);
                $mh->addParameter('postCode', $postCode);
                $mh->addParameter('email', $email);
                $mh->addParameter('phone', $phone);
                $mh->addParameter('query', $query);
                $mh->addParameter('subject', $subject);
                $mh->addParameter('level', $level);

                $mh->to('go-contactus@cambridge.edu.au');
                $mh->from('go@cambridge.edu.au');
                $mh->load('contact_us', 'go_contents');
                @$mh->sendMail();
            } else {
                echo json_encode('Recaptcha Authentication Failed');
                exit;
            }

            
        }

        $this->set('success', true);
    }
    
    public function getStatesProvinces($name = null) {
        $statesHelper = Loader::helper('lists/states_provinces', 'go_contents');
        $countryCode = $this->post('country');
        
        $states = $statesHelper->getStateProvinceArray($countryCode);
        if ($states) {
            $html = '<select class="form-control go-input" name="state">';
            // ANZGO-1872
            if ($name) {
                $html = '<select class="form-control go-input" name="'.$name.'">';
            }

            $html .= '<option value="">State/Province *</option>';
            foreach($states as $sk => $sv) {
                $html .= '<option value=' . $sk . '>' . $sv . '</option>';
            }
            $html .= '</select>';
            
        } else {
            $html = '<input type="text" class="form-control go-input" placeholder="State/Province *" name="state"/>';
        }
        echo $html;
        exit;
    }
}
