<?php 
class ListHolderPageTypeController extends Controller {

	public function on_start() {
		$currentPage = $this->getCollectionObject();
		Loader::model('page_list');
		$pl = new PageList();
		$pl->filterByParentID($currentPage->getCollectionID());
		$res = $pl->get(1);
		
		$child = $res[0];
		if($child instanceof Page) {
			$this->redirect($child->getCollectionPath());
		} else {
			$u = new User();
			if($u->isLoggedIn()) {
				$wh = Loader::helper('user_wishlists','core_commerce');
				$wishlist = $wh->addFirstList($u->getUserID());
				if($wishlist instanceof Page) {
					$this->redirect($wishlist->getCollectionPath());
				}
			} else { die(t('Access Denied')); }
		}		
	}
	
} ?>