<style type="text/css">
#ccm-core-commerce-product-additional-images {padding-left: 150px}
</style>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Product Images'), false, false, false)?>
<div class="ccm-pane-body">

<h3><?php echo $product->getProductName()?></h3>

<?php  
$form = Loader::helper('form');
$ast = Loader::helper('concrete/asset_library');

$prThumbnailImage = $product->getProductThumbnailImageObject();
$prAltThumbnailImageFID = $product->getProductAlternateThumbnailImageObject();
$prFullImage = $product->getProductFullImageObject();

$ih = Loader::helper('concrete/interface');

$ast = Loader::helper('concrete/asset_library');
$images = array();
if ($this->controller->isPost()) {
	foreach($this->post('additionalProductImageFID') as $fID) {
		$images[] = File::getByID($fID);
	}
} else {
	$images = $product->getAdditionalProductImages();
}
?>
	<form method="post" id="ccm-core-commerce-product-images" action="<?php echo $this->url('/dashboard/core_commerce/products/images', 'save')?>">
	
	<?php echo $form->hidden('productID', $product->getProductID()); ?>

	<h4><?php echo t('Standard Images')?></h4>
	<div class="clearfix ">
	<?php echo $form->label('prThumbnailImageFID', t('Thumbnail Image'))?>
	<div class="input">
		<div style="width: 300px">
		<?php echo $ast->image('prThumbnailImageFID', 'prThumbnailImageFID', t('Choose Image'), $prThumbnailImage)?>
		</div>
	</div>
	</div>

	<div class="clearfix ">
	<?php echo $form->label('prAltThumbnailImageFID', t('Alternate Thumbnail'))?>
	<div class="input">
		<div style="width: 300px">
		<?php echo $ast->image('prAltThumbnailImageFID', 'prAltThumbnailImageFID', t('Choose Image'), $prAltThumbnailImageFID)?>
		</div>
	</div>
	</div>

	<div class="clearfix">
	<?php echo $form->label('prFullImageFID', t('Full Image'))?>
	<div class="input">
		<div style="width: 300px">
		<?php echo $ast->image('prFullImageFID', 'prFullImageFID', t('Choose Image'), $prFullImage)?>
		</div>
	</div>
	</div>

	<h4><?php echo t('Additional Images')?></h4>
	
	
	
	<div id="ccm-core-commerce-product-additional-images">
	<?php  foreach($images as $f) { ?>
		<div class="ccm-core-commerce-product-additional-image"><input type="hidden" name="additionalProductImageFID[]" value="<?php echo $f->getFileID()?>" />
			<img src="<?php echo $f->getThumbnailSRC(1)?>" class="ccm-core-commerce-product-additional-thumbnail" />
			<a href="javascript:void(0)" onclick="ccm_coreCommerceRemoveProductImage(this)" class="ccm-core-commerce-product-additional-remove"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/delete_small.png" /></a>
			<h5><?php echo $f->getTitle()?></h5>
			<div class="ccm-spacer">&nbsp;</div>
		</div>
		
	<?php  } ?>
	</div>
	
	<br/>
	<div class="clearfix">
	<label></label>
	<div class="input">
	<?php echo $ih->button_js(t('Add Image'), 'ccm_chooseAsset=ccm_chooseAdditionalImage; javascript:ccm_launchFileManager(\'&fType=' . FileType::T_IMAGE . '\')', 'l	eft');?>
	</div>
	</div>

	
</div>
<div class="ccm-pane-footer">
	<a href="<?php echo $this->url('/dashboard/core_commerce/products/search', 'view_detail', $product->getProductID())?>" class="btn"><?php echo t('Back to Product')?></a>
	<a href="javascript:void(0)" onclick="$('#ccm-core-commerce-product-images').get(0).submit()" class="ccm-button-right btn primary"><span><?php echo t('Save')?></span></a>
</div>	
</form>
	
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

<script type="text/javascript">
var ccm_chooseAsset;
$(function() {
	ccm_activateFileSelectors();
	$("div#ccm-core-commerce-product-additional-images").sortable({
		handle: 'img.ccm-core-commerce-product-additional-thumbnail',
		cursor: 'move',
		opacity: 0.5
	});
});

ccm_coreCommerceRemoveProductImage = function(obj) {
	$(obj).parent().fadeOut(120, function() {
		$(obj).parent().remove();	
	});
}

var ccm_chooseImageTimeout = false;
ccm_chooseAdditionalImage = function(obj) {
	clearTimeout(ccm_chooseImageTimeout);
	html = '<div class="ccm-core-commerce-product-additional-image"><input type="hidden" name="additionalProductImageFID[]" value="' + obj.fID + '" />';
	html += '<img src="' + obj.thumbnailLevel1 + '" class="ccm-core-commerce-product-additional-thumbnail" />';
	html += '<a href="javascript:void(0)" onclick="ccm_coreCommerceRemoveProductImage(this)" class="ccm-core-commerce-product-additional-remove"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/delete_small.png" /><\/a>';
	html += '<h5>' + obj.title + '<\/h5>';
	html +='<div class="ccm-spacer">&nbsp;<\/div><\/div>';
	$("#ccm-core-commerce-product-additional-images").append(html);
	// this hackery is due to internet explorer. I hate you internet explorer.
	ccm_chooseImageTimeout = setTimeout(function() {
		ccm_chooseAsset = false;
	}, 200);
}
</script>
