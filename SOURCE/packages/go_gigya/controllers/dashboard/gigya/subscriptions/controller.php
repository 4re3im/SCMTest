<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 17/05/2019
 * Time: 10:33 AM
 */

Loader::library('gigya/GSSDK');
Loader::library('gigya/GigyaAccount');

Loader::library('hub-sdk/autoload');
Loader::library('Activation/hub_activation');

use HubEntitlement\Models\Activation;
use HubEntitlement\Models\Permission;
// GCAP-848 added by mtanada 20200504
use HubEntitlement\Models\Product;
class DashboardGigyaSubscriptionsController extends Controller
{
    const PACKAGE_NAME = 'go_gigya';
    const TABS = [
        ['tab-1', 'Subscriptions', true]
    ];

    /**
     * @var \HubEntitlement\Models\Product
     */
    protected $product;

    public function on_start()
    {
        $html = Loader::helper('html');
        $this->addHeaderItem(
            '<link rel="stylesheet" type="text/css" href="' . (string)$html->css(
                'dashboard/gigya/subscriptions.css',
                static::PACKAGE_NAME
            )->href . '?v=14.1"></link>');

        $this->addFooterItem(
            '<script src="' . (string)$html->javascript(
                'dashboard/gigya/subscriptions.js',
                static::PACKAGE_NAME
            )->href . '?v=15.2"></script>');

        Loader::model('subscriptions', static::PACKAGE_NAME);
    }

    public function view($userID = false)
    {
        $user = false;
        if ((bool)$userID !== false) {
            $gigyaAcct = new GigyaAccount($userID);
            $user = $gigyaAcct->getAccountInfo();
            // GCAP-1005 Added by mabrigos 20201028
            $_SESSION['role'] = $user->getRole();
            $_SESSION['firstName'] = $user->getFirstName();
            $_SESSION['lastName'] = $user->getLastName();
            $_SESSION['email'] = $user->getEmail();
            $this->set('UID', $userID);
            $this->set('tabs', static::TABS);
        }

        $this->set('user', $user);
    }

    // GCAP-1091 Modified by mtanada 20201120 To handle both IDs
    public function load()
    {
        $systemID = $this->get('systemID');
        $uId = $this->get('uid');

        echo $this->buildResults($systemID, $uId);
        die;
    }

    public function search()
    {
        $term = $this->get('term');
        $subscriptions = new Subscriptions();

        $results = $subscriptions->getSubscriptionsByTerm($term);
        echo json_encode($results);
        die;
    }

    // GCAP-848 modified by mtanada 20200504
    public function add()
    {
        $userID = $this->post('gigya_system_id');

        if (empty($userID)) {
            $userID = $this->post('gigya_uid');
        }
        // GCAP-1005 Added by mabrigos 20201028
        $user = array(
            'uID' => $this->post('gigya_uid'),
            'externalId' => $this->post('gigya_system_id') !== null ? $this->post('gigya_system_id') : null,
            'Email' => $_SESSION['email'],
            'FirstName' => $_SESSION['firstName'],
            'LastName' => $_SESSION['lastName'],
            'Type' => $_SESSION['role']
        );

        $saID = $this->post('sa_id');
        $sID = $this->post('s_id');

        // GCAP-848 added by mtanada 20200504
        $tabs = $this->getHubProductTabs($sID);
        $subscriptions = new Subscriptions();
        $productTabs = array();

        foreach ($tabs as $tab) {
            if (isset($tab['id'])) {
                array_push($productTabs, $tab['id']);
            }
        }

        $titleIds = $subscriptions->getTitleIds($productTabs);
        $listTitletabs = array();

        if ($titleIds) {
            foreach ($titleIds as $titleId => $value) {
                $listTitletabs[$value['TitleID']][] = (int)$value['tabId'];
            }
            $titleSeriesIds = $subscriptions->getSeriesIds($titleIds);
        }

        $u = new User();
        $createdBy = $u->uID;

        // Get all activations of the user
        $userActivations = Activation::where([
            'user_id' => $userID,
            'is_paginated' => 0
        ]);

        $entitlementStillActivatedByUser = false;
        foreach ($userActivations as $userActivation) {
            if ($userActivation->Archive === 'Y') {
                continue;
            }
            $userPermission = $userActivation->permission;
            $daysRemaining = $userActivation->daysRemaining;

            /* Compares fetched permission's entitlement id to current selected entitlement id */
            if ($userPermission->entitlement_id === (int)$saID &&
                $userActivation->DateDeactivated === null &&
                $daysRemaining > 0) {
                $entitlementStillActivatedByUser = true;
                break;
            }
        }

        if ($entitlementStillActivatedByUser === true) {
            echo json_encode([
                'success' => false,
                'message' => 'You still have active subscription to this 
                Product. Please contact your administrator if you are not 
                able to access this Product on your Resources'
            ]);
            exit;
        }

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
        // GCAP-848 added by mtanada 20200504
        $activationLib->setPrivileges($listTitletabs, $titleSeriesIds);
        // GCAP-1005 modified by mabrigos 20201028
        $activationResult = $activationLib->activateGlobalGoProducts($user);

        echo json_encode([
            'status' => $activationResult['success'],
            'message' => $activationResult['message']
        ]);
        die;
    }

