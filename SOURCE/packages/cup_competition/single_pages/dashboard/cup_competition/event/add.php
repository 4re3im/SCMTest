<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$html = Loader::helper('html');
$ih = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');

$entry = $eventObj->getAssoc();
?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Event'), false);?>
<?php //Loader::packageElement('alert_message_header', 'cup_content'); ?>

<form method="post" id="ccm-core-commerce-product-add-form" enctype="multipart/form-data" action="<?php echo $this->url('/dashboard/cup_competition/event/add', 'submit')?>">
<div class="ccm-pane-body">
	<?php Loader::packageElement('alert_message_header', 'cup_competition'); ?>
	
	<?php echo $valt->output('create_event')?>
	<?php Loader::packageElement('event/form', 'cup_competition', array('entry'=>@$entry)); ?>
</div>
<div class="ccm-pane-footer">
	<input type="hidden" name="create" value="1" />
	<input id="cc-parent-page" type="hidden" name="parentCID" value="0" />
	<a href="<?php echo $this->url('/dashboard/cup_competition/event')?>" class="btn"><?php echo t('Back to Event')?></a>
	<input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Add')?>"/>
</div>	
</form>

<div style="clear:both"></div>
<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);?>
