<form method="post" action="<?php echo $action?>">
	<?php 
	if(Loader::helper('multilingual','core_commerce')->isEnabled()) {
		$language = Loader::helper('default_language','multilingual')->getSessionDefaultLocale();
	} else {
		$language = "";
	}
	$methods = $o->getAvailablePaymentMethods($language);
	if (count($methods) > 0) { ?>
		<?php  foreach($methods as $sm) { ?>
			<div class="ccm-core-commerce-checkout-form-payment-method-option">
				<?php echo $form->radio('paymentMethodID', $sm->getPaymentMethodID(), $sm->getPaymentMethodID() == $o->getOrderPaymentMethodID())?>
				<?php echo $sm->getPaymentMethodName()?>
			</div>
		<?php  } ?>
	<?php  } else { ?>
		<?php echo t('Payment is Unavailable.');?>
	<?php  } ?>
	
	<div class="ccm-core-commerce-cart-buttons">
		<?php echo $this->controller->getCheckoutNextStepButton(t('Purchase'))?>
		<?php echo $this->controller->getCheckoutPreviousStepButton()?>
	</div>
	<div class="ccm-spacer"></div>
</form>