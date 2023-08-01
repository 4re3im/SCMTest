<?php  

class DashboardCoreCommerceSettingsCurrencyController extends Controller {

	public function on_start() {
		$this->set("concrete_interface", Loader::Helper('concrete/interface'));
	}
	
	public function save_currency() {
		if ($this->isPost()) {
			$pkg = Package::getByHandle('core_commerce');
			
			if(!$this->post('USE_ZEND_CURRENCY')) {
				$pkg->saveConfig('CURRENCY_SYMBOL', $this->post('CURRENCY_SYMBOL'));
				$pkg->saveConfig('CURRENCY_SYMBOL_LEFT_PLACEMENT', $this->post('CURRENCY_SYMBOL_LEFT_PLACEMENT')?'1':'0');
				$pkg->saveConfig('CURRENCY_THOUSANDS_SEPARATOR', $this->post('CURRENCY_THOUSANDS_SEPARATOR'));
				$pkg->saveConfig('CURRENCY_DECIMAL_POINT', $this->post('CURRENCY_DECIMAL_POINT'));
			}
			$pkg->saveConfig('USE_ZEND_CURRENCY', $this->post('USE_ZEND_CURRENCY')?'1':'0');
			
		}
        
        $this->set('message', t('Currency Settings Updated.'));
	}



	
}
