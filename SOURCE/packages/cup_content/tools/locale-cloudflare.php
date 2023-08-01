<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

if(isset($_GET['license_key'])){
	$pkg  = Package::getByHandle('cup_content');
	$pkgID = $pkg->getPackageID();
	$store_value = $pkg->saveConfig('CUP_CONTENT_LICENSE', $_GET['license_key']);
	echo 'license update';
	/*
	$db = Loader::db();      
	$cup_content_store_value = $db->getOne('SELECT cfValue FROM Config WHERE cfKey = ? AND pkgID = ?', array('CUP_CONTENT_LICENSE', 'cup_content'));
	if($cup_content_store_value){
		$db->execute('UPDATE `Config` SET `cfValue` = ? WHERE `cfKey` = ?', array( $_GET['license_key'], 'CUP_CONTENT_LICENSE'));
	}else{
		$db->execute('INSERT INTO Config VALUES (?, NULL, ?, 0, ?)', array('CUP_CONTENT_LICENSE',  $_GET['license_key'], $pkgID));
	}
	*/
}




Loader::model('order/current','core_commerce');
Loader::model('title/model','cup_content');
Loader::model('attribute/categories/core_commerce_order','core_commerce');

Loader::model('sales/tax/rate', 'core_commerce');





/******************/
	function clearLineAttr($order){
		Loader::model('order/line_item', 'core_commerce');
		Loader::model('attribute/categories/core_commerce_order', 'core_commerce');
		$db = Loader::db();
		$akIDs = $db->GetCol("select CoreCommerceOrderAttributeValues.akID from CoreCommerceOrderAttributeValues inner join AttributeKeys on CoreCommerceOrderAttributeValues.akID = AttributeKeys.akID inner join AttributeTypes on AttributeKeys.atID = AttributeTypes.atID where orderID = ? and atHandle = ?", array($order->getOrderID(), CoreCommerceOrderAttributeKey::ORDER_ADJUSTMENT_ATTRIBUTE_TYPE_HANDLE));
		//$items = array();
		foreach($akIDs as $akID) {
			$ak = CoreCommerceOrderAttributeKey::getByID($akID);
			$val = $order->getAttributeValueObject($ak);
			if (is_object($val)) {
				//$items[] = $val->getValue();
				if (is_object($val)) {
					$val->delete();
				}
				$order->reindex();
			}
		}
	}


	function setupEnabledRates($order) {
		/*
		$address = $order->getAttribute('shipping_address');
		if (!is_object($address)) {
			$address = $order->getAttribute('billing_address');
		}
		if (!is_object($address)) {
			return false;
		}
		*/
		$list = CoreCommerceSalesTaxRate::getList();
		$order->clearAttribute('sales_tax');
		if(CoreCommerceOrderAttributeKey::getByHandle('other_tax')) {
			$order->clearAttribute('other_tax');
		}
		foreach($list as $rate) {
			$doTax = false;
			$amount = 0;
			
			if ($rate->isSalesTaxRateEnabled()) {
				//$doTax = true;
				/*
				if ($rate->getSalesTaxRateCountry() != '' && $rate->getSalesTaxRateStateProvince() && $rate->getSalesTaxRatePostalCode() != '') {
					// they have to be in all three
					$doTax = ($rate->getSalesTaxRateCountry() == $address->getCountry() && $rate->getSalesTaxRateStateProvince() == $address->getStateProvince() && fnmatch($rate->getSalesTaxRatePostalCode(), $address->getPostalCode()));
				} else if ($rate->getSalesTaxRateCountry() != '' && $rate->getSalesTaxRateStateProvince()) {
					$doTax = ($rate->getSalesTaxRateCountry() == $address->getCountry() && $rate->getSalesTaxRateStateProvince() == $address->getStateProvince());
				} else if ($rate->getSalesTaxRateCountry() != '') {
					$doTax = ($rate->getSalesTaxRateCountry() == $address->getCountry());
				}
				*/
				//echo "Rate: ",$rate->getSalesTaxRateCountry()."  ".$rate->getSalesTaxRateAmount()."\n";
				if ($rate->getSalesTaxRateCountry() != '') {
					$country_code = 'AU';
					if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_NZ') == 0){
						$country_code = 'NZ';
					}
					if($rate->getSalesTaxRateCountry() == $country_code){
						//echo "\t do TAX\n";
						$doTax = true;
					}
					//$doTax = ($rate->getSalesTaxRateCountry() == $country_code);
				}
			}
			
			$allItemsAreTaxable = true;
			if ($doTax) {
				foreach($order->getProducts() as $product) {
					if ($product->productRequiresSalesTax()) {
						$doTax = true;
					} else {
						$allItemsAreTaxable = false;
					}
				}
			}
			//check to see if the rate has a special set, make that flag true.
         //
         if ($doTax) {
            $specialSetID = $rate->getSalesTaxRateSpecialSet();
            if ($specialSetID > 0) {
               Loader::model('product/set','core_commerce');
               $specialSet = CoreCommerceProductSet::getByID($specialSetID);
               if(!($specialSet->contains($product->getProductObject()) && $product->productRequiresSalesTax())) {
                  $allItemsAreTaxable = false;
               }
            }
         }

			$ak = CoreCommerceOrderAttributeKey::getByHandle('tax_exempt_id');
			if($ak instanceof AttributeKey) {
				if(strlen($order->getAttribute($ak))) {
					$doTax = false;
				}
			}
			
			if ($doTax) {
				
				// tax handling methodologies - we only get here if we do in fact care about sales tax on this order
				
				// 1. Are all items in the cart taxable? If so, then we apply sales tax to the sub-total
				if ($allItemsAreTaxable) {
					$taxableAmount = $order->getDiscountedOrderTotal();
					//echo "\n\nA taxableAmount: ".$taxableAmount;
					//$amount += round(($rate->getSalesTaxRateAmount() / 100) * $amt, 2);
				} else {
					$taxableAmount = 0;
					if ($specialSetID > 0) {
						foreach($order->getProducts() as $product) {
							if(($specialSet->contains($product->getProductObject()) && $product->productRequiresSalesTax())) {
							$taxableAmount += $product->getProductQuantizedPrice();
						}
					  }
					}else{
						foreach($order->getProducts() as $product) {
							if ($product->productRequiresSalesTax()) {
								$taxableAmount += $product->getProductQuantizedPrice();
							}
						}
					}
					
					//echo "\n\nB taxableAmount: ".$taxableAmount;
					// now we loop through all discounts and subtract them taxable income
					// your discounts come out of taxable income first.
					$items = $order->getOrderLineItems();
					foreach($items as $it) {
						switch($it->getLineItemType()) {
							case '-':
								$taxableAmount -= $it->getLineItemTotal();
								break;
						}
					}
					//$amount += round(($rate->getSalesTaxRateAmount() / 100) * $taxableAmount, 2);				
				}
				//echo (string)$taxableAmount ." + ";
				
				if ($rate->includeShippingInSalesTaxRate()) {
        			// Get the shipping method name so we can pick it out of the line items
        			$shipMethod = $order->getOrderShippingMethod();
					if ($shipMethod) {
        				$shipMethodName = $order->getOrderShippingMethod()->getName();

        				// Get the shipping cost
        				$shippingPrice = 0.00;
        				$items = $order->getOrderLineItems();
        				foreach($items as $it) {
            				$itName = $it->getLineItemName();
            				if ($itName == $shipMethodName) {
                				$shippingPrice += $it->getLineItemTotal();
            				}
        				}

						// Add tax based on the shipping cost
						$taxableAmount += $shippingPrice;
						//$amount += round(($rate->getSalesTaxRateAmount() / 100) * $shippingPrice, 2);
					}
				}
				
			}
			//echo $shippingPrice." = ".$taxableAmount."\n";
			if ($doTax) {
				if ($rate->isSalesTaxIncludedInProduct()) {
					$amount += round($taxableAmount - ($taxableAmount/(1+($rate->getSalesTaxRateAmount() / 100))),2);
				}else{
					$amount += round(($rate->getSalesTaxRateAmount() / 100) * $taxableAmount, 2);				
				}
			}
			//echo $taxableAmount." * ". $rate->getSalesTaxRateAmount() / 100 ." = ". $amount;
			//exit;

			//echo "\n\nAmount: ".$amount."\n\n";
			
			if ($doTax && $amount > 0) {
				$type = '+';
				if ($rate->isSalesTaxIncludedInProduct()) {
					$type = '=';
				}
				$salesTax = $order->getAttribute('sales_tax');
				if (is_object($salesTax) && CoreCommerceOrderAttributeKey::getByHandle('other_tax')) {
				   $order->setAttribute('other_tax', array('label' => $rate->getSalesTaxRateName(), 'type' => $type, 'value' => $amount));
				} else {
				   $order->setAttribute('sales_tax', array('label' => $rate->getSalesTaxRateName(), 'type' => $type, 'value' => $amount));
				}
			}
		}
	}

