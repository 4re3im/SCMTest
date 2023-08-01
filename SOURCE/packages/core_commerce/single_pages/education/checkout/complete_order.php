<?php  
$form_attribute->setAttributeObject($o);

$steps = $this->controller->getSteps();
?>
<div id="ccm-core-commerce-checkout-one-page">

<?php  Loader::packageElement('cart_item_list', 'core_commerce', array('edit' => false)) ?>

<div id="ccm-core-commerce-checkout-form-billing" class="ccm-core-commerce-checkout-form">
	<h1><?php echo t('Billing Information')?></h1>
	
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="25%">
			<label for="oEmail"><?php echo t('Email Address')?> 
				<span class="ccm-required">*</span>
			</label>
			<?php echo $form->text('oEmail', $o->getOrderEmail())?>
		</td>
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
		$i = 0;
		foreach($keys as $ak) {
			if ($i % 3 == 0) {
				print '<tr>';
			}
			if (!in_array($ak->getAttributeKeyHandle(), $billing_akHandles)) { ?>	
				<tr>
					<td colspan="4"><?php echo $form_attribute->display($ak->getAttributeKeyHandle(), $ak->isOrderAttributeKeyRequired())?></td>
				</tr>
			<?php  }
		}
	} ?>
	</table>
</div>


<div id="ccm-core-commerce-checkout-form-billing" class="ccm-core-commerce-checkout-form">
	<h1><?php echo t('Shipping Information')?></h1>
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="3">
			<?php echo $form->checkbox('useBillingAddressForShipping', 1, $useBillingAddressForShipping); ?>
			<?php echo $form->label('useBillingAddressForShipping', t('Use Billing Address'))?>
			<?php echo $form->hidden('useBillingAddressAction', $this->url('/checkout/shipping/address', 'update_shipping_to_billing'))?>
		</td>
	</tr>
	
	<tr>
		<td>
			<?php  
			$ak = CoreCommerceOrderAttributeKey::getByHandle('shipping_first_name');
			echo $form_attribute->display($ak, $ak->isOrderAttributeKeyRequired()); 
			?>
		</td>
		<td>
			<?php  
			$ak = CoreCommerceOrderAttributeKey::getByHandle('shipping_last_name');
			echo $form_attribute->display($ak, $ak->isOrderAttributeKeyRequired()); 
			?>
		</td>
		<td>
			<?php  
			$ak = CoreCommerceOrderAttributeKey::getByHandle('shipping_phone');
			echo $form_attribute->display($ak, $ak->isOrderAttributeKeyRequired()); 
			?>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<?php  
			$ak = CoreCommerceOrderAttributeKey::getByHandle('shipping_address');
			echo $form_attribute->display($ak, $ak->isOrderAttributeKeyRequired()); 
			?>
		</td>
	</tr>
	<?php 
	$set = AttributeSet::getByHandle('core_commerce_order_shipping');
	if (is_object($set)) { 
		$keys = $set->getAttributeKeys();
		foreach($keys as $ak) {
			if (!in_array($ak->getAttributeKeyHandle(), $shipping_akHandles)) { ?>
				<tr>
					<td colspan="3"><?php echo $form_attribute->display($ak->getAttributeKeyHandle())?></td>
				</tr>
			<?php  }
		}
	} ?>
	</table>

</div>
Shipping Method

<div id="ccm-core-commerce-checkout-form-shipping-method" class="ccm-core-commerce-checkout-form">

<?php  if (count($methods) > 0) { ?>
	<?php  foreach($methods as $sm) { 
		
		$type = $sm->getShippingType();
		if (!isset($typeID) || ($typeID != $type->getShippingTypeID())) { ?>
			<strong><?php echo $type->getShippingTypeName()?></strong><br/>
		<?php  } ?>
		
		
		<div class="ccm-core-commerce-checkout-form-shipping-method-option">
			<?php echo $form->radio('shippingMethodID', $sm->getID(), $order->getOrderShippingMethodID())?>
			<?php echo $sm->getName()?> <?php  if ($sm->getPrice() > 0) {  print t('(Cost: <strong>%s</strong>)', $sm->getDisplayPrice()); } else { print t('Cost: <strong>Free</strong>'); }  ?>
		</div>
	
		<?php  $typeID = $type->getShippingTypeID(); ?>
		
	<?php  } ?>
<?php  } else { ?>
	<?php echo t('Shipping is Unavailable.');?>
<?php  } ?>
</div>


Payment Info
</div>