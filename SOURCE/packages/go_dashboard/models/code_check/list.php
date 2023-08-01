<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class GoDashboardCodeCheckList extends DatabaseItemList { 
	
    private $queryCreated;

	protected $itemsPerPage = 10;

    protected function setBaseQuery() {
        $this->setQuery("SELECT u.uID, u.uName,u.uEmail, ac.CreatedDate, ac.Info
			FROM Log_AccessCode ac 
			INNER JOIN Users u ON u.uID = ac.UserID");

    }
 
    protected function createQuery() {
        if (!$this->queryCreated) {
            $this->setBaseQuery();
            $this->queryCreated = 1;
        }
    }
 
    public function get($itemsToGet = 0, $offset = 0) {
        $codefail = array();
        $this->createQuery();
        $r = parent::get($itemsToGet, $offset);

        foreach ($r as $row) {
            $codefail[] = new GoDashboardCodeCheck($row['uID'], $row);
        }

        return $codefail;
        
    }

    public function sortByCreatedDate($order = 'desc') {
        $this->sortBy('CreatedDate', $order);
    } 

 	public function filterByAction($value, $comparison = '=') {
        $this->filter('ac.Action', $value, $comparison); 
    }

  
    public function filterByGroups() {        
        $db = Loader::db();
 
        $this->addToQuery("INNER JOIN UserGroups ug ON ug.uID = u.uID ");
        //$qvalue = $db->quote('%' . $value . '%');
        $this->filter(false, "ug.gID IN (5,6)");
        
  
    }


    // public function filterByTitleName($name) {      
    //     $db = Loader::db();

    //     $qvalue = $db->quote('%' . $name . '%');
        
    //     $this->filter(false, '(  Firstname LIKE ' . $qvalue . ')');
                
    // }

    // Filters by search keywords
	public function filterByKeywords($keywords) {
		$db = Loader::db();
		$qkeywords = $db->quote('%' . $keywords . '%');
		
		$this->filter(false, '( AccessCode = ' . $qkeywords . ')');
	}


}
