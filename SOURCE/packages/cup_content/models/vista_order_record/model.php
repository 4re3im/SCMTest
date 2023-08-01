<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('price', 'core_commerce');

/*
define('FORMAT_IMAGES_FOLDER', DIR_PACKAGES.DIRECTORY_SEPARATOR.'cup_content'.DIRECTORY_SEPARATOR.
				'images'.DIRECTORY_SEPARATOR.'formats'.DIRECTORY_SEPARATOR);
*/

class CupContentVistaOrderRecord extends Object {

	public static function saveData($orderID, $data){
		$data = serialize($data);
		
		$db = Loader::db();
		$q = "select * from CupContentVistaOrderRecord where orderID = ?";	
		$result = $db->getRow($q, array($orderID));
		if($result){
			$q = "DELETE FROM CupContentVistaOrderRecord where orderID = ?";	
			$result = $db->Execute($q, array($orderID));
		}
		
		$q = "INSERT INTO CupContentVistaOrderRecord (
											orderID ,data
										)
										VALUES (
											?, ?
										)";
										
		$result = $db->Execute($q, array($orderID, $data));	
		return $result;
	}
	
	public static function loadData($orderID){
		$data = false;
	
		$db = Loader::db();
		$q = "select * from CupContentVistaOrderRecord where orderID = ?";	
		$result = $db->getRow($q, array($orderID));
		if($result){
			$data = unserialize($result['data']);
		}
		
		return $data;
	}

	
}