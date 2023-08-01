<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$valt = Loader::helper('validation/token');

?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Author'), false, false, false)?>
<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>

<form method="post" id="ccm-core-commerce-product-add-form" action="<?php echo $this->url('/dashboard/cup_content/authors/edit', $entry['id'])?>">
<div class="ccm-pane-body">
	
	<?php echo $valt->output('edit_author')?>
	<?php Loader::packageElement('author/form', 'cup_content', array('entry'=>$entry)); ?>
</div>
<div class="ccm-pane-footer">
	<input type="hidden" name="create" value="1" />
	<input id="cc-parent-page" type="hidden" name="parentCID" value="0" />
	<a href="<?php echo $this->url('/dashboard/cup_content/authors')?>" class="btn"><?php echo t('Back to Authors')?></a>
	<input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Save')?>"/>
</div>	
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>