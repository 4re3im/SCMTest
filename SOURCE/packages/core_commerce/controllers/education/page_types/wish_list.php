<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::controller('/cart');
Loader::model('order/wish_list','core_commerce');
class WishlistPageTypeController extends CartController {

	public $helpers = array('form', 'concrete/urls','navigation');
	
	/* 
	* remove the order record if the wishlist is removed
	*/ 
	public function on_page_delete($c) {
		$orderID = $c->getAttribute('ecommerce_order_id');
		if($orderID) {
			$order = CoreCommerceWishListOrder::getByID($order->getOrderID());
			if($order->getOrderStatus() == CoreCommerceOrder::STATUS_NEW) {
				$order->delete($order->getOrderID());
			}
		}
	}
	

	public function on_start() {
		if(!$this->canView()) {
			$this->render('page_forbidden');
			exit;
		}
		parent::on_start();
		$this->addHeaderItem(Loader::helper('html')->javascript('jquery.ui.js'));
		$this->addHeaderItem(Loader::helper('html')->css('jquery.ui.css'));
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.dialog.js'));
		$this->addHeaderItem(Loader::helper('html')->css('ccm.dialog.css'));
		$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.wishlist.css', 'core_commerce'));
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.core.commerce.wishlist.js', 'core_commerce'));
		
		if($this->canManage()) {
			$c = $this->getCollectionObject();
			if($c->getCollectionTypeHandle() == 'wish_list') {
				Loader::helper('user_wishlists', 'core_commerce')->saveLastList($c->getCollectionID());
			} else {
				Loader::helper('user_registries', 'core_commerce')->saveLastList($c->getCollectionID());
			}
		}
	}
	
	
	/* 
	 * Updates this wishlist, 
	 * optionally moves all the products into the current order and starts checkout.
	 * @see packages/core_commerce/controllers/CartController#update()
	 */
	public function update() {
		if($this->post('purchase_wishlist')) {
			$this->allowRedirect = false;
			if($this->canManage()) {
				parent::update();
				$wishlist = $this->getOrder();
				$cart = CoreCommerceCurrentOrder::get();
				$wishlist->moveAllProductsToOrder($cart->getOrderID());
			} else {
				$wishlist = $this->getOrder();
				$cart = CoreCommerceCurrentOrder::get();
				$wishlist->copyAllProductsToOrder($cart->getOrderID());
			}
			Loader::helper('checkout/step', 'core_commerce');
			header('Location: ' . CoreCommerceCheckoutStep::getBase() . View::url('/checkout'));
			exit;
		} elseif($this->canManage()) {
			// record the page ID so we can pre-select the last wishlist we were working with.
			$_SESSION['coreCommerceLastAccessedWishlistID'] = $this->getCollectionObject()->getCollectionID();
			parent::update();
		}
	}
	
	
	public function getOrder() {
		$u = new User();
		$page = $this->getCollectionObject();
		
		do {
			// check to see if the page is associated with a specific id
			$existingOrder = $page->getAttribute('ecommerce_order_id');	
			if(is_numeric($existingOrder) && $existingOrder > 0) {
				$order = CoreCommerceWishListOrder::getByID($existingOrder);
				if($order && $this->canView()) {
					break;
				}
			} 
			
			/* 
			//wishist pages should always have an orderID associated  - if that's not the case we can revive this code..
			
			// grab the first order of the type wishlist from the current user
			Loader::model('order/list','core_commerce');
			$ol = new CoreCommerceOrderList();
			$ol->filterByCustomerUserID($u->getUserID());
			$ol->filterByOrderType('wishlist');
			$ol->filterByOrderStatus(0);
			$results = $ol->get(1);
			$order = $results[0];
			if($order instanceof CoreCommerceOrder) {
				$order = CoreCommerceWishListOrder::getByID($order->getOrderID());
				break;
			} else {
			*/
			
			// no wishlist order for this user, add one
			if($this->canManage()) {
				$order = CoreCommerceWishListOrder::add();
			}			
		} while(false);
		if($this->canManage() && $existingOrder != $order->getOrderID()) {
			$page->setAttribute('ecommerce_order_id', $order->getOrderID());
		}
		
		return $order;
	}
	
	public function remove() {
		if(!$this->canManage()) { die(t('Access Denied')); }
		$c = $this->getCollectionObject();
		$parentID = $c->getCollectionParentID();
		$parent = Page::getByID($parentID);
		$c->delete();
		$this->redirect($parent->getCollectionPath());
	}
	
	public function rename() {
		if(!$this->canManage()) { die(t('Access Denied')); }
		
		$name = $this->post('name');	
		if(strlen($name)) {
			$c = $this->getCollectionObject();
			$c->update(array('cName'=>$name));
			$this->redirect($c->getCollectionPath());
		}
	}


	public function addNew() {
		if(!$this->canManage()) { die(t('Access Denied')); }
		$name = $this->post('name');
		
		if(strlen($name)) {
			$page = $this->getCollectionObject();
			$parent = Page::getByID($page->getCollectionParentID());
			$handle = $page->getCollectionTypeHandle();
			$ct = CollectionType::getByHandle($handle);
			$wishlist = $parent->add($ct,array('cName'=>$name));
			$this->redirect($wishlist->getCollectionPath());
		}
	}
	
	public function makePublic($public = true) {
		$page = $this->getCollectionObject();
		$result = array('status'=>'error','message'=>t('Access Denied'));
		if($this->canManage()) {
			if($public) {
				$page->setAttribute('ecommerce_list_is_public', 1);
				$result['status'] = 'public';
				$result['message'] = t('This list is currently public');
			} else {
				$page->setAttribute('ecommerce_list_is_public', 0);
				$result['status'] = 'private';
				$result['message'] = t('This list is currently private');
			}
		}
		$json = Loader::helper('json');
		echo $json->encode($result);		
		exit;
	}
	
	/**
	 * Checks to see if the current user can administer this wishlist
	 * returns true if the user owns it or the current user has concrete5 permissions to administer the page
	 * @return boolean
	*/
	public function canManage() {
		$u = new User();
		$page = $this->getCollectionObject();
		if($page->getCollectionUserID() == $u->getUserID()) {
			return true;		
		} else {			
			$cp = new CollectionPermissions($page);
			if($cp->canAdminPage()) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	
	/**
	 * checks to see if the current user can view the wishlist
	 * returns true if the user owns the wishlist, if it's set to public 
	 * or if the user has concrete5 permissions to admin the page
	 * @return boolean
	 */
	public function canView() {
		// if this user owns it, or it's been made public
		// @todo add check for attribute
		$u = new User();
		$page = $this->getCollectionObject();
		if($page->getCollectionUserID() == $u->getUserID()) {
			return true;		
		} elseif($page->getAttribute('ecommerce_list_is_public')) {
			return true;
		} else {
			$cp = new CollectionPermissions($page);
			if($cp->canAdminPage()) { 
				return true;
			} else {
				return false;
			}
		}
	}
}