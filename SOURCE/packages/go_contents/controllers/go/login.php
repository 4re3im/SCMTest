<?php

/**
 * @author Ariel Tabag <atabag@cambridge.org> 2015
 */
defined('C5_EXECUTE') or die("Access Denied.");

Loader::library('authentication/open_id');
Loader::model('gousermodel', 'go_contents');
Loader::model('cup_go_php_session', 'go_contents');
// ANZGO-3630 added by Maryjes Tanada 02/12/2018
Loader::library('HotMaths/api');
// GCAP-Campion added by mtanada 20190924
Loader::library('gigya/GigyaAccount');
class GologinController extends Controller
{
    public $helpers = array('form');
    private $openIDReturnTo;
    protected $locales = array();
    protected $supportsPageCache = true;
    private $userModel;

    // ANZGO-3378
    // modified by: James Bernardez
    // date: 2017-05-15
    private $url = "";

    public function on_start()
    {
        $v = View::getInstance();

        $this->userTypeMessage($_SESSION['hmID'], $_SESSION['accesscode']);

        // ANZGO-3913 modified by jbernardez 20181105
        // updating to using just go_theme as login page is not a pop-up anymore
        $v->setTheme(PageTheme::getByHandle('go_theme'));

        // ANZGO-3889 modified by mtanada 20181025
        $pageURL = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $pageURL = substr($pageURL, -7);
        $this->set('pageURL', $pageURL);
        // SB-576 added by mabrigos 20200529
        $_SESSION['fromLogin'] = true;
        $this->error = Loader::helper('validation/error');
        $this->userModel = new GoUserModel();
    }

    /**
     * ANZGO-3630 Added by Maryjes Tanada 02/12/2018
     * Guide for users: Access code is for Student / Teacher
     */
    public function userTypeMessage($hmID, $accessCode)
    {
        $hmApiParams = array('hmID' => $hmID, 'accessCode' => $accessCode);
        $hmApi = new HotMathsApi($hmApiParams);

        // GCAP-422 modified by mtanada 20190524 (bug fix)
        if (isset($hmID) && $hmID > 0) {
            $result = $hmApi->isHmProduct();
            if ($result) {
                $hmProduct = $hmApi->getHmProduct();
                if ($hmProduct->subscriberType === 'TEACHER') {
                    $teacherMessage = 'This is a teacher product. <br/>';
                    $teacherMessage .= 'You need to activate it on a Cambridge GO teacher account.';
                    $this->set('teacherMessage', $teacherMessage);
                }
            }
        }
    }

    // automagically run by the controller once we're done with the current method
    // method is passed to this method, the method that we were just finished running
    public function on_before_render()
    {
        if ($this->error->has()) {
            $this->set('error', $this->error);
        }
    }

    public function complete_openid_email()
    {
        $email = $this->post('uEmail');
        $vals = Loader::helper('validation/strings');
        $valc = Loader::helper('concrete/validation');
        if (!$vals->email($email)) {
            $this->error->add(t('Invalid email address provided.'));
        } elseif (!$valc->isUniqueEmail($email)) {
            $this->error->add(t('The email address %s is already in use. Please choose another.',
                $_POST['uEmail']));
        }

        if (!$this->error->has()) {
            // complete the openid record with the provided email
            if (isset($_SESSION['uOpenIDRequested'])) {
                $oa = new OpenIDAuth();
                $ui = $oa->registerUser($_SESSION['uOpenIDRequested'], $email);
                User::loginByUserID($ui->getUserID());
                $oa->reinstatePreviousRequest();
                $this->finishLogin();
            }
        }
    }

    public function view($requireLogin = false)
    {
        if (isset($_GET['errorCode']) && strlen($_GET['errorCode'])) {
            if ($_GET['errorCode'] == 403002) {
                $_SESSION['expiredVerification'] = true;
                $this->redirect('/go/resend_verification');
            }
        }
        // ANZGO-3456 added by james bernardez 20170802
        // added redirect if user is already set
        global $u;
        if ((isset($u)) && ($u->getUserID() > 0)) {
            // GCAP-Campion added by mtanada 20190924
            if ($this->get('loginProvider')) {
                // User done in progressive profiling
                $u->logout();
            } elseif ($this->get('regToken')) {
                // User have not accepted terms / not done progressive profiling
                $u->logout();
                $gigyaAccount = new GigyaAccount();
                $gigyaAccount->gigyaLogoutBySystemID($u->getUserID());
            } else {
                $this->redirect('/go/myresources/');
            }
        }

        if ($requireLogin) {
            $this->set('requireLogin',
                'You must log in to view your resources.');
        }

        if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
            $this->set('uNameLabel', t('Email Address'));
        } else {
            $this->set('uNameLabel', t('Username'));
        }

