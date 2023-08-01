<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
class CupContentTitleSamplePageList extends DatabaseItemList { 

	protected $queryCreated = 0;
	protected $attributeFilters = array();
	protected $autoSortColumns = array('filename', 'filesize', 'description', 'createdAt');
	protected $itemsPerPage = 10;
	protected $attributeClass = '';	//'CoreCommerceProductAttributeKey';
	
	public function get($itemsToGet = 0, $offset = 0) {
		$list = array();
		Loader::model('title_sample_page/model', 'cup_content');
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);

		foreach($r as $row) {
			$obj = new CupContentTitleSamplePage($row['id']);
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
		$this->setQuery('SELECT sp.* from CupContentTitleSamplePages sp');
	}
	
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			$this->queryCreated = 1;
		}
	}
	
	public function filterByFilename($name, $comparison = '=') {
		$this->filter('sp.filename',$name,$comparison);
	}
	
	public function filterByTitleID($titleID, $comparison = '=') {
		$this->filter('sp.titleID',$titleID,$comparison);
	}
	
	public function filterByPageProofOnly(){
		$this->filter('sp.is_page_proof',1,'=');
	}
	
	public function filterByDepartment($val){
		if(in_array($val, array('Primary', 'Secondary'))){
			$db = Loader::db();
			$val = $db->quote('%[' . $val . ']%');
			$query = 'sp.titleID in (
				SELECT id FROM CupContentTitle WHERE hasDownloadableFile = 1 and divisions like '.$val.'
			)';
			$this->filter('sp.is_page_proof',1,'=');
		}
	}
	
}