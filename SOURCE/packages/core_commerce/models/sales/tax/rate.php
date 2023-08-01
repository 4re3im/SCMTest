<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

if(!function_exists('fnmatch')) {
    function fnmatch($pattern, $string) {
        return preg_match("#^".strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.', '\[' => '[', '\]' => ']'))."$#i", $string);
    } // end
}

class CoreCommerceSalesTaxRate extends Object {

	public function getSalesTaxRateID() {return $this->salesTaxRateID;}
	public function isSalesTaxRateEnabled() { return $this->salesTaxRateIsEnabled;}
	public function getSalesTaxRateName() {return $this->salesTaxRateName;}
	public function getSalesTaxRateStateProvince() {return $this->salesTaxRateStateProvince;}
	public function getSalesTaxRateCountry() {return $this->salesTaxRateCountry;}
	public function getSalesTaxRatePostalCode() {return $this->salesTaxRatePostalCode;}
	public function isSalesTaxIncludedInProduct() {return $this->salesTaxRateIncludedInProduct;}
	public function includeShippingInSalesTaxRate() {return $this->salesTaxRateIncludeShipping;}
	public function getSalesTaxRateAmount() {return Loader::helper('number')->flexround($this->salesTaxRateAmount);}
   public function getSalesTaxRateSpecialSet() {return $this->salesTaxRateSpecialSet;}
	
	protected function load($salesTaxRateID) {
		$db = Loader::db();
		$row = $db->GetRow('select * from CoreCommerceSalesTaxRates where salesTaxRateID = ?', array($salesTaxRateID));
		$this->setPropertiesFromArray($row);
	}
	
	public static function getByID($salesTaxRateID) {
		$ed = new CoreCommerceSalesTaxRate();
		$ed->load($salesTaxRateID);
		if ($ed->getSalesTaxRateID() > 0) {
			return $ed;
		}
	}

	public static function getList($filters = array()) {
		$db = Loader::db();
		$q = 'select salesTaxRateID from CoreCommerceSalesTaxRates where 1=1';
		foreach($filters as $key => $value) {
			if (is_string($key)) {
				$q .= ' and ' . $key . ' = ' . $value . ' ';
			} else {
				$q .= ' and ' . $value . ' ';
			}
		}
		$r = $db->Execute($q);
		$list = array();
		while ($row = $r->FetchRow()) {
			$list[] = CoreCommerceSalesTaxRate::getByID($row['salesTaxRateID']);
		}
		$r->Close();
		return $list;
	}
	
	public function add($args) {
		$txt = Loader::helper('text');
		
		extract($args);
		
		$_salesTaxRateIsEnabled = 0;
		$_salesTaxRateIncludedInProduct = 0;
		$_salesTaxRateIncludeShipping = 0;
		
		if ($salesTaxRateIsEnabled) {
			$_salesTaxRateIsEnabled = 1;
		}
		if ($salesTaxRateIncludedInProduct) {
			$_salesTaxRateIncludedInProduct = 1;
		}
		if ($salesTaxRateIncludeShipping) {
			$_salesTaxRateIncludeShipping = 1;
		}
		
		$db = Loader::db();
		$a = array($salesTaxRateSpecialSet, $salesTaxRateName, $_salesTaxRateIsEnabled, $salesTaxRateAmount, $salesTaxRateCountry, $salesTaxRateStateProvince, $salesTaxRatePostalCode, $_salesTaxRateIncludedInProduct, $_salesTaxRateIncludeShipping);
		$r = $db->query("insert into CoreCommerceSalesTaxRates (salesTaxRateSpecialSet, salesTaxRateName, salesTaxRateIsEnabled, salesTaxRateAmount, salesTaxRateCountry, salesTaxRateStateProvince, salesTaxRatePostalCode, salesTaxRateIncludedInProduct, salesTaxRateIncludeShipping) values (?, ?, ?, ?, ?, ?, ?, ?, ?)", $a);
		
		if ($r) {
			$salesTaxRateID = $db->Insert_ID();
			$rate = CoreCommerceSalesTaxRate::getByID($salesTaxRateID);
			return $rate;
		}
	}

	public function update($args) {
		$txt = Loader::helper('text');
		
		extract($args);
		
		$_salesTaxRateIsEnabled = 0;
		$_salesTaxRateIncludedInProduct = 0;
		$_salesTaxRateIncludeShipping = 0;
		
		if ($salesTaxRateIsEnabled) {
			$_salesTaxRateIsEnabled = 1;
		}
		if ($salesTaxRateIncludedInProduct) {
			$_salesTaxRateIncludedInProduct = 1;
		}
		if ($salesTaxRateIncludeShipping) {
			$_salesTaxRateIncludeShipping = 1;
		}
		
		$db = Loader::db();
		$a = array($salesTaxRateSpecialSet,$salesTaxRateName, $_salesTaxRateIsEnabled, $salesTaxRateAmount, $salesTaxRateCountry, $salesTaxRateStateProvince, $salesTaxRatePostalCode, $_salesTaxRateIncludedInProduct, $_salesTaxRateIncludeShipping, $this->getSalesTaxRateID());
		$r = $db->query("update CoreCommerceSalesTaxRates set salesTaxRateSpecialSet = ?, salesTaxRateName = ?, salesTaxRateIsEnabled = ?, salesTaxRateAmount = ?, salesTaxRateCountry = ?, salesTaxRateStateProvince = ?, salesTaxRatePostalCode =?, salesTaxRateIncludedInProduct = ?, salesTaxRateIncludeShipping = ? where salesTaxRateID = ?", $a);
		
		if ($r) {
			$newrate = CoreCommerceSalesTaxRate::getByID($salesTaxRateID);
			return $newrate;
		}
	}

	public static function setupEnabledRates($order) {
		$address = $order->getAttribute('shipping_address');
		if (!is_object($address)) {
			$address = $order->getAttribute('billing_address');
		}
		if (!is_object($address)) {
			return false;
		}
		$list = self::getList();
		$order->clearAttribute('sales_tax');
		if(CoreCommerceOrderAttributeKey::getByHandle('other_tax')) {
			$order->clearAttribute('other_tax');
		}
		foreach($list as $rate) {
			$doTax = false;
			$amount = 0;

			if ($rate->isSalesTaxRateEnabled()) {
				if ($rate->getSalesTaxRateCountry() != '' && $rate->getSalesTaxRateStateProvince() && $rate->getSalesTaxRatePostalCode() != '') {
					// they have to be in all three
					$doTax = ($rate->getSalesTaxRateCountry() == $address->getCountry() && $rate->getSalesTaxRateStateProvince() == $address->getStateProvince() && fnmatch($rate->getSalesTaxRatePostalCode(), $address->getPostalCode()));
				} else if ($rate->getSalesTaxRateCountry() != '' && $rate->getSalesTaxRateStateProvince()) {
					$doTax = ($rate->getSalesTaxRateCountry() == $address->getCountry() && $rate->getSalesTaxRateStateProvince() == $address->getStateProvince());
				} else if ($rate->getSalesTaxRateCountry() != '') {
					$doTax = ($rate->getSalesTaxRateCountry() == $address->getCountry());
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
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceSalesTaxRates where salesTaxRateID = ?', array($this->getSalesTaxRateID()));
	}

}
