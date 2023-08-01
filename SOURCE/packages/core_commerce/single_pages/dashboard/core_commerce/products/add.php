<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$txt = Loader::helper('text');
$vals = Loader::helper('validation/strings');
$valt = Loader::helper('validation/token');
$valc = Loader::helper('concrete/validation');
$dtt = Loader::helper('form/date_time');
$form = Loader::helper('form');
$ast = Loader::helper('concrete/asset_library');
$ih = Loader::helper('concrete/interface');
$uh = Loader::helper('urls', 'core_commerce');

?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Product'), false, false, false)?>
<form method="post" id="ccm-core-commerce-product-add-form" onsubmit="askAboutAPage();return false;" action="<?php echo $this->url('/dashboard/core_commerce/products/add', 'submit')?>">
<div class="ccm-pane-body">
	<?php echo $valt->output('create_product')?>
	<?php  Loader::packageElement('product/form', 'core_commerce'); ?>
</div>
<div class="ccm-pane-footer">
	<input type="hidden" name="create" value="1" />
	<input id="cc-parent-page" type="hidden" name="parentCID" value="0" />
	<a href="<?php echo $this->url('/dashboard/core_commerce/products/search')?>" class="btn"><?php echo t('Back to Products')?></a>
	<a href="javascript:void(0)" onclick="askAboutAPage()" class="ccm-button-right btn primary accept"><?php echo t('Add')?></a>
</div>	
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

<script type="text/javascript">
	function askAboutAPage() {
		$.fn.dialog.open({
			href: "<?php echo $uh->getToolsURL('create_page')?>",
			title: "<?php echo t('Create a Product Page?')?>",
			width: 550,
			modal: true,
			appendButtons: true,
			onOpen:function(){},
			onClose: function(){$('#ccm-core-commerce-product-add-form').get(0).submit()},
			height: 480
		});
	}
</script>
