<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$html = Loader::helper('html');
$ih = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');

$entry = $entryObj->getAssoc();
?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('View Competition Entry'), false);?>
<?php //Loader::packageElement('alert_message_header', 'cup_content'); ?>

<form method="post" id="ccm-core-commerce-product-add-form" action="<?php echo $this->url('/dashboard/cup_competition/entry/viewEntry/'.$entryObj->id)?>">

<div class="ccm-pane-body">
	<?php Loader::packageElement('alert_message_header', 'cup_competition'); ?>
	<?php Loader::packageElement('event_entry/view', 'cup_competition', array('entryObj'=>@$entryObj)); ?>
</div>
<div class="ccm-pane-footer">
	<input type="hidden" name="create" value="1" />
	<input id="cc-parent-page" type="hidden" name="parentCID" value="0" />
	<a href="<?php echo $this->url('/dashboard/cup_competition/entry/viewByEvent', $entryObj->eventID)?>" class="btn"><?php echo t('Back to Event Entries')?></a>
	<input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Save')?>"/>
</div>
</form>

<div style="clear:both"></div>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);?>
