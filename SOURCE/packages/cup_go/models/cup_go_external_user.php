<?php

/**
 * Model for external User 
 * @author Ariel Tabag <atabag@cambridge.org>
 */
class CupGoExternalUser extends Model
{
    var $db;
    var $_tbl = 'CupGoExternalUser';
    
    public function __construct()
    {
        $this->db = Loader::db();
    }
    
    /**
     * Insert External User
     * Return user data 
     * @param type $params
     */
    public function processExternalUser($params)
    {
        $brandCodes = json_decode($params['brandCodes']);
        //check if user exis by user id
        
        foreach($brandCodes as $brandCode) {
            $user = $this->get($params['userId'], $brandCode);
            
            if ($user) {
                $this->update($params, $brandCode);
            } else {
                $this->insert($params, $brandCode);
            }
        }
        
        return $this->userDetails($params['userId']);
    }
    
    public function insert($params, $brandCode)
    {
        $sql = "INSERT INTO $this->_tbl (authToken,uID,externalID,tokenExpiryDate,brandCodes) VALUES (?, ?, ?, ?, ?)";
        $values = array(
            $params['authorizationToken'],
            $params['userId'],
            $params['externalId'],
            $params['tokenExpiryDate'],
            $brandCode
        );
        
        return $this->db->Execute($sql, $values);
    }
    
    public function update($params, $brandCode)
    {
        // SB-9 modified by jbernardez 20191106
        $sql = "UPDATE $this->_tbl SET authToken = ?, tokenExpiryDate = ?, brandCodes = ?, externalID = ? WHERE uID = ? AND brandCodes = ?";
        $values = array(
            $params['authorizationToken'],
            $params['tokenExpiryDate'],
            $brandCode,
            $params['externalId'],
            $params['userId'],
            $brandCode
        );
        
        return $this->db->Execute($sql, $values);
    }

    /**
     * SB-424 added by jbernardez 20200131
     */
    public function updateBrandExpiryDate($brandExpiryDate, $externalId)
    {
        // SB-9 modified by jbernardez 20191106
        $sql = "UPDATE $this->_tbl SET brandExpiryDate = ? WHERE externalID = ?";
        $values = array(
            $brandExpiryDate,
            $externalId
        );
        
        return $this->db->Execute($sql, $values);
    }
    
    public function get($userID, $brandCode)
    {
        $sql = "SELECT * FROM $this->_tbl WHERE uID = ? AND brandCodes = ?";
        $values = array(
            $userID,
            $brandCode
        );
        
        return $this->db->GetRow($sql, $values);
    }
    
    public function userDetails($userID)
    {
        $sql  = 'SELECT ? as userID, ak_uFirstName as firstname, ak_ulastName as lastname,uEmail as email ';
        $sql .= 'FROM Users u LEFT JOIN UserSearchIndexAttributes usia ON u.uID = usia.uID WHERE u.uID = ?';
        $values = array(
            $userID,
            $userID
        );
        
        return $this->db->GetRow($sql, $values);
    }

    // SB-9 added by jbernardez 20191106
    public function removeExternalUser($authorizationToken, $brandCode)
    {
        $sql  = "DELETE FROM $this->_tbl WHERE authToken = ? AND brandCodes = ? ";
        $values = array(
            $authorizationToken,
            $brandCode
        );

        $result = $this->db->Execute($sql, $values);

        if ($result) {
            return true;
        }

        return false;
    }
}