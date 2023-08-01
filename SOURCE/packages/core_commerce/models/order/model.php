<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('price', 'core_commerce');
class CoreCommerceOrder extends Object {

    const STATUS_NEW = 0;
    const STATUS_PENDING = 1;
    const STATUS_AUTHORIZED = 2;
    const STATUS_SHIPPED = 3;
    const STATUS_COMPLETE = 4;
    const STATUS_INCOMPLETE = 9000;
    const STATUS_CANCELLED = 90;

    const TYPE_WISHLIST	= 'wishlist';
    const TYPE_ORDER 	= 'order';
    const TYPE_REGISTRY = 'registry';

	protected $products = array();
	protected $productsLoaded = false;
	
	/**
	 * adds a new order record to the database and returns the new order object
	 * @return CoreCommerceOrder
	 */
	public function add() {
		$db = Loader::db();
		$dt = Loader::helper('date');
		$db->Execute('insert into CoreCommerceOrders (oDateAdded, oStatus) values (?, 0)', array($dt->getLocalDateTime()));
		$id = $db->Insert_ID();
		return CoreCommerceOrder::getByID($id);
	}
	
	public function delete($orderID) {
		$db = Loader::db();
		$orderID = $this->getOrderID();
		if($orderID) {
			// remove order attributes
			$attribs = CoreCommerceOrderAttributeKey::getList();
			if(is_array($attribs) && count($attribs)) {
				foreach($attribs as $ak) {
					$av = $this->getAttributeValueObject($ak);
					if($av instanceof AttributeValue) {
						$av->delete();
					}
				}
			}
			
			// remove products
			$products = $this->getProducts();
			if(is_array($products) && count($products)) {
				foreach($products as $p) {
					$this->removeProduct($p);
				}
			}
			
			// remove the search index 
			$db->query("DELETE FROM CoreCommerceOrderSearchIndexAttributes WHERE orderID = ?", array($orderID));
			
			// remove status history
			$db->query("DELETE FROM CoreCommerceOrderStatusHistory WHERE orderID = ?", array($orderID));	
			
			// remove order number
			$db->query("DELETE FROM CoreCommerceOrderInvoiceNumbers WHERE orderID = ?", array($orderID));

			// remove the order record
			$db->query("DELETE FROM CoreCommerceOrders WHERE orderID = ?", array($orderID));	
			return true;	
		} else {
			return false;
		}
	}
	
	
	/**
	 * The total count of items within an order, not distinct products
	 * @return number
	 */
	public function getTotalProducts() {
		$db = Loader::db();
		$r = $db->GetOne('select sum(quantity) from CoreCommerceOrderProducts where orderID = ?', array($this->orderID)); //should be sum(quantity) for the orderID.  Look in the OrderProducts table to see what it really should be.
		if ($r > 0) {
			return $r;
		}
		return 0;
	}
	
	public function load($id) {
		$db = Loader::db();
		$query = "SELECT cco.*, cci.orderInvoiceNumber as invoiceNumber
				FROM CoreCommerceOrders cco 
				LEFT JOIN CoreCommerceOrderInvoiceNumbers cci ON cci.orderID = cco.orderID
				WHERE cco.orderID = ?";
		$row = $db->GetRow($query, array($id));
		if ($row != false && is_array($row)) {
			$this->setPropertiesFromArray($row);
		}
	}
	
	/**
	 * @param int $id
	 * @return CoreCommerceOrder | false
	 */
	public static function getByID($id) {
		$db = Loader::db();
		$r = $db->GetOne('select orderID from CoreCommerceOrders where orderID = ?', array($id));
		if ($r == $id) {
			$pr = new CoreCommerceOrder();
			$pr->load($id);
			return $pr;
		} else {
			return false;
		}
	}

	public static function isValidOrderID($id) {
		$db = Loader::db();
		$r = $db->GetOne('select orderID from CoreCommerceOrders where orderID = ?', array($id));
		if ($r == $id) {
			return true;
		}
	}
	
	public function loadProducts() {
		Loader::model('order/product', 'core_commerce');
		$db = Loader::db();
		$r = $db->Execute('select orderProductID from CoreCommerceOrderProducts where orderID = ?', array($this->orderID));
		while ($row = $r->fetchRow()) {
			$pr = CoreCommerceOrderProduct::getByID($row['orderProductID']);
			$this->products[] = $pr;
		}
		$this->productsLoaded = true;
	}
	
