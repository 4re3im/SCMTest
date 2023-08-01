<?php  $form = Loader::helper('form'); ?>

<fieldset>
<legend><?php echo t('Authorize.net AIM Information')?></legend>

<div class="clearfix">
<?php echo $form->label('PAYMENT_METHOD_AUTHORIZENET_AIM_API_LOGIN', t('API Login ID'))?>
<div class="input">
<?php echo $form->text('PAYMENT_METHOD_AUTHORIZENET_AIM_API_LOGIN', $PAYMENT_METHOD_AUTHORIZENET_AIM_API_LOGIN)?>
<span class="help-inline"><?php echo t('Required')?></span>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_KEY', t('Transaction Key'))?>
<div class="input">
<?php echo $form->text('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_KEY', $PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_KEY)?>
<span class="help-inline"><?php echo t('Required')?></span>
</div>
</div>

<div class="clearfix">
<label><?php echo t('Card Code Verification (CCV)')?></label>
<div class="input">
	<ul class="inputs-list">
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_AIM_CCV', 'true', $PAYMENT_METHOD_AUTHORIZENET_AIM_CCV == 'true')?> <span><?php echo t('Enabled')?></span></label></li>
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_AIM_CCV', 'false', $PAYMENT_METHOD_AUTHORIZENET_AIM_CCV != 'true')?> <span><?php echo t('Disabled')?></span></label></li>
	</ul>
</div>
</div>

<div class="clearfix">
<label><?php echo t('Test Mode')?></label>
<div class="input">
	<ul class="inputs-list">
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_AIM_TEST_MODE', 'test-account', $PAYMENT_METHOD_AUTHORIZENET_AIM_TEST_MODE == '' ||
		                                                                             $PAYMENT_METHOD_AUTHORIZENET_AIM_TEST_MODE == 'test-account')?> <span><?php echo t('Test Account')?></span></label></li>
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_AIM_TEST_MODE', 'test-mode', $PAYMENT_METHOD_AUTHORIZENET_AIM_TEST_MODE == 'test-mode')?> <span><?php echo t('Live (Test Mode)')?></span></label></li>
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_AIM_TEST_MODE', 'live', $PAYMENT_METHOD_AUTHORIZENET_AIM_TEST_MODE == 'live')?> <span><?php echo t('Live')?></span></label></li>
	</ul>
</div>
</div>

<div class="clearfix">
<label><?php echo t('Transaction Type')?></label>
<div class="input">
	<ul class="inputs-list">
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_TYPE', 'authorization', $PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_TYPE != 'sale')?> <span><?php echo t('Authorization')?></span></label></li>
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_TYPE', 'sale', $PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_TYPE == 'sale')?> <span><?php echo t('Sale')?></span></label></li>
	</ul>
</div>
</div>

<div class="clearfix">
<label><?php echo t('Send Receipt Email')?></label>
<div class="input">
	<ul class="inputs-list">
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_AIM_EMAIL_RECEIPT', 'true', $PAYMENT_METHOD_AUTHORIZENET_AIM_EMAIL_RECEIPT == 'true')?> <span><?php echo t('Yes')?></span></label></li>
		<li><label><?php echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_AIM_EMAIL_RECEIPT', 'false', $PAYMENT_METHOD_AUTHORIZENET_AIM_EMAIL_RECEIPT != 'true')?> <span><?php echo t('No')?></span></label></li>
	</ul>
</div>
</div>

</fieldset>