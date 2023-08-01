<?php
defined('C5_EXECUTE') or die("Access Denied.");

$db = Loader::db();
// Loader::model('go_users/model', 'go_dashboard');
// Loader::model('go_users/list', 'go_dashboard');


// TODO:
// create this model
// $userlist = new GoDashboardGoUsersList();

if ($_GET['term'] != '') {

        //To be continued by ariel
	$term = $_GET['term']; 
        
	$query = "SELECT ID, name, isbn13 FROM CupContentTitle WHERE isbn13 LIKE '%$term%'  AND isGoTitle=1  ORDER BY name";
        
    $rows = $db->GetAll($query);        

	$users = array();

	foreach($rows as $c) {
		$obj = new stdClass;
		$obj->label = $c['name'] . ' ('.  $c['isbn13']  . ')' ;
		$obj->keywords = $_GET['term'];
		$obj->name = $c['name'];
		$obj->isbn = $c['isbn13'];
		$obj->id = $c['ID'];				
		$users[] = $obj;			
	}

	$jh = Loader::helper('json');

	echo $jh->encode($users);

}


