<?php

/**
 * ANZGO-3495 Refactored Activate Controller Page
 * Modified by Shane Camus 09/13/2017
 * Makes use of Activation Library
 */

defined('C5_EXECUTE') || die(_("Access Denied."));


class ActivateController extends Controller
{
    protected $pkgHandle = 'go_contents';

    public function on_start()
    {
        parent::on_start();
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("activate_theme"));

        $html = Loader::helper('html');
        Loader::library('Activation/library');
        Loader::library('Activation/hub_activation');

        // ANZGO-3760 modified by mtanada 20180806
        $this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . (string)$html->css(
            'bootstrap.min.css',
            $this->pkgHandle
            )->href . '?v=2"></link>');
        $this->addHeaderItem($html->css('animate.css', $this->pkgHandle));
        // ANZGO-3789 modified by jbernardez 20180706
        $this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . (string)$html->css(
            'custom.css',
            $this->pkgHandle
            )->href . '?v=7.4"></link>');
        // SB-341 added by mabrigos 20190917
        $this->addHeaderItem($html->javascript('googleTagManager.js', 'go_theme'));
        $this->addFooterItem($html->javascript('jquery.min.js', $this->pkgHandle));
        $this->addFooterItem($html->javascript('bootstrap.min.js', $this->pkgHandle));
        //ANZGO-3759 modified by mtanada 20180703
        // ANZGO-3789 modified by jbernardez 20180706
        $this->addFooterItem('<script type="text/javascript" src="' . (string)$html->javascript(
            'custom.js',
            $this->pkgHandle
            )->href . '?v=3"></script>');
        // ANZGO-3853 Modified by mtanada 20180907
        // ANZGO-3854 Modified by jbernardez 20180913
        $this->addFooterItem('<script type="text/javascript" src="' . (string)$html->javascript(
            'activate_new.js',
            $this->pkgHandle
            )->href . '?v=7.3"></script>');
        $this->addFooterItem($html->javascript('go-core.js', 'go_theme'));

        // SB-61 added by mtanada 20190207 rebotify
        $this->addFooterItem('<div id="rebotifyChatbox" botid="5a039f95653367000586111b">
        <script src="https://enterprise.rebotify.com/js/chatbox/rebotifyChatbox.js"></script></div>');
    }

    // ANZGO-3755 Added by Shane Camus 06/14/18
    public function view()
    {
        $this->set('name', $this->getDisplayName());
    }

    // ANZGO-3755 Added by Shane Camus 06/14/18
    public function getDisplayName()
    {
        $name = '';
        $u = new User();

        if ($u->isLoggedIn()) {
            $ui = UserInfo::getByID($u->getUserID());
            $email = explode("@", $ui->uEmail);
            $name = !empty($ui->getAttribute('uFirstName')) ? $ui->getAttribute('uFirstName') : $email[0];
        }

        return $name;
    }

    public function activateProduct()
    {
        $data = $this->post();

        // HUB- Modified by John Renzo S. Sunico, 08/28/2018
        $activationLibrary = new HubActivation($data);
        $result = $activationLibrary->activateProduct();

        if ($result['success']) {
            CupGoLogs::trackUser("Activate Code", "Success", "Subscription successful : " . $data['accessCode']);
        } else {
            CupGoLogs::trackUser("Activate Code", "Fail", $result['action'] . " : " . $data['accessCode']);
        }

        echo json_encode($result);
        exit;
    }

}
