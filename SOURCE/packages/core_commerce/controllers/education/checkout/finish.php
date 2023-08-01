<?php 
Loader::model('order/current', 'core_commerce');
Loader::model('order/previous', 'core_commerce');

class CheckoutFinishController extends Controller { 
	public $helpers = array('concrete/urls');
	
	public function on_start() {
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('ccm.core.commerce.order-print.css','core_commerce'));
	}

	public function view() {
		$order = CoreCommerceCurrentOrder::get(); // this ensures the previous order is the order we just completed
		$previousOrder = @CoreCommercePreviousOrder::get();
		$this->set('previousOrder',$previousOrder);
	}

}