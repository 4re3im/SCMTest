<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of activate
 *
 * @author paulbalila
 */
class GoUserLandingController extends Controller 
{
    private $ui;
    
    public function on_start()
    {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_plain_theme"));
    }
    
    public function view() 
    {
        global $u;

        if ($u->isLoggedIn()){
            $this->ui = UserInfo::getByID($u->getUserID());    
            $user_name = $this->ui->getAttribute('uFirstName') ." ". $this->ui->getAttribute('uLastName');
            //workaround to get name of the user
            $email = explode("@", $this->ui->uEmail);
            $user = ( $user_name == "") ? $user_name : $email[0];
            $this->set('user', $user);
        }
    }

    // ANZGO-3789 added by jbernardez 20180710
    public function stopBanner($value) 
    {
        $_SESSION['stopBanner'] = $value;
    }
}
