<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('order/model', 'core_commerce');
class CoreCommerceWishListOrder extends CoreCommerceOrder {
	/** 
	 * All wishlists require login
	*/
	public function requiresLogin() {
		return true;
	}
	
	/* 
	 * @return CoreCommerceWishListOrder
	*/
	public function add() {
		$order = parent::add();
		$u = new User();
		$order->setOrderUserID($u->getUserID());
		$order->setOrderType(CoreCommerceOrder::TYPE_WISHLIST);
		return CoreCommerceWishlistOrder::getByID($order->getOrderID());
	}

	/**
	 * @param int $id
	 * @return CoreCommerceWishListOrder
	 */
	public static function getByID($id) {
		$db = Loader::db();
		$r = $db->GetOne('select orderID from CoreCommerceOrders where oType = ? AND orderID = ?', array(CoreCommerceOrder::TYPE_WISHLIST, $id));
		if ($r == $id) {
			$pr = new CoreCommerceWishListOrder();
			$pr->load($id);
			return $pr;
		} else {
			return false;
		}
	}
}
