<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
class CupContentSeriesList extends DatabaseItemList { 

	protected $queryCreated = 0;
	protected $attributeFilters = array();
	protected $autoSortColumns = array('name', 'prettyUrl', 'modifiedAt', 'createdAt');
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
		$authors = array();
		Loader::model('series/model', 'cup_content');
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);

		foreach($r as $row) {
			$sr = new CupContentSeries($row['id']);
			$sr->prspDisplayOrder = $row['prspDisplayOrder'];
			$authors[] = $sr;
		}
		return $authors;
	}
	
	public function getTotal(){
		$this->createQuery();
		return parent::getTotal();
	}
	
	protected function setBaseQuery() {
		$this->setQuery('SELECT sr.* from CupContentSeries sr');
	}
	
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			$this->queryCreated = 1;
		}
	}
	
	public function filterByName($name, $comparison = '=') {
		$this->filter('sr.name',$name,$comparison);
	}
	
	public function filterByNameKeywords($keywords) {
		$db = Loader::db();
		$qkeywords = $db->quote('%' . $keywords . '%');
		
		$this->filter(false, 'sr.name like ' . $qkeywords );
	}
	
	public function filterByFormat($format, $is_keywords = false){
		$db = Loader::db();
		if($is_keywords){
			$format = '%'.$format.'%';
		}
		$qformat = $db->quote($format);
		$this->filter(false, 'sr.id IN (select seriesID as id from CupContentSeriesFormats where format like '.$qformat.')');
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
		
		$this->filter(false, '( sr.name like ' . $qkeywords 
								. ' or sr.shortDescription like ' . $qkeywords
								. ' or sr.longDescription like ' . $qkeywords 
								. ' or sr.divisions like ' . $qkeywords 
								. ' or sr.regions like ' . $qkeywords 
								. ' or sr.tagline like ' . $qkeywords 
								. ' or sr.reviews like ' . $qkeywords 
							. ')');
	}
}