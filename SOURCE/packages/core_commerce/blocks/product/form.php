<style type="text/css">
table.ccm-grid {border-left: 1px solid #D4D4D4; border-top: 1px solid #D4D4D4; font-size: 12px; }
table.ccm-grid th, table.ccm-grid > tbody > tr > td {border-right: 1px solid #D4D4D4; border-bottom: 1px solid #D4D4D4; font-size: 12px; padding: 7px; background: #fff}
table.ccm-grid tr.ccm-row-alt td {background-color: #F0F5FF !important}
table.ccm-grid th {font-weight: bold; color: #999999; background-color: #efefef; text-align: center;}
table.ccm-grid tr.version-active td, table.ccm-grid tr.active td {font-weight: bold; font-size: 13px}
table.ccm-grid td.actor img {float: right}
table.ccm-grid td.ccm-grid-cb {text-align: center}
table.ccm-grid img {border: 0px}
</style>

<div class="ccm-block-field-group">
<h4><?php echo t('Product')?></h4>
<?php echo $form->radio('inheritProductIDFromCurrentPage', '0', $inheritProductIDFromCurrentPage)?>
<?php echo $form->label('inheritProductIDFromCurrentPage2', t('Choose specific product'))?>
&nbsp;&nbsp;
<?php echo $form->radio('inheritProductIDFromCurrentPage', '1', $inheritProductIDFromCurrentPage)?>
<?php echo $form->label('inheritProductIDFromCurrentPage1', t('Inherit from current page'))?>

<div class="ccm-core-commerce-product-block-choose-product" style="<?php  if ($inheritProductIDFromCurrentPage == 1) { ?>display: none<?php  } ?>">
<br/>
<?php echo $prh->selectOne('productID', t('Select Product'), $product); ?>
</div>

</div>

<div class="ccm-block-field-group">
<h4><?php echo t('Where should the product properties be displayed?')?></h4>

<table border="0" cellspacing="0" class="ccm-grid" cellpadding="0" width="400" id="ccm-core-commerce-product-attribute-grid">
<thead>
<tr>
	<th width="100%"><?php echo t('Property')?></th>
	<th><?php echo t('Page')?></th>
	<th><?php echo t('Callout')?></th>
	<th><?php echo t('Lightbox')?></th>
	<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php  
Loader::model('product/display_property', 'core_commerce');
$list = new CoreCommerceProductDisplayPropertyList();
$list->setPropertyOrder($controller->propertyOrder);
$displayProperties = $list->get();

$attributesL = $controller->getAttributes('L', false);
$attributesC = $controller->getAttributes('C', false);
$attributesP = $controller->getAttributes('P', false);


foreach($displayProperties as $property) { 
	if ($property->type == 'fixed') {
		$varP = ${$property->handle . 'P'};
		$varC = ${$property->handle . 'C'};
		$varL = ${$property->handle . 'L'};
	} else {
		$varP = in_array($property->akID, $attributesP);
		$varC = in_array($property->akID, $attributesC);
		$varL = in_array($property->akID, $attributesL);
	}
	?>

	<tr>
		<td width="100%"><?php echo $form->hidden('displayPropertyOrder[]', $property->handle)?><?php echo $property->name?></td>
		<td class="ccm-grid-cb"><?php echo $form->checkbox($property->handle . '[]', 'P', $varP)?></td>
		<td class="ccm-grid-cb"><?php echo $form->checkbox($property->handle . '[]', 'C', $varC)?></td>
		<td class="ccm-grid-cb"><?php echo $form->checkbox($property->handle . '[]', 'L', $varL)?></td>
		<td><img src="<?php echo ASSETS_URL_IMAGES?>/icons/up_down.png" width="14" height="14" class="ccm-core-commerce-attribute-handle" /></td>
	</tr>


<?php  } ?>

</tbody>
</table>

</div>

<div class="ccm-block-field-group">
	<h4><?php echo t('Inventory') ?></h4>
		<?php echo $form->checkbox('hideSoldOut',1,$hideSoldOut); ?>
		&nbsp;&nbsp;
		<?php echo $form->label('hideSoldOut',t('Hide if this product is sold out.'))?>
</div>	
<div class="ccm-block-field-group">
<h4><?php echo t('Adding to Cart')?></h4>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td valign="top">
	<?php echo $form->checkbox('displayLinkToFullPage', 1, $displayLinkToFullPage)?> <?php echo $form->label('displayLinkToFullPage', t('Link product to its default page.'))?><br />
	<?php echo $form->checkbox('displayAddToCart', 1, $displayAddToCart)?> <?php echo $form->label('displayAddToCart', t('Include add to cart link.'))?><br />
	<span style="margin-left:15px">&nbsp;</span><?php echo $form->checkbox('displayQuantity', 1, $displayQuantity)?> <?php echo $form->label('displayQuantity', t('Allow quantity to be chosen.'))?>
	</td>
	<td><div style="width: 60px">&nbsp;</div></td>
	<td valign="top">
	<?php echo $form->label('addToCartText', t('Add to Cart Button Text'))?><br/>
	<?php echo $form->text('addToCartText', $addToCartText)?></div>
	</td>
</tr>
</table>
</div>

<div class="ccm-block-field-group">
	<h4><?php echo t('Image')?></h4>
	<?php echo $form->checkbox('displayImage', 1, $displayImage)?> <?php echo $form->label('displayImage', t('Display product image.'))?>
	<div class="ccm-core-commerce-product-block-image-fields" style="padding-left:22px;">
		<br/>
		<div>
			<?php echo  $form->select('primaryImage', $controller->getAvailableImageOptions(), $primaryImage); ?>
		</div>
		<br/>
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="top">
			<?php echo $form->label('imageMaxWidth', t('Width'))?><br/>
			<?php echo $form->text('imageMaxWidth', $imageMaxWidth, array('size'=>'6'))?>
			</td>
			<td><div style="width: 10px">&nbsp;</div></td>
			<td valign="top">
				<?php echo $form->label('imageMaxHeight', t('Height'))?><br/>
				<?php echo $form->text('imageMaxHeight', $imageMaxHeight, array('size'=>'6'))?>
			</td>
			<td><div style="width: 10px">&nbsp;</div></td>
			<td valign="top">
				<?php echo t('Image Position')?><br/>
				<?php echo $form->radio('imagePosition', 'L', $imagePosition)?>
				<?php echo $form->label('imagePosition1', t('Left'))?>
				&nbsp;&nbsp;
				<?php echo $form->radio('imagePosition', 'T', $imagePosition)?>
				<?php echo $form->label('imagePosition2', t('Top'))?>
				&nbsp;&nbsp;
				<?php echo $form->radio('imagePosition', 'R', $imagePosition)?>
				<?php echo $form->label('imagePosition3', t('Right'))?>
				&nbsp;&nbsp;
				<?php echo $form->radio('imagePosition', 'B', $imagePosition)?>
				<?php echo $form->label('imagePosition4', t('Bottom'))?>
			</td>
		</tr>
		</table>
		<br/>
		<div>
			<?php echo $form->label('primaryImageHoverFID', t('Alternate image to display on roll-over:'))?>
			<?php 
				$imageOptions = array_merge(array('-'=>t('None')),$controller->getAvailableImageOptions());
				echo $form->select('primaryHoverImage', $imageOptions, $primaryHoverImage); 
			?>
		</div>
	</div>
</div>

<div class="ccm-block-field-group">
	<h4><?php echo t('Use Overlays')?></h4>
	<div>
		<?php echo $form->checkbox('useOverlaysC', 'C', $useOverlaysC)?>
		<?php echo $form->label('useOverlaysC', t('Callout'))?>
		<div class="ccm-core-commerce-product-block-image-fields" style="padding-left:22px;">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top">
					<?php echo $form->label('overlayCalloutImage', t('Image'))?><br/>
					<?php echo  $form->select("overlayCalloutImage", $imageOptions, $overlayCalloutImage); ?>
				</td>
				<td><div style="width: 10px">&nbsp;</div></td>
				<td valign="top">
					<?php echo $form->label('overlayCalloutImageMaxWidth', t('Width'))?><br/>
					<?php echo $form->text('overlayCalloutImageMaxWidth', $overlayCalloutImageMaxWidth, array('size'=>'6'))?>
				</td>
				<td><div style="width: 10px">&nbsp;</div></td>
				<td valign="top">
					<?php echo $form->label('overlayCalloutImageMaxHeight', t('Height'))?><br/>
					<?php echo $form->text('overlayCalloutImageMaxHeight', $overlayCalloutImageMaxHeight, array('size'=>'6'))?>
				</td>
			</tr>
			</table>
		</div>
	</div>
	&nbsp;&nbsp;
	<div>
		<?php echo $form->checkbox('useOverlaysL', 'L', $useOverlaysL)?>
		<?php echo $form->label('useOverlaysL', t('Lightbox'))?>
		<div class="ccm-core-commerce-product-block-image-fields" style="padding-left:22px;">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top">
					<?php echo $form->label('overlayLightboxImageMaxWidth', t('Image Width'))?><br/>
					<?php echo $form->text('overlayLightboxImageMaxWidth', $overlayLightboxImageMaxWidth, array('size'=>'6'))?>
				</td>
				<td><div style="width: 10px">&nbsp;</div></td>
				<td valign="top">
					<?php echo $form->label('overlayLightboxImageMaxHeight', t('Image Height'))?><br/>
					<?php echo $form->text('overlayLightboxImageMaxHeight', $overlayLightboxImageMaxHeight, array('size'=>'6'))?>
				</td>
			</tr>
			</table>
		</div>
	</div>
</div>

<style type="text/css">
img.ccm-core-commerce-attribute-handle {margin: 0px auto 0px auto; display: block; cursor: move;}
</style>

<script type="text/javascript">
$("#ccm-core-commerce-product-attribute-grid tbody").sortable({items: 'tr', handle: '.ccm-core-commerce-attribute-handle', stop: function() {
	ccm_setupGridStriping('ccm-core-commerce-product-attribute-grid');
}});
</script>
