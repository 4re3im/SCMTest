<?php 
class UserWishlistsHelper {

	public $pageTypeHandle = 'wish_list';
	public $defaultPageInfo = array('cName'=>'Default Wishlist', 'cHandle'=>'wishlist');
	
	public function getBasePath() {
		return "/wishlists/";
	}
	
	public function getDefaultPath($uID) {
		return $this->getBasePath().$uID.'/wishlist';	
	}
	
	public function getUserLists($u = NULL) {
		if(!isset($u)) {
			$u = new User();		
		}
		
		Loader::model('page_list');
		$pl = new PageList();
		$pl->filterByCollectionTypeHandle($this->pageTypeHandle);
		$pl->filterByUserID($u->getUserID());
		$wishlists = $pl->get();
		if(!count($wishlists)) {
			$wishlist = $this->addFirstList($u->getUserID());
			$wishlists[] = $wishlist;
		}
		return $wishlists;
	}
	
	public function getUpdateAction($page) {
		return View::url($page->getCollectionPath(), 'update');
	}

	public function addFirstList($uID) {
		$ui = UserInfo::getByID($uID);
		if(!$ui instanceof UserInfo || $ui->isError()) {
			return false;
		}
		
		$wishlist = Page::getByPath($this->getDefaultPath($uID));
		if($wishlist instanceof Page && $wishlist->getCollectionID()) {
			// if we know the last wishlist this user was modifying, then we'll direct them there
			if($this->getLastList()) {
				$lastWishlist = Page::getByID($this->getLastList());
				if($lastWishlist->getCollectionUserID() == $ui->getUserID()) {
					$wishlist = $lastWishlist;
				}
			}
			return $wishlist;
		} else {
			$parent = Page::getByPath($this->getBasePath().$uID);
			if(!($parent instanceof Page && $parent->getCollectionID())) {
				$section = Page::getByPath($this->getBasePath());
				$parent = $section->add(CollectionType::getByHandle('list_holder'), array('cName'=>$ui->getUserName(), 'cHandle'=>$uID));
			}
			
			$ct = CollectionType::getByHandle($this->pageTypeHandle);
			$wishlist = $parent->add($ct,array('cName'=>t($this->defaultPageInfo['cName']),'cHandle'=>$this->defaultPageInfo['cHandle']));
			return $wishlist;
		}
	}
	
	/**
	 * saves the cID of the last wishlist accessed in the user's session
	 * @param int $cID
	 * @return void
	 */
	public function saveLastList($cID) {
		$_SESSION['coreCommerceLastAccessed'.$this->pageTypeHandle.'ID'] = $cID;
	}
	
	/**
	 * retrievs the cID of the last wishlist accessed
	 * @return int $cID | false
	 */
	public function getLastList() {
		$cID = $_SESSION['coreCommerceLastAccessed'.$this->pageTypeHandle.'ID'];
		if(isset($cID) && $cID > 0) {
			return $cID;
		} else { 
			return false;
		}
	}
	
} 
?>