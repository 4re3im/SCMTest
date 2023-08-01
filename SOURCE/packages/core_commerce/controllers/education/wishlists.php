<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

class WishlistsController extends Controller {
	
	public function on_start() {
		$u = new User();
		if($u->isLoggedIn()) {
			$wh =  Loader::helper('user_wishlists','core_commerce');
			$wishlist = $wh->addFirstList($u->getUserID());
			$this->redirect($wishlist->getCollectionPath());
		} else {
			$this->render('page_forbidden'); exit;
		}
	}

}