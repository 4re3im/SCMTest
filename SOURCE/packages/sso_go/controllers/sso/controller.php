<?php

/**
 * API for requests
 *
 * @author jamesbernardez
 */
class SsoController extends Controller
{

    protected $pkg_handle = 'sso_go';

    public function __construct()
    {
        parent::__construct();
    }

    public function on_start()
    {

    }

    public function view()
    {

    }

    public function userDetails($authToken)
    {
        // Get User ID from $authToken
        $uID = UserValidationHash::getUserID($authToken, UVTYPE_LOGIN_FOREVER);

        if ($uID) {
            // Retrieve User Details from $uID
            $u = User::getByUserID($uID);

            // get user group
            $group = array();
            foreach ($u->getUserGroups() as $key => $value) {
                if (strlen($value) > 1) {
                    $group[$key] = $value;
                }
            }

            $user = array('id' => $u->getUserID(), 'email' => $u->getUserName(), 'group' => $group);

            echo json_encode($user);
            exit;
        } else {
            echo json_encode(array('status' => FALSE));
            exit;
        }
    }

    // HUB-159 Modified by John Renzo S. Sunico, 09/03/2018
    public function userResourceAccessByTitle($authToken, $title)
    {
        // Get User ID from $authToken
        $uID = UserValidationHash::getUserID($authToken, UVTYPE_LOGIN_FOREVER);

        if ($uID) {
            Loader::library('Activation/user_activation');
            Loader::model('sso', $this->pkg_handle);

            $u = User::getByUserID($uID);

            $ssoGoModel = new SsoGoModel();
            $tabs = $ssoGoModel->getTabByPrivateTab($title);

            $activationLib = new UserActivation();
            $activationLib->setUserId($u->getUserID());
            $subscribedTabIds = $activationLib->getSubscribedTabIds();

            $hasAccessToTab = !empty(array_intersect($subscribedTabIds, $tabs));
            echo json_encode(['status' => $hasAccessToTab]);
            exit;
        } else {
            echo json_encode(array('status' => false));
            exit;
        }
    }

}
