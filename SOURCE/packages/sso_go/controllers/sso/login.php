<?php
/**
 * Handles all Edit Go product editor functions
 *
 * @author paulbalila
 */
Loader::library('authentication/open_id');
class SsoLoginController extends Controller {

	protected $pkg_handle = 'sso_go';
	private $url;

	public function __construct() {
		parent::__construct();
	}

	public function on_start() {
		$v = View::getInstance();
		$v->setTheme(PageTheme::getByHandle("sso_go_theme"));
		$this->error = Loader::helper('validation/error');
	}
    
	public function on_before_render() {
		if ($this->error->has()) {
			$this->set('error', $this->error);
		}
	}

	public function view() {
		/**
		 * Added by Paul Balila, 2017-04-19, ANZGO-3310
		 * Added to append the url in the login form.
		 */
		$this->set('url',$this->get('u'));
    }

    public function do_login() {
		/*
		* Added by Paul Balila, 2017-04-21, ANZGO-3310
		* Placed to set this here just in case the user
		* login has an error.
		*/
		$this->set('url',$this->post('redirectURL'));
		$ip = Loader::helper('validation/ip');
		$vs = Loader::helper('validation/strings');

		$loginData['success']=0;

		try {
			if(!$_COOKIE[SESSION]) {
				throw new Exception(t('Your browser\'s cookie functionality is turned off. Please turn it on.'));
			}

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

			$u = new User($this->post('uName'), $this->post('uPassword'));

			if ($u->isError()) {

				switch($u->getError()) {
					case USER_NON_VALIDATED:
						throw new Exception(t('This account has not yet been validated. Please check the email associated with this account and follow the link it contains.'));
						break;
					case USER_INVALID:
						if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
							throw new Exception(t('Invalid email address or password.'));
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
						// the account we logged in with is the same as the existing user from the open id. that means
						// we link the account to open id and keep the user logged in.
						$oa->linkUser($_SESSION['uOpenIDRequested'], $u);
					} else {
						// The user HAS logged in. But the account they logged into is NOT the same as the one
						// that links to their OpenID. So we log them out and tell them so.
						$u->logout();
						throw new Exception(t('This account does not match the email address provided.'));
					}
				}

				$loginData['success']=1;
				$loginData['msg']=t('Login Successful');
				$loginData['uID'] = intval($u->getUserID());


			}

			// Added by Paul Balila, 2017-04-18, ANZGO-3310
			// Check if we have a saved redirectUrl from the saved session.
			$loginData['redirectURL'] = $this->post('redirectURL');
			$this->finishLogin($loginData);

		} catch(Exception $e) {
			$ip->logSignupRequest();
			if ($ip->signupRequestThreshholdReached()) {
				$ip->createIPBan();
			}
			$this->error->add($e);
			$loginData['error']=$e->getMessage();
		}

		if ($_REQUEST['format']=='JSON') {
			$jsonHelper=Loader::helper('json');
			echo $jsonHelper->encode($loginData);
			die;
		}
	}

	protected function finishLogin( $loginData=array() ) {

		$u = new User();

		// We are maintaining user login as per business rules.
		// Confirmed with the team on 2017-04-18.
		// if ($this->post('uMaintainLogin')) {
			$u->setUserForeverCookie();
		// }

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
		foreach($aks as $uak) {
			if ($uak->isAttributeKeyRequiredOnRegister()) {
				$av = $ui->getAttributeValueObject($uak);
				if (!is_object($av)) {
					$unfilledAttributes[] = $uak;
				}
			}
		}

		if ($this->post('completePartialProfile')) {
			foreach($unfilledAttributes as $uak) {
				$e1 = $uak->validateAttributeForm();
				if ($e1 == false) {
					$this->error->add(t('The field "%s" is required', $uak->getAttributeKeyDisplayName()));
				} elseif ($e1 instanceof ValidationErrorHelper) {
					$this->error->add($e1);
				}
			}

			if (!$this->error->has()) {
				// the user has needed to complete a partial profile, and they have done so,
				// and they have no errors. So we save our profile data against the account.
				foreach($unfilledAttributes as $uak) {
					$uak->saveAttributeForm($ui);
					$unfilledAttributes = array();
				}
			}
		}

		if (count($unfilledAttributes) > 0) {
			$u->logout();
			$this->set('invalidRegistrationFields', true);
			$this->set('unfilledAttributes', $unfilledAttributes);
		}
		$txt = Loader::helper('text');
		$rcID = $this->post('rcID');
		$nh = Loader::helper('validation/numbers');

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

		/*
		//full page login redirect (non-ajax login)
		if( strlen($loginData['redirectURL']) && $_REQUEST['format']!='JSON' ) {
			header('Location: ' . $loginData['redirectURL']);
			exit;
		}
		*/

		$dash = Page::getByPath("/dashboard", "RECENT");
		$dbp = new Permissions($dash);

		Events::fire('on_user_login',$this);

		//End JSON Login
		if ($_REQUEST['format']=='JSON')
			return $loginData;

		//should administrator be redirected to dashboard?  defaults to yes if not set.
		$adminToDash=intval(Config::get('LOGIN_ADMIN_TO_DASHBOARD'));

		//Full page login, standard redirection
		$u = new User(); // added for the required registration attribute change above. We recalc the user and make sure they're still logged in
		if ($u->isRegistered()) {

			// Added by Paul Balila, 2017-04-18, ANZGO-3310
			// Save session details and user details in database.
			Loader::model('sso', $this->pkg_handle);
			$ssoGoModel = new SsoGoModel();
			$ssoGoModel->insert();

			if ($u->config('NEWSFLOW_LAST_VIEWED') == 'FIRSTRUN') {
				$u->saveConfig('NEWSFLOW_LAST_VIEWED', 0);
			}

			if ($loginData['redirectURL']) {
				//make double secretly sure there's no caching going on
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Pragma: no-cache");
				header('Expires: Fri, 30 Oct 1998 14:19:41 GMT'); //in the past
				$this->externalRedirect( $loginData['redirectURL'] );
			} elseif ($dbp->canRead() && $adminToDash) {
				$this->redirect('/dashboard');
			} else {
				//options set in dashboard/users/registration
				$login_redirect_cid=intval(Config::get('LOGIN_REDIRECT_CID'));
				$login_redirect_mode=Config::get('LOGIN_REDIRECT');

				//redirect to user profile
				if ($login_redirect_mode=='PROFILE' && ENABLE_USER_PROFILES) {
					$this->redirect( '/profile/', $u->uID );

				//redirect to custom page
				} elseif ($login_redirect_mode=='CUSTOM' && $login_redirect_cid > 0) {
					$redirectTarget = Page::getByID( $login_redirect_cid );
					if (intval($redirectTarget->cID)>0) $this->redirect( $redirectTarget->getCollectionPath());
					else $this->redirect('/');

				//redirect home
				} else $this->redirect('/');
			}
		}
  	}

	/*
	 * Added by Paul Balila, 2017-04-21, ANZGO-3310
	 * Logout functionality for the SSO.
	 */
	public function logout() {
		$u = new User();
		$u->logout();
		$this->redirect('/go');
	}

}
