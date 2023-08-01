<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of controller
 *
 * @author paulbalila
 */
class GoController extends Controller
{
    const FIRST_LOGIN = 'firstLogin';
    public function on_start()
    {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_theme"));
    }

    public function view()
    {
        /** ANZGO-3653 Modified by Maryjes Tanada 03/07/2018
         * Added to redirect logged in users to MyResources page instead of Home page (/go)
         * ANZGO-3741 Modified by Maryjes Tanada 05/30/2018
         * Filter non-admin users to redirect to myresources & admins will still be able to access home page
         */
        $u = new User();
        $isAdmin = $u->uGroups[3] === 'Administrators';
        if ($u->isLoggedIn() && !$isAdmin) {
            CupGoLogs::trackUser('MyResources', 'View', '');
            $this->redirect('/go/myresources/');
        }
        // ANZGO-3013
        CupGoLogs::trackUser('Home', 'View', '');
        //SB-102 added by mabrigos 20190322
        unset($_SESSION['visitedMyResources']);
    }

    public function checkCookie()
    {
        $u = new User();
        echo ($u->isLoggedIn()) ? 1 : 0;
        exit;
    }

    /**
     * Creates the session variable needed for the initial marketing popup.
     */
    public function popupSeenCheck()
    {
        echo json_encode(array('status' => $_COOKIE['mktg-popup-flag']));
        exit;
    }

    /**
     * Sets marketing popup session variable to true. Meaning the popup has been seen
     * and should not be seen afterwards. Unless the session has been restarted.
     */
    public function popupSeen()
    {
        setcookie('mktg-popup-flag', 'seen');
    }

    /**
     * Hides the user guide popup by setting the User attribute, "showHelp" to true.
     */
    public function hideHelpPopup()
    {
        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());
        $userAttribs = AttributeSet::getByHandle('misc');

        foreach ($userAttribs->getAttributeKeys() as $ua) {
            if ($ua->getAttributeKeyHandle() == 'showHelp') {
                $ui->setAttribute($ua, 1);
            }
        }
    }

    // ANZUAT-128
    public function hideHelpSession()
    {
        $_SESSION['hideHelpSession'] = true;
    }

    // ANZUAT-128
    /**
     * This goes in tandem with hideHelpPopup
     * record the date of the first login to attribute
     */
    private function recordFirstLogin()
    {
        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());
        $userAttribs = AttributeSet::getByHandle('misc');

        $today = date("Ymdhis");

        foreach ($userAttribs->getAttributeKeys() as $ua) {
            if ($ua->getAttributeKeyHandle() == static::FIRST_LOGIN) {
                $ui->setAttribute($ua, $today);
            }
        }
    }

    // ANZUAT-128
    public function firstLoginCheck()
    {
        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());
        $userAttribs = AttributeSet::getByHandle('misc');

        $firstLogin = '';
        $returnValue = '';

        foreach ($userAttribs->getAttributeKeys() as $ua) {
            if ($ua->getAttributeKeyHandle() == static::FIRST_LOGIN) {
                $firstLogin = $ui->getAttribute(static::FIRST_LOGIN);
            }
        }

        // if no value for firstLogin, call recordFirstLogin to enter initial value
        if ((is_null($firstLogin)) || ($firstLogin == '')) {
            $this->recordFirstLogin();
            $returnValue = false;
        } else {
            $today = date("Ymdhis");

            // check if firstLogin is greater than 7 days
            if ($firstLogin < strtotime('-7 days', $today)) {
                $returnValue = true;
            } else {
                $returnValue = false;
            }
        }
        echo trim($returnValue);
        die();
    }

    public function contactUsMore()
    {
        $this->set('viewmore', true);
    }
}
