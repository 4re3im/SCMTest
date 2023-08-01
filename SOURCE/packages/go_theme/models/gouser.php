<?php
/**
 * Description of password
 *
 * @author paulbalila
 */
class GoUser {
    private $db;
    public function __construct() {
        $this->db = Loader::db();
    }
    
    public function getUserManualActivation($uID) {
        $query = "SELECT usia.`ak_uManuallyActivated` AS man_act, usia.`ak_uMAStaffID` AS man_staffid, usia.`ak_uActivatedDate` AS man_date FROM `UserSearchIndexAttributes` AS usia WHERE usia.`uID` = ?";
        $result = $this->db->Execute($query,array($uID));
        return $result->fetchRow();
    }

    public function updateManualActivation($uID,$mad) {
        $query = "UPDATE `UserSearchIndexAttributes` SET `ak_uManuallyActivated` = ?, `ak_uMAStaffID` = ?, `ak_uActivatedDate` = ? WHERE `uID` = ?";
        $result = $this->db->Execute($query,array($mad['status'],$mad['staff'],$mad['date'],$uID));
        return $result;
    }
}