	public function isOrderBelowMinimumThreshold() {
		$pkg = Package::getByHandle("core_commerce");
		if ($pkg->config('ORDER_TOTAL_ENABLE_MINIMUM')) {
			$total = $this->getOrderTotal();
			if ($total < $pkg->config('ORDER_TOTAL_MINIMUM_AMOUNT')) {
				return true;
			}
		}	
		return false;
	}
	
	public static function getOrderMinimumTotal() {
		$pkg = Package::getByHandle("core_commerce");
		return $pkg->config('ORDER_TOTAL_MINIMUM_AMOUNT');
	}
	
	public function getOrderLineItems() {
		Loader::model('order/line_item', 'core_commerce');
		Loader::model('attribute/categories/core_commerce_order', 'core_commerce');
		$db = Loader::db();
		$akIDs = $db->GetCol("select CoreCommerceOrderAttributeValues.akID from CoreCommerceOrderAttributeValues inner join AttributeKeys on CoreCommerceOrderAttributeValues.akID = AttributeKeys.akID inner join AttributeTypes on AttributeKeys.atID = AttributeTypes.atID where orderID = ? and atHandle = ?", array($this->getOrderID(), CoreCommerceOrderAttributeKey::ORDER_ADJUSTMENT_ATTRIBUTE_TYPE_HANDLE));
		$items = array();
		foreach($akIDs as $akID) {
			$ak = CoreCommerceOrderAttributeKey::getByID($akID);
			$val = $this->getAttributeValueObject($ak);
			if (is_object($val)) {
				$items[] = $val->getValue();
			}
		}
		return $items;
	}
	
	public function getOrderID() {return $this->orderID;}
	public function getOrderName() {return '';}	
	public function getOrderUserID() {return $this->uID;}
	public function getOrderEmail() {return $this->oEmail;}
	public function getOrderShippingMethodID() {return $this->oShippingMethodID;}
	public function getOrderShippingMethod() {
		Loader::model('shipping/method', 'core_commerce');
		$em = CoreCommerceShippingMethod::getAvailableMethodByID($this->oShippingMethodID);
		return $em;
	}
	public function getOrderPaymentMethodID() {return $this->oPaymentMethodID;}
	public function getOrderPaymentMethod() {
		Loader::model('payment/method', 'core_commerce');
		//echo "Payment Method ID: {$this->oPaymentMethodID}";
		//print_r(debug_backtrace());
		$em = CoreCommercePaymentMethod::getByID($this->oPaymentMethodID);
		return $em;
	}
	public function getOrderDateAdded() { return $this->oDateAdded;}
	public function getProducts() {
		if (!$this->productsLoaded) {
			$this->loadProducts();
		}
		return $this->products;
	}
	
	public function getInvoiceNumber($formatted = true) {
		if($formatted) {
			$pkg = Package::getByHandle('core_commerce');
			$format = $pkg->config('ORDER_INVOICE_NUMBER_FORMAT');
			if(!strlen($format)) {
				$format = '%06s';
			}
			return sprintf($format,$this->invoiceNumber);
			//return $this->invoiceNumber;
		} else {
			return $this->invoiceNumber;
		}
	}
	
	
	
	public function addProduct($product, $quantity) {
		Loader::model('order/product', 'core_commerce');
		$ecp = CoreCommerceOrderProduct::add($this, $product, $quantity);
      
		return $ecp;
	}

	public function clearAttribute($ak) {
		Loader::model('attribute/categories/core_commerce_order', 'core_commerce');
		$db = Loader::db();
		if (!is_object($ak)) {
			$ak = CoreCommerceOrderAttributeKey::getByHandle($ak);
		}
		$cav = $this->getAttributeValueObject($ak);
		if (is_object($cav)) {
			$cav->delete();
		}
		$this->reindex();
	}
	
	public function setOrderUserID($uID) {
		$db = Loader::db();
		$db->Execute('update CoreCommerceOrders set uID = ? where orderID = ?', array($uID, $this->orderID));
	}
	
	public function setShippingMethod($method) {
		$db = Loader::db();
		$db->Execute('update CoreCommerceOrders set oShippingMethodID = ? where orderID = ?', array($method->getID(), $this->orderID));
		$this->setAttribute('total_shipping', array('label' => $method->getName(), 'type' => '+', 'value' => $method->getPrice()));
	}

