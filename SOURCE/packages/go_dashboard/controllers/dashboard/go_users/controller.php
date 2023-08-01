<?php

defined('C5_EXECUTE') || die(_("Access Denied."));

Loader::model('go_users/model', 'go_dashboard');
Loader::model('go_users/list', 'go_dashboard');
Loader::model('go_users/teacher_list', 'go_dashboard');
Loader::model('go_user_subscription/model', 'go_dashboard');
Loader::model('go_user_subscription/list', 'go_dashboard');

// Hotmaths API
Loader::library('HotMaths/api');

// HUB-10 Added by John Renzo S. Sunico, April 17, 2018
Loader::library('hub-sdk/autoload');

Loader::library('Activation/hub_activation');

// ANZGO-3169 Added by John Renzo S. Sunico July 26, 2017
Loader::helper('user_validation_hashes', 'go_dashboard');

Loader::library('gigya/GigyaAccount');

use HubEntitlement\Models\Activation;
use HubEntitlement\Models\Permission;

class DashboardGoUsersController extends Controller
{
    private $pkgHandle = 'go_dashboard';

    public function on_start()
    {
        Loader::model('go_users/model', $this->pkgHandle);
        Loader::model('go_users/list', $this->pkgHandle);
        Loader::model('go_users/teacher_list', $this->pkgHandle);
        Loader::model('go_user_subscription/model', $this->pkgHandle);
        Loader::model('go_user_subscription/list', $this->pkgHandle);

        // Hotmaths API
        Loader::library('HotMaths/api');

        // ANZGO-3169 Added by John Renzo S. Sunico July 26, 2017
        Loader::helper('user_validation_hashes', $this->pkgHandle);
    }

    /**
     * ANZGO-3678 added by jbernardez 20180404
     * ANZGO-3708 Modified by Maryjes Tanada 05/04/2018 Remove 2 weeks
     * filterByInterval(14) in view & add pagination
     */
    public function view()
    {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->javascript('go_dashboard_teachers.js', $this->pkgHandle));

        $teacherlist = new GoDashboardGoUsersTeacherList();
        $teacherlist->filterByTeacherGroup(5);
        // ANZGO-3678 added by jbernardez 20180411
        $teacherlist->filterBySFAttribute(0);
        // ANZGO-3678 added by jbernardez 20180504
        $teacherlist->filterByDateTillToday('2016-11-30 00:00:00');
        $teacherlist->sortByCreatedDate();

