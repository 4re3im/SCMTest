<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
class CoreCommerceProductSet extends Object { 

	/** 
	 * Adds a new product set
	 */
	public static function add($name, $ui) {
		$db = Loader::db();
		$uID = $ui->getUserID();
		$db->Execute('insert into CoreCommerceProductSets (prsName, uID) values (?, ?)', array($name, $uID));
		$prsID = $db->Insert_ID();
		if ($prsID > 0) {
			$prs = CoreCommerceProductSet::getByID($prsID);
			return $prs;
		}
	}

	public function update($name) {
		$db = Loader::db();
		$db->Execute('update CoreCommerceProductSets set prsName = ? where prsID = ?', array($name, $this->prsID));
		$this->prsName = $name;
	}

	public function getProductSetID() {return $this->prsID;}
	public function getProductSetName() {return $this->prsName;}
	
	/**
	 * @param int $id
	 * @return CoreCommerceProductSet
	 */
	public static function getByID($id) {
		$db = Loader::db();
		$r = $db->GetRow('select * from CoreCommerceProductSets where prsID = ?', array($id));
		if (is_array($r) && $r['prsID'] > 0) {
			$pr = new CoreCommerceProductSet();
			$pr->setPropertiesFromArray($r);
			return $pr;
		}
	}
	
	public static function getList() {
		$db = Loader::db();
		$sets = array();
		$r = $db->Execute('select prsID from CoreCommerceProductSets order by prsName asc');
		while ($row = $r->FetchRow()) {
			$prs = CoreCommerceProductSet::getByID($row['prsID']);
			if (is_object($prs)) {
				$sets[] = $prs;
			}
		}
		return $sets;
	}

	public function updateProductSetDisplayOrder($products) {
		$db = Loader::db();
		$db->Execute('update CoreCommerceProductSetProducts set prspDisplayOrder = 0 where prsID = ?', $this->getProductSetID());
		$i = 0;
		foreach($products as $productID) {
			$db->Execute('update CoreCommerceProductSetProducts set prspDisplayOrder = ? where prsID = ? and productID = ?', array($i, $this->getProductSetID(), $productID));
			$i++;
		}
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceProductSets where prsID = ?', array($this->prsID));
		$db->Execute('delete from CoreCommerceProductSetProducts where prsID = ?', array($this->prsID));
	}

	public function contains(CoreCommerceProduct $product) {
		$db = Loader::db();
		$r = $db->GetOne('select count(productID) from CoreCommerceProductSetProducts where prsID = ? and productID = ?', array(
			$this->getProductSetID(),
			$product->getProductID()
		));
		return $r;
	}
	
}