	public function setPaymentMethod($method) {
		$db = Loader::db();
		$db->Execute('update CoreCommerceOrders set oPaymentMethodID = ? where orderID = ?', array($method->getPaymentMethodID(), $this->orderID));
	}
	
	public function clearShippingMethod() {
		$db = Loader::db();
		$db->Execute('update CoreCommerceOrders set oShippingMethodID = null where orderID = ?', array($this->orderID));
		$this->clearAttribute('total_shipping');
	}

	/**
	 * Tests to see if a different orderproduct with the same product ID + options is in the order
	 * @param CoreCommerceOrderProduct $ecp
	 * @return CoreCommerceOrderProduct|boolean
	 */
	public function orderContainsOtherProduct(CoreCommerceOrderProduct $ecp) {
		$products = $this->getProducts();
		
		foreach($products as $ecpo) {
			if ($ecpo->getProductID() == $ecp->getProductID() 
				&& $ecpo->getOrderProductID() != $ecp->getOrderProductID()
				&& $ecp->hasEqualAttributes($ecpo)) {
				return $ecpo;
			}
		}
		return false;
	}
	
	
	/**
	 * Given a product, it returns the total quantity in the order regardless of the variation
	 * @param CoreCommerceProduct $ecp
	 * @return number
	 */
	public function getProductTotalQuantityInOrder($ecp) {
		$products = $this->getProducts();
		// tests to see if a different orderproduct with the same product ID + options is in the order
		
		$quantity = 0;
		foreach($products as $ecpo) {
			if ($ecpo->getProductID() == $ecp->getProductID()) {
				$quantity += $ecpo->getQuantity();
			}
		}
		return $quantity;
	}
	
	
	public function getBaseOrderTotal() {
		if (!$this->productsLoaded) {
			$this->loadProducts();
		}

		$total = 0;
		foreach($this->products as $pr) {
			$total += $pr->getQuantity() * $pr->getProductCartPrice();
		}
		return $total;
	}

	public function getBaseOrderDisplayTotal() {
		return CoreCommercePrice::format($this->getBaseOrderTotal());
	}
	
	/** 
	 * Returns the order minus any discount line items
	 */
	 
	public function getDiscountedOrderTotal() {
		$total = $this->getBaseOrderTotal();
		$items = $this->getOrderLineItems();
		foreach($items as $it) {
			switch($it->getLineItemType()) {
				case '-':
					$total -= $it->getLineItemTotal();
					break;
				case '=':
					// nothing
					break;
			}
		}
		return $total;		
	}
	
	public function getOrderTotal() {
		$total = $this->getBaseOrderTotal();
		$items = $this->getOrderLineItems();
		foreach($items as $it) {
			switch($it->getLineItemType()) {
				case '-':
					$total -= $it->getLineItemTotal();
					break;
				case '=':
					// nothing
					break;
				default:
					$total += $it->getLineItemTotal();
					break;
			}
		}
		return $total;		
	}

	public function getOrderDisplayTotal() {
		return CoreCommercePrice::format($this->getOrderTotal());
	}

	public function removeProduct($pr) {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceOrderProducts where orderProductID = ? and orderID = ?', array($pr->getOrderProductID(), $this->getOrderID()));
		//update stats
		$db->Replace('CoreCommerceProductStats', array('productID' => $pr->getProductID(), 'totalPurchases' => 'totalPurchases - 1'), 'productID', false);
	}	

	public function requiresShipping() {
		$products = $this->getProducts();
		$shipping = false;
		
		foreach($products as $pr) {
			if ($pr->productRequiresShipping()) {
				$shipping = true;
				break;
			}
		}
		return $shipping;	
	}

	public function setAttribute($ak, $value) {
		Loader::model('attribute/categories/core_commerce_order', 'core_commerce');
		if (!is_object($ak)) {
			$ak = CoreCommerceOrderAttributeKey::getByHandle($ak);
		}
		$ak->setAttribute($this, $value);
		$this->reindex();
	}

	public function reindex() {
		$attribs = CoreCommerceOrderAttributeKey::getAttributes($this->getOrderID(), 'getSearchIndexValue');
		$db = Loader::db();

		$db->Execute('delete from CoreCommerceOrderSearchIndexAttributes where orderID = ?', array($this->getOrderID()));
		$searchableAttributes = array('orderID' => $this->getOrderID());
		$rs = $db->Execute('select * from CoreCommerceOrderSearchIndexAttributes where orderID = -1');
		AttributeKey::reindex('CoreCommerceOrderSearchIndexAttributes', $searchableAttributes, $attribs, $rs);
	}
	
