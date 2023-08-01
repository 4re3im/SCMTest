<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('order/model', 'core_commerce');
class CoreCommerceCurrentOrder extends CoreCommerceOrder {

	static $orderID = 0;

	/**
	 * gets the current order from the session
	 * @return CoreCommerceCurrentOrder
	 */
	public static function get() {
		if (self::$orderID == 0) {
			self::$orderID = CoreCommerceCurrentOrder::getFromSession();
		}
		$order = new CoreCommerceCurrentOrder();
		$order->load(self::$orderID);

		// reset the cart and set the previous order
		if ($order->getStatus() != CoreCommerceOrder::STATUS_NEW && $order->getStatus() != CoreCommerceOrder::STATUS_INCOMPLETE) { 
			Loader::model('order/previous','core_commerce');
			CoreCommercePreviousOrder::set($order->getOrderID());
			CoreCommerceCurrentOrder::clear();
			$order = CoreCommerceCurrentOrder::get();
		}

		// check for logged in user
		$u = new User();
		if ($u->isRegistered() && $u->getUserID() != $order->getOrderUserID()) {
			$order->setOrderUserID($u->getUserID());
		}
		
		return $order;
	}
	
	// PHP !@#!@#!@#
	
	private function getFromSession() {
		$enc = Loader::helper('encryption');
		$u = new User();
		
		$orderID = NULL;
		do {
			// from the current session
			if(isset($_SESSION['coreCommerceCurrentOrderID']) && $_SESSION['coreCommerceCurrentOrderID'] > 0) {
				$orderID = $_SESSION['coreCommerceCurrentOrderID'];
				self::setOrderCookie($orderID);
				//echo "<!-- OID:{$orderID} FROM SESSION -->";
				break;
			}
			
			// from an existing cookie
			if(isset($_COOKIE['coreCommerceCurrentOrderID']) && strlen($_COOKIE['coreCommerceCurrentOrderID'])) {
				$cookie = $enc->decrypt(urldecode($_COOKIE['coreCommerceCurrentOrderID']));
				if(is_numeric($cookie) && $cookie > 0) {
					$orderID = $cookie;
					$_SESSION['coreCommerceCurrentOrderID'] = $orderID;
					//echo "<!-- OID:{$orderID} FROM COOKIE -->";
					break;
				}
			}
			
			// the logged in user's last incomplete order
			if($u->getUserID()) {
				//@todo
			}
			
		} while(false);
		
		if(!isset($orderID) || !is_numeric($orderID) || $orderID <= 0) {
			// create a new order record
			$order = CoreCommerceOrder::add();
			$orderID = $order->getOrderID();
			$_SESSION['coreCommerceCurrentOrderID'] = $orderID;
			//echo "<!-- OID:{$orderID} NEW ID -->";
		}
		return $orderID;
	}
	
	
	protected static function setOrderCookie($orderID) {
		if($_COOKIE['coreCommerceCurrentOrderID'] != Loader::helper('encryption')->encrypt($orderID) && !headers_sent()) {
			setcookie('coreCommerceCurrentOrderID',urlencode(Loader::helper('encryption')->encrypt($orderID)),time()+86400*30,DIR_REL.'/');
		}
	}
	
	
	protected static function clearOrderCookie() {
		unset($_COOKIE['coreCommerceCurrentOrderID']);
		if(!headers_sent()) {
			setcookie('coreCommerceCurrentOrderID', '', -500, DIR_REL.'/');	
		}
	}
	
	
	public function getCart() {
		$cart = CoreCommerceCart::get();
		return $cart;
	}
	
	public function clear() {
		self::$orderID = 0;
		unset($_SESSION['coreCommerceCurrentOrderID']);
		self::clearOrderCookie();
	}
	
	public function getAvailablePaymentMethods($language = "") {
		Loader::model("payment/method", "core_commerce");
		$methods = CoreCommercePaymentMethod::getEnabledList($language);
		$m2 = Events::fire('core_commerce_on_get_payment_methods', $this, $methods);
		if ($m2 != false) {
			return $m2;
		}
		return $methods;
	}
	
	/** 
	 * Tests to see whether the current order requires login to complete
	 * This is true if the order has groups assigned to it, or whether it has as 
	 * "Must login to purchase" checkbox assigned to it, or the user has turned on the 
	 * global "must login to purchase" option in the dashboard
	 */
	public function requiresLogin() {
		 // first we check global config option
		$pkg = Package::getbyHandle('core_commerce');
		if ($pkg->config('CHECKOUT_FORCE_LOGIN')) {
			return true;
		}

		$products = $this->getProducts();
		foreach($products as $prod) {
			if ($prod->productRequiresLoginToPurchase()) {
				return true;
			}
		}
		
		$groups = array();
		foreach ($products as $product) {
			if (count($product->getProductPurchaseGroupIDArray())) {
				return true;
			}
		}
		
		return false;
	}

	public function getAvailableShippingMethods() {
		// responsible for passing products to all enabled shipping methods
		$methods = array();
		Loader::model("shipping/type", "core_commerce");
		Loader::model("shipping/method", "core_commerce");
		$types = CoreCommerceShippingType::getEnabledList();
		foreach($types as $t) {
			if ($t->canShipToShippingAddress($this)) {
				$m = $t->getController()->getAvailableShippingMethods($this);
				if ($m != false) {
					if (is_array($m)) {
						foreach($m as $_m) {
							$methods[] = $_m;	
						}
					} else {
						$methods[] = $m;
					}			
				}
			}
		}
		
		$m2 = Events::fire('core_commerce_on_get_shipping_methods', $this, $methods);
		if ($m2 != false) {
			return $m2;
		}
		return $methods;
	}

}