    public function toggle()
    {
        $userID = $this->get('gigya_system_id');

        if (empty($userID)) {
            $userID = $this->get('gigya_uid');
        }

        $subsID = $this->get('subscription_id');

        $subscription = Activation::find($subsID);
        if ($subscription->DateDeactivated) {
            $subscription->DateDeactivated = null;
        } else {
            $subscription->DateDeactivated = date('Y-m-d H:i:s');
        }

        echo json_encode(['status' => $subscription->save()]);
        die;
    }

    // GCAP-1091 Added by mtanada 20201120
    protected function getActivations($userId)
    {
        return Activation::where([
            'user_id' => $userId,
            'orderField' => 'created_at',
            'orderDirection' => 'DESC',
            'is_paginated' => 0
        ]);
    }

    // GCAP-1091 Modified by mtanada 20201120
    protected function buildResults($systemId = null, $uId = null)
    {
        $activationsWithSystemId = [];
        $activationsWithUid = [];

        if (!is_null($systemId)) {
            $activationsWithSystemId = $this->getActivations($systemId);
        }
        if (!is_null($uId)) {
            $activationsWithUid = $this->getActivations($uId);
        }
        $activations = array_merge($activationsWithSystemId, $activationsWithUid);

        $newSubs = [];
        foreach ($activations as $activation) {
            if ($activation->Archive === 'Y') {
                continue;
            }

            $permission = $activation->permission()->fetch();
            $entitlement = $permission->entitlement()->fetch();
            $product = $entitlement->product()->fetch();

            $daysRemaining = $activation->DaysRemaining;

            $active = 'Y';
            if ((bool)$daysRemaining === false || $activation->DateDeactivated) {
                $active = 'N';
            }

            $newSubs[] = [
                'ID' => $activation->id,
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
                'Subscription' => $product->CMS_Name,
                'DateDeactivated' => $activation->DateDeactivated
            ];
        }
        $args = array('subscriptions' => $newSubs, 'showCreator' => true);
        ob_start();
        Loader::packageElement('gigya/user_details/subscriptions/table', static::PACKAGE_NAME, $args);
        return ob_get_clean();
    }

    // GCAP-839 Added and modified by mtanada 20200422 reference: SB-303
    public function setActivateDeactivateUserSubscription()
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
                $dateDeactivated = null;
            } else {
                $dateDeactivated = date('Y-m-d H:i:s');
            }
            try {
                $subscription->metadata = array_merge(
                    $subscription->metadata,
                    array('DateDeactivated' => $dateDeactivated)
                );
            } catch (Exception $e) {
                error_log($e);
                return false;
            }
            $subscription->save();
            unset($subscription);
        }
        $this->displayUserSubscription($userID);
    }

    // GCAP-839 Added and modified by mtanada 20200422 reference: SB-303
    public function archiveUserSubscription()
    {
        $userID = $this->post('userID');
        $usIDs  = $this->post('usIDs');

        if ((!isset($userID) || $userID === '')
            || (!isset($usIDs) || $usIDs === '')) {

            echo json_encode(false);
            exit;
        }

        foreach ($usIDs as $usID) {
            $subscription = Activation::find($usID);
            $archivedData = [
                'DateDeactivated' => date('Y-m-d H:i:s'),
                'Archive' => 'Y',
                'ArchivedDate' => date('Y-m-d H:i:s')
            ];
            try {
                $subscription->metadata = array_merge(
                    $subscription->metadata,
                    $archivedData
                );
            } catch (Exception $e) {
                error_log($e);
                return false;
            }
            $subscription->save();
            unset($subscription);
        }
        $this->displayUserSubscription($userID);
    }

    // GCAP-839 Added and modified by mtanada 20200422 reference: SB-303
    public function setUserSubscriptionEndDate()
    {
        $userID  = $this->post('userID');
        $usIDs   = $this->post('usIDs');
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
            try {
                $subscription->ended_at = date_format($date, 'Y-m-d H:i:s');
            } catch (Exception $e) {
                error_log($e);
                return false;
            }
            $subscription->save();
            unset($subscription);
        }
        $this->displayUserSubscription($userID);
    }

    // GCAP-839 Added and modified by mtanada 20200422 reference: HUB-152
    public function displayUserSubscription($userID)
    {
        usleep(750000);
        $userSubscriptions = [];
        if ($userID !== null ){
            $userSubscriptions = Activation::where([
                'user_id' => $userID
            ]);
        }

        if (count($userSubscriptions) > 0) {
            echo $this->buildResults($userID);
        }
        exit;
    }

    /*
     * GCAP-848 added by mtanada 20200504
     * Always use find() for fetching data with ID
     */
    protected function getHubProductTabs($sId)
    {
        if (!$this->product) {
            $this->product = Product::find((int)$sId);
        }
        return $this->product->Tabs;
    }
}