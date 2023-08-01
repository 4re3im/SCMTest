<?php  

class DashboardCoreCommerceSettingsUserController extends Controller {

	public function on_start() {
		$this->set("concrete_interface", Loader::Helper('concrete/interface'));
	}

	/**
	 * Adds the pages, page types etc.. for the the wishlist functionality
	 * disabling only sets the config value to false to hide the button
	 * @param boolean $enable
	 * @return void
	 */
	protected function enableWishlists($enable = true) {
		Loader::model('single_page');			
		$pkg = Package::getByHandle('core_commerce');
		
		if($enable) {
			// verify attributes are in place
			Loader::model('attribute/categories/collection');
			$ak_list_public = CollectionAttributeKey::getByHandle('ecommerce_list_is_public');
			if(!$ak_list_public) {
				$at = AttributeType::getByHandle('boolean');
				$ak_list_public = CollectionAttributeKey::add($at,array('akHandle'=>'ecommerce_list_is_public','akName'=>t('eCommerce List is Public')));
			}
			
			$ak_ecom_order_id = CollectionAttributeKey::getByHandle('ecommerce_order_id');
			if(!$ak_ecom_order_id) {
				$at = AttributeType::getByHandle('number');
				$ak_ecom_order_id = CollectionAttributeKey::add($at,array('akHandle'=>'ecommerce_order_id','akName'=>t('eCommerce Order ID')));
			}
			
			$pdt = CollectionType::getByHandle('wish_list');
			if( !$pdt || !intval($pdt->getCollectionTypeID()) ){
				$data['ctHandle'] = 'wish_list';
				$data['ctName'] = t('Wishlist');
				$pdt = CollectionType::add($data, $pkg);
				$pdt->assignCollectionAttribute($ak_ecom_order_id);
				$pdt->assignCollectionAttribute($ak_list_public);
			}
			
			
			// add the wishlist single page
			$wishlist = Page::getByPath('/wishlists');
			if ($wishlist->isError() || (!is_object($wishlist))) {
				$wishlist = SinglePage::add('/wishlists', $pkg);
				$wishlist->setAttribute('exclude_nav',1);
				$wishlist->setAttribute('exclude_page_list',1);
				$wishlist->setAttribute('exclude_search_index',1);
				$wishlist->setAttribute('exclude_sitemapxml',1);
			}
			
			$alias = Page::getByPath('/profile/wishlists');
			if ($alias->isError() || (!is_object($alias))) {
				$profile = Page::getByPath('/profile');
				$alias = $wishlist->addCollectionAlias($profile);
				//$aPage = Page::getByID($alias);
				//$aPage->setAttribute('exclude_nav',0);
			}
			
			$pdt = CollectionType::getByHandle('list_holder');
			if( !$pdt || !intval($pdt->getCollectionTypeID()) ){
				$data['ctHandle'] = 'list_holder';
				$data['ctName'] = t('List Holder');
				$pdt = CollectionType::add($data, $pkg);
			}
			
		} else {
			// disable
		}
	}
	
	protected function enableGiftRegistries($enable = true) {
		Loader::model('single_page');			
		$pkg = Package::getByHandle('core_commerce');
		if($enable) {
			// verify attributes are in place
			Loader::model('attribute/categories/collection');
			
			$ak_list_public = CollectionAttributeKey::getByHandle('ecommerce_list_is_public');
			if(!$ak_list_public) {
				$at = AttributeType::getByHandle('boolean');
				$ak_list_public = CollectionAttributeKey::add($at,array('akHandle'=>'ecommerce_list_is_public','akName'=>t('eCommerce List is Public')));
			}
			
			$ak_ecom_order_id = CollectionAttributeKey::getByHandle('ecommerce_order_id');
			if(!$ak_ecom_order_id) {
				$at = AttributeType::getByHandle('number');
				$ak_ecom_order_id = CollectionAttributeKey::add($at,array('akHandle'=>'ecommerce_order_id','akName'=>t('eCommerce Order ID')));
			}
			
			$pdt = CollectionType::getByHandle('registry');
			if( !$pdt || !intval($pdt->getCollectionTypeID()) ){
				$data['ctHandle'] = 'registry';
				$data['ctName'] = t('Gift Registry');
				$pdt = CollectionType::add($data, $pkg);
				$pdt->assignCollectionAttribute($ak_ecom_order_id);
				$pdt->assignCollectionAttribute($ak_list_public);
			}
			
			// add the wishlist single page
			$registry = Page::getByPath('/gift_registries');
			if ($registry->isError() || (!is_object($registry))) {
				$registry = SinglePage::add('/gift_registries', $pkg);
				$registry->setAttribute('exclude_nav',1);
				$registry->setAttribute('exclude_page_list',1);
				$registry->setAttribute('exclude_search_index',1);
				$registry->setAttribute('exclude_sitemapxml',1);
			}
			
			$alias = Page::getByPath('/profile/gift_registries');
			
			if ($alias->isError() || (!is_object($alias))) {
				$profile = Page::getByPath('/profile');
				$alias = $registry->addCollectionAlias($profile);
				//$aPage = Page::getByID($alias);
				//$aPage->setAttribute('exclude_nav',0);
			}
			
			$pdt = CollectionType::getByHandle('list_holder');
			if( !$pdt || !intval($pdt->getCollectionTypeID()) ){
				$data['ctHandle'] = 'list_holder';
				$data['ctName'] = t('List Holder');
				$pdt = CollectionType::add($data, $pkg);
			}
		}
	}	
	function save_user() {
		$pkg = Package::getByHandle('core_commerce');
		$pkg->saveConfig('PROFILE_MY_ORDERS_ENABLED',($this->post('PROFILE_MY_ORDERS_ENABLED')?1:0));
		
		$pkg->saveConfig('WISHLISTS_ENABLED',($this->post('WISHLISTS_ENABLED')?1:0));
		$pkg->saveConfig('GIFT_REGISTRIES_ENABLED',($this->post('GIFT_REGISTRIES_ENABLED')?1:0));
		
		if($pkg->config('WISHLISTS_ENABLED')) {
			$this->enableWishlists(true);
		} else {
			$this->enableWishlists(false);
		}
		
		if($pkg->config('GIFT_REGISTRIES_ENABLED')) {
			$this->enableGiftRegistries(true);
		} else {
			$this->enableGiftRegistries(false);
		}
		
		$this->set("message", t('User settings saved.'));	
	}
	
	
}
