<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('shipping/controller', 'core_commerce');
Loader::model('title/model', 'cup_content');

class CoreCommerceAusFlatShippingTypeController extends CoreCommerceShippingController {

	protected $shippingMethods = array(
		'AUS_FLAT' => 'Postage & Handling'
	);

	public function type_form() {
		$pkg = Package::getByHandle('core_commerce');
		$SHIPPING_TYPE_FLAT_BASE = $pkg->config('SHIPPING_TYPE_AUS_FLAT_BASE');
		$SHIPPING_TYPE_FLAT_PER_ITEM = $pkg->config('SHIPPING_TYPE_AUS_FLAT_PER_ITEM');
		$this->set('SHIPPING_TYPE_AUS_FLAT_BASE', $SHIPPING_TYPE_FLAT_BASE);
		$this->set('SHIPPING_TYPE_AUS_FLAT_PER_ITEM', $SHIPPING_TYPE_FLAT_PER_ITEM);
	}
	
	public function validate() {
		$e = parent::validate();
		
		if ($this->post('SHIPPING_TYPE_FLAT_BASE') === '') {
			$e->add(t('You must specify a minimum base shipping price, even if it is zero.'));
		}
		if ($this->post('SHIPPING_TYPE_FLAT_BASE') === '') {
			$e->add(t('You must specify a minimum per item shipping price, even if it is zero.'));
		}		
		return $e;
	}
	
	public function save() {
		$pkg = Package::getByHandle('core_commerce');
		$pkg->saveConfig('SHIPPING_TYPE_AUS_FLAT_BASE', $this->post('SHIPPING_TYPE_FLAT_BASE'));
		$pkg->saveConfig('SHIPPING_TYPE_AUS_FLAT_PER_ITEM', $this->post('SHIPPING_TYPE_FLAT_PER_ITEM'));
	}
	
	public function getAvailableShippingMethods($currentOrder) {
		$pkg = Package::getByHandle('core_commerce');
		$shipping = $pkg->config('SHIPPING_TYPE_AUS_FLAT_BASE');
		$perItem = $pkg->config('SHIPPING_TYPE_AUS_FLAT_PER_ITEM');
		
		$has_free_shipping_product = false;
		$has_normal_shipping_product = false;
		
		$reqdShipping = false;
		foreach($currentOrder->getProducts() as $pr) {
			if ($pr->productRequiresShipping()) {
				$thisPerItem = $perItem;
				
				$title = CupContentTitle::fetchByProductId($pr->getProductID());
				if($title->is_free_shipping){
					$has_free_shipping_product = true;
					$thisPerItem = 0;
				}else{
					
					if ($pr->getProductShippingModifier() != '') {
						$thisPerItem += $pr->getProductShippingModifier();
					}
					$has_normal_shipping_product = true;
				}
				$shipping += ($thisPerItem * $pr->getQuantity());
				$reqdShipping = true;
			}
		}
		if(!$reqdShipping){
			$shipping = 0;
		}else{
			if($has_free_shipping_product && !$has_normal_shipping_product){
				$shipping = 0;
			}
		}		
		

		$ecm = new CoreCommerceShippingMethod($this->getShippingType(), 'AUS_FLAT');
		$ecm->setPrice($shipping);
		$ecm->setName(t('Postage & Handling'));
		return $ecm;
	}
	
}
