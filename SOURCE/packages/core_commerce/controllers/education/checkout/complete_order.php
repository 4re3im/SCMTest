<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::controller('/checkout');
Loader::model('attribute/categories/core_commerce_order','core_commerce');
class CheckoutCompleteOrderController extends CheckoutController {
	
	public $helpers = array('form', 'form/attribute');	
	
	public function on_start() {
		parent::on_start();
		
		$this->set('billing_akHandles', array('billing_first_name', 'billing_last_name', 'billing_address', 'billing_phone'));
		$this->set('shipping_akHandles', array('shipping_first_name', 'shipping_last_name', 'shipping_address', 'shipping_phone'));
		
	}
	
	public function view() {
		$order = CoreCommerceCurrentOrder::get();
		$this->set('o',$order);
		
		$billing_c = Loader::controller('/checkout/billing');
		$billing_c->view();		
	}

	
	
	
	
}