<?php 
$settings = Page::getByPath('/dashboard/core_commerce/settings', 'ACTIVE');
?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Email Settings'), false, false, false, array(), $settings);?>
<?php 
$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.dashboard.css', 'core_commerce'));
$ih = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');
$pkg = Package::getByHandle('core_commerce');
$form = Loader::helper('form');

?>
<form method="post" action="<?php echo $this->action('save_email')?>" id="ccm-core-commerce-user-settings-form">
<div class="ccm-pane-body">
	<?php  
	$pkg = Package::getByHandle('core_commerce'); ?>
	
	<?php  $emails = str_replace(',', "\n", $pkg->config('ENABLE_ORDER_NOTIFICATION_EMAIL_ADDRESSES')) ?>
	<?php  $blurb = $pkg->config('RECEIPT_EMAIL_BLURB'); if (empty($blurb)) { $blurb = t('Thank you for your purchase!'); } ?>


<fieldset>
<legend><?php echo t('Receipts')?></legend>
<div class="clearfix">
<?php echo $form->label('EMAIL_RECEIPT_EMAIL', t('From Email'))?>
<div class="input">
<?php echo $form->text('EMAIL_RECEIPT_EMAIL', $pkg->config('EMAIL_RECEIPT_EMAIL'), array('class' => 'span7'))?>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('EMAIL_RECEIPT_NAME', t('From Name'))?>
<div class="input">
<?php echo $form->text('EMAIL_RECEIPT_NAME', $pkg->config('EMAIL_RECEIPT_NAME'), array('class' => 'span7'))?>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('RECEIPT_EMAIL_BLURB', t('Custom Receipt Message'))?>
<div class="input">
<?php echo $form->textarea('RECEIPT_EMAIL_BLURB', $blurb, array('class' => 'span8', 'rows' => 7))?>
</div>
</div>

</fieldset>

<fieldset>
<legend><?php echo t('Notifications')?></legend>

<div class="clearfix">
<label></label>
<div class="input">
<ul class="inputs-list">
	<li><label><?php echo $form->checkbox('ENABLE_ORDER_NOTIFICATION_EMAILS', 1, $pkg->config('ENABLE_ORDER_NOTIFICATION_EMAILS'))?> <span><?php echo t('Send notification emails when an order is placed')?></span></label></li>
</ul>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('EMAIL_NOTIFICATION_EMAIL', t('From Email'))?>
<div class="input">
<?php echo $form->text('EMAIL_NOTIFICATION_EMAIL', $pkg->config('EMAIL_NOTIFICATION_EMAIL'), array('class' => 'span7'))?>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('EMAIL_NOTIFICATION_NAME', t('From Name'))?>
<div class="input">
<?php echo $form->text('EMAIL_NOTIFICATION_NAME', $pkg->config('EMAIL_NOTIFICATION_NAME'), array('class' => 'span7'))?>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('ENABLE_ORDER_NOTIFICATION_EMAIL_ADDRESSES', t('Send Notifications To'))?>
<div class="input">
<?php echo $form->textarea('ENABLE_ORDER_NOTIFICATION_EMAIL_ADDRESSES', $emails, array('class' => 'span7', 'rows' => 5))?>
<span class="help-inline"><?php echo t('One email address per line')?></span>
</div>
</div>

</fieldset>
</div>


<div class="ccm-pane-footer">
	<?php  echo $concrete_interface->submit(t('Save'),'Save','right', 'primary')?>
</div>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>