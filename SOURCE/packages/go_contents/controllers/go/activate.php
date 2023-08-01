<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of activate
 *
 * @author Ariel Tabag <atabag@cambridge.org>
 */
Loader::model('cup_go_user_subscription', 'go_contents');
Loader::model('access_code', 'codecheck');


// Hotmaths API
Loader::library('HotMaths/api');

class GoActivateController extends Controller
{

    protected $pkgHandle = 'go_contents';

    public function on_start()
    {
        $v = View::getInstance();
        // ANZGO-3943 modified by mtanada 20181204
        $v->setTheme(PageTheme::getByHandle("go_theme"));

        $html = Loader::helper('html');
        $this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . (string)$html->css(
            'activate.css',
            'go_contents'
        )->href . '?v=2.1"></link>');
        // ANZGO-3582 modified by jbernardez 20180112
        Loader::library('Activation/library');
        Loader::library('Activation/hub_activation');
    }

    public function view()
    {
        global $u;
        $v = View::getInstance();

        if (!$u->isLoggedIn()) {
            $_SESSION['redirectError'] = "You must login first before you can activate a resource.";
            $this->redirect('/go/login');
        } else {
            unset($_SESSION['redirectError']);
        }
    }

    public function processCode($accesscode, $reactivationcode = null, $printAccessCode = null)
    {
        /*
         * ANZGO-3326 Modified by John Renzo S. Sunico, 04/20/2017
         * Modified this to return JSON response.
         */
        header('Content-Type: application/json');

        // ANZGO-3854 added by jbernardez 20180913
        if ($reactivationcode == 'printAccessCode') {
            $reactivationcode = null;
        }

        // ANZGO-3582 modified by jbernardez 20180112
        // now use the Activation Library to consolidate all modifications
        $data['accessCode']         = $accesscode;
        $data['terms']              = 'true';
        $data['reactivationCode']   = $reactivationcode; // ANZGO-3853 added by mtanada 20180906
        $data['printAccessCode']    = $printAccessCode; // ANZGO-3854 added by jbernardez 20180913

        // HUB-146 Modified by John Renzo S. Sunico, 08/28/2018
        $activationLibrary = new HubActivation($data);
        $result = $activationLibrary->activateProduct();

        if ($result['success']) {
            CupGoLogs::trackUser("Activate Code", "Success", "Subscription successful : " . $data['accessCode']);
        } else {
            CupGoLogs::trackUser("Activate Code", "Fail", $result['action'] . " : " . $data['accessCode']);
        }

        // ANZGO-3617 added by jbernardez 20180123
        // if the action is equal to activate-TNGProduct, then return resource for subscription
        // if not, then do not add title and subscription to load quickly
        if ($result['action'] === 'activate-TNGProduct') {
            Loader::model('hub_activation_list', 'go_contents');
            $resourceHelper = Loader::helper('myresources_hub', 'go_contents');
            $activation = $activationLibrary->lastActivation;
            $activationList = new HubActivationList();
            $activationList->fetchMyResourcesListByActivationId($activation->id);
            $activationList->sortSubscriptions(null);
            $subscriptions = $activationList->getPage(0, null);
            $contents = $resourceHelper->formatDisplay($subscriptions);

            $result['title'] = $contents;
            $result['subscriptions'] = $subscriptions;
        }

        echo json_encode($result);
        exit;
    }
}
