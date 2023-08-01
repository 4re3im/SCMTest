<?php  
$form = Loader::helper('form'); 
$ih = Loader::helper("concrete/interface");
$valt = Loader::helper('validation/token');
$mls = Loader::helper('multilingual','core_commerce');
?>

<fieldset>
<legend><?php echo t('Details')?></legend>
<div class="clearfix">
<label><?php echo t('Handle')?></label>
<div class="input">
	<input type="text" disabled="disabled" class="span4" value="<?php echo $method->getPaymentMethodHandle()?>" />
</div>
</div>

<div class="clearfix">
<label><?php echo t('Name')?></label>
<div class="input">
	<?php echo $form->text('paymentMethodName', $method->getPaymentMethodName(), array('class' => 'span6'))?>
</div>
</div>

<?php  if($mls->isEnabled()) { ?>

<div class="clearfix">
<?php echo $form->label('paymentMethodLanguage', t('Language'))?>
<div class="input">
	<?php  echo $form->select('paymentMethodLanguage',$mls->getSectionSelectArray(),$method->getPaymentMethodLanguage()); ?>
</div>
</div>

<?php  } ?>

<div class="clearfix">
<?php echo $form->label('paymentMethodIsEnabled', t('Enabled'))?>
<div class="input"><?php 
		print $form->select('paymentMethodIsEnabled', array('0' => t('No'), '1' => t('Yes')), $method->isPaymentMethodEnabled());
	?></div>
</div>

</fieldset>

<?php echo $form->hidden('paymentMethodID', $method->getPaymentMethodID())?>
<?php echo $valt->output('update_payment_method')?>

<?php  $method->render('method_form'); ?>