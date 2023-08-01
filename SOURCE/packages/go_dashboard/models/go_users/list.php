<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class GoDashboardGoUsersList extends DatabaseItemList { 
	
    private $queryCreated;

	protected $itemsPerPage = 20;

    protected function setBaseQuery() {
        $this->setQuery("SELECT * FROM Users");

    }
 
    protected function createQuery() {
        if (!$this->queryCreated) {
            $this->setBaseQuery();
            $this->queryCreated = 1;
        }
    }
 
    public function get($itemsToGet = 0, $offset = 0) {
        $teacherlist = array();
        $this->createQuery();
        $r = parent::get($itemsToGet, $offset);

        foreach ($r as $row) {
            $teacherlist[] = new GoDashboardGoUsers($row['uID'], $row);
        }

        return $teacherlist;
        
    }

    public function sortByCreatedDate($order = 'DESC') {
        $this->sortBy('uDateAdded', $order);
    } 



 	public function filterByAction($value, $comparison = '=') {
        $this->filter('ac.Action', $value, $comparison); 
    }

  

    public function filterByuID($value, $comparison = '=') {
        $this->filter('uID', $value, $comparison); 
    }



    //Filters by search keywords
	public function filterByKeyword($keywords) {
		$db = Loader::db();
		$qkeywords = $db->quote('%' . $keywords . '%');
		
		$this->filter(false, '( uEmail LIKE' . $qkeywords . ')');
	}


}
