<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 
// translate this file if we're multilingualing it
if(Loader::helper('multilingual','core_commerce')->isEnabled()) {
	Loader::helper('default_language','multilingual')->setupSiteInterfaceLocalization();
}

Loader::model('product/model', 'core_commerce');
Loader::model('order/product', 'core_commerce');
Loader::model('cart', 'core_commerce');
$jh = Loader::helper('json');

$cart = CoreCommerceCart::get();
$quantity = $cart->getTotalProducts();
$items = ' ';
if ($quantity == 1) {
	$items .= ' '. t("item");
} else {
	$items .= ' '. t("items");
}

//This looks like it's encoding as { "10 items" : items } or something.
echo $jh->encode($quantity. $items);
?>
