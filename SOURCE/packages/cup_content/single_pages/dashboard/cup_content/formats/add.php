<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$valt = Loader::helper('validation/token');

?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Format'), false, false, false)?>
<form method="post" id="ccm-core-commerce-product-add-form" enctype="multipart/form-data" action="<?php echo $this->url('/dashboard/cup_content/formats/add', 'submit')?>">
<div class="ccm-pane-body">
	<?php echo $valt->output('create_format')?>
	<?php Loader::packageElement('format/form', 'cup_content', array('entry'=>@$entry)); ?>
</div>
<div class="ccm-pane-footer">
	<input type="hidden" name="create" value="1" />
	<input id="cc-parent-page" type="hidden" name="parentCID" value="0" />
	<a href="<?php echo $this->url('/dashboard/cup_content/formats')?>" class="btn"><?php echo t('Back to Formats')?></a>
	<input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Add')?>"/>
</div>	
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>