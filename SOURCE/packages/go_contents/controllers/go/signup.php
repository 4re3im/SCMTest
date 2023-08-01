<?php
defined('C5_EXECUTE') or die("Access Denied.");

class GoSignUpController extends Controller
{

    public $helpers = array('form', 'html');
    public $packageHandle = 'go_signup';

    private $postCountry;
    // SB-571 Added by mtanada 20200519
    private $providersList;

    private $pmByEmail;

    public function __construct()
    {
        $this->postCountry = array(
            'United States' => 'uStateUS',
            'Canada' => 'uStateCA',
            'Australia' => 'uStateAU',
            'New Zealand' => 'uStateNZ',
        );

        // SB-571 Added by mtanada 20200519
        $this->providersList = array('saml-Campion-Education');
    }

    public function on_start()
    {
        $html = Loader::helper('html');
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle('go_theme'));
    }

    // SB-288 modified by jbernardez 20190801
    public function view($uType = null)
    {
        if (!isset($uType)) {
            $this->redirect('go/');
        }

        $this->set('uType', $uType);
        $this->set('packageHandle', $packageHandle);
    }

    public function do_register($uType = 'student')
    {
        $this->set('uType', $uType);
        $registerData['success'] = 0;

        $userHelper = Loader::helper('concrete/user');
        $e = Loader::helper('validation/error');
        $ip = Loader::helper('validation/ip');
        $txt = Loader::helper('text');
        $vals = Loader::helper('validation/strings');
        $valc = Loader::helper('concrete/validation');
        $valf = Loader::helper('validation/form');

        $username = $this->post('uEmail');
        $password = $this->post('uPassword');
        $passwordConfirm = $this->post('uPasswordConfirm');

        // clean the username
        $username = trim($username);
        $username = preg_replace('/ +/', ' ', $username);

        if (!$ip->check()) {
            $e->add($ip->getErrorMessage());
        }

        if (!isset($_POST['uEmail'])) {
            $e->add(t('Email address is a required field.'));
        } else {
            if (!$vals->email($_POST['uEmail'])) {
                $e->add(t('Please provide a valid email address.'));
            } else {
                if (!$valc->isUniqueEmail($_POST['uEmail'])) {
                    $e->add(t(
                        'The email address %s is already in use. Please choose another.',
                        $this->post('uEmail')
                    ));
                }
            }
        }

        if (!$valc->isUniqueUsername($username)) {
            $e->add(t(
                'The username %s already exists. Please choose another',
                $username
            ));
        }

        if ($username == USER_SUPER) {
            $e->add(t('Invalid Username'));
        }

        if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
            $e->add(t('A password must be between %s and %s characters',
                USER_PASSWORD_MINIMUM, USER_PASSWORD_MAXIMUM));
        }

        if (strlen($password) >= USER_PASSWORD_MINIMUM && !$valc->password($password)) {
            $e->add(t('A password may not contain ", \', >, <, or any spaces.'));
        }

        if ($password) {
            if ($password != $passwordConfirm) {
                $e->add(t('The two passwords provided do not match.'));
            }
        }

        if ($uType == 'student') {
            $set1 = AttributeSet::getByHandle('uContactDetails');
            $attributes1 = $set1->getAttributeKeys();

            $set2 = AttributeSet::getByHandle('uCheckboxes');
            $attributes2 = $set2->getAttributeKeys();

            $aks = (Object)array_merge($attributes1, $attributes2);

            foreach ($aks as $uak) {
                if ($uak->isAttributeKeyRequiredOnRegister()) {
                    $e1 = $uak->validateAttributeForm();
                    if ($e1 == false) {
                        $e->add(t('The field "%s" is required',
                            $uak->getAttributeKeyDisplayName()));
                    } else {
                        if ($e1 instanceof ValidationErrorHelper) {
                            $e->add($e1);
                        }
                    }
                }
            }
        } else {
            $aks = UserAttributeKey::getRegistrationList();
            foreach ($aks as $uak) {
                if ($uak->isAttributeKeyRequiredOnRegister()) {
                    $e1 = $uak->validateAttributeForm();
                    if ($e1 == false) {
                        $e->add(t('The field "%s" is required',
                            $uak->getAttributeKeyDisplayName()));
                    } else {
                        if ($e1 instanceof ValidationErrorHelper) {
                            $e->add($e1);
                        }
                    }
                }
            }
        }

        if (!$e->has()) {
            // do the registration
            $data = $this->post();
            $data['uName'] = $username;
            $data['uPassword'] = $password;
            $data['uPasswordConfirm'] = $passwordConfirm;
            $data['uGroup'] = $uType;

            $process = UserInfo::register($data);

            if (is_object($process)) {
                foreach ($aks as $uak) {
                    $uak->saveAttributeForm($process);
                    if ($uak->akHandle == 'uFirstName') {
                        $firstName = $process->getAttribute('uFirstName');
                    }

                    if ($uak->akHandle === 'uPMByEmail') {
                        $byEmailID = $uak->akID;
                    }

                    if ($uak->akHandle === 'uPMByRegularPost') {
                        $byPostID = $uak->akID;
                    }

                    if ($uak->akHandle === 'uCustomerCare') {
                        $byCustomerCareID = $uak->akID;
                    }
                }

                $userGroup = ucfirst($uType);
                if (!is_object(Group::getByName($userGroup))) {
                    $g = Group::add($userGroup,
                        'Group for ' . $userGroup . ' users.');
                } else {
                    $g = Group::getByName($userGroup);
                }

                $u = User::getByUserID($process->getUserID());
                $ui = UserInfo::getByID($u->uID);

                if (!$u->inGroup($g)) {
                    $u->enterGroup($g);
                }

                if ($uType === 'student') {
                    // for ticket ANZGO-2083
                    // We deactivate initially set students' isActive property to FALSE as per business rules.
                    $ui->deactivate();

                    // Set marketing properties
                    if (isset($data['akID'][$byEmailID]['value'])) {
                        $ui->setAttribute('uPMByEmail', true);
                    } else {
                        $ui->setAttribute('uPMByEmail', false);
                    }
                } else {
                    // for ticket ANZGO-2140, Dec 1, 2015
                    // For teachers, they are automatically validated regardless if they click the
                    // verification link or not.
                    $ui->markValidated();

                    if (isset($data['akID'][$byEmailID]['value'])) {
                        $ui->setAttribute('uCustomerCare', true);
                    }

                    if (isset($data['akID'][$byEmailID]['value'])) {
                        $ui->setAttribute('uPMByEmail', true);
                    }

                    if (isset($data['akID'][$byPostID]['value'])) {
                        $ui->setAttribute('uPMByRegularPost', true);
                    }
                }

                if (REGISTER_NOTIFICATION) { // do we notify someone if a new user is added?
                    $mh = Loader::helper('mail');
                    if (EMAIL_ADDRESS_REGISTER_NOTIFICATION) {
                        $mh->to(EMAIL_ADDRESS_REGISTER_NOTIFICATION);
                    } else {
                        $adminUser = UserInfo::getByID(USER_SUPER_ID);
                        if (is_object($adminUser)) {
                            $mh->to($adminUser->getUserEmail());
                        }
                    }

                    $mh->addParameter('uID', $process->getUserID());
                    $mh->addParameter('user', $process);
                    $mh->addParameter('uName', $process->getUserName());
                    $mh->addParameter('uEmail', $process->getUserEmail());
                    $attribs = UserAttributeKey::getRegistrationList();
                    $attribValues = array();
                    foreach ($attribs as $ak) {
                        $attribValues[] = $ak->getAttributeKeyDisplayName('text') . ': ' . $process->getAttribute($ak->getAttributeKeyHandle(),
                                'display');
                    }
                    $mh->addParameter('attribs', $attribValues);

                    if (defined('EMAIL_ADDRESS_REGISTER_NOTIFICATION_FROM')) {
                        $mh->from(EMAIL_ADDRESS_REGISTER_NOTIFICATION_FROM,
                            t('Website Registration Notification'));
                    } else {
                        $adminUser = UserInfo::getByID(USER_SUPER_ID);
                        if (is_object($adminUser)) {
                            $mh->from($adminUser->getUserEmail(),
                                t('Website Registration Notification'));
                        }
                    }
                    if (REGISTRATION_TYPE == 'manual_approve') {
                        $mh->load('user_register_approval_required');
                    } else {
                        $mh->load('user_register');
                    }
                    $mh->sendMail();
                }

                // Added Aril 28, 2014
                // Now we add the user data to the Salesforce table

                // now we log the user in
                if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
                    $u = new User($this->post('uEmail'),
                        $this->post('uPassword'));
                } else {
                    $u = new User($this->post('uName'),
                        $this->post('uPassword'));
                }

                // if this is successful, uID is loaded into session for this user
                $rcID = $this->post('rcID');
                $nh = Loader::helper('validation/numbers');
                if (!$nh->integer($rcID)) {
                    $rcID = 0;
                }

                // now we check whether we need to validate this user's email address
                if (defined('USER_VALIDATE_EMAIL') && USER_VALIDATE_EMAIL) {

                    if (USER_VALIDATE_EMAIL > 0) {
                        $uHash = $process->setupValidation();

                        $mh = Loader::helper('gomail',
                            'go_contents'); // Custom helper; extended Mailhelper
                        if (defined('EMAIL_ADDRESS_VALIDATE')) {
                            $mh->from(EMAIL_ADDRESS_VALIDATE,
                                t('Validate Email Address'));
                        } else {
                            // ANZGO-3008
                            // fix email address error
                            $mh->from('admin@cambridge.edu.au');
                        }
                        $mh->addParameter('uEmail', $this->post('uEmail'));
                        $mh->addParameter('uHash', $uHash);
                        $mh->addParameter('uFirstName', $firstName);
                        $mh->to($this->post('uEmail'));
                        $mh->load('validate_user_email', 'go_contents');
                        $mh->sendMail();

                        $redirectMethod = 'register_success_validate';
                        $registerData['msg'] = join('<br><br>',
                            $this->getRegisterSuccessValidateMsgs());

                        $u->logout();
                    }
                } else {
                    if (defined('USER_REGISTRATION_APPROVAL_REQUIRED') && USER_REGISTRATION_APPROVAL_REQUIRED) {
                        $ui = UserInfo::getByID($u->getUserID());
                        $ui->deactivate();
                        $redirectMethod = 'register_pending';
                        $registerData['msg'] = $this->getRegisterPendingMsg();
                        $u->logout();
                    }
                }

                if (!$u->isError()) {
                    if (!$redirectMethod) {
                        $redirectMethod = 'register_success';
                        $registerData['msg'] = $this->getRegisterSuccessMsg();
                    }

                    $registerData['uID'] = intval($u->uID);
                }

                $registerData['success'] = 1;

                if ($_REQUEST['format'] != 'JSON') {
                    $this->redirect('/go/signup', $redirectMethod, $rcID);
                }
            }
        } else {
            $ip->logSignupRequest();
            if ($ip->signupRequestThreshholdReached()) {
                $ip->createIPBan();
            }
            $this->set('error', $e);
            $registerData['errors'] = $e->getList();
            $this->set('errors', $registerData['errors']);
        }

        if ($_REQUEST['format'] == 'JSON') {
            $jsonHelper = Loader::helper('json');
            echo $jsonHelper->encode($registerData);
            die;
        }
    }

    public function register_success_validate($rcID = 0)
    {
        $this->set('rcID', $rcID);
        $this->set('success', 'validate');
        $this->set('successMsg', $this->getRegisterSuccessValidateMsgs());
    }

    public function register_success($rcID = 0)
    {
        $this->set('rcID', $rcID);
        $this->set('success', 'registered');
        $this->set('successMsg', $this->getRegisterSuccessMsg());
    }

    public function register_pending()
    {
        $this->set('rcID', $rcID);
        $this->set('success', 'pending');
        $this->set('successMsg', $this->getRegisterPendingMsg());
    }

    public function getRegisterSuccessMsg()
    {
        return t('Your account has been created, and you are now logged in.');
    }

    public function getRegisterSuccessValidateMsgs()
    {
        $msgs = array();
        $msgs[] = t('A confirmation email message has been sent to your email account.');
        $msgs[] = t('To activate your Cambridge GO account, follow the instructions in the email.');
        $msgs[] = t('NOTE: If you do not receive your confirmation email, please email us at go@cambridge.edu.au.');
        return $msgs;
    }

    public function getRegisterPendingMsg()
    {
        return t('You are registered but a site administrator must review your account, you will not be able to login until your account has been approved.');
    }

    public function checkIfEmailIsUnique()
    {
        $valc = Loader::helper('concrete/validation');

        if (!$valc->isUniqueEmail($this->post('uEmail'))) {
            echo json_encode(array('success' => false));
        } else {
            echo json_encode(array('success' => true));
        }

        exit;
    }

    /**
     * For ticket ANZGO-1955
     */
    public function getStatesProvinces()
    {
        $countryCode = $this->post('country');
        $af = Loader::helper('form/attribute', 'go_contents');
        $set = AttributeSet::getByHandle('uTeacherContactDetails');
        $attributes = $set->getAttributeKeys();

        if (in_array($countryCode, array_keys($this->postCountry))) {
            foreach ($attributes as $ak) {
                if ($ak->getAttributeKeyHandle() == $this->postCountry[$countryCode]) {
                    echo $af->display($ak, false, false, 'composer', '', true);
                    break;
                }
            }
        } else {
            foreach ($attributes as $ak) {
                if ($ak->getAttributeKeyHandle() === 'uState') {
                    echo $af->display($ak, false, false, 'composer', '', true);
                    break;
                }
            }
        }

        exit;
    }

    public function registerGigyaUser()
    {
        Loader::library('gigya/GigyaService');
        Loader::library('gigya/GigyaAccount');

        $uId = $this->post('UID');
        $uIdSignature = $this->post('UIDSignature');
        $uIdTimeStamp = (int)$this->post('signatureTimestamp');
        $profileData = $this->post('data');
        $isSubscribedToCustomerCare = (isset($profileData['subscribe']) && $profileData['subscribe'] === 'true');

        $gigyaService = new GigyaService();
        $isValid = $gigyaService->verifyUser(
            $uId,
            $uIdSignature,
            $uIdTimeStamp
        );

        $response = [];
        if (!$isValid) {
            $response['success'] = false;
            echo json_encode($response);
            exit;
        }

        $user = User::getByGigyaUID($uId);
        $userInfo = UserInfo::getByID($user->getUserID());

        $subscriptions = $this->post('subscriptions');
        if ($subscriptions) {
            $userInfo->setAttribute('uCustomerCare', true);
            $options = new SelectAttributeTypeOptionList();
            foreach ($subscriptions as $subscription) {
                $option = SelectAttributeTypeOption::getByValue($subscription);
                $options->add($option);
            }

            if (iterator_count($options) > 0) {
                $userInfo->setAttribute('uProductsUsing', $options);
            }
        }

        $response['success'] = $user->isRegistered();

        echo json_encode($response);
        exit;
    }

    public function getInstitutions()
    {
        $keyword = $this->post('keyword');

        // SB-610 added by jbernardez 20200619
        // Convert keyword to one word for the cache,
        // keyword does not accept 2 words
        // also pre replace special characters for keywords
        $cacheKeyword = str_replace(' ', '_', $keyword);
        $cacheKeyword = preg_replace('/[^A-Za-z0-9]/', '_', $cacheKeyword);

        // SB-452 added by jbernardez 20200513
        $cacheResult = Cache::get($cacheKeyword, false);

        if ($cacheResult !== false) {
            $result = $cacheResult;
        } else {
            Loader::library('gigya/datastore/GigyaInstitution');
            $gigyaInstitution = new GigyaInstitution();
            $result = $gigyaInstitution->searchByKeyword($keyword);

            Cache::set($cacheKeyword, false, $result);
        }

        echo json_encode($result);
        exit;
    }

    public function getHomeSchoolData()
    {
        Loader::library('gigya/datastore/GigyaInstitution');
        $gigyaInstitution = new GigyaInstitution();
        $result = $gigyaInstitution->getHomeSchoolData();
        echo json_encode($result);
        exit;
    }

    public function getUnvalidatedSchoolData()
    {
        Loader::library('gigya/datastore/GigyaInstitution');
        $gigyaInstitution = new GigyaInstitution();
        $result = $gigyaInstitution->getUnvalidatedSchoolData();
        echo json_encode($result);
        exit;
    }

    /* SB-571 Added by mtanada 20200518
     * List of user's IdP/s
     */
    public function getUserIdpList()
    {
        Loader::library('gigya/GigyaAccount');
        $email = $this->post('email');
        $gigyaAccount = new GigyaAccount();
        $idps = array();

        $response = $gigyaAccount->getUserIdpByEmail($email);
        $results = (array) json_decode($response, true);
        foreach ($results as $result) {
            if (!in_array($result['idType'], $this->providersList)) continue;
            $idps[] = $result['idType'];
        }
        echo json_encode($idps);
        exit;
    }

    // SB-571 Added by mtanada 20200522
    public function campionSsoUrl()
    {
        $email = $this->post('email');
        $params = CAMPION_SSO_URL . '?&email=' . $email;
        echo $params;
        exit;
    }
}
