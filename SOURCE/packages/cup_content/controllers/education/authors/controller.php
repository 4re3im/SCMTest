<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class AuthorsController extends Controller {

	public function view() { 
		exit("authors");
	}
	
	public function changeLocale($locale = FALSE){
		if($locale === FALSE){
			$locale = 'en_US';
		}
		Localization::changeLocale($locale);
		echo "done".DIR_REL;
		
		if (isset($locale) && $locale) {
			setcookie('DEFAULT_LOCALE', $locale, time()+60*60*24*365);
			$_SESSION['DEFAULT_LOCALE'] = $locale;
		}
		if (empty($locale)) {
			setcookie('DEFAULT_LOCALE', '', time() - 3600);
			unset($_SESSION['DEFAULT_LOCALE']);
		}
		$lang = MultilingualSection::getByLocale($_REQUEST['ccmMultilingualSiteDefaultLanguage']);
	
	
		echo "|".Loader::helper('default_language','multilingual')->getSessionDefaultLocale()."|";
		exit();
	}
	
	public function viewCart(){
		Loader::model('order/current', 'core_commerce');
		Loader::model('order/product', 'core_commerce');
		
		$cart = CoreCommerceCurrentOrder::get();
		$products = $cart->getProducts();
		
		print_r($products);
		exit();
	}
	
	public function clearCart(){
		Loader::model('order/current', 'core_commerce');
		Loader::model('order/product', 'core_commerce');
		
		$cart = CoreCommerceCurrentOrder::get();
		$products = $cart->getProducts();
		
		foreach($products as $pr){
			$cart->removeProduct($pr);
		}
		echo "clear cart";
		exit();
	}
	
}