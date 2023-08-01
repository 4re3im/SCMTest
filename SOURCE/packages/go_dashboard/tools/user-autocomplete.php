<?php
defined('C5_EXECUTE') or die("Access Denied.");

$db = Loader::db();
// Loader::model('go_users/model', 'go_dashboard');
// Loader::model('go_users/list', 'go_dashboard');


// TODO:
// create this model
// $userlist = new GoDashboardGoUsersList();

if ($_GET['term'] != '') {

	// and put these methods
	// $term = $db->quote('%'.$_GET['term'].'%');
	// $users = $userlist->filterByKeyword($_GET['term']);

	$term = $_GET['term'];
        //ALTER TABLE `UserSearchIndexAttributes` CHANGE `ak_uFirstName` `ak_uFirstName` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
        //ALTER TABLE `UserSearchIndexAttributes` ADD INDEX(`ak_uFirstName`);
        //ALTER TABLE `UserSearchIndexAttributes` CHANGE `ak_uLastName` `ak_uLastName` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
        //ALTER TABLE `UserSearchIndexAttributes` ADD INDEX(`ak_uLastName`);

        $terms = explode(' ', $term);

				// Modified by Paul Balila for ticket ANZGO-2642, 2016-09-07
				// Added searching via uID and first name + last name matches
	$query = "SELECT u.uID, usi.ak_uFirstName, usi.ak_uLastName, u.uEmail
				FROM Users u
				LEFT JOIN UserSearchIndexAttributes usi ON usi.uID = u.uID
				WHERE usi.ak_uFirstName LIKE '%$term%'
				OR usi.ak_uLastName LIKE '%$term%'
				OR usi.uID LIKE '%$term%'
				OR CONCAT(usi.ak_uFirstName, ' ', usi.ak_uLastName) LIKE '%$term%'
				OR u.uEmail LIKE '%$term%' ORDER BY usi.ak_uFirstName";

//        $query = "SELECT u.uID, usi.ak_uFirstName, usi.ak_uLastName, u.uEmail
//                        FROM Users u
//                        LEFT JOIN UserSearchIndexAttributes usi ON usi.uID = u.uID
//                        WHERE u.uEmail LIKE '%$term%' ORDER BY usi.ak_uFirstName";

//echo $query;

    $rows = $db->GetAll($query);

	$users = array();

	foreach($rows as $c) {
		$obj = new stdClass;
		$obj->label = $c['uID'] . ' / ' . $c['ak_uFirstName']  . ' ' . $c['ak_uLastName'] . ' ('.  $c['uEmail']  . ')' ;
		$obj->value = $c['ID'] . ' / ' . $c['ak_uFirstName']  . ' ' . $c['ak_uLastName'] . ' ('.  $c['uEmail']  . ')' ;
		$obj->id = $c['uID'];
		$users[] = $obj;
	}

	$jh = Loader::helper('json');

	echo $jh->encode($users);

}
