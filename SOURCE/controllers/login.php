<?php
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * ANZGO-3223 Added by John Renzo Sunico, May 05, 2017
 * Added to override dashboard logout Redirect.
 */
class LoginController extends Concrete5_Controller_Login
{
    public function logout()
    {
        // ANZGO-3378, James Bernardez, 20170518
         $u = new User();
         $u->logout();
         $this->redirect('/go/');
         //$this->redirect('/sso/logout/'); //removed by ariel
    }
}