/******************/










// $locale = $_GET['locale'];
$locale = $_SESSION['DEFAULT_LOCALE'];

/*
if(!in_array($locale, array('en_AU', 'en_NZ'))){
	$locale = 'en_AU';
}
*/


Localization::changeLocale($locale);

if (isset($locale) && $locale) {
	setcookie('DEFAULT_LOCALE', $locale, time()+60*60*24*365);
	$_SESSION['DEFAULT_LOCALE'] = $locale;
}
if (empty($locale)) {
	setcookie('DEFAULT_LOCALE', '', time() - 3600);
	unset($_SESSION['DEFAULT_LOCALE']);
}
$lang = MultilingualSection::getByLocale($locale);

$cart = CoreCommerceCurrentOrder::get();

unset($_SESSION['cup_support']['workshop_registration']);


foreach($cart->getProducts() as $each_product){
	
	$requested_qty = $each_product->getQuantity();
	
	$pid = $each_product->getProductObject()->getProductID();
	$title_object = CupContentTitle::fetchByProductId($pid);
	
	$expected_pid = 0;
	
	if(strcmp($locale, 'en_AU') == 0){
		$expected_pid = $title_object->auProductID;
	}elseif(strcmp($locale, 'en_NZ') == 0){
		$expected_pid = $title_object->nzProductID;
	}
	
	$cart->removeProduct($each_product);
	/*
	if($expected_pid != $pid){
		$cart->removeProduct($each_product);
		/ *
		if(strcmp($locale, 'en_AU') == 0 
			&& $title_object->shouldAUProduct()){
					
			$pr = $title_object->getAuProduct();
			$stock_qty = $pr->getProductQuantity();
			if($pr){
				if($stock_qty > $requested_qty){
					$cart->addProduct($pr, $requested_qty);
				}else{
					$cart->addProduct($pr, $stock_qty);
				}
			}
		}elseif(strcmp($locale, 'en_NZ') == 0 
			&& $title_object->shouldNZProduct()){
					
			$pr = $title_object->getNzProduct();
			$stock_qty = $pr->getProductQuantity();
			if($pr){
				if($stock_qty > $requested_qty){
					$cart->addProduct($pr, $requested_qty);
				}else{
					$cart->addProduct($pr, $stock_qty);
				}
			}
		}
		* /
	}
	*/
}

//$method = $order->getOrderShippingMethod();
//$order->setShippingMethod($method);

//setupEnabledRates($cart);
//clearLineAttr($cart);
//setupEnabledRates($cart);

/*
print_r($cart);
echo "\n========\n";
print_r($cart->getAttribute('sales_tax'));
echo "\n========\n";
print_r($cart->getAttribute('other_tax'));
*/


?>