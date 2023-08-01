<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

class DashboardCoreCommerceSettingsStoreHomeController extends Controller {
	public $helpers = array('form', 'concrete/interface');

	function save_homepage() {
        $pkg = Package::getByHandle('core_commerce');
        $pkg->saveConfig('STORE_ROOT', $this->post('STORE_ROOT'));
        $this->set("message", t('Store homepage saved.'));
        $this->view();
	}

	public function view() {
		$this->set('pkg',Package::getByHandle('core_commerce'));
	}

}