        $txt = Loader::helper('text');
        // pre-populate the username if supplied, if its an email address with special characters
        // the email needs to be urlencoded first,
        if (isset($_GET['uName']) && strlen($_GET['uName'])) {
            $this->set('uName', trim($txt->email($_GET['uName'])));
        }

        //added by ariel
        $url = $this->get('u');
        if ($url) {
            $this->$url = $url;
            $_SESSION['url'] = $this->$url;
        }

        $languages = array();
        $locales = array();
        if (Config::get('LANGUAGE_CHOOSE_ON_LOGIN')) {
            $locales = Localization::getAvailableInterfaceLanguageDescriptions();
            $locales = array_merge(array('' => t('** Default')), $locales);
        }
        $this->locales = $locales;
        $this->set('locales', $locales);
        $this->openIDReturnTo = BASE_URL . View::url(
                '/go_login',
                'complete_openid'
            );
        $this->clearOpenIDSession();
    }

    private function clearOpenIDSession()
    {
        unset($_SESSION['uOpenIDError']);
        unset($_SESSION['uOpenIDRequested']);
        unset($_SESSION['uOpenIDExistingUser']);
    }

    public function complete_openid()
    {
        $v = Loader::helper('validation/numbers');
        $oa = new OpenIDAuth();
        $oa->setReturnURL($this->openIDReturnTo);
        $oa->complete();
        $response = $oa->getResponse();
        if ($response->code == OpenIDAuth::E_CANCEL) {
            $this->error->add(t('OpenID Verification Cancelled'));
            $this->clearOpenIDSession();
        } elseif ($response->code == OpenIDAuth::E_FAILURE) {
            $this->error->add(t('OpenID Authentication Failed: %s',
                $response->message));
            $this->clearOpenIDSession();
        } else {
            switch ($response->code) {
                case OpenIDAuth::S_USER_CREATED:
                case OpenIDAuth::S_USER_AUTHENTICATED:
                    if ($v->integer($response->message)) {
                        User::loginByUserID($response->message);
                        $this->set('uOpenID', $response->openid);
                        $oa->reinstatePreviousRequest();
                        $this->finishLogin();
                    }
                    break;
                case OpenIDAuth::E_REGISTRATION_EMAIL_INCOMPLETE:
                    // we don't have an email address, but the account is valid
                    // valid display identifier comes back in message
                    $_SESSION['uOpenIDRequested'] = $response->message;
                    $_SESSION['uOpenIDError'] = OpenIDAuth::E_REGISTRATION_EMAIL_INCOMPLETE;
                    break;
                case OpenIDAuth::E_REGISTRATION_EMAIL_EXISTS:
                    // an email address came back with us from the openid server
                    // but that email already exists
                    $_SESSION['uOpenIDRequested'] = $response->openid;
                    $_SESSION['uOpenIDExistingUser'] = $response->user;
                    $_SESSION['uOpenIDError'] = OpenIDAuth::E_REGISTRATION_EMAIL_EXISTS;
                    break;
            }
        }
        $this->set('oa', $oa);
    }

    public function account_deactivated()
    {
        $this->error->add(t('This user is inactive. Please contact us regarding this account.'));
    }

    public function do_login()
    {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle('go_theme'));

        $ip = Loader::helper('validation/ip');
        $vs = Loader::helper('validation/strings');

        $loginData['success'] = 0;
        $errorCount = 0;

        //added by ariel for activation and other pages that needs login
        if ($_SESSION['redirectURL']) {
            $loginData['redirectURL'] = $_SESSION['redirectURL'];
        }

        try {
            if (!$ip->check()) {
                throw new Exception($ip->getErrorMessage());
            }
            if (OpenIDAuth::isEnabled() && $vs->notempty($this->post('uOpenID'))) {
                $oa = new OpenIDAuth();
                $oa->setReturnURL($this->openIDReturnTo);
                $return = $oa->request($this->post('uOpenID'));
                $resp = $oa->getResponse();
                if ($resp->code == OpenIDAuth::E_INVALID_OPENID) {
                    throw new Exception(t('Invalid OpenID.'));
                }
            }

            if ((!$vs->notempty($this->post('uName'))) || (!$vs->notempty($this->post('uPassword')))) {
                if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
                    throw new Exception(t('An email address and password are required.'));
                } else {
                    throw new Exception(t('A username and password are required.'));
                }
            }

            // Test old password from old GO before finishing log in
            if ($this->userModel->compare_pass($this->post('uName'),
                $this->post('uPassword'))) {
                $u = new User($this->post('uName'), $this->post('uPassword'));
                if ($u->isError()) {
                    switch ($u->getError()) {
                        case USER_NON_VALIDATED:
                            throw new Exception(t('This account has not yet been validated.
                            Please check the email associated with this account and follow the link it contains.'));
                            break;
                        case USER_INVALID:
                            if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
                                // ANZGO-3913 modified by jbernardez 20181108
                                throw new Exception(t('Invalid email address or password. Please try again.'));
                            } else {
                                throw new Exception(t('Invalid username or password.'));
                            }
                            break;
                        case USER_INACTIVE:
                            throw new Exception(t('This user is inactive. Please contact us regarding this account.'));
                            break;
                    }
                } else {
                    if (OpenIDAuth::isEnabled() && $_SESSION['uOpenIDExistingUser'] > 0) {
                        $oa = new OpenIDAuth();
                        if ($_SESSION['uOpenIDExistingUser'] == $u->getUserID()) {
                            //the account we logged in with is the same as the existing user from the open id that means
                            // we link the account to open id and keep the user logged in.
                            $oa->linkUser($_SESSION['uOpenIDRequested'], $u);
                        } else {
                            // The user HAS logged in. But the account they logged into is NOT the same as the one
                            // that links to their OpenID. So we log them out and tell them so.
                            $u->logout();
                            throw new Exception(t('This account does not match the email address provided.'));
                        }
                    }

                    $loginData['success'] = 1;
                    $loginData['uMaintainLogin'] = 1;
                    $loginData['msg'] = t('Login Successful');
                    $loginData['uID'] = intval($u->getUserID());
                }

                // ANZGO-3736 added by jbernardez 20180601
                if (isset($_SESSION['errorCount'])) {
                    unset($_SESSION['errorCount']);
                }

                $loginData = $this->finishLogin($loginData);
            } else {
                throw new Exception(t('Invalid credentials.'));
            }
        } catch (Exception $e) {
            $ip->logSignupRequest();

            if ($ip->signupRequestThreshholdReached()) {
                $ip->createIPBan();
            }

            $this->error->add($e);
            $loginData['error'] = $e->getMessage();

            // ANZGO-3736 added by jbernardez 20180601
            if (!isset($_SESSION['errorCount'])) {
                $errorCount++;
                $_SESSION['errorCount'] = $errorCount;
            } else {
                $_SESSION['errorCount']++;
            }

            // ANZGO-3736 added by jbernardez 20180601
            // log to sumoLogic
            Loader::library('Sumo Logic/api');
            $sumoLogic = new SumoLogicApi();

            $data = array(
                'timestamp' => date('r'),
                'info' => 'Login Error',
                'ipAddress' => $_SERVER['REMOTE_ADDR']
            );

            $sumoLogic->log($data);
        }

        if ($_REQUEST['format'] == 'JSON') {
            $jsonHelper = Loader::helper('json');
            echo $jsonHelper->encode($loginData);
        }

    }

    protected function finishLogin($loginData = array())
    {
        $u = new User();
        // ANZGO-3013
        // this is added as $u is not instantiated when being called
        // inside a class after login, this is also why activation of accesscode
        // is called in my resources and not right after login (in this function)
        $userID = $u->getUserID();

        if ($loginData['uMaintainLogin']) {
            // Added by Paul Balila, 2017-05-05, ANZGO-3338
            // Removes duplicate hashes if there is one.
            $this->userModel->removeDuplicateHashes($u->uID);
            $u->setUserForeverCookie();
        }

        if (count($this->locales) > 0) {
            if (Config::get('LANGUAGE_CHOOSE_ON_LOGIN') && $this->post('USER_LOCALE') != '') {
                $u->setUserDefaultLanguage($this->post('USER_LOCALE'));
            }
        }

        // Verify that the user has filled out all
        // required items that are required on register
        // That means users logging in after new user attributes
        // have been created and required will be prompted here to
        // finish their profile
        $this->set('invalidRegistrationFields', false);
        Loader::model('attribute/categories/user');
        $ui = UserInfo::getByID($u->getUserID());
        $aks = UserAttributeKey::getRegistrationList();

        $unfilledAttributes = array();

        // ANZGO-3672 added by jbernardez 20180327
        // add this first to show customer care checkbox
        // check if this is a teacher first
        // added check if is student
        $isTeacher = false;
        foreach ($u->uGroups as $key => $value) {
            if (strtolower($value) == 'teacher') {
                $isTeacher = true;
            }
        }

        $isStudent = false;
        foreach ($u->uGroups as $key => $value) {
            if (strtolower($value) == 'student') {
                $isStudent = true;
            }
        }

        if ($isTeacher) {
            foreach ($aks as $uak) {
                if ($uak->getAttributeKeyHandle() == 'uCustomerCare') {
                    $av = $ui->getAttributeValueObject($uak);
                    $unfilledAttributes[] = $uak;
                }
            }
        }

        $isRequired = false;
        foreach ($aks as $uak) {
            if ($uak->isAttributeKeyRequiredOnRegister()) {
                $av = $ui->getAttributeValueObject($uak);
                if (!is_object($av)) {
                    $unfilledAttributes[] = $uak;
                    $isRequired = true;
                }
            }
        }

        // ANZGO-3672 added by jbernardez 20180328
        // add this as bypass if not teacher or student
        if ((!$isTeacher && !$isStudent) || ($isRequired == false)) {
            $unfilledAttributes = array();
        }

        if ($this->post('completePartialProfile')) {
            foreach ($unfilledAttributes as $uak) {
                // ANZGO-3672 added by jbernardez 20180327
                // add ucustomercare as an exception, always true
                if ($uak->getAttributeKeyHandle() == 'uCustomerCare') {
                    $e1 = true;
                } else {
                    $e1 = $uak->validateAttributeForm();
                }
                if ($e1 == false) {
                    $this->error->add(t('', $uak->getAttributeKeyDisplayName()));
                } elseif ($e1 instanceof ValidationErrorHelper) {
                    $this->error->add($e1);
                }
            }

            if (!$this->error->has()) {
                // the user has needed to complete a partial profile, and they have done so,
                // and they have no errors. So we save our profile data against the account.
                foreach ($unfilledAttributes as $uak) {
                    $uak->saveAttributeForm($ui);
                    $unfilledAttributes = array();
                }
            }
        }

        if (count($unfilledAttributes) > 0) {
            // ANZGO-3672 added by jbernardez 20180326
            // added a blank error just to add that there was an error sort of
            // as the object User will be instantiated on the next refresh, rendering the user
            // we shall set a session to handle this in my resources page
            $this->error->add(t('', $uak->getAttributeKeyDisplayName()));

            // Note: Session will be destroyed after log out, e.g. Access codes, User etc.
            $u->logout();

            // ANZGO-3672 added by jbernardez 20180327
            // force the ccmUserHash to blank as it does not work on the logout method
            setcookie('ccmUserHash', '', 315532800, DIR_REL . '/',
                (defined('SESSION_COOKIE_PARAM_DOMAIN') ? SESSION_COOKIE_PARAM_DOMAIN : ''),
                (defined('SESSION_COOKIE_PARAM_SECURE') ? SESSION_COOKIE_PARAM_SECURE : false),
                (defined('SESSION_COOKIE_PARAM_HTTPONLY') ? SESSION_COOKIE_PARAM_HTTPONLY : false));

            // ANZGO-3672 added by jbernardez 20180327
            // set uName
            $this->set('uName', $this->post('uName'));
            $this->set('invalidRegistrationFields', true);
            $this->set('unfilledAttributes', $unfilledAttributes);
        }

        $txt = Loader::helper('text');
        $rcID = $this->post('rcID');
        $nh = Loader::helper('validation/numbers');

        // ANZGO-3013
        CupGoLogs::trackUser(
            'Login',
            'Login - Success',
            $ui->getUserEmail(),
            $userID
        );

        // set redirect url
        if ($nh->integer($rcID)) {
            $nh = Loader::helper('navigation');
            $rc = Page::getByID($rcID);
            $url = $nh->getLinkToCollection($rc, true);
            $loginData['redirectURL'] = $url;
        } elseif (strlen($rcID)) {
            $rcID = trim($rcID, '/');
            $nc2 = Page::getByPath('/' . $rcID);
            if (is_object($nc2) && !$nc2->isError()) {
                $loginData['redirectURL'] = BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '/' . $rcID;
            }
        }

        $dash = Page::getByPath('/dashboard', 'RECENT');
        $dbp = new Permissions($dash);
        Events::fire('on_user_login', $this);
        //End JSON Login
        if ($_REQUEST['format'] == 'JSON') {
            return $loginData;
        }

        // should administrator be redirected to dashboard? defaults to yes if not set.
        $adminToDash = intval(Config::get('LOGIN_ADMIN_TO_DASHBOARD'));

        // Full page login, standard redirection
        // Added for the required registration attribute change above.
        // We recalc the user and make sure they're still logged in
        $u = new User();

        if ($u->isRegistered()) {

            // update user php session
            // Modified by Paul Balila, 2017-04-17
            // ANZGO-3310
            $CupGoPhpSession = new CupGoPhpSession();
            $CupGoPhpSession->insert();

            // ANZGO-3378 added by James Bernardez 2017-05-15
            // call build, then redirect to url, if there is url redirect
            if (isset($_SESSION['url'])) {
                $url = $_SESSION['url'];
                unset($_SESSION['url']);
                $this->redirect('/sso/build/?' . http_build_query(array('u' => $url)));
            }

            // ANZGO-2851
            // check for accesscode session
            // if there is a accesscode, proceed to activate code
            if ($_SESSION['accesscode'] != '') {
                // force to go to my resources for access code validation/activation
                // this is forced this was as global $u isn't set on login
                // on redirect to my resources refreshed $u and now has user data

                // ANZGO-3378 added by James Bernardez 2017-05-12
                // call SSO page to create all AuthTokens for SSO sites
                // cancel redirect to my resources, redirect to build first, then go to my resources
                $this->redirect('/sso/build/?' . http_build_query(array('u' => '/go/myresources/')));
            }

            if ($u->config('NEWSFLOW_LAST_VIEWED') == 'FIRSTRUN') {
                $u->saveConfig('NEWSFLOW_LAST_VIEWED', 0);
            }

            if ($loginData['redirectURL']) {
                // make double secretly sure there's no caching going on
                header('Cache-Control: no-store, no-cache, must-revalidate');
                header('Pragma: no-cache');
                header('Expires: Fri, 30 Oct 1998 14:19:41 GMT');
                $this->externalRedirect($loginData['redirectURL']);
            } elseif ($dbp->canRead() && $adminToDash) {
                // ANZGO-3378 added by James Bernardez 2017-05-12
                // if admin, jump to build, then dashboard
                $this->redirect('/sso/build/?' . http_build_query(array('u' => '/dashboard')));
            } else {
                // options set in dashboard/users/registration
                $login_redirect_cid = intval(Config::get('LOGIN_REDIRECT_CID'));
                $login_redirect_mode = Config::get('LOGIN_REDIRECT');

                // redirect to user profile
                if ($login_redirect_mode == 'PROFILE' && ENABLE_USER_PROFILES) {
                    $this->redirect('/profile/', $u->uID);

                    //redirect to custom page
                } elseif ($login_redirect_mode == 'CUSTOM' && 0 > 0) {
                    $redirectTarget = Page::getByID($login_redirect_cid);

                    if (intval($redirectTarget->cID) > 0) {

                        $this->redirect($redirectTarget->getCollectionPath());
                    } else {
                        // ANZGO-3378
                        // added by James Bernardez 2017-05-12
                        // if default, jump to build, then myresources
                        //Added by Ariel for HA work
                        //$this->redirect('/sso/build/?' . http_build_query(array("u" => "/go/myresources/")));
                        $this->redirect("/go/myresources/");
                    }

                } else {
                    // ANZGO-3378
                    // added by James Bernardez 2017-05-12
                    // if default, jump to build, then myresources
                    //added by Ariel for HA work
                    //$this->redirect('/sso/build/?' . http_build_query(array("u" => "/go/myresources/")));
                    $this->redirect("/go/myresources/");
                }
            }
        }
    }

    public function password_sent()
    {
        $this->set('intro_msg', $this->getPasswordSentMsg());
    }

    public function getPasswordSentMsg()
    {
        return t('An email containing instructions on resetting your password has been sent to your account address.');
    }

    public function logout()
    {
        // ANZUAT-128
        $_SESSION['hideHelpSession'] = false;

        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle('go_theme'));

        // ANZGO-3013
        CupGoLogs::trackUser('Logout', 'Logout');

        $u = new User();
        $u->logout();
        /*
         * Disables SSO Logout Temporarily
         *
            $logoutUrls = json_encode([
            COOKIE_URL_EPUB,
            COOKIE_URL_ITB,
            COOKIE_URL_TH,
            COOKIE_URL_THS
            ]);
            $logoutUrls = base64_encode($logoutUrls);

            $this->redirect('/go/?ssoLogout=' . $logoutUrls);
         */

        $this->redirect('/go/');
    }

    public function forward($cID = 0)
    {
        $nh = Loader::helper('validation/numbers');
        if ($nh->integer($cID) && intval($cID) > 0) {
            $this->set('rcID', intval($cID));
        }
    }

    // responsible for validating a user's email address
    public function v($hash = '')
    {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle('go_theme'));
        $ui = UserInfo::getByValidationHash($hash);
        if (is_object($ui)) {
            $ui->markValidated();

            // Added for ticket ANZGO-2140, Dec 1, 2015
            // We do this just in case the user is a student.
            // Who is deactivated initially as per business rules
            $ui->activate();

            $this->set('uEmail', $ui->getUserEmail());
            $this->set('validated', true);
        }
    }

    public function change_password($uHash = '')
    {
        $db = Loader::db();
        $h = Loader::helper('validation/identifier');
        $e = Loader::helper('validation/error');
        $ui = UserInfo::getByValidationHash($uHash);
        if (is_object($ui)) {
            $hashCreated = $db->GetOne(
                'SELECT uDateGenerated FROM UserValidationHashes WHERE uHash=?',
                array($uHash)
            );
            if ($hashCreated < (time() - (USER_CHANGE_PASSWORD_URL_LIFETIME))) {
                $h->deleteKey('UserValidationHashes', 'uHash', $uHash);
                throw new Exception(
                    t('Key Expired. Please visit the forgot password page again to have a new key generated.')
                );
            } else {

                if (strlen($_POST['uPassword'])) {

                    $userHelper = Loader::helper('concrete/user');
                    $userHelper->validNewPassword($_POST['uPassword'], $e);

                    if (strlen($_POST['uPassword']) && $_POST['uPasswordConfirm'] != $_POST['uPassword']) {
                        $e->add(t('The two passwords provided do not match.'));
                    }

                    if (!$e->has()) {
                        $ui->changePassword($_POST['uPassword']);
                        $h->deleteKey('UserValidationHashes', 'uHash', $uHash);
                        $this->set('passwordChanged', true);

                        $u = $ui->getUserObject();
                        if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
                            $_POST['uName'] = $ui->getUserEmail();
                        } else {
                            $_POST['uName'] = $u->getUserName();
                        }
                        $this->do_login();

                        CupGoLogs::trackUser(
                            'Login',
                            'Change Password - Success',
                            $u->getUserEmail()
                        );

                        return;
                    } else {
                        // This else is always used (due to return above), no need for else statement.
                        $this->set('uHash', $uHash);
                        $this->set('changePasswordForm', true);
                        $this->set('errorMsg', join('<br>', $e->getList()));
                    }
                } else {
                    $this->set('uHash', $uHash);
                    $this->set('changePasswordForm', true);
                }
            }
        } else {
            throw new Exception(
                t('Invalid Key. Please visit the forgot password page again to have a new key generated.')
            );
        }
    }

    public function forgot_password()
    {
        $loginData['success'] = 0;

        $vs = Loader::helper('validation/strings');
        $em = $this->post('uEmail');
        try {
            if (!$vs->email($em)) {
                throw new Exception(t('Invalid email address.'));
            }

            $oUser = UserInfo::getByEmail($em);
            if (!$oUser) {
                throw new Exception(t('We have no record of that email address.'));
            }

            $mh = Loader::helper('mail');

            if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
                $mh->addParameter('uName', $oUser->getUserEmail());
            } else {
                $mh->addParameter('uName', $oUser->getUserName());
            }
            $mh->to($oUser->getUserEmail());

            //generate hash that'll be used to authenticate user, allowing them to change their password
            $uHash = UserValidationHash::add($oUser->getUserID(),
                UVTYPE_CHANGE_PASSWORD);

            $changePassURL = BASE_URL . View::url('/go_login',
                    'change_password', $uHash);
            $mh->addParameter('changePassURL', $changePassURL);

            if (defined('EMAIL_ADDRESS_FORGOT_PASSWORD')) {
                $mh->from(EMAIL_ADDRESS_FORGOT_PASSWORD, t('Forgot Password'));
            } else {
                $adminUser = UserInfo::getByID(USER_SUPER_ID);
                if (is_object($adminUser)) {
                    $mh->from($adminUser->getUserEmail(), t('Forgot Password'));
                }
            }
            $mh->load('forgot_password');
            @$mh->sendMail();

            $loginData['success'] = 1;
            $loginData['msg'] = $this->getPasswordSentMsg();
        } catch (Exception $e) {
            $this->error->add($e);
            $loginData['error'] = $e->getMessage();
        }

        if ($_REQUEST['format'] == 'JSON') {
            $jsonHelper = Loader::helper('json');
            echo $jsonHelper->encode($loginData);
            die;
        }

        if ($loginData['success'] == 1) {
            $this->redirect('/go_login', 'password_sent');
        }
    }

    // ANZGO-3640 Modified by Shane Camus 02/21/18
    public function login_api($username, $password)
    {
        $u = new User($username, $password);

        // ANZGO-3795 modified by jbernardez 20180717
        if ($u->isActive()
            && ((isset($u->getUserGroups()[3]) && $u->getUserGroups()[3] == 'Administrators')
                || (isset($u->getUserGroups()[14]) && $u->getUserGroups()[14] == 'Production (code creation)')
                || (isset($u->getUserGroups()[20]) && $u->getUserGroups()[20] == 'Old Administrators'))
        ) {
            $result = $u;
        } else {
            $result = array('error' => 'Invalid user name and/or password! Please try again.');
        }

        echo json_encode($result);

        exit;

    }

    /**
     * ANZGO-228 Added by John Renzo S. Sunico 01/10/2019
     */
    public function gigyaLogin()
    {
        // GCAP-531 - gxbalila
        // Plain C5 POST, GET not working?! (:shookt:) so used file_get_contents
        $input = json_decode(file_get_contents('php://input'));
        $uId = $input->UID;
        $uIdSignature = $input->UIDSignature;
        $uIdTimeStamp = (int)$input->signatureTimestamp;

        $user = User::loginByGigyaUID($uId, $uIdSignature, $uIdTimeStamp);

        $response = [];
        if (!$user) {
            $response['success'] = false;

            echo json_encode($response);
            exit;
        }

        $response['success'] = true;

        $response['sso'] = [
            COOKIE_URL_EPUB,
            COOKIE_URL_ITB,
            COOKIE_URL_TH,
            COOKIE_URL_THS
        ];
        $response['userHash'] = User::getForeverCookie($_SESSION['uID']);

        $redirectTo = $input->redirect;
        $response['redirectTo'] = $redirectTo
            ? '/sso/build/?u=' . $redirectTo
            : '/go/myresources/';

        echo json_encode($response);
        exit;
    }
}