	public function getStatus() {
		return $this->oStatus;
	}
	
	public function getOrderStatus() {return $this->oStatus;}
	public function getOrderAvailableStatuses() {
		// eventually this might limit based on what statuses have come before
		$statusKeys = array(self::STATUS_PENDING, self::STATUS_INCOMPLETE, self::STATUS_AUTHORIZED, self::STATUS_SHIPPED, self::STATUS_COMPLETE, self::STATUS_CANCELLED);
		$statuses = array();
		foreach($statusKeys as $v) {
			$statuses[$v] = self::getOrderStatusText($v);
		}
		return $statuses;
	}
	public function getOrderStatusText($status = false) {
		$text = '';
		if ($status === false) {
			$status = $this->oStatus;
		}		
		switch($status) {
			case CoreCommerceOrder::STATUS_NEW;
				$text = t('New');
				break;
			case CoreCommerceOrder::STATUS_INCOMPLETE;
				$text = t('Payment Incomplete');
				break;
			case CoreCommerceOrder::STATUS_PENDING;
				$text = t('Pending');
				break;
			case CoreCommerceOrder::STATUS_AUTHORIZED;
				$text = t('Authorized');
				break;
			case CoreCommerceOrder::STATUS_SHIPPED;
				$text = t('Shipped');
				break;
			case CoreCommerceOrder::STATUS_COMPLETE;
				$text = t('Complete');
				break;
			case CoreCommerceOrder::STATUS_CANCELLED;
				$text = t('Cancelled');
				break;
		}
		return $text;
	}
	
	public function getOrderStatusHistory() {
		$db = Loader::db();
		$status = array();
		Loader::model('order/status_history', 'core_commerce');
		$r = $db->Execute('select orderStatusHistoryID from CoreCommerceOrderStatusHistory where orderID = ? order by oshDateSet desc', $this->orderID);;
		while ($row = $r->FetchRow()) {
			$status[] = CoreCommerceOrderStatusHistory::getByID($row['orderStatusHistoryID']);
		}
		return $status;
	}

	public function setStatus($status=self::STATUS_NEW) {
      $ye_olde_status = $this->oStatus;
		$this->oStatus  = $status;
		$db = Loader::db();
		$db->Execute('update CoreCommerceOrders set oStatus = ? where orderID = ?', array($status, $this->orderID));
		
		// insert into status history table
		$u = new User();
		$date = Loader::helper('date');
		$uID = ($u->isRegistered()) ? $u->getUserID() : 0;
		$v = array($this->orderID, $status, $date->getLocalDateTime(), $uID);
		$db->Execute('insert into CoreCommerceOrderStatusHistory (orderID, oshStatus, oshDateSet, uID) values (?, ?, ?, ?)', $v);
		
		// Now we decrement quantity if that is in fact that setting
		$pkg = Package::getByHandle('core_commerce');
		if ($pkg->config('MANAGE_INVENTORY') == 1) {
			if (($pkg->config('MANAGE_INVENTORY_TRIGGER') == 'SHIPPED' && $status == self::STATUS_SHIPPED) 
			|| ($pkg->config('MANAGE_INVENTORY_TRIGGER') == 'COMPLETED' && $status == self::STATUS_COMPLETE)) {
				$products = $this->getProducts();
				foreach ($products as $product) {
					$baseProduct = $product->getProductObject();
					$baseProduct->decreaseProductQuantity($product->getQuantity());
				}
			}
		}
		$this->assignInvoiceNumber();
      Events::fire('core_commerce_change_order_status', $this, $this->oStatus, $ye_olde_status);
	}
	
	public function setOrderStatus($status=self::STATUS_NEW) {
		$this->setStatus($status);
	}
	
