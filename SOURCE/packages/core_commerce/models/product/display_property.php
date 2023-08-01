<?php 

class CoreCommerceProductDisplayPropertyList extends Object {

	protected $sortColumns = array();
	protected $displayCategories = array();
	
	public function setPropertyOrder($order) {
		$this->sortColumns = $order;
	}
	
	public function get() {
		$properties = $this->load();
		if (is_array($this->sortColumns) && count($this->sortColumns) > 0) {
			// now, we have to fill out our sortColumns array for any item that doesn't appear in it
			foreach($properties as $prop) {
				if (!in_array($prop->handle, $this->sortColumns)) {
					$this->sortColumns[] = $prop->handle;
				}
			}
			usort($properties, array('CoreCommerceProductDisplayPropertyList', 'sort'));
		}
		return $properties;	
	}
	
	public function setDisplayCategory($category, $propertyHandles) {
		$this->displayCategories[$category] = $propertyHandles;
	}
	
	protected function sort($a, $b) {
		$key1 = array_search($a->handle, $this->sortColumns);
		$key2 = array_search($b->handle, $this->sortColumns);
    	return ($key1 < $key2) ? -1 : 1;
    }
	
	protected function load() {
		static $properties;
		if (!isset($properties)) {
			$properties = array(
				(object) array('type' => 'fixed', 'name' => t('Name'), 'handle' => 'displayName', 'plHandle' => 'Name'),
				(object) array('type' => 'fixed', 'name' => t('Description'), 'handle' => 'displayDescription', 'plHandle' => 'Description'),
				(object) array('type' => 'fixed', 'name' => t('Price'), 'handle' => 'displayPrice', 'plHandle' => 'Price'),
				(object) array('type' => 'fixed', 'name' => t('Discount'), 'handle' => 'displayDiscount', 'plHandle' => 'Discount'),
				(object) array('type' => 'fixed', 'name' => t('Dimensions'), 'handle' => 'displayDimensions', 'plHandle' => 'Dimensions'),
				(object) array('type' => 'fixed', 'name' => t('Quantity in Stock'), 'handle' => 'displayQuantityInStock', 'plHandle' => 'Quantity')
			);

			Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
			$attributes = CoreCommerceProductAttributeKey::getList();
			foreach($attributes as $ak) {
				$properties[] = (object) array('type' => 'attribute', 'akID' => $ak->getAttributeKeyID(), 'name' => $ak->getAttributeKeyName(), handle => "displayAKID[" . $ak->getAttributeKeyID() . "]");
			}
		}
		return $properties;
	}
	

}