        // ANZGO-3745 modified by jbernardez 20180606
        $this->set('teacherL', $teacherlist);
        $pageNumber = $this->post('ccm_paging_p');
        $this->set('teacherlist', $teacherlist->getPage($pageNumber));
        $this->set('pagination', $teacherlist->displayPaging(false, true));
    }

    public function add()
    {
        $this->addNote();
    }

    // HUB-10 Modified by John Renzo S. Sunico
    public function search($id = null)
    {
        $postID = filter_input(INPUT_POST, 'user_id');

        if ($postID) {
            $id = $postID;
        }

        $html = Loader::helper('html');
        $form = Loader::helper('form');
        $this->set('form', $form);

        $this->addHeaderItem(
            $html->css('go_dashboard_user.css', 'go_dashboard')
        );
        $this->addHeaderItem(
            $html->javascript('go_dashboard_user.js', 'go_dashboard')
        );
        $this->addHeaderItem(
            $html->css('user_subscription.css', $this->pkgHandle)
        );

        $jsPath = (string)$html->javascript(
                'user_subscription.js', $this->pkgHandle
            )->file . "?v=1";
        $this->addFooterItem(
            '<script type="text/javascript" src="' . $jsPath . '"></script>'
        );

        $userList = new GoDashboardGoUsersList();
        $userObj = new GoDashboardGoUsers();

        if ($this->post('is_ajax') == 'yes') {
            $func = $this->post('func');
            switch ($func) {
                case 'note':
                    $this->addNote();
                    break;
                case 'toggleusersubscription':
                    $this->toggleUserSubscription();
                    break;
                case 'addusersubscription':
                    $this->addUserSubscription();
                    break;
                case 'tracking-general':
                    $this->getUserTrackingGeneral();
                    break;
                default:
                    break;
            }

            exit;
        }

        $userSubscriptions = null;
        $userTrackingGeneral = null;
        $userActivationErrors = null;
        $noteResults = null;

        if (isset($id) && !empty($id)) {
            $userList->filterByuID(trim($id));
            $noteResults = $userObj->getUserNotes($id);

            // HUB-152 Modified by Carl Lewi R. Godoy 08/28/2018
            $userSubscriptions = Activation::where([
                'user_id' => $id,
                'orderField' => 'created_at',
                'orderDirection' => 'DESC',
                'is_paginated' => 0
            ]);

            $userTrackingGeneral = $userObj->getUserTrackingGeneral($id);
            $userActivationErrors = $userObj->getUserActivationErrors($id);
        } else {
            $this->redirect('dashboard/go_users');
        }

        $tabs = array(
            array('tab-1', 'General', true),
            array('tab-2', 'Tools'),
            array('tab-3', 'Subscriptions'),
            array('tab-4', 'Tracking-General'),
            array('tab-5', 'Activation-Errors'),
        );

        $this->set('tabs', $tabs);
        $this->set('user', $userList->getPage());
        $this->set('noteResults', $noteResults);
        $this->set('userSubscriptions', $userSubscriptions);
        $this->set('userTrackingGeneral', $userTrackingGeneral);
        $this->set('userActivationErrors', $userActivationErrors);
    }

    public function getUserTrackingGeneral()
    {
        $userID = $this->post('user_id');
        $userTrackingGeneral = new GoDashboardGoUsers();
        $userTrackingGeneralResult = $userTrackingGeneral
            ->getUserTrackingGeneral($userID);

        if (count($userTrackingGeneralResult) > 0) {

            echo <<<html
            <table id='ccm-product-list' class='ccm-results-list' cellspacing='0' cellpadding='0' border='0'>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Page Name</th>
                        <th>Action</th>
                        <th>Infos</th>
                        <th>Access Level</th>
                    </tr>
                </thead>
                <tbody>
html;

            if (isset($userTrackingGeneralResult)) {
                foreach ($userTrackingGeneralResult as $userTrackingGeneral) {
                    echo "<tr class='ccm-list-record'> ";
                    echo '    <td>' . $userTrackingGeneral['CreatedDate'] . '</td>';
                    echo '    <td>' . $userTrackingGeneral['PageName'] . '</td>';
                    echo '    <td>' . $userTrackingGeneral['Action'] . '</td>';
                    echo '    <td>' . $userTrackingGeneral['Info'] . '</td>';
                    echo '    <td>' . $userTrackingGeneral['Access_Level'] . '</td>';
                    echo "</tr>";
                }
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<div style="background:#08c; padding:5px;">' .
                'Sorry, Your query Returned empty result, please try again!</div>';
        }

        exit;
    }

    public function addNote()
    {
        $userID = $this->post('user_id');
        $note = $this->post('note');

        $userNotes = new GoDashboardGoUsers();
        $userNotes->addUserNotes($userID, $note);
        $noteResults = $userNotes->getUserNotes($userID);

        if (count($noteResults) > 0) {
            echo "<table id='ccm-product-list' class='ccm-results-list' cellspacing='0' cellpadding='0' border='0'>";
            echo "        <thead>";
            echo "            <tr>";
            echo "                <th>Date</th>";
            echo "                <th>Note</th>";
            echo "            </tr>";
            echo "        </thead>";
            echo "        <tbody>";
            if (isset($noteResults)) {
                foreach ($noteResults as $noteResult) {
                    echo "                       <tr class='ccm-list-record'> ";
                    echo "                            <td>" . $noteResult['CreationDate'] . "</td>";
                    echo "                            <td>" . $noteResult['NoteText'] . "</td>";
                    echo "                        </tr>";
                }
            }
            echo "        </tbody>";
            echo "    </table>";
        } else {
            echo '<div style="background:#08c; padding:5px;">' .
                'Sorry, Your query Returned empty result, please try again!</div>';
        }

        exit;
    }

    public function toggleUserSubscription()
    {

        $userID = $this->post('user_id');
        $usID = $this->post('usid');

        // HUB-152 Modified by Carl Lewi R. Godoy 08/28/2018
        $subscription = Activation::find($usID);
        if ($subscription->DateDeactivated) {
            $subscription->DateDeactivated = null;
        } else {
            $subscription->DateDeactivated = date('Y-m-d H:i:s');
        }
        $subscription->save();

        $this->displayUserSubscription($userID);
    }

    // SB-303 modfied by jbernardez 20190822
    public function userSubscriptionActivations()
    {
        $switchToActive = $this->post('switchToActive');
        $userID = $this->post('userID');
        $usIDs = $this->post('usIDs');

        if ((!isset($switchToActive) || $switchToActive === '') 
            || (!isset($userID) || $userID === '') 
            || (!isset($usIDs) || $usIDs === '')) {

            echo json_encode(false);
            exit;
        }

        foreach ($usIDs as $usID) {
            $subscription = Activation::find($usID);
            if ($switchToActive === 'true') {
                $subscription->DateDeactivated = null;
            } else {
                $subscription->DateDeactivated = date('Y-m-d H:i:s');
            }
            $subscription->save();
            unset($subscription);
        }

        $this->displayUserSubscription($userID);
    }

    // SB-303 modfied by jbernardez 20190822
    public function userSubscriptionArchives()
    {
        $userID = $this->post('userID');
        $usIDs = $this->post('usIDs');

        if ((!isset($userID) || $userID === '') 
            || (!isset($usIDs) || $usIDs === '')) {

            echo json_encode(false);
            exit;
        }

        foreach ($usIDs as $usID) {
            $subscription = Activation::find($usID);
            $subscription->DateDeactivated = date('Y-m-d H:i:s');
            $subscription->Archive = 'Y';
            $subscription->ArchivedDate = date('Y-m-d H:i:s');
            $subscription->save();
            unset($subscription);
        }

        $this->displayUserSubscription($userID);
    }

    // SB-303 modfied by jbernardez 20190822
    public function userSubscriptionEndDates()
    {
        $userID = $this->post('userID');
        $usIDs = $this->post('usIDs');
        $endDate = $this->post('endDate');

        if ((!isset($endDate) || $endDate === '') 
            || (!isset($userID) || $userID === '') 
            || (!isset($usIDs) || $usIDs === '')) {

            echo json_encode(false);
            exit;
        }

        foreach ($usIDs as $usID) {
            $subscription = Activation::find($usID);
            $date = date_create($endDate);
            $subscription->ended_at = date_format($date, 'Y-m-d H:i:s');
            $subscription->save();
            unset($subscription);
        }

        $this->displayUserSubscription($userID);
    }

    public function createSubscriptionHtml($userId)
    {
        // Get all activations of the user
        $activations = Activation::where([
            'user_id' => (int) $userId,
            'orderField' => 'created_at',
            'orderDirection' => 'DESC',
            'is_paginated' => 0
        ]);

        $newSubs = [];
        foreach ($activations as $activation) {
            // SB-283 added by mabrigos 20190726
            if ($activation->Archive === "Y") { 
                continue; 
            }
            $permission = $activation->permission()->fetch();
            $entitlement = $permission->entitlement()->fetch();
            $product = $entitlement->product()->fetch();

            $daysRemaining = $activation->daysRemaining;

            $active = 'Y';
            if (!$daysRemaining || $activation->DateDeactivated) {
                $active = 'N';
            }

            $newSubs[] = [
                'ID' =>  $activation->id,
                'CreationDate' => $activation->created_at->format('Y-m-d H:i:s'),
                'StartDate' => $activation->activated_at->format('Y-m-d H:i:s'),
                'EndDate' => $activation->ended_at->format('Y-m-d H:i:s'),
                'Duration' => $entitlement->Duration,
                'Active' => $active,
                'AccessCode' => $permission->proof,
                'PurchaseType' => $activation->PurchaseType,
                'DaysRemaining' => $activation->DaysRemaining,
                'CreatedBy' => $activation->CreatedBy,
                'SubType' => $entitlement->Type,
                'Subscription' => $product->CMS_Name
            ];
        }
        $args = array('subscriptions' => $newSubs, 'showCreator' => true);
        ob_start();
        Loader::packageElement('subscription-refresh', $this->pkgHandle, $args);
        return ob_get_clean();
    }

    // HUB-152 Modified by Carl Lewi R. Godoy 08/28/2018
    public function displayUserSubscription($userID)
    {
        // SB-303 added by jbernardez/mabrigos 20190828
        // this is a bad solution for displaying the list properly
        usleep(750000);
        
        $userSubscriptions = [];
        if ($userID !== null ){
            $userSubscriptions = Activation::where([
                'user_id' => $userID
            ]);
        }

        if (count($userSubscriptions) > 0) {
            echo "<table id='ccm-product-list' class='ccm-results-list' cellspacing='0' cellpadding='0' border='0'>";
            echo "    <thead>";
            echo "        <tr>";
            echo "            <th><input type='checkbox' id='checkAll'></th>";
            echo "            <th>Subscription</th>";
            echo "            <th>SubType</th>";
            echo "            <th>Creation Date</th>";
            echo "            <th>EndDate</th>";
            echo "            <th>Duration</th>";
            echo "            <th>Active</th>";
            echo "            <th>AccessCode</th>";
            echo "            <th>Days Remaining</th>";
            echo "            <th>Purchase Type</th>";
            echo "            <th>Added by</th>";
            echo "        </tr>";
            echo "      </thead>";
            echo "      <tbody>";
            echo $this->createSubscriptionHtml($userID);
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<div style="background:#08c; padding:5px;">' .
                'Sorry, Your query Returned empty result, please try again!</div>';
        }
        exit;
    }

    /**
     * ANZGO-3722 Modified by Shane Camus 05/23/18
     */
    public function addUserSubscription()
    {

        $userID = $this->post('user_id');
        $saID = $this->post('sa_id');
        $sID = $this->post('s_id');

        // HUB-152 Modified by Carl Lewi R. Godoy 08/28/2018
        $u = new User();
        $createdBy = $u->uID;
        // Get user activations
        $userActivations = Activation::where([
            'user_id' => $userID,
            'is_paginated' => 0
        ]);

        $entitlementStillActivatedByUser = false;
        foreach ($userActivations as $userActivation) {
            $userPermission = $userActivation->permission;
            $daysRemaining = $userActivation->daysRemaining;

            /* Compares fetched permission's entitlement id to current selected entitlement id */
            if ($userPermission->entitlement_id == $saID &&
                    $userActivation->DateDeactivated === null &&
                    $daysRemaining > 0) {
                $entitlementStillActivatedByUser = true;
                break;
            }
        }

        // HUB-68 Added by Carl Lewi Godoy 06/29/2018
        if ($entitlementStillActivatedByUser === true) {
            echo json_encode([
                'success' => false,
                'message' => 'You still have active subscription to this 
                Product. Please contact your administrator if you are not 
                able to access this Product on your Resources'
            ]);
            exit;
        }

        // Create permission without proof
        $permission = new Permission([
            'entitlement_id' => (int)$saID,
            'released_at' => date('Y-m-d H:i:s'),
            'expired_at' => null,
            'limit' => 1,
            'is_active' => 1
        ]);
        $permission->save();

        // Activate created permission with dummy proof
        $activationLib = new HubActivation([
            'accessCode' => '0000-0000-0000-0000',
            'terms' => 'true'
        ]);
        $activationLib->setActivationOwner($userID);
        $activationLib->setPermission($permission);
        $activationLib->activateInGo = false;
        $activationLib->setPurchaseType(HubActivation::PURCHASE_TYPE_CMS);
        $activationLib->setCreatedBy($createdBy);
        $activationResult = $activationLib->activateProduct();

        if (!$activationResult['success']) {
            echo json_encode([
                'success' => false,
                'message' => $activationResult['message']
            ]);
            exit;
        }

        $html = $this->createSubscriptionHtml($userID);

        $out = array(
            'success' => true,
            'message' => $html
        );
        echo json_encode($out);
        exit;
    }

    /**
     * ANZGO-3722 Added by Shane Camus 05/23/18
     * @param $userID
     * @param $saID
     * @param $sID
     * @return array
     */
    public function validateUserSubscriptionAddition($userID, $saID, $sID)
    {
        $userSubscription = new GoDashboardGoUsers();

        if ($userSubscription->checkUserSubscription($userID, $saID)) {
            return array(
                'success' => false,
                'message' => 'You still have active subscription to this Product.
                Please contact your administrator if you are not able to access this Product on your Resources'
            );
        }

        $userInfo = $userSubscription->getUserInfo($userID);

        if ($userInfo['gID'] == 5 && $this->hasTeacherProductSubscription($userID, $sID)) {
            return array(
                'success' => false,
                'message' => 'You already have an existing Teacher Version of the same product.'
            );
        }

        return array('success' => true);
    }

    /**
     * ANZGO-3722 Added by Shane Camus 05/23/18
     * @param $userID
     * @param $sID
     * @return bool
     */
    public function hasTeacherProductSubscription($userID, $sID)
    {
        $userSubscription = new GoDashboardGoUsers();
        $hasTeacherProduct = false;
        $existingSubscriptions = $userSubscription->getUserSubscriptions($userID);
        $subscriptionToBeAdded = $userSubscription->getSubscriptionDetails($sID);
        $subtypeOfSubscriptionToBeAdded = strtolower($subscriptionToBeAdded['Name']);

        foreach ($existingSubscriptions as $subscription) {
            $status = $subscription['Active'];
            $subtype = strtolower($subscription['SubType']);

            if ($subscription['TitleID'] == $subscriptionToBeAdded['TitleID'] && $status == 'Y' &&
                strpos($subtype, 'teacher') !== false && strpos($subtypeOfSubscriptionToBeAdded, 'teacher') === false) {
                $hasTeacherProduct = true;
            }
        }

        return $hasTeacherProduct;
    }

    public function saveUserGeneralInfo()
    {
        $u = new GoDashboardGoUsers();
        $id = $this->post('userid');
        $data = $u->getUserInfo($id);
        $verified = $this->post('verified');
        $manuallyActivated = $this->post('manuallyactivated');
        $manuallyActivatedChecker = false;
        $activatedDate = $this->post('activateddate');

        if ($data['uIsValidated'] != $verified && $verified == 1) {
            $manuallyActivated = 1;
            $manuallyActivatedChecker = true;
            $activatedDate = date("Y-m-d H:i:s");
        }

        $adminUser = new User();

        $ui = UserInfo::getByID($id);
        $ui->setAttribute('uEmail', $this->post('email'));
        $ui->setAttribute('uFirstName', $this->post('firstname'));
        $ui->setAttribute('uLastName', $this->post('lastname'));
        $ui->setAttribute('uSecurityQuestion', $this->post('question'));
        $ui->setAttribute('uSecurityAnswer', $this->post('answer'));
        $ui->setAttribute('uSchoolAddress', $this->post('address'));
        $ui->setAttribute('uSuburb', $this->post('suburb'));
        $ui->setAttribute('uPostcode', $this->post('postcode'));
        $ui->setAttribute('uCountry', $this->post('country'));
        $ui->setAttribute('uSchoolPhoneNumber', $this->post('phonenumber'));
        $ui->setAttribute('uPositionTitle', $this->post('position'));
        $ui->setAttribute('uSchoolName', $this->post('school'));
        $ui->setAttribute('uPositionType', $this->post('title'));
        $ui->setAttribute('uCustomerCare', $this->post('customercare'));
        $ui->setAttribute('uPMBYEmail', $this->post('promotional_email'));
        $ui->setAttribute('uPMByRegularPost', $this->post('promotional_post'));

        $data = array(
            'UserSearchIndexAttributes' => array(
                'ak_uPositionType' => $this->post('title'),
                'ak_uState' => $this->post('state'),
                'ak_uActivatedDate' => $activatedDate,
                'ak_uManuallyActivated' => $manuallyActivated,
                'ak_uMAStaffID' => $adminUser->getUserID(),
                'ak_uNotes' => $this->post('sernote')
            ),
            'Users' => array(
                'uIsActive' => $this->post('active'),
                'uIsValidated' => $this->post('verified'),
                'uDateAdded' => $this->post('creationdate')
            ),
            'UserGroups' => array('gID' => $this->post('usertype')),
            'SalesforceContacts' => array('accountID' => $this->post('salesforceid'))
        );

        switch ($this->post('country')) {
            case 'Australia':
                $ui->setAttribute('uStateAU', $this->post('state'));
                break;
            case 'New Zealand':
                $ui->setAttribute('uStateNZ', $this->post('state'));
                break;
            case 'Canada':
                $ui->setAttribute('uStateCA', $this->post('state'));
                break;
            case 'United States':
                $ui->setAttribute('uStateUS', $this->post('state'));
                break;
            default:
                $ui->setAttribute('uState', $this->post('state'));
                break;
        }

        $dashboardGoUsers = new GoDashboardGoUsers($id);
        $dashboardGoUsers->saveUserGeneralInfo($data);

        if ($manuallyActivatedChecker) {
            $userInfo = $u->getUserInfo($id);
            $staff = new GoDashboardGoUsers($userInfo['ak_uMAStaffID']);
            echo json_encode(array(
                'ak_uActivatedDate' => $userInfo['ak_uActivatedDate'],
                'ak_uManuallyActivated' => $userInfo['ak_uManuallyActivated'],
                'ak_uMAStaffID' => $userInfo['ak_uMAStaffID'],
                'uName' => $staff->uName
            ));
            //get current user info
        } else {
            echo true;
        }

        exit;
    }

    /**
     * @param array $userData
     * @return bool
     */
    public function saveUserToGigya(array $userData)
    {
        $goUsers = new GoDashboardGoUsers();
        $result = $goUsers->getGigyaIDFromUID($userData['userid']);

        // Do transformation here to avoid putting C5 functions in GigyaAccount class.
        $group = Group::getByID($userData['usertype']);
        $userData['usertype'] = strtolower($group->gName);

        $gigyaAccount = new GigyaAccount();
        $status = $gigyaAccount->editUserInfo($result['gUID'], $userData);

        return $status;
    }

    /**
     * @param String $id
     */
    public function archiveUser($id)
    {
        $dashboardGoUsers = new GoDashboardGoUsers($id);

        $dashboardGoUsers->archiveUser();
        exit;
    }

    /**
     * ANZGO-3169 Added by John Renzo S. Sunico, July 26, 2017
     * Generate Reset Password Link
     */

    public function generateResetPasswordLink()
    {
        $u = new User();
        $vs = Loader::helper('validation/strings');
        $em = $this->post('uEmail');
        $response = array();

        if (!$u->isLoggedIn() || !array_intersect($u->getUserGroups(), ['Administrators', 'CUP Staff'])) {
            http_response_code(403);
            exit;
        }

        try {
            if (!$vs->email($em)) {
                throw new Exception(t('Invalid email address.'));
            }

            $oUser = UserInfo::getByEmail($em);

            if (!$oUser) {
                $this->set('errorMsg', 'We have no record of that email address.');
                throw new Exception(t('We have no record of that email address.'));
            }

            $uHash = UserValidationHash::add($oUser->getUserID(), UVTYPE_CHANGE_PASSWORD, true);
            $response['resetLink'] = BASE_URL . View::url('/go/forgot_password', 'change_password', $uHash);
            $response['success'] = true;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            $response['success'] = false;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // ANZGO-3678 added by jbernardez 20180405

    /**
     * Sends information to Salesforce via API
     */
    public function salesforceUpdate()
    {
        Loader::library('SalesForce/library');

        $id = $this->post('id');
        $u = User::getByUserID($id);
        $ui = UserInfo::getByID($id);
        $response = array();

        $userAttributeKey = new UserAttributeKey();
        $userAttributes = $userAttributeKey->getAttributes($id);

        // ANZGO-3718 modified by jbernardez 20180516
        // SB-26 modified by jbernardez 20190129
        $data = array(
            'title'             => 'Teacher',
            'firstName'         => $userAttributes->getAttribute('uFirstName'),
            'lastName'          => $userAttributes->getAttribute('uLastName'),
            'email'             => $u->uName,
            'schoolCampus'      => $userAttributes->getAttribute('uSchoolName'),
            'query'             => '',
            // SB-26 modified by jbernardez 20190129
            'productsUsing'     => $userAttributes->getAttribute('uProductsUsing'),
            'customerCare'      => $userAttributes->getAttribute('uCustomerCare'),
            // SB-26 modified by jbernardez 20190129
            'schoolPostCode'    => $userAttributes->getAttribute('uSchoolPostCode'),
        );

        $salesForce = new SalesForceLibrary($data);
        // ANZGO-3718 modified by jbernardez 20180516
        // changed sendLead() to sendContact()
        // sendLead() method still exist and can be used
        $response = $salesForce->sendContact();

        if ($response['error'] === false) {
            // save response ID
            // save this to new attribute uSalesForceID
            $ui->setAttribute('uSalesForceID', $response['result']->id);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // ANZGO-3678 added by jbernardez 20180405
    public function salesforceHide()
    {
        $id = $this->post('id');
        $ui = UserInfo::getByID($id);

        $ui->setAttribute('uHideSalesForce', 1);

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

}
