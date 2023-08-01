<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 25/01/2021
 * Time: 10:17 PM
 */
Loader::library('hub-sdk/autoload');
Loader::library('Activation/hub_activation');

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use HubEntitlement\Models\Activation;
use HubEntitlement\Models\Permission;

class DashboardInstitutionManagementReviewController extends Controller
{
    private $gi;
    private $reviewHelper;
    const SCRIPTS_JS_VERSION = '1.1';
    const SUBSCRIPTION_JS_VERSION = '1.1';

    const PACKAGE_HANDLE = 'institution_management';

    public function on_start()
    {
        $this->reviewHelper = Loader::helper('review', static::PACKAGE_HANDLE);
        Loader::library('gigya/datastore/GigyaInstitution');
        $htmlHelper = Loader::helper('html');
        $this->gi = new GigyaInstitution();

        Loader::model('subscriptions', static::PACKAGE_HANDLE);

        $cssPath = (string)$htmlHelper->css('tabs.css', static::PACKAGE_HANDLE)->file . "?v=1.0";
        $this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . $cssPath . '">');

        $scriptsJsHref = (string)$htmlHelper->javascript(
            'review/scripts.js',
            static::PACKAGE_HANDLE)
            ->href;
        $scriptsJsHref .= '?v=' . static::SCRIPTS_JS_VERSION;

        $subscriptionJsHref = (string)$htmlHelper->javascript(
            'review/subscriptions.js',
            static::PACKAGE_HANDLE)
            ->href;
        $subscriptionJsHref .= '?v=' . static::SUBSCRIPTION_JS_VERSION;

        $this->addFooterItem(
            '<script type="text/javascript" src="' . $scriptsJsHref . '" ></script>'
        );

        $this->addFooterItem(
            '<script type="text/javascript" src="' . $subscriptionJsHref . '" ></script>'
        );

