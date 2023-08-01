<?php 
Loader::helper('user_wishlists','core_commerce');
class UserRegistriesHelper extends UserWishlistsHelper {

	public $pageTypeHandle = 'registry';
	public $defaultPageInfo = array('cName'=>'Default Gift Registry', 'cHandle'=>'registry');
	
	public function getBasePath() {
		return "/gift_registries/";
	}
	
	public function getDefaultPath($uID) {
		return $this->getBasePath().$uID.'/registry';	
	}

}