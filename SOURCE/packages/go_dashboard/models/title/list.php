<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
class CupContentTitleList extends DatabaseItemList { 

	protected $queryCreated = 0;
	protected $attributeFilters = array();
	protected $autoSortColumns = array('displayName', 'displaySubtitle', 'prettyUrl', 'modifiedAt', 'createdAt');
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
		$list = array();
		Loader::model('title/model', 'cup_content');
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);

		foreach($r as $row) {
			$obj = new CupContentTitle($row['id']);
			$obj->prspDisplayOrder = $row['prspDisplayOrder'];
			$list[] = $obj;
		}
		return $list;
	}
	
	public function getTotal(){
		$this->createQuery();
		return parent::getTotal();
	}
	
	protected function setBaseQuery() {
		$this->setQuery('SELECT ti.* from CupContentTitle ti');
	}
	
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			$this->queryCreated = 1;
		}
	}
	
	public function filterByName($name, $comparison = '=') {
		$this->filter('ti.name',$name,$comparison);
	}
	
	public function filterByIsEnabled($value = true) {
		if($value){
			$this->filter('ti.isEnabled', 1, '=');
		}else{
			$this->filter('ti.isEnabled', 1, '<>');
		}
	}
	
	public function filterByIds($ids) {
        if(is_array($ids) && count($ids) > 0){
		    $this->filter(false, 'ti.id in ('.implode(', ', $ids).')');
        }
	}

    public function filterByExcludeIds($ids) {
        if(is_array($ids) && count($ids) > 0){
             $this->filter(false, 'ti.id NOT IN ('.implode(', ', $ids).')');
        }
    }
	
	public function filterBySeries($series, $comparison = '=') {
		$this->filter('ti.series',$series,$comparison);
	}
	
	public function filterByHasInspectionCopy($value = true){
		if($value){
			$this->filter('ti.hasInspectionCopy',1,'=');
		}else{
			$this->filter('ti.hasInspectionCopy',1,'<>');
		}
	}
	
	
	public function filterByHasSamplePages($value = true){
		if($value){
			$this->filter(false, 'ti.id IN 
							(SELECT DISTINCT (titleID) AS titleID
								FROM CupContentTitleSamplePages)');
		}
	}
	
	public function filterByHasAccessCode($value = 1){
		if($value){
			$this->filter('ti.hasAccessCode', 1, '=');
		}else{
			$this->filter('ti.hasAccessCode', 0, '=');
		}
	}
	
	public function filterByNewProduct(){
		$this->filter('ti.new_product_flag', 1, '=');
	}
	
	public function filterByHasPageProof(){
		$q = 'ti.id IN (
			SELECT distinct(titleID) FROM CupContentTitleSamplePages WHERE is_page_proof = 1
		)';
		$this->filter(false, $q);
	}
	
	public function filterByISBN($isbn){
		$db = Loader::db();
		$isbn = $db->quote('%' . $isbn . '%');
		$this->filter(false, '( ti.isbn13 like '.$isbn.' OR ti.isbn10 like '.$isbn.' )');
	}
	
	public function filterByRegion($region){
		if(strcmp($region, 'All Australia') == 0){
			/*
			$this->filter('ti.regions','%[New South Wales]%','like');
			$this->filter('ti.regions','%[Northern Territory]%','like');
			$this->filter('ti.regions','%[Queensland]%','like');
			$this->filter('ti.regions','%[South Australia]%','like');
			$this->filter('ti.regions','%[Tasmania]%','like');
			$this->filter('ti.regions','%[Victoria]%','like');
			$this->filter('ti.regions','%[Western Australia]%','like');
			*/
			$this->filter('ti.regions','%[Australia]%','like');
		}else{
			$this->filter('ti.regions','%['.$region.']%','like');
		}		
		
			
	}
	
	public function filterByNameKeywords($keywords) {
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
		
		$this->filter(false, 'ti.name like ' . $qkeywords);
	}
	
	public function filterBySubject($subject_name){
		$db = Loader::db();
		$subject_name = $db->quote($subject_name);
		$sub_query = '( SELECT cts.titleID FROM CupContentTitleSubjects cts WHERE cts.subject like '.$subject_name.')';
		$sub_query = 'ti.id IN '.$sub_query;
		//$this->filter('ti.id',$sub_query,'in');
		$this->filter(false,$sub_query);
	}
	
	public function filterByDepartment($val){
		if(in_array($val, array('Primary', 'Secondary'))){
			$db = Loader::db();
			//$val = $db->quote('%[' . $val . ']%');
			$this->filter('ti.divisions','%['.$val.']%','like');
		}
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
		
		$this->filter(false, '( ti.name like ' . $qkeywords . ' or ti.tagline like ' . $qkeywords . ')');
	}
}