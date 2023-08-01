<?php
/**
 * Builds the user details in case there is a session saved but no details saved.
 * Happens when a user has a maintained login.
 *
 * @author paulbalila
 */
class SsoBuildController extends Controller {

    protected $pkg_handle = 'sso_go';

    public function __construct() {
        parent::__construct();
    }

    public function on_start() {
		$v = View::getInstance();
		$v->setTheme(PageTheme::getByHandle("go_theme"));
    }

    public function view() {
		$html = Loader::helper('html');
		$this->addFooterItem($html->javascript('build.js', $this->pkg_handle));

		$url = $this->get('u');

		// Check if a user is logged in.
		$u = new User();
		if(!$u->isLoggedIn()) {
			$this->redirect('/go/login/?' . http_build_query(array("u" => $url)));
			exit;
		}

		// Loader::model('sso',$this->pkg_handle);
		// $ssoGoModel = new SsoGoModel();
		// $ssoGoModel->update();
		$this->set('requestUrl', $url);
    }
    
}
