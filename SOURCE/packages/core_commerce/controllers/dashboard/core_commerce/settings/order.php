<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

class DashboardCoreCommerceSettingsOrderController extends Controller {
	public $helpers = array('form', 'concrete/interface');
	
function save_order_settings() {
        $pkg = Package::getByHandle('core_commerce');
        $pkg->saveConfig('SECURITY_USE_SSL', $this->post('SECURITY_USE_SSL'));
		Config::save('BASE_URL_SSL', trim($this->post('BASE_URL_SSL'), '/'));
		$pkg->saveConfig('ONE_PAGE_CHECKOUT', ($this->post('ONE_PAGE_CHECKOUT')?1:0));
		$pkg->saveConfig('ORDER_TOTAL_ENABLE_MINIMUM', ($this->post('ORDER_TOTAL_ENABLE_MINIMUM')?1:0));
		$pkg->saveConfig('ORDER_TOTAL_MINIMUM_AMOUNT', $this->post('ORDER_TOTAL_MINIMUM_AMOUNT'));
		$pkg->saveConfig('CHECKOUT_FORCE_LOGIN', ($this->post('CHECKOUT_FORCE_LOGIN')?1:0));
		$this->set('message', t('Order settings updated.'));
		$this->view();
	}
	
	
	public function view() {
		$pkg = Package::getByHandle('core_commerce');
		$this->set('pkg',$pkg);
		$this->set('order_total_enable_minimum',($pkg->config('ORDER_TOTAL_ENABLE_MINIMUM')?1:0));
		$this->set('order_total_minimum_amount', $pkg->config('ORDER_TOTAL_MINIMUM_AMOUNT'));
		$this->set('CHECKOUT_FORCE_LOGIN', $pkg->config('CHECKOUT_FORCE_LOGIN') ? 1 : 0);
		
		$this->set('use_ssl',$pkg->config('SECURITY_USE_SSL'));
		$base_url_ssl = Config::get('BASE_URL_SSL');
		$this->set('base_url_ssl', ($base_url_ssl ? $base_url_ssl : preg_replace('/http:/', 'https:', BASE_URL)));
		
	}
	
}
