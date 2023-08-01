<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
/*
	$id
*/

$uh = Loader::helper('urls', 'core_commerce');
if(!isset($pkg)) {
	$pkg = Package::getByHandle('core_commerce');
}
$c = Page::getCurrentPage();

$link_before = '';
$link_after = '';

$form = Loader::helper('form');
?>

<style>
.ccm-productListImage > img.ccm-productListDefaultImage, .ccm-productListImage:hover > img.ccm-productListHoverImage {
	display:block;
}
.ccm-productListImage > img.ccm-productListHoverImage, .ccm-productListImage:hover > img.ccm-productListDefaultImage {
	display:none;
}
</style>

<?php
// Nick Ingarsia, 27/10/14
// As this is now also called via AJAX, instanciate a view object, so we can use the url method
// $this is not available when called via a tool
$v = new View;
?>
<form method="post" id="ccm-core-commerce-add-to-cart-form-<?php echo $id?>" action="<?php echo $v->url('/education/cart', 'update')?>">


<?php
// Nick Ingarsia, 27/10/14
// Check for a cID passed in via AJAX
$collectionID = isset($cID) ? $cID : $c->getCollectionID();
?>
<input type="hidden" name="rcID" value="<?php echo $collectionID ?>" />

<?php 
		Loader::model('product/display_property', 'core_commerce'); 
		$list = new CoreCommerceProductDisplayPropertyList();
		$list->setPropertyOrder($propertyOrder);
		$displayProperties = $list->get();
		$properties = array();
		
		
		Loader::packageElement('product/display/properties', 'core_commerce', array('linkToProductPage' => $linkToProductPage, 'properties' => $properties, 'product' => $product));

		//if ($displayAddToCart):
		if (true):
		?>

			
			<?php if ($displayQuantity) { ?>
				<?php  if ($product->productIsPhysicalGood()) { ?>
					<?php echo $form->text("quantity", 1, array(/*"style" => "width: 20px"*/));?>
				<?php  } else { ?>
					<?php echo $form->hidden("quantity", 1);?>
					<span class="fixed_quantity">1</span>
				<?php  } ?>
			<?php  } ?>
			
			
			<?php  if ($product->isProductEnabled()) { ?>
				<?php echo $form->submit('submit', 'Add to cart'); ?>
				<img src="<?php echo ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" class="ccm-core-commerce-add-to-cart-loader" />

			<?php  } else { ?>
				<strong><?php echo t('This product is unavailable.')?></strong>
			<?php  } ?>
			
			<!--
			<?php if($pkg->config('WISHLISTS_ENABLED')) {?>
				<div>
					<?php  echo $form->button('submit-wishlist',t("Add To Wishlist"), array('class'=>'ccm-core-commerce-add-to-wishlist-button'));?>
				</div>
			<?php  }
			if($pkg->config('GIFT_REGISTRIES_ENABLED')) {?>
				<div>
					<?php  echo $form->button('submit-registry',t("Add To Gift Registry"), array('class'=>'ccm-core-commerce-add-to-registry-button'));?>
				</div>
			<?php  } ?>
			-->
				<?php echo $form->hidden('productID', $product->getProductID()); ?>
		<?php  endif; ?>
</form>


<?php
	// Nick Ingarsia, 27/10/14
	// As this is now also called via AJAX, also add in this the javascript if a page object
	// does not exist
?>
<?php  if (!is_object($c) || (is_object($c) && !$c->isEditMode())) { ?>
<script type="text/javascript">
	$(function() {
		ccm_coreCommerceRegisterAddToCart('ccm-core-commerce-add-to-cart-form-<?php echo $id?>', '<?php echo $uh->getToolsURL('cart_dialog')?>');
		<?php  if($pkg->config('WISHLISTS_ENABLED')) { ?>
			ccm_coreCommerceRegisterAddToWishList('ccm-core-commerce-add-to-cart-form-<?php echo $id?>', '<?php echo $uh->getToolsURL('wishlist/add_to_wishlist')?>?rcID=<?php  echo Page::getCurrentPage()->getCollectionID()?>');
			ccm_coreCommerceRegisterAddToRegistry('ccm-core-commerce-add-to-cart-form-<?php echo $id?>', '<?php echo $uh->getToolsURL('wishlist/add_to_registry')?>?rcID=<?php  echo Page::getCurrentPage()->getCollectionID()?>');
		<?php  } ?>
	});
</script>
<?php  } ?>
