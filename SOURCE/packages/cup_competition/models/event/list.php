<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
class CupCompetitionEventList extends DatabaseItemList { 

	protected $queryCreated = 0;
	protected $attributeFilters = array();
	protected $autoSortColumns = array('id', 'name', 'category', 'type', 'modifiedAt', 'createdAt');
	protected $itemsPerPage = 10;
	protected $attributeClass = '';	//'CoreCommerceProductAttributeKey';
	
	public function get($itemsToGet = 0, $offset = 0) {
		$events = array();
		Loader::model('event/model', 'cup_competition');
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);

		foreach($r as $row) {
			$at = new CupCompetitionEvent($row['id']);
			$at->prspDisplayOrder = $row['prspDisplayOrder'];
			$events[] = $at;
		}
		return $events;
	}
	
	public function getTotal(){
		$this->createQuery();
		return parent::getTotal();
	}
	
	protected function setBaseQuery() {
		$this->setQuery('SELECT evt.* from CupCompetitionEvent evt');
	}
	
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			$this->queryCreated = 1;
		}
	}
	
	public function filterByName($name, $comparison = '=') {
		$this->filter('evt.name',$name,$comparison);
	}
	
		// Filters by "keywords"
	public function filterByKeywords($keywords) {
		$db = Loader::db();
		$qkeywords = $db->quote('%' . $keywords . '%');
		
		$this->filter(false, '( evt.name like ' . $qkeywords . ')');
	}
}