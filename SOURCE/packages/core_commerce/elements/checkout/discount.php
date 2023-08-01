<form method="post" action="<?php echo $action?>">
	<?php echo t('To claim a discount, enter a valid coupon code below. If you don\'t have a coupon code, just click "Next" to skip this step.')?>
	<?php echo $form->text('discount_code', $discount_code, array('style' => 'width: 150px'))?>&nbsp;&nbsp;
	<?php echo $this->controller->getCheckoutNextStepButton()?>
	<div class="ccm-core-commerce-cart-buttons">
	<?php echo $this->controller->getCheckoutPreviousStepButton()?>
	</div>
	<div class="ccm-spacer"></div>
</form>