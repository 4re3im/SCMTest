<?php  $form = Loader::helper('form'); ?>

<div class="clearfix">
<label><?php echo t('Send Receipt Email')?></label>
<div class="input">
	<ul class="inputs-list">
		<li><label><?php echo $form->radio('PAYMENT_METHOD_DEFAULT_EMAIL_RECEIPT', 'true', $PAYMENT_METHOD_DEFAULT_EMAIL_RECEIPT == 'true')?> <span><?php echo t('Yes')?></span></label></li>
		<li><label><?php echo $form->radio('PAYMENT_METHOD_DEFAULT_EMAIL_RECEIPT', 'false', $PAYMENT_METHOD_DEFAULT_EMAIL_RECEIPT != 'true')?> <span><?php echo t('No')?></span></label></li>
	</ul>
</div>
</div>

<div class="clearfix">
<label><?php echo t('Transaction Type')?></label>
<div class="input">
	<ul class="inputs-list">
		<li><label><?php echo $form->radio('PAYMENT_METHOD_DEFAULT_TRANSACTION_TYPE', 'authorization', $PAYMENT_METHOD_DEFAULT_TRANSACTION_TYPE == 'authorization')?> <span><?php echo t('Authorization')?></span></label></li>
		<li><label><?php echo $form->radio('PAYMENT_METHOD_DEFAULT_TRANSACTION_TYPE', 'sale', $PAYMENT_METHOD_DEFAULT_TRANSACTION_TYPE != 'authorization')?> <span><?php echo t('Sale')?></span></label></li>
	</ul>
</div>
</div>