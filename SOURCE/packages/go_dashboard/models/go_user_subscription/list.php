<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class GoDashboardGoUserSubscriptionList extends DatabaseItemList { 
	
    private $queryCreated;

	protected $itemsPerPage = 10;

    protected function setBaseQuery() {
        $this->setQuery("SELECT * FROM CupGoUserSubscription");

    }
 
    protected function createQuery() {
        if (!$this->queryCreated) {
            $this->setBaseQuery();
            $this->queryCreated = 1;
        }
    }
 
    public function get($itemsToGet = 0, $offset = 0) {
        $usersubscriptionlist = array();
        $this->createQuery();
        $r = parent::get($itemsToGet, $offset);

        foreach ($r as $row) {
            $usersubscriptionlist[] = new GoDashboardGoUserSubscription($row['ID'], $row);
        }

        return $usersubscriptionlist;
        
    }

    public function sortByCreatedDate($order = 'DESC') {
        $this->sortBy('CreationDate', $order);
    } 

 	public function filterByAccessCode($value, $comparison = '=') {
        $this->filter('AccessCode', $value, $comparison); 
    }

  
    public function filterByUserID($value, $comparison = '=') {
        $this->filter('UserID', $value, $comparison); 
    }


    // public function filterByGroups() {        
    //     $db = Loader::db();
 
    //     $this->addToQuery("INNER JOIN UserGroups ug ON ug.uID = Users.uID ");
    //     //$qvalue = $db->quote('%' . $value . '%');
    //     $this->filter(false, "ug.gID = 6");
        
  
    // }





    // public function filterByTitleName($name) {      
    //     $db = Loader::db();

    //     $qvalue = $db->quote('%' . $name . '%');
        
    //     $this->filter(false, '(  Firstname LIKE ' . $qvalue . ')');
                
    // }

    //Filters by search keywords
	// public function filterByKeyword($keywords) {
	// 	$db = Loader::db();
	// 	$qkeywords = $db->quote('%' . $keywords . '%');
		
	// 	$this->filter(false, '( uID = ' . $qkeywords . ')');
	// }


}
