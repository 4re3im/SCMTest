<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('attribute/categories/core_commerce_product_option', 'core_commerce');
Loader::model('product/model', 'core_commerce');

class DashboardCoreCommerceProductsOptionsController extends Controller {
	
	public $helpers = array('form');
	

	public function on_start() {
		$this->set('disableThirdLevelNav', true);
		$otypes = AttributeType::getList('core_commerce_product_option');
		$types = array();
		foreach($otypes as $at) {
			$types[$at->getAttributeTypeID()] = $at->getAttributeTypeName();
		}
		$this->set('types', $types);
		$this->set('category', AttributeKeyCategory::getByHandle('core_commerce_product_option'));
	}
		
	public function delete($akID, $token = null){
		try {
			$ak = CoreCommerceProductOptionAttributeKey::getByID($akID); 
				
			if(!($ak instanceof CoreCommerceProductOptionAttributeKey)) {
				throw new Exception(t('Invalid attribute ID.'));
			}
			
			$productID = $ak->getProductID();
			
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_attribute', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$ak->delete();
			if ($ak->getProductID() > 0) { 
				$this->redirect('/dashboard/core_commerce/products/search', 'view_detail', $productID, 'option_deleted');
			} else {
				$this->redirect('/dashboard/core_commerce/products/options', 'option_deleted');
			}
		} catch (Exception $e) {
			$this->set('error', $e);
		}
	}
	
	public function view($productID = 0, $task = false) {
		$db = Loader::db();
		if ($productID > 0) {
			$product = CoreCommerceProduct::getByID($productID);
			$this->set('product', $product);
			$globalOptions = CoreCommerceProductOptionAttributeKey::getList();
			$nonGlobalOptions = CoreCommerceProductOptionAttributeKey::getList($product);
			$ngopts = array();
			foreach($nonGlobalOptions as $ngopt) {
				$ngopts[] = $ngopt->getAttributeKeyID();
			}
			$opts = array();
			foreach($globalOptions as $gopt) {
				if (!in_array($gopt->getAttributeKeyID(), $ngopts)) {
					$opts[$gopt->getAttributeKeyID()] = $gopt->getAttributeKeyName();
				}
			}
			$this->set('globalOptions', $opts);
			switch($task) {
				case 'global_option_assigned':
					$this->set('message', t('Global product option assigned.'));
					break;
			}
		}
	}
	
	public function associate_global_product_option() {
		$ak = CoreCommerceProductOptionAttributeKey::getByID($this->post('akID'));
		$product = CoreCommerceProduct::getByID($this->post('productID'));
		$ak->assignGlobalKeyToProduct($product);
		$this->redirect('/dashboard/core_commerce/products/options', 'view', $product->getProductID(), 'global_option_assigned');
	}

	public function deassociate_global_product_option($productID, $akID) {
		$ak = CoreCommerceProductOptionAttributeKey::getByID($akID);
		$product = CoreCommerceProduct::getByID($productID);
		$ak->removeGlobalKeyFromProduct($product);
		$this->redirect('/dashboard/core_commerce/products/options', 'view', $product->getProductID(), 'global_option_assigned');
	}

	public function update_choice_order($productID) {
		$product = CoreCommerceProduct::getByID($productID);
		$uats = $_REQUEST['akID'];
		CoreCommerceProductOptionAttributeKey::updateAttributesDisplayOrder($product, $uats);
		exit;
	}
	
	public function select_type() {
		$atID = $this->request('atID');
		if ($this->request('productID' > 0)) { 
			$this->set('product', CoreCommerceProduct::getByID($this->request('productID')));
		}
		$at = AttributeType::getByID($atID);
		$this->set('type', $at);
	}
	
	public function option_created() {
		$this->set('message', t('Customer choice created.'));
	}

	public function option_updated() {
		$this->set('message', t('Customer choice updated.'));
	}

	public function option_deleted() {
		$this->set('message', t('Customer choice deleted.'));
	}
	
	public function add() {
		$this->select_type();
		$type = $this->get('type');
		$cnt = $type->getController();
		$args = $this->post();
		if ($this->post('productID')) { 
			$product = CoreCommerceProduct::getByID($this->post('productID'));
			if ($args['akHandle'] != '') {
				$args['akHandle'] = $product->getProductID() . '_' . $this->post('akHandle');
			}
		}
		$e = $cnt->validateKey($args);
		
		if ($e->has()) {
			$this->set('error', $e);
		} else {
			$type = AttributeType::getByID($this->post('atID'));
			if (is_object($product)) { 
				$ak = CoreCommerceProductOptionAttributeKey::add($type, $product, $args);
			} else {
				$ak = CoreCommerceProductOptionAttributeKey::addGlobal($type, $args);
			}
			if (is_object($product)) { 
				$this->redirect('/dashboard/core_commerce/products/search', 'view_detail', $product->getProductID(), 'option_created');
			} else {
				$this->redirect('/dashboard/core_commerce/products/options', 'option_created');
			}
		}
	}
	
	public function edit($akID = 0) {
		if ($this->post('akID')) {
			$akID = $this->post('akID');
		}
		$key = CoreCommerceProductOptionAttributeKey::getByID($akID);
		if (!is_object($key) || $key->getAttributeKeyID() < 1) { 
			$this->redirect('/dashboard/core_commerce/products/options');
		}
		$type = $key->getAttributeType();
		$this->set('key', $key);
		$this->set('type', $type);
		
		if ($this->isPost()) {
			$cnt = $type->getController();
			$cnt->setAttributeKey($key);
			$args = $this->post();
			if ($key->getProductID() > 0) { 
				if ($args['akHandle'] != '') {
					$args['akHandle'] = $key->getProductID() . '_' . $this->post('akHandle');
				}
			}

			$e = $cnt->validateKey($args);
			if ($e->has()) {
				$this->set('error', $e);
			} else {
				$type = AttributeType::getByID($this->post('atID'));
				$key->update($args);
				if ($key->getProductID() > 0) { 
					$this->redirect('/dashboard/core_commerce/products/search', 'view_detail', $key->getProductID(), 'option_updated');
				} else {
				$this->redirect('/dashboard/core_commerce/products/options', 'option_updated');
				}
			}
		}
	}
	
}