<?php  

class DashboardCoreCommerceSettingsEmailController extends Controller {

	public function on_start() {
		$this->set("concrete_interface", Loader::Helper('concrete/interface'));
	}

	function save_email() {
		if ($this->isPost()) {
			$pkg = Package::getByHandle('core_commerce');
			
			$pkg->saveConfig('ENABLE_ORDER_NOTIFICATION_EMAILS', $this->post('ENABLE_ORDER_NOTIFICATION_EMAILS')?'1':'0');
			$emails = preg_split("/[\s,]+/", $this->post('ENABLE_ORDER_NOTIFICATION_EMAIL_ADDRESSES'));
			$pkg->saveConfig('ENABLE_ORDER_NOTIFICATION_EMAIL_ADDRESSES', implode(',', $emails));
			$pkg->saveConfig('RECEIPT_EMAIL_BLURB', $this->post('RECEIPT_EMAIL_BLURB'));
	
			$pkg->saveConfig('EMAIL_RECEIPT_EMAIL', $this->post('EMAIL_RECEIPT_EMAIL'));
			$pkg->saveConfig('EMAIL_RECEIPT_NAME', $this->post('EMAIL_RECEIPT_NAME'));
			$pkg->saveConfig('EMAIL_NOTIFICATION_EMAIL', $this->post('EMAIL_NOTIFICATION_EMAIL'));
			$pkg->saveConfig('EMAIL_NOTIFICATION_NAME', $this->post('EMAIL_NOTIFICATION_NAME'));
		}
        
        $this->set('message', t('Email Settings Updated.'));
	}

	
	
}
