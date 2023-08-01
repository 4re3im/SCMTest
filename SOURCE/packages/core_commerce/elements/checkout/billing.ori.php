<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<form method="post" action="<?php echo $action?>">
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="25%"><label for="oEmail"><?php echo t('Email Address')?> <span class="ccm-required">*</span></label><?php echo $form->text('oEmail', $o->getOrderEmail())?></td>
		<td width="25%">
			<?php 
			$ak = CoreCommerceOrderAttributeKey::getByHandle('billing_first_name');
			echo $form_attribute->display($ak, $ak->isOrderAttributeKeyRequired());
			?>
		</td>
		<td width="25%">
			<?php 
			$ak = CoreCommerceOrderAttributeKey::getByHandle('billing_last_name');
			echo $form_attribute->display($ak, $ak->isOrderAttributeKeyRequired());
			?>
		</td>
		<td width="25%">
			<?php 
			$ak = CoreCommerceOrderAttributeKey::getByHandle('billing_phone');
			echo $form_attribute->display($ak, $ak->isOrderAttributeKeyRequired());
			?>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<?php 
			$ak = CoreCommerceOrderAttributeKey::getByHandle('billing_address');
			echo $form_attribute->display($ak, $ak->isOrderAttributeKeyRequired());
			?>
			</td>
	</tr>
	<?php 
	$set = AttributeSet::getByHandle('core_commerce_order_billing');
	if (is_object($set)) { 
		$keys = $set->getAttributeKeys();
		foreach($keys as $ak) {
			if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) { ?>	
				<tr>
					<td colspan="4"><?php echo $form_attribute->display($ak->getAttributeKeyHandle(), $ak->isOrderAttributeKeyRequired())?></td>
				</tr>
			<?php  }
		}
	} ?>
	</table>
	<?php  
	$u = new User();
	if($u->isRegistered()) { ?>
	<div class="ccm-core-commerce-profile-address-save">
		<label><?php echo t('Save Info For Future Purchases')?><?php  echo $form->checkbox('save_profile',1)?></label>
	</div>
	<?php  } ?>
	<div class="ccm-core-commerce-cart-buttons">
	<?php echo $this->controller->getCheckoutNextStepButton()?>
	<?php echo $this->controller->getCheckoutPreviousStepButton()?>
	</div>
	<div class="ccm-spacer"></div>
</form>
