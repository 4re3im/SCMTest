<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class Misc {
    
    public static function getGroups() {
        $db = Loader::db();
        $query = "SELECT * FROM Groups";
        $result = $db->GetAll($query);
        return ($result) ? $result : FALSE;
    }
}