<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('attribute/categories/core_commerce_order', 'core_commerce');
class CoreCommerceOrderList extends DatabaseItemList { 

	protected $attributeFilters = array();
	protected $autoSortColumns = array('oDateAdded', 'oStatus', 'invoiceNumber');
	protected $itemsPerPage = 10;
	protected $attributeClass = 'CoreCommerceOrderAttributeKey';
	protected $filteredProductSetIDs = array();
	protected $oStatusFilter = false;
	protected $oType = 'order';
	
	/* magic method for filtering by attributes. */
	public function __call($nm, $a) {
		if (substr($nm, 0, 8) == 'filterBy') {
			$txt = Loader::helper('text');
			$attrib = $txt->uncamelcase(substr($nm, 8));
			if (count($a) == 2) {
				$this->filterByAttribute($attrib, $a[0], $a[1]);
			} else {
				$this->filterByAttribute($attrib, $a[0]);
			}
		}			
	}
	
	/** 
	 * Filters by public date
	 * @param string $date
	 */
	public function filterByDateAdded($date, $comparison = '=') {
		$this->filter('orders.oDateAdded', $date, $comparison);
	}

	protected function setupProductSetFilters() {	
		$prsIDs = array_unique($this->filteredProductSetIDs);
		$prsIDs = array_filter($prsIDs,'is_numeric');
		
		$db = Loader::db();
		
		$i = 0;
		if(is_array($prsIDs) && count($prsIDs)) {
			foreach($prsIDs as $prsID) {
				if($prsID > 0) {
					if ($i == 0) {
						$this->addToQuery("left join CoreCommerceOrderProducts on CoreCommerceOrderProducts.orderID = orders.orderID left join CoreCommerceProductSetProducts prsl on prsl.productID = CoreCommerceOrderProducts.productID");
					}
					$this->filter(false,'prsl.productID IN (SELECT DISTINCT productID FROM CoreCommerceOrderProducts WHERE prsID = '.$db->quote($prsID).')');
					$i++;
				}
			}
		}
	}
	
	// Filters by "keywords"
	public function filterByKeywords($keywords) {
      Loader::model('attribute/categories/core_commerce_order','core_commerce');
		$db = Loader::db();
		//$keywords = $db->quote($keywords);
      $keys = CoreCommerceOrderAttributeKey::getSearchableList();
		//$keys = CoreCommerceOrderAttributeKey::getSearchableIndexedList();
		//$keys = CoreCommerceOrderAttributeKey::getColumnHeaderList();
		$attribsStr = '';
		foreach ($keys as $ak) {
			$cnt = $ak->getController();			
			$attribsStr.=' OR ' . $cnt->searchKeywords($keywords);
		}


      $this->addToQuery("left join CoreCommerceOrderProducts on CoreCommerceOrderProducts.orderID = orders.orderID left join CoreCommerceProducts products on products.productID = CoreCommerceOrderProducts.productID");
	   $attribsStr .=  ' OR products.prName like '.$db->quote('%'.$keywords.'%');	
		$this->filter(false, '( orders.orderID = '.$db->quote($keywords). ' ' . $attribsStr . ')');	
	}
	
	
	/**
	 * Filters orders by a single customer userID
	 * @param int $uID
	 * @return void
	 */
	public function filterByCustomerUserID($uID) {
		$this->filter('orders.uID',$uID);
	}

	protected function setBaseQuery() {
		$this->setQuery("SELECT orders.*, orderNumbers.orderInvoiceNumber as invoiceNumber
				FROM CoreCommerceOrders orders 
				LEFT JOIN CoreCommerceOrderInvoiceNumbers orderNumbers ON orderNumbers.orderID = orders.orderID");
	}

	public function filterBySet($prs) {
		$this->filteredProductSetIDs[] = intval($prs->getProductSetID());
	}

	/** 
	 * Returns an array of page objects based on current settings
	 */
	public function get($itemsToGet = 0, $offset = 0) {
		$orders = array();
		Loader::model('order/model', 'core_commerce');
		$this->createQuery();
		$this->setupProductSetFilters();
		$r = parent::get($itemsToGet, $offset);
		foreach($r as $row) {
			$o = CoreCommerceOrder::getByID($row['orderID']);			
			$orders[] = $o;
		}
		return $orders;
	}
	
	public function filterByOrderStatus($status) {
		$this->oStatusFilter = $status;
		$this->filter('oStatus', $status);
	}
	
	public function filterByOrderType($type) {
		$this->oType = $type;
	}
	
	public function getTotal(){
		$this->createQuery();
		return parent::getTotal();
	}
	
	//this was added because calling both getTotal() and get() was duplicating some of the query components
	protected function createQuery() {
		if(!$this->queryCreated) {
			$this->setBaseQuery();
			$this->setupAttributeFilters("left join CoreCommerceOrderSearchIndexAttributes on (orders.orderID = CoreCommerceOrderSearchIndexAttributes.orderID)");
			if ($this->oStatusFilter === false) {
				$this->filter('oStatus', 0, '>');
			}
			
			if(strlen($this->oType)) {
				$this->filter('oType', $this->oType);
			}
			
			$this->queryCreated = 1;
		}
	}
	
	//$key can be handle or fak id
	public function sortByAttributeKey($key,$order='asc') {
		$this->sortBy($key, $order); // this is handled natively now
	}

}
