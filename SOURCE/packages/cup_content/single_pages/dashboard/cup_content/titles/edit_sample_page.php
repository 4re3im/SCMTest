<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$html = Loader::helper('html');

$valt = Loader::helper('validation/token');

?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Sample Page'), false)?>
<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>

<form method="post" id="ccm-core-commerce-product-add-form" enctype="multipart/form-data" action="">
<div class="ccm-pane-body">
	<?php echo $valt->output('create_title')?>
	<?php Loader::packageElement('title_sample_page/form', 'cup_content', array('entry'=>@$entry)); ?>
</div>
<div class="ccm-pane-footer">
	<input type="hidden" name="create" value="1" />
	<input id="cc-parent-page" type="hidden" name="parentCID" value="0" />
	<a href="<?php echo $this->url('/dashboard/cup_content/titles/sample_page_list', $entry['id'])?>" class="btn"><?php echo t('Back to Sample Page List')?></a>
	<input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Save')?>"/>
</div>	
</form>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