        $this->log = new Logger('Attribute');
        $this->log->pushHandler(
            new StreamHandler('logs/attribute_institution.' . date("Y-m-d", time()) . '.log', Logger::INFO)
        );
    }

    public function view($oid = false)
    {
        if (!$oid) {
            $this->redirect('/dashboard/institution_management/search');
        }

        $subsModel = new Subscriptions();

        $result = $this->gi->getByOID($oid);
        $institution = $this->reviewHelper->format($result);
        $institutionSubscriptions = $subsModel->getInstitutionSubscriptions($oid);
        $this->set('userSubscriptions', $institutionSubscriptions);
        $this->set('institution', $institution);
    }

    public function searchByEmail()
    {
        $term = $this->get('term');
        $role = $this->get('role');
        $gigyaAcct = new GigyaAccount();

        $result = $gigyaAcct->searchUserByEmailAndRole($term, $role);
        $data = ['success' => false];

        if ($result) {
            $profile = json_decode($result['profile']);
            $instituteRole = json_decode($result['instituteRole']);
            foreach ($instituteRole as $institution) {
                if ($institution->role === $role) {
                    $userRole = $institution->role;
                }
            }
            $data = [
                'success' => true,
                'uid' => $result['UID'],
                'email' => $profile->email,
                'name' => "$profile->firstName $profile->lastName",
                'firstName' => $profile->firstName,
                'lastName' => $profile->lastName,
                'role' => $userRole
            ];
        }

        $formatted = [$data];

        echo json_encode($formatted);
        die;
    }

    public function runAttributeInsitution($oid)
    {
        Loader::helper('notification', static::PACKAGE_HANDLE);
        $nh = new NotificationHelper();

        if (!isset($_POST['studentIds']) && !isset($_POST['teacherIds'])) {
            echo $nh->getNotification('error', $nh::$ERROR_MISSING_DATA);
            die;
        }

        $uids = [
            'student' => !is_null($_POST['studentIds']) ? $_POST['studentIds'] : '',
            'teacher' => !is_null($_POST['teacherIds']) ? $_POST['teacherIds'] : ''
        ];

        $schoolDetails = [
            'oid' => $oid,
            'name' => $_POST['name']
        ];

        $isExportSuccessful = $this->reviewHelper->exportToS3($schoolDetails, $uids);

        if (!$isExportSuccessful) {
            $this->log->info(
                'Error to upload in S3 with school (' . $_POST['name'] . ') time Start: ' . date(DateTime::ISO8601)
            );
            echo $nh->getNotification('error', $nh::$ERROR_S3_BUCKET);
            die;
        }

        $this->log->info(
            'Uploaded to S3 with school (' . $_POST['name'] . ') time Start: ' . date(DateTime::ISO8601)
        );
        echo $nh->getNotification('success', $nh::$SUCCESS_GENERAL,
            ['uids' => $uids]);
        exit;
    }

    public function loadTable()
    {
        $oid = $_GET['oid'];
        $role = $_GET['role'];
        $gigyaAcct = new GigyaAccount();
        $options = ['page' => 1, 'limit' => 20];
        $accounts = $gigyaAcct->getUsersByOIDAndRole($oid, true, $options, $role);
        $hasResult = $accounts->getInt('totalCount') > 0;

        $this->reviewHelper->init($accounts, $oid);

        $data = [
            'hasResult' => $hasResult,
            'tableBody' => $this->reviewHelper->buildTableBody(),
            'pager' => $this->reviewHelper->buildPager()
        ];
        echo json_encode($data);
        die;
    }

    public function navigate($page = 1)
    {
        $oid = $_GET['oid'];
        $role = $_GET['role'];
        $gigyaAcct = new GigyaAccount();
        $options = ['page' => $page, 'limit' => 20];
        $accounts = $gigyaAcct->getUsersByOIDAndRole($oid, true, $options, $role);

        $this->reviewHelper->updatePage($page);
        $this->reviewHelper->init($accounts, $oid);

        $data = [
            'tableBody' => $this->reviewHelper->buildTableBody(),
            'pager' => $this->reviewHelper->buildPager()
        ];
        echo json_encode($data);
        die;
    }

    /**
     * Modify the current state of subscriptions.
     */
    public function update()
    {
        Loader::helper('notification', static::PACKAGE_HANDLE);
        $nh = new NotificationHelper();
        $data = $this->post();
        $oid = $data['oid'];
        $usIDs = $data['usIDs'];
        $param = $data['param'];
        $endDate = $data['endDate'];

        if (empty($oid) || empty($usIDs)) {
            echo $nh->getNotification('error', $nh::$ERROR_SUBSCRIPTION_MISSING_INFORMATION);
            exit;
        }

        $status = $this->reviewHelper->updateInstitutionSubscriptions($param, $usIDs, $endDate);
        if ($status) {
            $subscriptionsTable = $this->displaySubscriptionTable($oid);
            if (!$subscriptionsTable) {
                echo $nh->getNotification('error', $nh::$ERROR_SUBSCRIPTION_NOT_FOUND);
                exit;
            }

            echo $nh->getNotification(
                'success',
                $nh::$SUCCESS_GENERAL,
                ['body' => $subscriptionsTable]
            );
        } else {
            echo $nh->getNotification('error', $nh::$ERROR_SUBSCRIPTION_NO_ACTION);
        }
        die;
    }

    public function displaySubscriptionTable($oid, $isAdd = false)
    {
        if (!$isAdd) {
            usleep(750000);
        }

        $subsModel = new Subscriptions();
        $activations = $subsModel->getInstitutionSubscriptions($oid);
        if (count($activations) === 0) {
            return false;
        }

        return $this->reviewHelper->getSubscriptionsTable($activations);
    }

    public function search()
    {
        $term = $this->get('term');
        $subscriptions = new Subscriptions();
        $results = $subscriptions->getSubscriptionsByTerm($term);
        echo json_encode($results);
        die;
    }

    public function add()
    {
        $oid = $this->post('oid');
        $saID = $this->post('sa_id');
        $sID = $this->post('s_id');

        $subsModel = new Subscriptions();

        $u = new User();
        $createdBy = $u->uID;
        $institutionActivations = $subsModel->getInstitutionSubscriptions($oid);

        $entitlementStillActivatedByInstitution = false;
        foreach ($institutionActivations as $institutionActivation) {
            $institutionPermission = $institutionActivation->permission;
            $daysRemaining = $institutionActivation->daysRemaining;

            if ($institutionPermission->entitlement_id === $saID &&
                $institutionActivation->DateDeactivated === null &&
                $daysRemaining > 0) {
                $entitlementStillActivatedByInstitution = true;
                break;
            }
        }

        if ($entitlementStillActivatedByInstitution === true) {
            echo json_encode([
                'success' => false,
                'message' => 'You still have active subscription to this 
                product. Please contact your administrator if you are not 
                able to access this product on your resources'
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
        $activationLib->setPurchaseType(HubActivation::PURCHASE_TYPE_CMS);
        $activationLib->setCreatedBy($createdBy);
        $activationLib->setInstitutionId($oid);
        $activationResult = $activationLib->proceedSiteLicenseActivation();

        if (!$activationResult['success']) {
            echo json_encode([
                'success' => false,
                'message' => $activationResult['message']
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => $this->displaySubscriptionTable($oid, true)
        ]);
        exit;
    }
}