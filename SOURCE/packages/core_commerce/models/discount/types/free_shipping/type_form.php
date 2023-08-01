<?php  $form = Loader::helper('form'); ?>

<fieldset>
<legend><?php echo t('Free Shipping Details')?></legend>

<div class="clearfix">
	<?php echo $form->label('minimumPurchase', t('Minimum Purchase'))?>
	<div class="input">
			<?php echo $form->text('minimumPurchase', $minimumPurchase, array('class' => 'span4'))?>
		<span class="help-inline"><?php echo t('Required')?></span>
	</div>
</div>

<div class="clearfix">
	<?php echo $form->label('shippingMethod', t('Shipping Method'))?>
	<div class="input">
		<?php  print $form->select('shippingMethod', $methods, $shippingMethod); ?>
	</div>
</div>

</fieldset>