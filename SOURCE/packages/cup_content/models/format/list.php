<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
class CupContentFormatList extends DatabaseItemList { 

	protected $queryCreated = 0;
	protected $attributeFilters = array();
	protected $autoSortColumns = array('name', 'createdAt');
	protected $itemsPerPage = 10;
	protected $attributeClass = '';	//'CoreCommerceProductAttributeKey';
	
	/*
	// magic method for filtering by attributes. //
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
	*/
	
	public function get($itemsToGet = 0, $offset = 0) {
		$formats = array();
		Loader::model('format/model', 'cup_content');
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);
		//print_r($r);
		foreach($r as $row) {
			$ft = new CupContentFormat($row['id']);
			$ft->prspDisplayOrder = $row['prspDisplayOrder'];
			$formats[] = $ft;
		}
		return $formats;
	}
	
	public function getTotal(){
		$this->createQuery();
		return parent::getTotal();
	}
	
	protected function setBaseQuery() {
		$this->setQuery('SELECT ft.* from CupContentFormat ft');
	}
	
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			$this->queryCreated = 1;
		}
	}
	
	public function filterByName($name, $comparison = '=') {
		$this->filter('ft.name',$name,$comparison);
	}
	
		// Filters by "keywords"
	public function filterByKeywords($keywords) {
		$db = Loader::db();
		$qkeywords = $db->quote('%' . $keywords . '%');
		/*
		$keys = CoreCommerceProductAttributeKey::getSearchableIndexedList();
		$attribsStr = '';
		foreach ($keys as $ak) {
			$cnt = $ak->getController();			
			$attribsStr.=' OR ' . $cnt->searchKeywords($keywords);
		}
		*/
		
		$this->filter(false, '( ft.name like ' . $qkeywords . ' or ft.shortDescription like ' . $qkeywords . ' or ft.longDescription like ' . $qkeywords . ')');
	}
}