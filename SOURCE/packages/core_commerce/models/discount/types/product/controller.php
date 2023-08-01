<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('discount/controller', 'core_commerce');
class CoreCommerceProductDiscountTypeController extends CoreCommerceDiscountController {


	public function validateDiscount() {
		$e = parent::validateDiscount();
		if ($this->post('amount') === '') {
			$e->add(t('You must specify a discount amount.'));
		}
		if ($this->post('discountProductFilter') == 'sets' && (!is_array($this->post('prsID')))) {
			$e->add(t('You must assign this discount to at least one product set.'));
		}
		if ($this->post('discountProductFilter') == 'products' && (!is_array($this->post('productID')))) {
			$e->add(t('You must assign this discount to at least one product.'));
		}		
		return $e;
	}

	public function save() {
		$db = Loader::db();
		$db->Replace('CoreCommerceDiscountTypeProduct', array('amount' => $this->post('amount'), 'mode' => $this->post('mode'), 'discountProductFilter' => $this->post('discountProductFilter'), 'discountID' => $this->discount->getDiscountID()), array('discountID'), true);
		if ($this->post('discountProductFilter') == 'sets') {
			Loader::model('product/set', 'core_commerce');
			$sets = array();
			foreach($this->post("prsID") as $prsID) {
				$prs = CoreCommerceProductSet::getByID($prsID);
				if (is_object($prs)) {
					$sets[] = $prs;
				}
			}
			$this->assignProductSetsToDiscount($sets);
		} else if ($this->post('discountProductFilter') == 'products') {
			Loader::model('product/model', 'core_commerce');
			$products = array();
			foreach($this->post("productID") as $productID) {
				$pr = CoreCommerceProduct::getByID($productID);
				if (is_object($pr)) {
					$products[] = $pr;
				}
			}
			$this->assignProductsToDiscount($products);
		} else {
			$this->resetProductFilter();
		}		
	}
	
	protected function resetProductFilter() {
		$db = Loader::db();
		$db->Execute('update CoreCommerceDiscountTypeProduct set discountProductFilter = null where discountID = ?', array($this->discount->getDiscountID()));
		$db->Execute('delete from CoreCommerceDiscountTypeProductProductSets where discountID = ?', array($this->discount->getDiscountID()));
		$db->Execute('delete from CoreCommerceDiscountTypeProductProducts where discountID = ?', array($this->discount->getDiscountID()));
	}
	
	
	protected function assignProductSetsToDiscount($sets) {
		$db = Loader::db();
		$db->Execute('update CoreCommerceDiscountTypeProduct set discountProductFilter = "sets" where discountID = ?', array($this->discount->getDiscountID()));
		$db->Execute('delete from CoreCommerceDiscountTypeProductProductSets where discountID = ?', array($this->discount->getDiscountID()));
		foreach($sets as $s) {
			$db->Execute('insert into CoreCommerceDiscountTypeProductProductSets (discountID, prsID) values (?, ?)', array($this->discount->getDiscountID(), $s->getProductSetID()));
		}
	}

	protected function assignProductsToDiscount($products) {
		$db = Loader::db();
		$db->Execute('update CoreCommerceDiscountTypeProduct set discountProductFilter = "products" where discountID = ?', array($this->discount->getDiscountID()));
		$db->Execute('delete from CoreCommerceDiscountTypeProductProducts where discountID = ?', array($this->discount->getDiscountID()));
		foreach($products as $pr) {
			$db->Execute('insert into CoreCommerceDiscountTypeProductProducts (discountID, productID) values (?, ?)', array($this->discount->getDiscountID(), $pr->getProductID()));
		}
	}
	
	public function type_form() {
		$db = Loader::db();
		$discountProductFilterProductSetIDs = array();
		$discountProductFilterProductIDs = array();
		$discountProductFilter = '';
		if (is_object($this->discount)) {
			$r = $db->GetRow('select mode, amount, discountProductFilter from CoreCommerceDiscountTypeProduct where discountID = ?', array($this->discount->getDiscountID()));
			$this->set('mode', $r['mode']);
			$this->set('amount', Loader::helper('number')->flexround($r['amount']));

			$discountProductFilter = $r['discountProductFilter'];
			if ($discountProductFilter == 'sets') {
				$discountProductFilterProductSets = $this->getDiscountProductFilterProductSets();
				foreach($discountProductFilterProductSets as $prs) {
					$discountProductFilterProductSetIDs[] = $prs->getProductSetID();
				}
			}	
			if ($discountProductFilter == 'products') {
				$discountProductFilterProducts = $this->getDiscountProductFilterProducts();
				foreach($discountProductFilterProducts as $pr) {
					$discountProductFilterProductIDs[] = $pr->getProductID();
				}
			}
		}
		$this->set('discountProductFilter', $discountProductFilter);
		$this->set('discountProductFilterProductSetIDs', $discountProductFilterProductSetIDs);
		$this->set('discountProductFilterProductIDs', $discountProductFilterProductIDs);
	}

