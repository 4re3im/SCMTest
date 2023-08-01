<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('title/list', 'cup_content');
Loader::model('series/list', 'cup_content');

class CupContentEvent extends DatabaseItemList {
	protected $queryCreated = 0;
	protected $attributeFilters = array();
	protected $autoSortColumns = array('name', 'prettyUrl', 'modifiedAt', 'createdAt');
	protected $itemsPerPage = 10;
	protected $attributeClass = '';	//'CoreCommerceProductAttributeKey';
	
	protected $query_name = "";


	
	public function get($itemsToGet = 0, $offset = 0) {
		$authors = array();
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
		$seriesList = new CupContentSeriesList();
		$titleList = new CupContentTitleList();
		
		if($this->query_name && strlen($this->query_name) > 0){
			$seriesList->filterByName($this->query_name);
			$titleList->filterByName($this->query_name);
		}

		$this->setQuery('SELECT sr.* from CupContentSeries sr');
	
		if(!$this->queryCreated){
			$seriesList->createQuery();
			$query_1 = $seriesList->getQuery();
			
			$titleList->createQuery();
			$query_2 = $titleList->getQuery();
			
			$this->setBaseQuery();
			$this->queryCreated = 1;
		}
	}
}