	public function setOrderType($type) {
		$this->oType = $type;
		$db = Loader::db();
		$db->Execute('update CoreCommerceOrders set oType = ? where orderID = ?', array($type, $this->orderID));
	}
	
	
	public function setOrderEmail($em) {
		$this->oEmail = $em;
		$db = Loader::db();
		$db->Execute('update CoreCommerceOrders set oEmail = ? where orderID = ?', array($em, $this->orderID));
	}
	
	
	/** 
	 * Gets the value of the attribute for the order
	 */
	public function getAttribute($ak, $displayMode = false) {
		Loader::model('attribute/categories/core_commerce_order', 'core_commerce');
		if (!is_object($ak)) {
			$ak = CoreCommerceOrderAttributeKey::getByHandle($ak);
		}
		if (is_object($ak)) {
			$av = $this->getAttributeValueObject($ak);
			if (is_object($av)) {
				return $av->getValue($displayMode);
			}
		}
	}
	
	public function getAttributeValueObject($ak, $createIfNotFound = false) {
		$db = Loader::db();
		$av = false;
		$v = array($this->getOrderID(), $ak->getAttributeKeyID());
		$avID = $db->GetOne("select avID from CoreCommerceOrderAttributeValues where orderID = ? and akID = ?", $v);
		if ($avID > 0) {
			$av = CoreCommerceOrderAttributeValue::getByID($avID);
			if (is_object($av)) {
				$av->setOrder($this);
				$av->setAttributeKey($ak);
			}
		}
		
		if ($createIfNotFound) {
			$cnt = 0;
		
			// Is this avID in use ?
			if (is_object($av)) {
				$cnt = $db->GetOne("select count(avID) from CoreCommerceOrderAttributeValues where avID = ?", $av->getAttributeValueID());
			}
			
			if ((!is_object($av)) || ($cnt > 1)) {
				$av = $ak->addAttributeValue();
			}
		}
		
		return $av;
	}
	
	
	public function assignInvoiceNumber() {
		$db = Loader::db();
		if(!$this->getInvoiceNumber(false)) {
			$inv = $db->getOne("SELECT orderInvoiceNumber FROM CoreCommerceOrderInvoiceNumbers WHERE orderID = ?",array($this->getOrderID()));
			if(!$inv) {
				//@TODO check out on duplicate key mysql functionality
				$db->query("INSERT INTO CoreCommerceOrderInvoiceNumbers (orderID) VALUES (?)",array($this->getOrderID()));
				$this->invoiceNumber = $db->Insert_ID();
				return $this->invoiceNumber;
			}
		}
	}
	
	/**
	 * Move a product from $this order to another
	 * @param int $orderID
	 * @param CoreCommerceOrderProduct $product
	 * @return boolean
	 */
	public function moveProductToOrder($orderID, $product) {
		$db = Loader::db();
		$order = CoreCommerceOrder::getByID($orderID);
		if($order && $product instanceof CoreCommerceOrderProduct) {
			$vals = array($orderID, $this->getOrderID(), $product->getOrderProductID()); 
			$db->query("UPDATE CoreCommerceOrderProducts SET orderID = ? WHERE orderID = ? AND orderProductID = ?",$vals);
			$this->loadProducts();
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Moves all products from $this order into another order
	 * @param int $orderID
	 * @return boolean
	 */
	public function moveAllProductsToOrder($orderID) {
		$db = Loader::db();
		$order = CoreCommerceOrder::getByID($orderID);
		if($order) {
			$vals = array($orderID, $this->getOrderID()); 
			$db->query("UPDATE CoreCommerceOrderProducts SET orderID = ? WHERE orderID = ?",$vals);
			$this->loadProducts();
			return true;
		} else {
			return false;
		}
	}
	
	public function copyAllProductsToOrder($orderID) {
		$db = Loader::db();
		$order = CoreCommerceOrder::getByID($orderID);
		if($order) {
			$vals = array($orderID, $this->getOrderID()); 
			
			$query = "INSERT INTO CoreCommerceOrderProducts 
						(orderID, productID, quantity, prPricePaid, prDiscount, prName)
					SELECT ? as orderID, productID, quantity, prPricePaid, prDiscount, prName 
					FROM CoreCommerceOrderProducts WHERE orderID = ?";
			
			$db->query($query,$vals);
			$this->loadProducts();
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * Orders can contain products in only one language, so this gets the language string from
	 * the first product in the order ex: English would return 'en' 
	 * an empty order would return an empty string since no language has been established
	 * @return string
	 */
	public function getOrderLanguage() {
		$language = "";
		$products = $this->getProducts();
		if(is_array($products) && count($products) && $products[0] instanceof CoreCommerceOrderProduct) {
			$language = $products[0]->product->getProductLanguage();
		}
		return $language;
	}
	
}
