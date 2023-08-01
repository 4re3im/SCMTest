<?php 
defined('C5_EXECUTE') or die("Access Denied.");
Loader::controller('/profile/edit');

class ProfileOrderHistoryController extends ProfileEditController {
	
	public $helpers = array('concrete/urls');
	
	
	public function __construct() {
		parent::__construct();
	}
	
	public function on_start() {
		parent::on_start();
		$this->error = Loader::helper('validation/error');
		$this->set('vt', Loader::helper('validation/token'));
		$this->set('text', Loader::helper('text'));
		
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('ccm.core.commerce.profile.css','core_commerce'));
		$this->addHeaderItem($html->javascript('ccm.core.commerce.profile.js','core_commerce'));
	
	}
	
	public function duplicate_order() {
		Loader::model('order/model', 'core_commerce');
		$u = new User();
		$o = CoreCommerceOrder::getByID($this->post('orderID'));
		if ($u->getUserID() == $o->getOrderUserID()) {
		 	Loader::model('cart', 'core_commerce');
		 	CoreCommerceCurrentOrder::clear();
		 	$products = $o->getProducts();
			$ec = CoreCommerceCurrentOrder::get();
			foreach($products as $prod) {
				$pr = $ec->addProduct($prod, $prod->getQuantity());
				$attribs = $pr->getProductConfigurableAttributes();
				foreach($attribs as $at) { 
					$at->setAttribute($pr, $prod->getAttribute($at));
				}
				$pr->calculateAttributePrice();
			}

			// duplicate basic, billing and shipping information
			Loader::model('attribute/categories/core_commerce_order', 'core_commerce');
			$attributes = AttributeSet::getByHandle('core_commerce_order_billing')->getAttributeKeys();
			foreach($attributes as $eak) {
				$eak->setAttribute($ec, $o->getAttribute($eak->getAttributeKeyHandle()));
			}
			$attributes = AttributeSet::getByHandle('core_commerce_order_shipping')->getAttributeKeys();
			foreach($attributes as $eak) {
				$eak->setAttribute($ec, $o->getAttribute($eak));
			}
			$ec->setOrderEmail($o->getOrderEmail());

			$this->redirect('/checkout');
		}
	}
	
	
	public function view() {
		$pkg = Package::getByHandle('core_commerce');
		
		if(!$pkg->config('PROFILE_MY_ORDERS_ENABLED')) {
			header("HTTP/1.0 404 Not Found");
			$this->render("/page_not_found");
		}
		
		
		$u = new User();
		$ui = UserInfo::getByID($u->getUserID());
		$this->set('profile',$ui);
		
		Loader::model('order/list','core_commerce');
		$ol = new CoreCommerceOrderList();
		$ol->filterByCustomerUserID($u->getUserID());
		$ol->sortBy('oDateAdded', 'desc');
		$orders = $ol->get(5);
		$this->set('orders',$orders);
		$this->set('order_list',$ol);
	}
	
	public function orderDetails($orderID) {
			
	}

}
