<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

if(!is_array($exclude_sections)) {
	$exclude_sections = array();
}

Loader::model('payment/method', 'core_commerce');
Loader::model('order/product', 'core_commerce');

$method = "";
$pm = CoreCommercePaymentMethod::getByID($order->getOrderPaymentMethodID());
if ($pm) {
	$method = $pm->getPaymentMethodName();
}

$products = CoreCommerceOrderProduct::getByOrderID($order->getOrderID());
$adjustments = $order->getOrderLineItems();
$billing = $order->getAttribute('billing_address');
$shipping = $order->getAttribute('shipping_address');
$bill_attr = AttributeSet::getByHandle('core_commerce_order_billing');
$ship_attr = AttributeSet::getByHandle('core_commerce_order_shipping');
$form = Loader::helper('form');


?>
	<div class="ccm-order-header">
		<div class="ccm-core-commerce-order-billing" style="width:48%; float:left;">
		<?php  if(!in_array('billing',$exclude_sections)) { ?>
		<h4><span><?php echo ($shipping||$ship_attr)?t('Billing Information'):t('Billing/Shipping Information')?></span></h4>
	
		<table width="100%">
		<tr><td width="20%"><label><?php echo t('First Name')?>:</label></td><td><?php echo $order->getAttribute('billing_first_name')?></td></tr>
		<tr><td><label><?php echo t('Last Name')?>:</label></td><td><?php echo $order->getAttribute('billing_last_name')?></td></tr>
		<tr><td><label><?php echo t('Email')?>:</label></td><td><?php echo $order->getOrderEmail()?></td></tr>
	    <tr><td><label><?php echo t('Address1')?>:</label></td><td><?php echo $billing->getAddress1()?></td></tr>
	    <tr><td><label><?php echo t('Address2')?>:</label></td><td><?php echo $billing->getAddress2()?></td></tr>
	    <tr><td><label><?php echo t('City')?>:</label></td><td><?php echo $billing->getCity()?></td></tr>
	    <tr><td><label><?php echo t('State/Province')?>:</label></td><td><?php echo $billing->getStateProvince()?></td></tr>
	    <tr><td><label><?php echo t('Zip/Postal Code')?>:</label></td><td><?php echo $billing->getPostalCode()?></td></tr>
	    <tr><td><label><?php echo t('Country')?>:</label></td><td><?php echo $billing->getCountry()?></td></tr>
		<tr><td><label><?php echo t('Phone')?>:</label></td><td><?php echo $order->getAttribute('billing_phone')?></td></tr>
		<?php  if ($bill_attr) {
			$akHandles = array('billing_first_name', 'billing_last_name', 'billing_address', 'billing_phone');
	    	$keys = $bill_attr->getAttributeKeys();
	    	foreach($keys as $ak) {
				if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
	            	print '<tr><td><label>' . $ak->getAttributeKeyName() . ':</label></td><td>' . $order->getAttribute($ak) . '</td>';
				}
	    	}
		} ?>
		</table>
		<?php  } ?>
		</div>
		
		
		<div class="ccm-core-commerce-order-shipping" style="width:48%; float:left; margin-left: 20px">
		
			<?php  if (!in_array('billing',$exclude_sections) && ($shipping || $ship_attr)) { ?>
			<h4><span><?php echo t('Shipping Information');?></span></h4>
			<table width="100%">
			<?php  if ($shipping) { ?>
			<tr><td width="20%"><label><?php echo  t('First Name')?>:</label></td><td><?php echo $order->getAttribute('shipping_first_name')?></td></tr>
			<tr><td><label><?php echo  t('Last Name')?>:</label></td><td><?php echo $order->getAttribute('shipping_last_name')?></td></tr>
		    <tr><td><label><?php echo  t('Address1')?>:</label></td><td><?php echo $shipping->getAddress1()?></td></tr>
		    <tr><td><label><?php echo  t('Address2')?>:</label></td><td><?php echo $shipping->getAddress2()?></td></tr>
		    <tr><td><label><?php echo  t('City')?>:</label></td><td><?php echo $shipping->getCity()?></td></tr>
		    <tr><td><label><?php echo  t('State/Province')?>:</label></td><td><?php echo $shipping->getStateProvince()?></td></tr>
		    <tr><td><label><?php echo  t('Zip/Postal Code')?>:</label></td><td><?php echo $shipping->getPostalCode()?></td></tr>
		    <tr><td><label><?php echo  t('Country')?>:</label></td><td><?php echo $shipping->getCountry()?></td></tr>
			<tr><td><label><?php echo  t('Phone')?>:</label></td><td><?php echo $order->getAttribute('shipping_phone')?></td></tr>
			<?php  } ?>
			<?php  if ($ship_attr) {
				$akHandles = array('shipping_first_name', 'shipping_last_name', 'shipping_address', 'shipping_phone');
		    	$keys = $ship_attr->getAttributeKeys();
		    	foreach($keys as $ak) {
					if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
		            	print '<tr><td><label>' . $ak->getAttributeKeyName() . ':</label></td><td>' . $order->getAttribute($ak) . '</td>';
					}
		    	}
			} ?>
			</table>
			<?php  } ?>
		</div>

		<hr style="clear:both"/>
	</div>
	
	<table width="100%">
	<?php  if(!in_array('order_number',$exclude_sections)) {?>
	<tr>
		<td width="10%"><?php echo t('Order #')?>:</td>
 		<td width="90%"><?php echo $order->getInvoiceNumber()?></td>
	</tr>
	<?php  } ?>
	<?php  if(!in_array('products',$exclude_sections)) {?>
	<tr>
		<td valign="top"><?php  echo t('Products')?>:</td>
		<td>
			<table width="100%" class="ccm-results-list" cellspacing="0" cellpadding="0">
			<tr>
				<th width="5%"><?php  echo t('ID')?></th>
				<th width="20%"><?php  echo t('Name')?></th>
				<th width="8%"><?php  echo t('Price')?></th>
				<th width="7%"><?php  echo t('Quantity')?></th>
				<th width="60%"><?php  echo t('Options')?></th>
			</tr>
			<?php  $i = 0; foreach ($products as $op) { ?>
			<tr class="ccm-list-record<?php echo ($i++%2)?'-alt':''?>">
				<td><?php echo $op->getProductID()?></td>
				<td><?php echo $op->getProductName()?></td>
				<td><?php echo $op->getProductCartDisplayPrice()?></td>
				<td><?php echo $op->getQuantity()?></td>
				<td>
					<?php  if (is_object($op->product)) { ?>
						<?php  $attribs = $op->getProductConfigurableAttributes() ?>
						<?php  $text = ''; foreach($attribs as $ak) { ?>
							<?php  $text .= $ak->render('label','',true) . ": " . $op->getAttribute($ak, 'display')."," ?>
						<?php  } ?>
						<?php echo  rtrim($text, ",") ?>
					<?php  } else { ?>
						<?php echo t('Unknown. This product has been removed.')?>
					<?php  } ?>
				</td>
			</tr>
			<?php  } ?>
			</table>
		</td>
	</tr>
	<?php  } ?>
	<?php  if(!in_array('adjustments',$exclude_sections) && count($adjustments) > 0) {?>
	<tr>
		<td valign="top"><?php  echo t('Adjustments')?>:</td>
		<td>
			<table width="100%" class="ccm-results-list" cellspacing="0" cellpadding="0">
			<tr>
         <th width="25%"><?php  echo t('Name') ?></th>
            <th width="75%"><?php  echo t('Amount') ?></th>
			</tr>
			<?php  $i = 0; foreach ($adjustments as $adj) { ?>
			<tr class="ccm-list-record<?php echo ($i++%2)?'-alt':''?>">
				<td><?php echo $adj->getLineItemName()?></td>
				<td><?php echo $adj->getLineItemDisplayTotal()?></td>
			</tr>
			<?php  } ?>
			</table>
		</td>
	</tr>
	<?php  } ?>
	<?php  if(!in_array('total',$exclude_sections)) { ?>
	<tr>
		<td><?php  echo t('Total') ?>:</td>
		<td><?php echo $order->getOrderDisplayTotal()?></td>
	</tr>
	<?php  } ?>
	
	<?php  if(!in_array('payment_method',$exclude_sections)) { ?>
	<tr>
		<td><?php  echo t('Payment Method')?>:</td>
		<td><?php echo $method?></td>
	</tr>
	<?php  } ?>
	
	<?php  if (!in_array('user_account',$exclude_sections) && $order->getOrderUserID() > 0) { ?>
	<tr>
		<td><?php echo t('User Account:')?></td>
		<?php  $ui = UserInfo::getByID($order->getOrderUserID()); ?>
		<td><a href="<?php echo View::url('/dashboard/users/search?uID=' . $ui->getUserID())?>"><?php echo $ui->getUserName()?></a></td>
	</tr>
	<?php  } ?>
	
	</table>
