<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
class CoreCommerceProductList extends DatabaseItemList { 

	protected $attributeFilters = array();
	protected $autoSortColumns = array('prDateAdded', 'prName','prCurrentPrice','prPrice','prStatus', 'prspDisplayOrder');
	protected $itemsPerPage = 10;
	protected $attributeClass = 'CoreCommerceProductAttributeKey';
	protected $filteredProductSetIDs = array();
	
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
	
   public function filterByStatus($status, $comparison = '=') {
      $this->filter('pr.prStatus',$status,$comparison);
   }
   public function filterByQuantityUnlimited($from,$to,$unlimited) {
      $db = Loader::db();
      $greaterThan = '';
      $lessThan = '';
      $and = '';
      $or = '';

      if($from) {
         $greaterThan = "pr.prQuantity >= ".$db->quote($from);
      }
      if ($to) {
         $lessThan = "pr.PrQuantity <= ".$db->quote($to);
      }
      if ($lessThan && $greaterThan) {
         $and = " and ";
      }
      $quantityUnlimited = "pr.prQuantityUnlimited = ";
      $quantityUnlimited .= $unlimited ? "1 ":"0 ";
      if($unlimited && ($lessThan || $greaterThan)){
         $or = " or ";
      } else if (!$unlimited && ($lessThan || $greaterThan)) {
         $or = " and "; //lawl.
      }

      $filter = $lessThan .$and. $greaterThan .$or. $quantityUnlimited;
      $this->filter(false,$filter);
   }

   //public function filterByQuantity($quantity, $comparison = '=', $unlimited = false) {
   public function filterByQuantity($quantity, $comparison = '=') {
      if($quantity) {
         $this->filter('pr.prQuantity ',$quantity,$comparison);
      }
      //if ($unlimited) {
         //$this->addToQuery('left join CoreCommerceProducts prun on pr.prQuantityUnlimited = 1 and prun.productID = pr.productID');
      //}else{
         //$this->addToQuery('left join CoreCommerceProducts prun on pr.prQuantityUnlimited = 0 and prun.productID = pr.productID');
         ////$this->addToQuery('and pr.prQuantityUnlimited = 0');
      //}
   }

   public function filterBySpecialPrice($onSale) {
      if($onSale) {
         $this->filter(false,'(pr.prSpecialPrice <> pr.prPrice and pr.prSpecialPrice > 0)');
      } else {
         $this->filter(false, '(pr.prSpecialPrice = pr.prPrice or pr.prSpecialPrice = 0)');
      }
   }

	/** 
	 * Filters by public date
	 * @param string $date
	 */
	public function filterByDateAdded($date, $comparison = '=') {
		$this->filter('pr.prDateAdded', $date, $comparison);
	}

	public function displayOnlyProductsWithInventory() {
		$this->filter(false,'(pr.prQuantity > 0 OR pr.prQuantityUnlimited = 1)');
	}
	
	public function filterByCurrentPrice($price, $comparison = '=') {
		$this->filter('if(prSpecialPrice > 0 and prSpecialPrice <> prPrice, prSpecialPrice, prPrice)', $price, $comparison);
	}
	
	// Filters by "keywords"
	public function filterByKeywords($keywords) {
		$db = Loader::db();
		$qkeywords = $db->quote('%' . $keywords . '%');
		$keys = CoreCommerceProductAttributeKey::getSearchableIndexedList();
		$attribsStr = '';
		foreach ($keys as $ak) {
			$cnt = $ak->getController();			
			$attribsStr.=' OR ' . $cnt->searchKeywords($keywords);
		}
		$this->filter(false, '( pr.prName like ' . $qkeywords . ' or pr.prDescription like ' . $qkeywords . ' ' . $attribsStr . ')');
	}

	public function filterBySet($prs) {
      if ($prs instanceof CoreCommerceProductSet ) {
         $this->filteredProductSetIDs[] = intval($prs->getProductSetID());
      }
	}

   public function filterByNoSets() {
      $this->filter(false,('pr.productID not in (select productID from CoreCommerceProductSetProducts)'));
   }

	protected function setBaseQuery() {
		$prsIDs = array_unique($this->filteredProductSetIDs);
		$prsIDs = array_filter($prsIDs,'is_numeric');
		$prCurrentPrice = 'if(prSpecialPrice > 0 and prSpecialPrice <> prPrice, prSpecialPrice, prPrice) as prCurrentPrice';
		if (is_array($prsIDs) && count($prsIDs) > 0) { 
			$prspDisplayOrder = '';
			$sets = implode(',', $prsIDs);
			$prspDisplayOrder .= '(select max(prspDisplayOrder) from CoreCommerceProductSetProducts where productID = pr.productID and prsID in (' . $sets . ')) as prspDisplayOrder';
			$this->setQuery('SELECT distinct pr.productID, ' . $prspDisplayOrder . ', ' . $prCurrentPrice . ' from CoreCommerceProducts pr left join Pages on Pages.cID = pr.cID');
		} else {
			$this->setQuery('SELECT pr.productID, 0 as prspDisplayOrder, ' . $prCurrentPrice . ' from CoreCommerceProducts pr left join Pages on Pages.cID = pr.cID');
		}
	}

	/** 
	 * Returns an array of page objects based on current settings
	 */
	public function get($itemsToGet = 0, $offset = 0) {
		$products = array();
		Loader::model('product/model', 'core_commerce');
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);
		foreach($r as $row) {
			$pr = CoreCommerceProduct::getByID($row['productID']);
			$pr->prspDisplayOrder = $row['prspDisplayOrder'];
			$products[] = $pr;
		}
		return $products;
	}
	
	public function getTotal(){
		$this->createQuery();
		return parent::getTotal();
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
						$this->addToQuery("left join CoreCommerceProductSetProducts prsl on prsl.productID = pr.productID");
					}
					$this->filter(false,'prsl.productID IN (SELECT DISTINCT productID FROM CoreCommerceProductSetProducts WHERE prsID = '.$db->quote($prsID).')');
					$i++;
				}
			}
		}
	}
	
	//this was added because calling both getTotal() and get() was duplicating some of the query components
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			$this->setupAttributeFilters("left join CoreCommerceProductSearchIndexAttributes on (pr.productID = CoreCommerceProductSearchIndexAttributes.productID)");
			$this->setupProductSetFilters();
			$this->queryCreated = 1;
		}
	}
	
	//$key can be handle or fak id
	public function sortByAttributeKey($key,$order='asc') {
		$this->sortBy($key, $order); // this is handled natively now
	}

	public function sortByProductSetDisplayOrder() {
		$this->sortByMultiple('prspDisplayOrder asc', 'prsID asc');
	}
	
	public function filterByAttribute($column, $value, $comparison='=') {
		if (is_array($column)) {
			$column = $column[key($column)] . '_' . key($column);
		}
		
		if($comparison == '=') {
			$db = Loader::db();
			$this->attributeFilters[] = array(false,"(ak_{$column} = ".$db->quote($value)." OR TRIM(ak_{$column}) LIKE ".$db->quote("%\n".$value."\n%").")");
		} else {
			$this->attributeFilters[] = array('ak_' . $column, $value, $comparison);
		}
	}
	
	/**
	 * filters products by a specific language
	 * @param string $language
	 * @return void
	 */
	public function filterByLanguage($language) {
		$this->filter('pr.prLanguage', $language);
	}
	
}
