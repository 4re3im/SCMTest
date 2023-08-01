<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<form method="post" action="<?php echo $action?>">
	<?php 
	$methods = $o->getAvailableShippingMethods();
	if (count($methods) > 0) { ?>
		<?php  foreach($methods as $sm) {
			$type = $sm->getShippingType();
			if (!isset($typeID) || ($typeID != $type->getShippingTypeID())) { ?>
				<strong><?php echo $type->getShippingTypeName()?></strong><br/>
			<?php  } ?>

			<div class="ccm-core-commerce-checkout-form-shipping-method-option">
				<?php if ($sm->error !== false) {?>
					<?php echo $form->radio('null', $sm->getID(), $o->getOrderShippingMethodID(), array('disabled'=>'true'))?>
					<?php echo $sm->getName()?><span class='unavailable-method'><?php echo $sm->error?></span>
				<?php  } else { ?>
					<?php echo $form->radio('shippingMethodID', $sm->getID(), $o->getOrderShippingMethodID())?>
					<?php echo $sm->getName()?> <?php  if ($sm->getPrice() > 0) {  print t('(Cost: <strong>%s</strong>)', $sm->getDisplayPrice()); } else { print t('Cost: <strong>Free</strong>'); }  ?>
				<?php  } ?>
			</div>

			<?php  $typeID = $type->getShippingTypeID(); ?>

		<?php  } ?>
	<?php  } else { ?>
		<?php echo t('Shipping is Unavailable.');?>
	<?php  } ?>

	<div class="ccm-core-commerce-cart-buttons">
	<?php echo $this->controller->getCheckoutNextStepButton()?>
	<?php echo $this->controller->getCheckoutPreviousStepButton()?>
	</div>
	<div class="ccm-spacer"></div>
</form>