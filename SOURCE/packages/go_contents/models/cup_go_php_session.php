<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

class CupGoPhpSession extends Object {

    function __construct($sesskey = false) {

        $db = Loader::db();

        if($sesskey){

            $sql = "SELECT * FROM CupGoPhpSession WHERE sesskey = ?";

            $result = $db->getRow($sql, array($sesskey));

            if($result){

                $this->sesskey              = $result['sesskey'];
                $this->expiry               = $result['expiry'];
                $this->expiryref            = $result['expiryref'];
                $this->created              = $result['created'];
                $this->modified             = $result['modified'];
                $this->sessdata             = $result['sessdata'];
                $this->sessdataTNG          = $result['sessdataTNG'];

            }
        }

        $this->db = $db;
    }

    public static function fetchByKey($sesskey){
        $object = new CupGoPhpSession($sesskey);
        if($object->sesskey === FALSE){
            return FALSE;
        }else{
            return $object;
        }
    }

    public function insert(){

        $u = new User();

        $db = Loader::db();

        // Modified by Paul Balila, 2017-04-17, ANZGO-3310
        // Change saved user to User object.
        // Save user subscriptions in the stored DB session for easy retrieval.
        $subscriptions = array();
        /** 
        if(in_array('Student',$u->uGroups) || in_array('Teacher',$u->uGroups)) {
          $sql = "SELECT Private_TabText FROM CupGoTabs cgt "
                  . "WHERE cgt.ID IN (SELECT DISTINCT (TabID) FROM CupGoTabAccess cgta WHERE cgta.UserID = ?) "
                  . "AND (cgt.Private_TabText IS NOT NULL AND cgt.Private_TabText > '') AND Active = 'Y'";
          $subscriptions = $db->GetAll($sql,array($u->uID));
        } */
        $u->subscriptions = $subscriptions;

        $sql = "INSERT INTO CupGoPhpSession(sesskey,expiry,expireref,created,modified,sessdata,sessdataTNG) VALUES(?,DATE_ADD(NOW(), INTERVAL 2 HOUR),'',NOW(),NOW(),'',?)";

        $values = array(session_id(),json_encode($u));


        if($db->Execute($sql,$values)) return true;

        return false;

    }

}
