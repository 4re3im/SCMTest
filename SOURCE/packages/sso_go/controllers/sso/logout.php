<?php
/**
 * Handles all Edit Go product editor functions
 *
 * @author paulbalila
 */
Loader::library('authentication/open_id');
class SsoLogoutController extends Controller {

	protected $pkg_handle = 'sso_go';
	private $url;

	public function __construct() {
		parent::__construct();
	}

	public function on_start() {
		$v = View::getInstance();
		$v->setTheme(PageTheme::getByHandle("go_theme"));
	}

	public function view() {
		$html = Loader::helper('html');
		$this->addFooterItem($html->javascript('destroy.js', $this->pkg_handle));
    }

}
