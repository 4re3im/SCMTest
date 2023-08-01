<?php

/**
 * Description of activate
 *
 * @author paulbalila
 */
class ConfirmSignupController extends Controller {
    
    public function on_start() {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_plain_theme"));
    }
    
    public function view() {
        $email = $_POST['email'];
        $this->set('email',$email);
    }
}
