<?php 
Loader::library('price', 'core_commerce');
class CoreCommerceProductTieredPrice extends Object {

	protected $productID;
	protected $productTieredPricingID;
	protected $tierStart;
	protected $tierEnd;
	protected $tierPrice;
	
	public function getProductTieredPriceID() {return $this->productTieredPricingID;}
	public function getProductID() {return $this->productID;}
	public function getTierStart() {return $this->tierStart;}
	public function getTierEnd() {return $this->tierEnd;}
	public function getTierPrice() {
		$n = Loader::helper('number');
		return $n->flexround($this->tierPrice);
	}
	
	public function getTierDisplayPrice() {
		return CoreCommercePrice::format($this->tierPrice);
	}
	
	public static function getTiers($product) {
		$db = Loader::db();
		$tiers = array();
		$r = $db->Execute('select * from CoreCommerceProductTieredPricing where productID = ? order by tierStart asc', array($product->getProductID()));
		while ($row = $r->FetchRow()) {
			$obj = new CoreCommerceProductTieredPrice();
			$obj->setPropertiesFromArray($row);
			$tiers[] = $obj;
		}
		return $tiers;
	}
	
}

