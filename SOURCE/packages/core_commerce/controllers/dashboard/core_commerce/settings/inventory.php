<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

class DashboardCoreCommerceSettingsInventoryController extends Controller {
	public $helpers = array('form', 'concrete/interface');
	
	function save_inventory() {
        $pkg = Package::getByHandle('core_commerce');
        $pkg->saveConfig('MANAGE_INVENTORY', $this->post('MANAGE_INVENTORY'));
        $pkg->saveConfig('MANAGE_INVENTORY_TRIGGER', $this->post('MANAGE_INVENTORY_TRIGGER'));
        $pkg->saveConfig('NEGATIVE_QUANTITY', $this->post('NEGATIVE_QUANTITY'));
        $this->set("message", t('Inventory settings saved.'));
        $this->view();
	}
	
	
	public function view() {
		$this->set('pkg',Package::getByHandle('core_commerce'));	
	}
	
}
