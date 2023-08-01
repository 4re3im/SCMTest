<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('product/model', 'core_commerce');
Loader::model('cart', 'core_commerce');

if(!isset($cart)) {
	$cart = CoreCommerceCart::get();
}

if(!isset($cart_path)) {
	$cart_path = "/profile/wish-list";	
}

Loader::library('price', 'core_commerce');
$items = $cart->getProducts(); 
$uh = Loader::helper('urls', 'core_commerce');
$chs = Loader::helper('checkout/step', 'core_commerce');
if (!isset($edit)) {
	$edit =true;
}
$u = new User();
if ($cart->isOrderBelowMinimumThreshold() || ($cart->requiresLogin() && (!$u->isRegistered()))) {
	$checkoutDisabled = 'disabled="disabled" ';
}

if(count($items)){
	$itemsCount = 0;
	foreach($items as $it)
		$itemsCount += $it->getQuantity();
}

if(is_array($errors) && count($errors)) {
	print '<ul class="ccm-error">';
	foreach($errors as $error){
		print '<li>' . $error . '</li>';
	}
	print '</ul>';
}
?>
<table border="0" class="ccm-core-commerce-cart" cellspacing="0" cellpadding="0">
<tr>
	<th>&nbsp;</th>
	<th class="ccm-core-commerce-cart-name"><?php echo t('Name')?></th>
	<th class="ccm-core-commerce-cart-quantity"><?php echo t('Quantity')?></th>
	<th class="ccm-core-commerce-cart-quantity"><?php echo t('Price')?></th>
	<th>&nbsp;</th>
</tr>
<tr>

<?php  if (count($items) == 0) { ?>
	<td colspan="4"><?php echo t('Your wishlist is empty.')?></td>
<?php  } else { ?>

<?php  foreach($items as $it) { ?>
<tr>
	<td class="ccm-core-commerce-cart-thumbnail"><?php echo $it->outputThumbnail()?></td>
	<td class="ccm-core-commerce-cart-name"><?php echo $it->getProductName()?>
	<?php  $attribs = $it->getProductConfigurableAttributes()?>
	<?php  if (count($attribs) > 0) { ?>
		<br/>
		<?php 
		foreach($attribs as $ak) { ?>
			<?php echo $ak->render("label")?>: <?php echo $it->getAttribute($ak, 'display')?><br/>
		<?php  } 
	} ?>
	</td>
	<?php  if ($edit) { ?>
		<td class="ccm-core-commerce-cart-quantity"><?php echo $it->getQuantityField()?></td>
	<?php  } else { ?>
		<td class="ccm-core-commerce-cart-quantity"><?php echo $it->getQuantity()?></td>
	<?php  } ?>
	<td class="ccm-core-commerce-cart-price"><?php echo $it->getProductCartDisplayPrice()?></td>
	<td class="ccm-core-commerce-cart-remove">
		<?php  if ($edit) { ?>
		<a href="<?php echo View::url($cart_path, 'remove_product', $it->getOrderProductID())?>" <?php  if ($dialog) { ?> onclick="ccm_coreCommerceRemoveCartItem(this, '<?php echo $uh->getToolsURL('cart_dialog')?>'); return false" <?php  } ?>><img src="<?php echo DIR_REL?>/packages/core_commerce/images/icons/delete_small.png" width="16" height="16" /></a>
		<?php  } ?>
	</td>
</tr>
<?php  } ?>
<tr class="ccm-core-commerce-cart-subtotal">
	<td colspan="3">&nbsp;</td>
	<td class="ccm-core-commerce-cart-price"><?php echo $cart->getBaseOrderDisplayTotal()?></td>
	<td>&nbsp;</td>
</tr>
<?php  
if (!$edit) { 
	// now we include any of the order's attributes which affect the total 
	$items = $cart->getOrderLineItems(); 
	 
	foreach($items as $it) { ?>	
	<tr>
		<td class="ccm-core-commerce-cart-thumbnail">&nbsp;</td>
		<td class="ccm-core-commerce-cart-name"><?php echo $it->getLineItemName()?></td>
		<td>&nbsp;</td>
		<td class="ccm-core-commerce-cart-price"><?php echo $it->getLineItemDisplayTotal()?></td>
		<td>&nbsp;</td>
	</tr>
	
	<?php  }  ?>
	
	<?php  if (count($items) > 0) { ?>
	<tr class="ccm-core-commerce-cart-subtotal">
		<td colspan="3">&nbsp;</td>
		<td class="ccm-core-commerce-cart-price"><?php echo $cart->getOrderDisplayTotal()?></td>
		<td>&nbsp;</td>
	</tr>
	<?php  }
	
	}
	
}

if ($cart->isOrderBelowMinimumThreshold()) { ?>
	<tr class="ccm-core-commercial-cart-total-note">
		<td colspan="5"><?php echo t('Your order must be at least %s before you may check out.', CoreCommercePrice::format($cart->getOrderMinimumTotal()))?></td>
	</tr>
<?php  }

if ($cart->requiresLogin() && (!$u->isRegistered())) { ?>

	<tr class="ccm-core-commercial-cart-total-note">
		<?php  $cartPage = Page::getByPath($cart_path); ?>
		<td colspan="5"><?php echo t('You must <a href="%s">sign in</a> before you can purchase items.', View::url('/login', 'forward', $cartPage->getCollectionID()))?>
		<?php  
		if (ENABLE_REGISTRATION == 1) { ?>
			<br/><br/>
			<?php 
			print t('Not a member? <a href="%s">Register here</a>.', View::url('/register', 'forward', $cartPage->getCollectionID()));
		} ?></td>
	</tr>
<?php  } ?>

</table>

<?php echo  $form->hidden('rcID',$_REQUEST['rcID']);?>

<div style="clear: both">&nbsp;</div>
<script>
	$(function() {
		$('#ccm-core-commerce-cart-form<?php  if ($dialog) { ?>-dialog<?php  } ?> .ccm-error').hide();
		$('#ccm-core-commerce-cart-form<?php  if ($dialog) { ?>-dialog<?php  } ?> .ccm-error').slideDown(500,
			function () {
				setTimeout("$('#ccm-core-commerce-cart-form<?php  if ($dialog) { ?>-dialog<?php  } ?> .ccm-error').slideUp('slow');",5000);
			}
		);
	});
</script>
