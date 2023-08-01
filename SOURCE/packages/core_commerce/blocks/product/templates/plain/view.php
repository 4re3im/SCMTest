<?php    defined('C5_EXECUTE') or die(_("Access Denied."));

$uh = Loader::helper('urls', 'core_commerce');
$ih = Loader::helper('image');

$c = Page::getCurrentPage();

$link_before = '';
$link_after = '';

if (is_object($product)) {
$id = $product->getProductID();
if ($product->getProductCollectionID()>0 && $displayLinkToFullPage && $c->getCollectionID() != $product->getProductCollectionID()) {
	$linksTo = Page::getByID($product->getProductCollectionID());
	if ($linksTo->cID>0) {
		$link_before = '<a href="'.$this->url($linksTo->getCollectionPath()).'" class="ccm-productListImage">';
		$link_after = '</a>';
	}
}

// setup the image objects
if (!is_object($primaryImage)) {
	if($primaryImage != '') {
		$primaryImage = $product->getFileObjectFromImageOption($primaryImage);
	} else {
		$primaryImage = $product->getProductThumbnailImageObject();
	}
}

if(is_string($primaryHoverImage)) {
	$primaryHoverImage = $product->getFileObjectFromImageOption($primaryHoverImage);
}
if(is_string($overlayCalloutImage)) {
	$overlayCalloutImage = $product->getFileObjectFromImageOption($overlayCalloutImage);
} else {
	// legacy support
	$overlayCalloutImage = $product->getProductFullImageObject();
}


if ($displayImage) { 
	$pi = $primaryImage;
	if (is_object($pi)) {
		if($imageMaxWidth<=0) {$imageMaxWidth = 200;} 
		if($imageMaxHeight<=0) {$imageMaxHeight = 200;} 
		$thumb = $ih->getThumbnail($pi, $imageMaxWidth, $imageMaxHeight);
		$img = '<img src="' . $thumb->src . '" width="' . $thumb->width . '" height="' . $thumb->height . '" ';
		$himg = '';
		if (is_object($primaryHoverImage)) {
			$hthumb = $ih->getThumbnail($primaryHoverImage, $imageMaxWidth, $imageMaxHeight);
			if (is_object($hthumb)) {
				$img .= 'class="ccm-productListDefaultImage"';
				$himg = "<img src='{$hthumb->src}' width='{$thumb->width}' height='{$thumb->height}' class='ccm-productListHoverImage' />";
			}
		}
		$img .= ' />';
		if (!$useOverlaysL) {
			$img = $link_before.$img.$himg.$link_after;
		}
	}
	
	if ($useOverlaysL) {
		$images = $product->getAdditionalProductImages();
		if (is_object($images[0])) { // load up first image for the lightbox
			$fi = $images[0];
			if($overlayLightboxImageMaxWidth<=0) {$overlayLightboxImageMaxWidth = 600; }
			if($overlayLightboxImageMaxHeight<=0) {$overlayLightboxImageMaxHeight = 600; }
			$resized = $ih->getThumbnail($fi, $overlayLightboxImageMaxWidth, $overlayLightboxImageMaxHeight);
			$img = '<a href="' . $resized->src .'" class="ccm-core-commerce-add-to-cart-lightbox-image" title="' . $fi->getTitle() . '">' . $img . '</a>';
		}
	}
}

?>
<style>
.ccm-productListImage > img.ccm-productListDefaultImage, .ccm-productListImage:hover > img.ccm-productListHoverImage {
	display:block;
}
.ccm-productListImage > img.ccm-productListHoverImage, .ccm-productListImage:hover > img.ccm-productListDefaultImage {
	display:none;
}
</style>
<div class="ccm-core-commerce-add-to-cart-container">
<form method="post" id="ccm-core-commerce-add-to-cart-form-<?php   echo $id?>" action="<?php   echo $this->url('/cart', 'update')?>">
<input type="hidden" name="rcID" value="<?php   echo $c->getCollectionID()?>" />

<!-- image -->
	
	<div class="photo ccm-core-commerce-add-to-cart-image"><?php   echo $img?></div>

<!-- product info -->

	<div class="hproduct ccm-core-commerce-add-to-cart-product-info-container">
		<?php    if ($displayNameP) { ?>
			<div class="fn ccm-core-commerce-add-to-cart-product-name"><?php   echo $link_before.$product->getProductName().$link_after?></div>
		<?php    } ?>
		
		<?php    if ($displayPriceP) { ?>
			<div class="price ccm-core-commerce-add-to-cart-product-price"><?php   echo Loader::packageElement('product/price', 'core_commerce', array('product' => $product, 'displayDiscount' => $displayDiscountP)); ?></div>
		<?php    } ?>
		
		<?php    if ($displayDescriptionP) { ?>
			<div class="description ccm-core-commerce-add-to-cart-product-description"><?php   echo $product->getProductDescription()?></div>
		<?php    } ?>
		
		<?php    if ($displayDimensionsP) { ?>
			<div class="ccm-core-commerce-add-to-cart-product-dimensions"><?php   echo t('Dimensions: ')?><?php   echo $product->getProductDimensionLength()?>x<?php   echo $product->getProductDimensionWidth()?>x<?php   echo $product->getProductDimensionHeight()?> <?php   echo $product->getProductDimensionUnits()?></div>
		<?php    } 

		if ($displayQuantityInStockP) { ?>
		 	<div class="ccm-core-commerce-add-to-cart-product-quantity"><?php   echo t('# In Stock: ')?><?php   echo $product->getProductQuantity()?></div>
		<?php    } ?>
		
		<div class="ccm-core-commerce-add-to-cart-product-attributes">
		<?php    
		foreach($attributesP as $dak) { 
			$av = $product->getAttributeValueObject($dak);
			if (is_object($av)) { ?>
				<div class="ccm-core-commerce-add-to-cart-product-attributes-label"><?php   echo $dak->getAttributeKeyName()?></div> <div class="ccm-core-commerce-add-to-cart-product-attributes-value"><?php   echo $av->getValue('display')?></div>
			<?php    } ?>
		<?php    } ?>
		
		<!-- display add to cart option -->
		
		<?php    
		if ($displayAddToCart) {
			$attribs = $product->getProductConfigurableAttributes();			
			foreach($attribs as $at) { ?>
			<div class="ccm-core-commerce-add-to-cart-product-attributes">
				<div class="ccm-core-commerce-add-to-cart-product-option-attributes-label"><?php   echo $at->render("label")?><?php    if ($at->isProductOptionAttributeKeyRequired()) { ?> <span class="ccm-required">*</span><?php    } ?></div>
				<div class="ccm-core-commerce-add-to-cart-product-option-attributes-value"><?php   echo $at->render('form');?></div>
			</div>
			
			<!-- display add quantity to cart option -->
			
			<?php    } 
			if ($displayQuantity) { 
			?>
			<div class="ccm-core-commerce-add-to-cart-product-quantity">
				<div><?php   echo $form->label('quantity', t('Quantity'))?> <span class="ccm-required">*</span></div>
				<div class="ccm-core-commerce-add-to-cart-product-quantity-display">
				<?php    if ($product->productIsPhysicalGood()) { ?>
					<?php   echo $form->text("quantity", 1, array("style" => "width: 20px"));?>
				<?php    } else { ?>
					<?php   echo $form->hidden("quantity", 1);?>
					1
				<?php    } ?>
				</div>
			</div>
			<?php    } ?>
			
			<!-- add to cart button -->
			
			<div class="ccm-core-commerce-add-to-cart-product-button-box">
				<?php    if ($product->isProductEnabled()) { ?>
					<span class="ccm-core-commerce-add-to-cart-submit"><?php   echo $form->submit('submit', $addToCartText); ?></span>
					<img src="<?php   echo ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" class="ccm-core-commerce-add-to-cart-loader" />

				<?php    } else { ?>
					<strong><?php   echo t('This product is unavailable.')?></strong>
				<?php    } ?>
			</div>
			<?php   echo $form->hidden('productID', $product->getProductID()); ?>
		<?php    } ?>
	
		</div>
			
		<!-- callout -->

		<?php    if ($useOverlaysC) { ?>
			<div class="ccm-core-commerce-add-to-cart-callout">
				<div class="ccm-core-commerce-add-to-cart-callout-inner">
					<div class="ccm-core-commerce-add-to-cart-callout-image">
						<?php   
						if(is_object($overlayCalloutImage)) {
							$im = Loader::helper('image');
							if($overlayCalloutImageMaxWidth<=0) {
								$overlayCalloutImageMaxWidth = 300;
							}
							if($overlayCalloutImageMaxHeight<=0) {
								$overlayCalloutImageMaxHeight = 300;
							}
							$im->outputThumbnail($overlayCalloutImage, $overlayCalloutImageMaxWidth, $overlayCalloutImageMaxHeight);
						}
						?>
						
						<div>
						<?php    if ($displayNameC) { ?>
							<div class="ccm-core-commerce-add-to-cart-callout-name"><?php   echo $product->getProductName()?></div> 
						<?php    } ?>
						
						<?php    if ($displayPriceC) { ?>
							<div class="ccm-core-commerce-add-to-cart-callout-price"><?php   echo Loader::packageElement('product/price', 'core_commerce', array('product' => $product, 'displayDiscount' => $displayDiscount)); ?></div>
						<?php    } ?>
						</div>
						
						<?php    if ($displayDescriptionC) { ?>
						<div>
							<div class="ccm-core-commerce-add-to-cart-callout-description"><?php   echo $product->getProductDescription()?></div>
						</div>
						<?php    } ?>
						
						<?php    if ($displayDimensionsC) { ?>
						<div>
							<div class="ccm-core-commerce-add-to-cart-callout-dimensions"><?php   echo t('Dimensions')?>: <?php   echo $product->getProductDimensionLength()?>x<?php   echo $product->getProductDimensionWidth()?>x<?php   echo $product->getProductDimensionHeight()?> <?php   echo $product->getProductDimensionUnits()?></div>
						</div>
						<?php    } 

						if ($displayQuantityInStockC) { ?>
						<div>
							<div class="ccm-core-commerce-add-to-cart-callout-quantity"><?php   echo t('# in Stock')?>: <?php   echo $product->getProductQuantity()?></div>
						</div>
						<?php   } ?>
						
						<div class="ccm-core-commerce-add-to-cart-callout-attributes">
						<?php   foreach($attributesP as $dak) { 
							$av = $product->getAttributeValueObject($dak);
							if (is_object($av)) { ?>
								<div class="ccm-core-commerce-add-to-cart-callout-attribute-row">
									<span class="ccm-core-commerce-add-to-cart-callout-attributes-label"><?php   echo $dak->getAttributeKeyName()?></span>: <span class="ccm-core-commerce-add-to-cart-callout-attributes-value"><?php   echo $av->getValue('display')?></span><br />
								</div>
								<?php    } ?>
						<?php    } ?>
						</div>
					</div>
				</div>
			</div>

		<?php    } ?>

		<!-- lightbox -->

		<?php   
		if ($useOverlaysL) {
			for ($i = 1; $i < count($images); $i++ ) {
				$f = $images[$i];
				$resized = $ih->getThumbnail($f, $overlayLightboxImageMaxWidth, $overlayLightboxImageMaxHeight);
				?>
				<a style="display: none" href="<?php   echo $resized->src?>" title="<?php   echo $f->getTitle()?>" class="ccm-core-commerce-add-to-cart-lightbox-image">&nbsp;</a>
			<?php    } ?>	
			<div class="ccm-core-commerce-add-to-cart-lightbox-caption">
				<div>
				
				<?php    if ($displayNameL) { ?>
					<div class="ccm-core-commerce-add-to-cart-lightbox-name"><?php   echo $product->getProductName()?></div> 
				<?php    } ?>
				
				<?php    if ($displayPriceL) { ?>
					<div class="ccm-core-commerce-add-to-cart-lightbox-price"><?php   echo Loader::packageElement('product/price', 'core_commerce', array('product' => $product, 'displayDiscount' => $displayDiscount)); ?></div>
				<?php    } ?>
				
				<?php    if ($displayDescriptionL) { ?>
					<div class="ccm-core-commerce-add-to-cart-lightbox-description"><?php   echo $product->getProductDescription()?></div>		
				<?php    } ?>
				
				<?php    if ($displayDimensionsL) { ?>
					<div>
						<div class="ccm-core-commerce-add-to-cart-lightbox-dimensions"><?php   echo t('Dimensions')?>: <?php   echo $product->getProductDimensionLength()?>x<?php   echo $product->getProductDimensionWidth()?>x<?php   echo $product->getProductDimensionHeight()?> <?php   echo $product->getProductDimensionUnits()?></div>
					</div>
				<?php    } ?>

				<?php    if ($displayQuantityInStockL) { ?>
					<div>
						<div class="ccm-core-commerce-add-to-cart-lightbox-quantity"><?php   echo t('# in Stock')?>: <?php   echo $product->getProductQuantity()?></div>
					</div>
				<?php    } 
				foreach($attributesP as $dak) { 
					$av = $product->getAttributeValueObject($dak);
					if (is_object($av)) { ?>
						<div class="ccm-core-commerce-add-to-cart-lightbox-attributes-label"><?php   echo $dak->getAttributeKeyName()?></div> <div class="ccm-core-commerce-add-to-cart-lightbox-attributes-value"><?php   echo $av->getValue('display')?></div>
					<?php    } ?>
				<?php    } ?>
				</div>
			</div>
		<?php    } ?>

	</div>
</form>
</div>

<?php    if (!$c->isEditMode()) { ?>
<script type="text/javascript">
	$(function() {
		ccm_coreCommerceRegisterAddToCart('ccm-core-commerce-add-to-cart-form-<?php   echo $id?>', '<?php   echo $uh->getToolsURL('cart_dialog')?>');
		<?php    if ($useOverlaysC) { ?>
			ccm_coreCommerceRegisterCallout('ccm-core-commerce-add-to-cart-form-<?php   echo $id?>');
		<?php    } ?>
		<?php    if ($useOverlaysL) { ?>
			$('#ccm-core-commerce-add-to-cart-form-<?php   echo $id?> .ccm-core-commerce-add-to-cart-lightbox-image').lightBox({
				imageLoading: '<?php    echo ASSETS_URL_IMAGES?>/throbber_white_32.gif',
				imageBtnPrev: '<?php    echo $lightboxURL?>/images/lightbox-btn-prev.gif',	
				imageBtnNext: '<?php    echo $lightboxURL?>/images/lightbox-btn-next.gif',			
				imageBtnClose: '<?php    echo $lightboxURL?>/images/lightbox-btn-close.gif',	
				imageBlank:	'<?php    echo $lightboxURL?>/images/lightbox-blank.gif',   
				imageCaptionAdditional: '#ccm-core-commerce-add-to-cart-form-<?php   echo $id?> .ccm-core-commerce-add-to-cart-lightbox-caption'
			});
		<?php    } ?>
		
	});
</script>
<?php    } ?>
<?php    } ?>
