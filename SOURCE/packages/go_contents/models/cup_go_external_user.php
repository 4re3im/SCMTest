<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class CupGoExternalUser extends Object {
    
    function __construct($id = false) {
        
        if($id){
            
            $db = Loader::db();
            
            $sql = "SELECT * FROM CupGoExternalUser WHERE ID = ?";	
            
            $result = $db->getRow($sql, array($id));
            
            if($result){
                
                $this->id 			= $result['ID'];
                $this->auth_token               = $result['authToken'];
                $this->user_id                  = $result['uID'];
                $this->external_id              = $result['externalID'];
                $this->token_expiry_date        = $result['tokenExpiryDate'];
                $this->brand_codes              = $result['brandCodes'];

            }
        }
    }
    
    public static function fetchByID($id){
        $object = new CupGoExternalUser($id);
        if($object->id === FALSE){
            return FALSE;
        }else{
            return $object;
        }
    }
    	
}