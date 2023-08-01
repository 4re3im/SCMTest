<?php

/**
 * Provisioned Users Auto Complete Tool
 */

defined('C5_EXECUTE') || die("Access Denied.");

$db = Loader::db();
$jh = Loader::helper('json');
$u = new User();
$staffID = $u->getUserID();

if ($_GET['term'] != '') {
    $term = $_GET['term'];
    $terms = explode(' ', $term);

    $query = "SELECT users.uID, users.FirstName, users.LastName, users.Email ";
    $query .= "FROM ProvisioningUsers users ";
    $query .= "JOIN ProvisioningFiles files ON users.FileID=files.ID ";
    $query .= "WHERE files.StaffID = ? ";
    $query .= "AND (users.uID LIKE '%$term%' ";
    $query .= "OR users.FirstName LIKE '%$term%' ";
    $query .= "OR users.LastName LIKE '%$term%' ";
    $query .= "OR CONCAT(users.FirstName, ' ', users.LastName) LIKE '%$term%' ";
    $query .= "OR users.Email LIKE '%$term%') ";
    $query .= "ORDER BY users.FirstName ASC";

    $rows = $db->GetAll($query, array($staffID));

    $users = array();

    foreach ($rows as $c) {
        $obj = new stdClass;
        $obj->label = $c['uID'] . ' / ' . $c['FirstName'] . ' ' . $c['LastName'] . ' ('. $c['Email'] . ')' ;
        $obj->value = $c['uID'] . ' / ' . $c['FirstName'] . ' ' . $c['LastName'] . ' ('. $c['Email'] . ')' ;
        $obj->id = $c['uID'];
        $users[] = $obj;
    }

    echo $jh->encode($users);
}
