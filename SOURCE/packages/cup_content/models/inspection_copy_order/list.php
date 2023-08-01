<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('inspection_copy_order/model', 'cup_content');

class CupContentInspectionCopyOrderList extends DatabaseItemList { 

	protected $queryCreated = 0;
	protected $attributeFilters = array();
	protected $autoSortColumns = array('id', 'email', 'title', 'first_name', 'last_name', 'school_campus',
								'status', 'createdAt', 'modifiedAt');
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
		$orders = array();
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);

		foreach($r as $row) {
			$or = new CupContentInspectionCopyOrder($row['id']);
			//$or->prspDisplayOrder = $row['prspDisplayOrder'];
			$orders[] = $or;
		}
		return $orders;
	}
	
	public function getTotal(){
		$this->createQuery();
		return parent::getTotal();
	}
	
	protected function setBaseQuery() {
		$this->setQuery('SELECT ico.* from CupContentInspectionCopyOrder ico');
	}
	
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			$this->queryCreated = 1;
		}
	}
	
	public function filterByStatus($status, $comparison = '=') {
		$this->filter('ico.status',$status,$comparison);
	}
	
	public function filterByFirstName($keywords, $exactMatch = true) {
		$db = Loader::db();
		$qkeywords = $db->quote('%' . $keywords . '%');
		if($exactMatch){
			$qkeywords = $db->quote($keywords);
		}
		
		$this->filter(false, 'ico.first_name like ' . $qkeywords );
	}
	
	public function filterByLastName($keywords, $exactMatch = true) {
		$db = Loader::db();
		$qkeywords = $db->quote('%' . $keywords . '%');
		if($exactMatch){
			$qkeywords = $db->quote($keywords);
		}
		
		$this->filter(false, 'ico.last_name like ' . $qkeywords );
	}
	
	public function filterByLastSchoolName($keywords, $exactMatch = true) {
		$db = Loader::db();
		$qkeywords = $db->quote('%' . $keywords . '%');
		if($exactMatch){
			$qkeywords = $db->quote($keywords);
		}
		
		$this->filter(false, 'ico.school_campus like ' . $qkeywords );
	}
	
}