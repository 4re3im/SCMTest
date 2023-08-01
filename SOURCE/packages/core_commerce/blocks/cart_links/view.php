<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('cart', 'core_commerce');
$chs = Loader::helper('checkout/step', 'core_commerce');
$th = Loader::helper('concrete/urls')->getToolsURL('cart_dialog', 'core_commerce');
$qh = Loader::helper('concrete/urls')->getToolsURL('cart_quantity','core_commerce');
$c = Page::getCurrentPage();
//not totally sure why the items string is not displaying the number of items.
?>

<div class="cc-cart-links">
<?php  if ($showCartLink) { ?>
	<a href="<?php echo $this->url('/cart?rcID=' . $c->getCollectionID())?>" onclick="ccm_coreCommerceLaunchCart(this, '<?php echo $th?>'); return false"><?php echo $cartLinkText?></a>
<?php  } ?>
<?php  if ($showItemQuantity) { ?>
	(<span id="cc-cart-quantity" href="<?php echo $qh?>"><?php  echo $items . ' ' . ($items != 1 ? t('items'):t('item')) ?></span>) 
<?php  } ?>
	<span class='cc-checkout-link-show' style='<?php echo ($showCheckoutLink && $items > 0?'':'display:none')?>'>
		<a href="<?php  echo CoreCommerceCheckoutStep::getBase() . View::url('/checkout')?>"><?php  echo $checkoutLinkText?></a>
	</span>
</div>