<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

class GiftRegistriesController extends Controller {
	
	public function on_start() {
		$u = new User();
		if($u->isLoggedIn()) {
			$rh =  Loader::helper('user_registries','core_commerce');
			$registry = $rh->addFirstList($u->getUserID());
			$this->redirect($registry->getCollectionPath());
		} else {
			$this->render('page_forbidden'); exit;
		}
	}

	
	/**
	 * @todo possibly display a searchable list of gift registries in the view layer
	 * @return void
	 */
	//public function view() {}
}