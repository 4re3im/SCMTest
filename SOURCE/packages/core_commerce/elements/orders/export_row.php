<?php    
echo "<tr>";
$billing = $o->getAttribute('billing_address');
$shipping = $o->getAttribute('shipping_address');
$bill_attr = AttributeSet::getByHandle('core_commerce_order_billing');
$ship_attr = AttributeSet::getByHandle('core_commerce_order_shipping');
$shipping_method =  $o->getAttribute('total_shipping');
//$adjustments = $o->getOrderLineItems();
	echo "<td>".$o->getInvoiceNumber()."</td>";
	echo "<td>".$o->getOrderStatusText()."</td>";
	echo "<td>".$o->getOrderDateAdded()."</td>";
	echo "<td>".$o->getOrderEmail()."</td>";
	echo "<td>".$o->getOrderDisplayTotal()."</td>";
	echo "<td>".$o->getBaseOrderDisplayTotal()."</td>";
	echo "<td>".(strlen($shipping_method) ? $shipping_method : t('None.'))."</td>";
	echo "<td>".$o->getOrderPaymentMethod()->paymentMethodName."</td>";
//	echo "<td>".$o->getOrderShippingMethod()."</td>"; // Fatal error: Class 'CoreCommerceCurrentOrder' not found in /Users/gregjoyce/concrete/svn/concrete5-addons/trunk/web/applications/core_commerce/models/shipping/method.php on line 49
	/*if($adjustments)
		foreach($adjustments as $adj){
			echo "<td>".$adj->getLineItemDisplayTotal()."</td>";
		}*///leaving the adjustments out for now, they don't line up right.
	echo "<td>".$o->getTotalProducts()."</td>";



	//Billing Address
	echo "<td>".$o->getAttribute('billing_first_name')."</td>";
	echo "<td>".$o->getAttribute('billing_last_name')."</td>";
	echo "<td>".$billing->getAddress1()."</td>";
	echo "<td>".$billing->getAddress2()."</td>";
	echo "<td>".$billing->getCity()."</td>";
	echo "<td>".$billing->getPostalCode()."</td>";
	echo "<td>".$billing->getStateProvince()."</td>";
	echo "<td>".$billing->getCountry()."</td>";
	echo "<td>".$o->getAttribute('billing_phone')."</td>";
	if ($bill_attr) {
		$akHandles = array('billing_first_name', 'billing_last_name', 'billing_address', 'billing_phone');
		$keys = $bill_attr->getAttributeKeys();
			foreach($keys as $ak) {
				if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
					echo "<td>". $o->getAttribute($ak). "</td>";
			  }
		  }
	}

	
	//Shiping Address
	if ($shipping || $ship_attr) {
		echo "<td>".$o->getAttribute('shipping_first_name')."</td>";
		echo "<td>".$o->getAttribute('shipping_last_name')."</td>";
		if ($shipping) {
			echo "<td>".$shipping->getAddress1()."</td>";
			echo "<td>".$shipping->getAddress2()."</td>";
			echo "<td>".$shipping->getCity()."</td>";
			echo "<td>".$shipping->getPostalCode()."</td>";
			echo "<td>".$shipping->getStateProvince()."</td>";
			echo "<td>".$shipping->getCountry()."</td>";
         echo "<td>".$o->getAttribute('shipping_phone')."</td>";
		} else {
			echo('<td></td>');
			echo('<td></td>');
			echo('<td></td>');
			echo('<td></td>');
			echo('<td></td>');
			echo('<td></td>');
			echo('<td></td>');
		}			
		if ($ship_attr) {
			$akHandles = array('shipping_first_name', 'shipping_last_name', 'shipping_address', 'shipping_phone');
			$keys = $ship_attr->getAttributeKeys();
				foreach($keys as $ak) {
					if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
          
						echo "<td>". $o->getAttribute($ak). "</td>";
				  }
			  }
		}
	}

	echo "<td>" . $p->getProductID() . "</td>";
	echo "<td>" . $p->getProductName() . "</td>";
	echo "<td>" . $p->getProductCartPrice() . "</td>";
	echo "<td>" . $p->getQuantity() . "</td>";
		

	//headers for the other kinds of data see 'dashboard/core_commerce/orders/search/detail/{orderno}/
	$attribs = CoreCommerceProductOptionAttributeKey::getExportList(); 
	if(is_array($attribs) && count($attribs)) {
		foreach($attribs as $ak) {
			echo "<td>". $p->getAttribute($ak)."</td>"; 
		}
	}

echo"</tr>";
