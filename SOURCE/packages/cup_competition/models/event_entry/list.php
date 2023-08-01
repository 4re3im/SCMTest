<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
class CupCompetitionEventEntryList extends DatabaseItemList {

	protected $queryCreated = 0;
	protected $attributeFilters = array();
	protected $autoSortColumns = array('id', 'email', 'first_name', 'last_name', 'status', 'modifiedAt', 'createdAt');
	protected $itemsPerPage = 10;
	protected $attributeClass = '';	//'CoreCommerceProductAttributeKey';
	
	public function get($itemsToGet = 0, $offset = 0) {
		$events = array();
		Loader::model('event_entry/model', 'cup_competition');
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);

		foreach($r as $row) {
			$at = new CupCompetitionEventEntry($row['id']);
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
		$this->setQuery('SELECT ent.* from CupCompetitionEventEntry ent');
	}
	
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			$this->queryCreated = 1;
		}
	}
	
	public function filterByEventID($eventID, $comparison = '=') {
		$this->filter('ent.eventID',$eventID,$comparison);
	}
	
	public function filterByStatus($status, $comparison = 'LIKE') {
		$this->filter('ent.status', $status, $comparison);
	}
}