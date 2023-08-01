<?php  $form = Loader::helper('form'); ?>

<fieldset>
<legend><?php echo t('Authorize.net SIM Information')?></legend>

<div class="clearfix">
<?php echo $form->label('PAYMENT_METHOD_AUTHORIZENET_SIM_API_LOGIN', t('API Login ID'))?>
<div class="input">
<?php echo $form->text('PAYMENT_METHOD_AUTHORIZENET_SIM_API_LOGIN', $PAYMENT_METHOD_AUTHORIZENET_SIM_API_LOGIN)?>
<span class="help-inline"><?php echo t('Required')?></span>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_KEY', t('Transaction Key'))?>
<div class="input">
<?php echo $form->text('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_KEY', $PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_KEY)?>
<span class="help-inline"><?php echo t('Required')?></span>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('PAYMENT_METHOD_AUTHORIZENET_SIM_MD5_SECRET', t('MD5 Secret'))?>
<div class="input">
	<?php echo $form->text('PAYMENT_METHOD_AUTHORIZENET_SIM_MD5_SECRET', $PAYMENT_METHOD_AUTHORIZENET_SIM_MD5_SECRET, array('maxlength' => 18))?>
	<span class="help-inline"><?php echo t('Required')?></span>	
</div>
</div>

<div class="clearfix">
<label><?php echo t('Test Mode')?></label>
<div class="input">
	<ul class="inputs-list">
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE', 'test-account', $PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE == '' ||
		                                                                             $PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE == 'test-account')?> <span><?php echo t('Test Account')?></span></label></li>
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE', 'test-mode', $PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE == 'test-mode')?> <span><?php echo t('Live (Test Mode)')?></span></label></li>
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE', 'live', $PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE == 'live')?> <span><?php echo t('Live')?></span></label></li>
	</ul>
</div>
</div>

<div class="clearfix">
<label><?php echo t('Transaction Type')?></label>
<div class="input">
	<ul class="inputs-list">
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_TYPE', 'authorization', $PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_TYPE != 'sale')?> <span><?php echo t('Authorization')?></span></label></li>
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_TYPE', 'sale', $PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_TYPE == 'sale')?> <span><?php echo t('Sale')?></span></label></li>
	</ul>
</div>
</div>

<div class="clearfix">
<label><?php echo t('Send Receipt Email')?></label>
<div class="input">
	<ul class="inputs-list">
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_SIM_EMAIL_RECEIPT', 'true', $PAYMENT_METHOD_AUTHORIZENET_SIM_EMAIL_RECEIPT == 'true')?> <span><?php echo t('Yes')?></span></label></li>
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_SIM_EMAIL_RECEIPT', 'false', $PAYMENT_METHOD_AUTHORIZENET_SIM_EMAIL_RECEIPT != 'true')?> <span><?php echo t('No')?></span></label></li>
	</ul>
</div>
</div>

</fieldset>