	public function getDiscountProductFilterProductSets() {
		$sets = array();
		$db = Loader::db();
		Loader::model('product/set', 'core_commerce');
		$r = $db->Execute('select prsID from CoreCommerceDiscountTypeProductProductSets where discountID = ?', array($this->discount->getDiscountID()));
		while ($row = $r->FetchRow()) {
			$prs = CoreCommerceProductSet::getByID($row['prsID']);
			if (is_object($prs)) {
				$sets[] = $prs;
			}
		}
		return $sets;
	}
	
	public function getDiscountProductFilterProducts() {
		$products = array();
		$db = Loader::db();
		Loader::model('product/model', 'core_commerce');
		$r = $db->Execute('select productID from CoreCommerceDiscountTypeProductProducts where discountID = ?', array($this->discount->getDiscountID()));
		while ($row = $r->FetchRow()) {
			$pr = CoreCommerceProduct::getByID($row['productID']);
			if (is_object($pr)) {
				$products[] = $pr;
			}
		}
		return $products;
	}

	public function deleteDiscount() {
		// remove from order
		$o = CoreCommerceCurrentOrder::get();
		$o->clearAttribute('discount_product_adjustment');
		$products = $o->getProducts();
		foreach($products as $prod) {
			$prod->setOrderProductDiscount(0);
		}
	}

	public function applyDiscount() {
		Loader::model('cart', 'core_commerce');
		$o = CoreCommerceCurrentOrder::get();
		$db = Loader::db();
		$r = $db->GetRow('select mode, amount, discountProductFilter from CoreCommerceDiscountTypeProduct where discountID = ?', array($this->discount->getDiscountID()));
		$tamt = 0;
		
		// now we loop through all products that are in the order that are affected by this discount and add to the $tamt value
		if ($r['discountProductFilter'] == 'sets') {
			$setIDs = $db->GetCol('select prsID from CoreCommerceDiscountTypeProductProductSets where discountID = ?', array($this->discount->getDiscountID()));
			Loader::model('product/set', 'core_commerce');
			$sets = array();
			foreach($setIDs as $sid) {
				$set = CoreCommerceProductSet::getByID($sid);
				if (is_object($set)) {
					$sets[] = $set;
				}
			}
		} else if ($r['discountProductFilter'] == 'products') {
			$productIDs = $db->GetCol('select productID from CoreCommerceDiscountTypeProductProducts where discountID = ?', array($this->discount->getDiscountID()));
		}

		$products = $o->getProducts();
		foreach($products as $prod) {
			$doDiscountOnProduct = false;
			switch($r['discountProductFilter']) {
				case 'sets':
					if ($prod->inProductSet($sets)) {
						$doDiscountOnProduct = true;
					}
					break;
				case 'products':
					if (in_array($prod->getProductID(), $productIDs)) {
						$doDiscountOnProduct = true;
					}
					break;			
				default:
					$doDiscountOnProduct = true;
					break;
			}
			
			if ($doDiscountOnProduct) {
				switch($r['mode']) {
					case 'fixed':
						$amt = ($r['amount'] * $prod->getQuantity());
						break;				
					case 'percent':
						$amt = round(($r['amount'] / 100) * ($prod->getProductPriceToPay($prod->getQuantity()) * $prod->getQuantity()), 2);
						break;				
				}
				$tamt += $amt;
				// we store this so we can ultimately subtract this from product specific sales tax
				// we don't deduct this from the total ever. Instead we use a discount line item to do that
				// this is JUST for storing and for deducting while calculating sales tax
				$prod->setOrderProductDiscount($amt);
			}
			
		}
		
		// if the order already has an attribute of this type, we add to it
		$amtx = $o->getAttribute('discount_product_adjustment');
		$label = $this->discount->getDiscountName();
		if (is_object($amtx)) {
			$tamt += $amtx->getLineItemTotal();
			$label = $amtx->getLineItemName() . ', ' . $label;
		}
		if ($tamt > $o->getBaseOrderTotal()) {
			$tamt = $o->getBaseOrderTotal();
		}

		$o->setAttribute('discount_product_adjustment', array('label' => $label, 'type' => '-', 'value' => $tamt));
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceDiscountTypeProduct where discountID = ?', array($this->discount->getDiscountID()));
		$db->Execute('delete from CoreCommerceDiscountTypeProductProductSets where discountID = ?', array($this->discount->getDiscountID()));
		$db->Execute('delete from CoreCommerceDiscountTypeProductProducts where discountID = ?', array($this->discount->getDiscountID()));
	}
		

	
}