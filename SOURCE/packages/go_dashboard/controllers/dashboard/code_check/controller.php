<?php

/**
 * ANZGO-3500 Modified by Shane Camus 10/02/2017
 */

defined('C5_EXECUTE') || die(_("Access Denied."));

Loader::library('hub-sdk/autoload');
Loader::library('Activation/hub_activation');
Loader::library('gigya/GigyaAccount');

use HubEntitlement\Models\Permission;
use HubEntitlement\Repositories\PermissionRepository;

class DashboardCodeCheckController extends Controller
{
    private $pkgHandle = 'go_dashboard';
    private $messageFlag = true;

    public function on_start()
    {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->javascript('bootstrap-modal.js', $this->pkgHandle));
        $this->addHeaderItem($html->javascript('go_dashboard_user.js', $this->pkgHandle));
        $this->addHeaderItem($html->css('bootstrap.min.css', $this->pkgHandle));
        // GCAP-844 Added by machua 20200511 moved inline js to an external js
        $this->addHeaderItem('<script type="text/javascript" src="'
            . (string) $html->javascript('code_check.js', $this->pkgHandle)->href . '?v=1.2"></script>');
        Loader::library('HotMaths/api');
        Loader::model('code_check/model', $this->pkgHandle);
        Loader::model('code_check/list', $this->pkgHandle);
        Loader::model('user/user_model');
    }

    // HUB-08 Modified by John Renzo S. Sunico, 04/27/2018
    public function view($accessCode = '')
    {
        // SB-10 added by jbernardez 20191218
        if (isset($_SESSION['accessCodeCheck'])) {
            $accessCode = $_SESSION['accessCodeCheck'];
            unset($_SESSION['accessCodeCheck']);
            $this->redirect('/dashboard/code_check/' . $accessCode);
        }

        $searchString = $this->post('search_string') ?: '';
        $isHotMaths = $this->post('hmCheckbox') == 'on';

        if ($accessCode) {
            $this->set('parameter', true);
        }

        $accessCode = $accessCode ?: $searchString;

        if (empty($accessCode)) {
            $codeFailList = new GoDashboardCodecheckList();
            $codeFailList->filterByAction('Fail');
            $codeFailList->filterByGroups();
            $codeFailList->sortByCreatedDate();

            $this->set('codeFailList', $codeFailList->getPage());
            $this->set('codeFailListPagination', $codeFailList->displayPaging(false, true));

            return;
        }

        if ($isHotMaths) {
            $accessCodeDetails = $this->getHMAccessCodeDetails($accessCode);
            $this->set('status', $this->hmStatus($accessCodeDetails));
        } else {
            // HUB-157 Modified by Carl Lewi Godoy, 08/30/2018
            $accessCodeDetails = array_pop(
                Permission::where(['proof' => $accessCode])
            );

            $this->set('status', $this->goStatus($accessCodeDetails));
            $this->set('previousReleaseDates', $this->previousReleaseDates($accessCode));
            $this->set('codeErrors', $this->codeErrors($accessCode));

            // GCAP-844 Added by machua 20200512 to add errors for CLS users
            if (isset($_SESSION['CLSAlerts'])) {
                $this->set('CLSAlerts', $_SESSION['CLSAlerts']);
                unset($_SESSION['CLSAlerts']);
            } else {
                $this->set('CLSAlerts', null);
            }
        }

        $this->set('accessCodeDetails', $accessCodeDetails);
        $this->set('codeFlag', $this->messageFlag);
    }

    public function previousReleaseDates($accessCode)
    {
        $searchAccessCode = new GoDashboardCodeCheck();
        return $searchAccessCode->previousReleaseDates($accessCode);
    }

    public function codeErrors($accessCode)
    {
        $codeErrors = new GoDashboardCodeCheck();
        return $codeErrors->codeErrors($accessCode);
    }

    // HUB-08 Modified by John Renzo S. Sunico, 04/27/2018
    public function goStatus($accessCode)
    {
        $message = "This code can be activated. <br/>";
        $message .= "This code has been previously activated, but it can be activated more than once per year.";
        $releaseYear = '';

        if (empty($accessCode)) {
            $this->messageFlag = false;
            return "Access code not found.";
        }

        // HUB-157 Modified by Carl Lewi Godoy, 08/30/2018
        $lastActivation = $accessCode->getLastActivation();
        $isUsable = $accessCode->IsUsable ? 'Y' : 'N';
        $usageCount = count($accessCode->activations);
        $usageMax = $accessCode->limit;
        $isActive = $accessCode->is_active ? 'Y' : 'N';
        $isSubscriptionActive = $accessCode->entitlement->Active;
        $userID = $lastActivation->user_id;
        $userSubscriptionID = $lastActivation->id;

        if ($isSubscriptionActive == 'N') {
            $this->messageFlag = false;
            return "This code cannot be activated, because the subscription is not active.
                    The content may not yet be ready - please contact production.";
        }

        if ($isActive == 'N') {
            $this->messageFlag = false;
            $message = "This code cannot be activated, because a staff member has manually turned off access.
                        <br/>Please see production staff about this code.";
        }

        if (!$userID && $isUsable == 'Y' && $usageCount == 0 && !$userSubscriptionID) {
            $message = "This code has never been activated.";
        }

        if ($usageCount == $usageMax) {
            $this->messageFlag = false;
            $message = "This code has been activated the maximum amount of times ($usageCount)
                        and can no longer be used.";
        }

        if ($userID && $usageCount < $usageMax && $isUsable == 'N') {
            $message = "This code has been activated and cannot be used.<br/>
                        This code will be released to the secondhand market on September 1st $releaseYear";
        }

        return $message;
    }

    // HUB-8 Modified by John Renzo S. Sunico, 05/04/2018
    public function toggleCode($codeID, $code)
    {
        // HUB-157 Modified by Carl Lewi Godoy, 08/30/2018
        $permission = array_pop(
            Permission::where([
                'proof' => $code
            ])
        );
        $permission->is_active = !$permission->is_active;
        $permission->save();

        $this->redirect('/dashboard/code_check/' . $code);
    }

    public function search()
    {
        $codeModel = new GoDashboardCodeCheck();
        $result = $codeModel->search($_POST['term']);
        echo json_encode($result);
        exit;
    }

    public function codeAction()
    {
        $data = $_POST;
        $accessCodeId = $data['code_id'];
        $accessCode = $data['code'];

        // HUB-157 Modified by Carl Lewi Godoy, 08/30/2018
        if ($data['action'] === 'redeem') {
            // HUB-56 Modified by John Renzo S. Sunico, 06/21/2018
            $activationLib = new HubActivation([
                'accessCode' => $accessCode,
                'terms' => 'true'
            ]);

            $userID = $data['id'];
            $gigyaAccount = new GigyaAccount($userID);
            $accountInfo = $gigyaAccount->getAccountInfo();
            $platforms = $accountInfo->getOriginPlatform();
            
            if (strpos($platforms, "ANZ") !== false) {
                $userID = $accountInfo->getSystemID();
            }

            $activationLib->setActivationOwner($userID);

            // GCAP-844 Modified by machua 20200511 get series, titles and tab IDs
            $permission = Permission::where([
                'proof' => $accessCode
            ]);
            $permission = array_pop($permission);
            $activationLib->setPermission($permission);

            if (strpos($platforms, "Go") !== false || strpos($platforms, "go") !== false) {
                $entitlement = $permission->entitlement;
                $product = $entitlement->product()->fetch();

                $codeCheck = new GoDashboardCodeCheck();
                $titleArray = $codeCheck->getTitleIds($product->Tabs);
                $privileges = $this->formatPrivileges($titleArray);

                //GCAP-874 Modified by machua 20200514 to continue adding standalone to ANZ users
                if ($privileges !== null) {
                    $activationLib->setFormattedPrivileges($privileges);
                } else {
                    if (strpos($platforms, "ANZ") === false) {
                        $_SESSION['CLSAlerts'] = array('error' => 'Subscription is not compatible to CLS users.');
                        exit;
                    }
                }
            }

            $activationLib->activateInGo = false;
            $activationLib->activateProduct();
        } else {
            $permission = Permission::find($accessCodeId);
            $permissionRepository = new PermissionRepository($permission);
            $permissionRepository->releasePermission();
            $permissionRepository->addToReleaseArchive();

            $this->redirect('/dashboard/code_check/' . $accessCode);
        }
        exit;
    }

    /**
     * ANZGO-3500 Added by Shane Camus 10/02/2017
     * @param $accessCode
     * @return mixed
     */
    public function getHMAccessCodeDetails($accessCode)
    {
        $hmAPI = new HotMathsApi(
            array(
                'userId' => 0,
                'accessCode' => $accessCode,
                'response' => 'STRING'
            )
        );

        return $hmAPI->validateAccessCode();
    }

    /**
     * ANZGO-3554 Added by Shane Camus 10/25/17
     * @return mixed
     */
    public function getAccessCodeViaPartialCode()
    {
        $accessCode = $_GET['term'];
        header('Content-type: application/json');
        $hmAPI = new HotMathsApi(
            array(
                'userId' => 0,
                'accessCode' => $accessCode,
                'response' => 'STRING'
            )
        );

        echo json_encode($hmAPI->findAccessCodes());
        exit;
    }

    /**
     * ANZGO-3500 Added by Shane Camus 10/02/2017
     * @param $accessCodeDetails
     * @return string
     */
    public function hmStatus($accessCodeDetails)
    {
        $isFound = !isset($accessCodeDetails->success);
        $message = "This code can be activated.";

        if (!$isFound) {
            $this->messageFlag = false;
            return "Access code not found.";
        }

        $isCancelled = $accessCodeDetails->cancelled;

        if ($isCancelled) {
            $this->messageFlag = false;
            return "This code cannot be activated, because a staff member has manually turned off access.
                    <br/>Please see production staff about this code.";
        }

        $usageMax = $accessCodeDetails->maxActivations + $accessCodeDetails->maxReactivations;
        $usageRemaining = $accessCodeDetails->activationsRemaining + $accessCodeDetails->reactivationsRemaining;
        $usageCount = $usageMax - $usageRemaining;
        $canBeActivated = true;

        if ($usageCount != 0) {
            $index = $usageCount - 1;
            $endDate = $accessCodeDetails->activationCodeUses[$index]->endDate . ' 24:00:00';
            $today = date('Y-m-d H:i:s');
            $canBeActivated = $endDate < $today;
        }

        if ($usageCount == 0) {
            $message = "This code has never been activated.";
        }

        if ($usageCount == $usageMax) {
            $this->messageFlag = false;
            $message = "This code has been activated the maximum amount of times ($usageCount) ";
            $message .= "and can no longer be used.";
        }

        if (!$canBeActivated) {
            $message = "This code has been activated and cannot be used.";
        }

        return $message;
    }

    //GCAP-844 Added by machua 20200430 search from Gigya instead of C5
    public function searchUserByEmail()
    {
        $email = $_GET['term'];

        header('Content-type: application/json');
        $gigyaAcct = new GigyaAccount();
        $result = $gigyaAcct->getProfileByEmail($email);
        $data = ['value' => 'No results found...'];

        $profile = json_decode($result['profile']);
        if ($result) {
            $fullName = $profile->firstName . ' ' . $profile->lastName;
            $data = [
                'label' => $result['UID'],
                'value' => $profile->email,
                'name' => $fullName,
                'id' => $result['UID']
            ];
        }

        $formatted = $data;

        echo json_encode($formatted);
        exit;
    }

    // GCAP-844 Added by machua 20200511 format privileges for metadata
    public function formatPrivileges($titleArray)
    {
        $codeCheck = new GoDashboardCodeCheck();
        $privileges = array();
        $groupedArray = array();
        foreach ($titleArray as $key => $item) {
            $seriesId = $codeCheck->getSeriesId($item['TitleID']);
            if ($seriesId === null) {
                return null;
            }
            $groupedArray[$seriesId][$item['TitleID']][] = (int) $item['tabId'];
        }

        foreach ($groupedArray as $seriesID => $titles) {
            $privilege = array();
            $privilege['series']['id'] = (int) $seriesID;
            foreach ($titles as $titleID => $tabs) {
                $title = array();
                $title['id'] = (int) $titleID;
                $title['tabs'] = $tabs;
                $privilege['titles'][] = $title;
            }
            $privileges[] = $privilege;
        }

        return $privileges;
    }

    
    //GCAP-844 Added by machua 20200430 get user data from Gigya
    public function getUserProfile($userID)
    {
        $gigyaAccount = new GigyaAccount($userID);
        $account = $gigyaAccount->getAccountInfo();
        $accountProfile = $account->getProfile();

        $userProfile = array();

        if ($accountProfile !== null) {
            $userProfile['firstName'] = $accountProfile->GetString('firstName');
            $userProfile['lastName'] = $accountProfile->GetString('lastName');
            $userProfile['fullName'] = $userProfile['firstName'] . ' ' . $userProfile['lastName'];
            $userProfile['email'] = $accountProfile->GetString('email');  
        } else {
            $account = $gigyaAccount->searchUserByGOUID($userID);
            if ($account !== null) {
                $accountProfile = $account->profile;
                $userProfile['firstName'] = $accountProfile->firstName;
                $userProfile['lastName'] = $accountProfile->lastName;
                $userProfile['fullName'] = $userProfile['firstName'] . ' ' . $userProfile['lastName'];
                $userProfile['email'] = $accountProfile->email;
            }
        }

        return $userProfile;
    }

    public function getC5UserProfile($userID)
    {
        $ui = UserInfo::getByID($userID);

        $userProfile = array();

        if ($ui !== null) {
            $userProfile['firstName'] = $ui->getAttriute('uFirstName');
            $userProfile['lastName'] = $ui->getAttribute('uLastName');
            $userProfile['fullName'] = $userProfile['firstName'] . ' ' . $userProfile['lastsName'];
            $userProfile['email'] = $ui->uEmail;
        }

        return $userProfile;
    }

}
