<?php  $form = Loader::helper('form'); ?>

<fieldset>
<legend><?php echo t('Paypal Information')?></legend>
<div class="clearfix">
	<?php echo $form->label('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL', t('Paypal Email'))?>
	<div class="input">
		<?php echo $form->text('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL', $PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL, array('class' => 'span6'))?>
		<span class="help-inline"><?php echo t('Required')?></span>
	</div>
</div>

<div class="clearfix">
	<label><?php echo t('Test Mode')?></label>
	<div class="input">
		<ul class="inputs-list">
			<li><label><?php echo $form->radio('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE', 'test', $PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE != 'live')?> <span><?php echo t('Test Mode')?></span></label></li>
			<li><label><?php echo $form->radio('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE', 'live', $PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE == 'live')?> <span><?php echo t('Live')?></span></label></li>
		</ul>
	</div>
</div>

<div class="clearfix">
	<label><?php echo t('Transaction Type')?></label>
	<div class="input">
		<ul class="inputs-list">
			<li><label><?php echo $form->radio('PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE', 'authorization', $PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE != 'sale')?> <span><?php echo t('Authorization')?></span></label></li>
			<li><label><?php echo $form->radio('PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE', 'sale', $PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE == 'sale')?> <span><?php echo t('Sale')?></span></label></li>
		</ul>
	</div>
</div>

<div class="clearfix">
	<label><?php echo t('Address')?></label>
	<div class="input">
		<ul class="inputs-list">
			<li><label><?php echo $form->radio('PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS', 'shipping', $PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS == 'shipping')?> <span><?php echo t('Shipping (if available)')?></span></label></li>
			<li><label><?php echo $form->radio('PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS', 'billing', $PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS != 'shipping')?> <span><?php echo t('Billing')?></span></label></li>
		</ul>
	</div>
</div>

<div class="clearfix">
	<?php echo $form->label('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE', t('Currency'))?>
	<div class="input">
		<?php echo $form->select('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE', $paypal_currency_codes, $PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE);?>
	</div>
</div>

</fieldset>