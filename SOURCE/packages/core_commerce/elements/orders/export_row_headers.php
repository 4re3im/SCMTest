<?php  
echo "<tr>";
$billing = $o->getAttribute('billing_address');
$shipping = $o->getAttribute('shipping_address');
Loader::model('attribute/categories/core_commerce_product_option','core_commerce');
$bill_attr = AttributeSet::getByHandle('core_commerce_order_billing');
$ship_attr = AttributeSet::getByHandle('core_commerce_order_shipping');
//$adjustments = $o->getOrderLineItems();

	echo "<td><b>".t('Order #')."</b></td>";
	echo "<td><b>".t('Order Status')."</b></td>";
	echo "<td><b>".t('Date / Time Placed')."</b></td>";
	echo "<td><b>".t('E-Mail')."</b></td>";
	echo "<td><b>".t('Final Cost')."</b></td>";
	echo "<td><b>".t('Products Total')."</b></td>";
	echo "<td><b>".t('Shipping Total')."</b></td>";
	echo "<td><b>".t('Payment Method')."</b></td>";
//	echo "<td><b>".t('Shipping Method')."</b></td>";//Fatal error: Class 'CoreCommerceCurrentOrder' not found in /Users/gregjoyce/concrete/svn/concrete5-addons/trunk/web/applications/core_commerce/models/shipping/method.php on line 49
/*	if($adjustments)
		foreach($adjustments as $adj){
			echo "<td><b>".$adj->getLineItemName()."</b></td>";
		}*/
	echo "<td><b>".t('Quantity Ordered')."</b></td>";



	//Billing Address
	$prefix = (($shipping||$ship_attr)?t('Billing'):t('Bill / Ship'))." ";
	echo "<td><b>".$prefix. t('First Name')."</b></td>";
	echo "<td><b>".$prefix. t('Last Name')."</b></td>";
	echo "<td><b>".$prefix. t('Address1')."</b></td>";
	echo "<td><b>".$prefix. t('Address2')."</b></td>";
	echo "<td><b>".$prefix. t('City')."</b></td>";
	echo "<td><b>".$prefix. t('Postal Code')."</b></td>";
	echo "<td><b>".$prefix. t('State/Province')."</b></td>";
	echo "<td><b>".$prefix. t('Country')."</b></td>";
	echo "<td><b>".$prefix. t('Phone')."</b></td>";
	if ($bill_attr) {
		$akHandles = array('billing_first_name', 'billing_last_name', 'billing_address', 'billing_phone');
		$keys = $bill_attr->getAttributeKeys();
			foreach($keys as $ak) {
				if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
					echo "<td><b>". $ak->getAttributeKeyName(). "</b></td>";
			  }
		  }
	}

	
	//Shiping Address
	if ($shipping || $ship_attr) {
		$prefix = t('Shipping'). " ";
		echo "<td><b>".$prefix. t('First Name')."</b></td>";
		echo "<td><b>".$prefix. t('Last Name')."</b></td>";
		echo "<td><b>".$prefix. t('Address1')."</b></td>";
		echo "<td><b>".$prefix. t('Address2')."</b></td>";
		echo "<td><b>".$prefix. t('City')."</b></td>";
		echo "<td><b>".$prefix. t('Postal Code')."</b></td>";
		echo "<td><b>".$prefix. t('State/Province')."</b></td>";
		echo "<td><b>".$prefix. t('Country')."</b></td>";
		echo "<td><b>".$prefix. t('Phone')."</b></td>";
	}
	if ($ship_attr) {
		$akHandles = array('shipping_first_name', 'shipping_last_name', 'shipping_address', 'shipping_phone');
		$keys = $ship_attr->getAttributeKeys();
			foreach($keys as $ak) {
				if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
					echo "<td><b>". $ak->getAttributeKeyName(). "</b></td>";
			  }
		  }
	}

	echo "<td><b>".t('Product ID')."</b></td>";
	echo "<td><b>".t('Product Name')."</b></td>";
	echo "<td><b>".t('Product Price')."</b></td>";
	echo "<td><b>".t('Product Quantity')."</b></td>";

	$attribs = CoreCommerceProductOptionAttributeKey::getExportList(); 
	if(is_array($attribs) && count($attribs)) {
		foreach($attribs as $ak) {
			echo "<td><b>". $ak->getAttributeKeyName()."</b></td>"; 
		}
	}
echo"</tr>";
