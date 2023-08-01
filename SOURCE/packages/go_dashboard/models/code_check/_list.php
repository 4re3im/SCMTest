<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class _GoDashboardCodeCheckList extends DatabaseItemList { 

	protected $queryCreated = 0;
	protected $attributeFilters = array();
	protected $autoSortColumns = array('ID', 'Firstname', 'Lastname', 'Email');
	protected $itemsPerPage = 10;
	protected $attributeClass = '';	//'CoreCommerceProductAttributeKey';
	
	public function get($itemsToGet = 0, $offset = 0) {
		$codelist = array();
		Loader::model('code_check/model', 'go_dashboard');
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);

		foreach($r as $row) {
			$codelist[] = new DashboardCodeCheckCodeCheckController($row['id'], $row);
		}

		return $codelist;
	}
	
	public function getTotal(){
		$this->createQuery();
		return parent::getTotal();
	}
	

	protected function setBaseQuery() {
		$this->setQuery("SELECT u.uID, u.uName,u.uEmail,ac.CreatedDate, ac.Info
			FROM Log_AccessCode ac 
			INNER JOIN Users u ON u.uID = ac.UserID
			INNER JOIN UserGroups ug ON ug.uID = u.uID 
			WHERE ac.UserID = u.uID
			AND ac.Action='Fail'
			AND ug.gID IN (5,6)
			ORDER BY ac.CreatedDate");
	}



	
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			$this->queryCreated = 1;
		}
	}
	
	public function last50CodeFailes() {
		$db = Loader::db();

		$r = $db->Execute("SELECT u.uID, u.uName,u.uEmail,ac.CreatedDate, ac.Info
			FROM Log_AccessCode ac 
			JOIN Users u ON u.uID = ac.UserID
			JOIN UserGroups ug ON ug.uID = u.uID 
			WHERE ac.UserID = u.uID
			AND ac.Action='Fail'
			AND ug.gID IN (5,6)
			ORDER BY ac.CreatedDate LIMIT 50");
		

		while ($row = $r->FetchRow()) {
			//print_r($row);	
			$codefails[] = $row;			

		}

		return $row;
	}


	public function filterByAccessCode($name, $comparison = '=') {
		$this->filter('AccessCode',$name,$comparison);
	}
	
	public function filterByUserID($name, $comparison = '=') {
		$this->filter('UserID',$name,$comparison);
	}


		// Filters by "keywords"
	public function filterByKeywords($keywords) {
		$db = Loader::db();
		$qkeywords = $db->quote('%' . $keywords . '%');
		
		$this->filter(false, '( AccessCode like ' . $qkeywords . ')');
	}

}