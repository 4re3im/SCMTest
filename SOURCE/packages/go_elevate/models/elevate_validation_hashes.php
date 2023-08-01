<?php  defined('C5_EXECUTE') or die(_('Access Denied.'));


class ElevateValidationHashes
{
    public static function getByUserID($uID, $token)
    {
        $db = Loader::db();
        $sql = "SELECT uID FROM ElevateValidationHashes ";
        $sql .= "WHERE uID = ? AND token = ? LIMIT 1;";
        $result = $db->getRow($sql, array($uID, $token));
        return $result['uID'];
    }

    // ANZGO-3408 Created by Shane Camus 06/09/2017
    // Go Mobile: create an API/service for login and access of Ereader

    public static function checkUserID($uID)
    {
        $db = Loader::db();
        $sql = "SELECT * FROM ElevateValidationHashes ";
        $sql .= "WHERE uID = ?;";
        $result = $db->getRow($sql, array($uID));
        return $result;
    }

    public static function insertHash($uID, $token)
    {
        $db = Loader::db();
        $sql = "INSERT INTO ElevateValidationHashes (uID, token, dateCreated) ";
        $sql .= "VALUES (?, ?, CURRENT_TIMESTAMP());";
        $result = $db->query($sql, array($uID, $token));
        return $result;
    }

    public static function updateHash($uID, $token)
    {
        $db = Loader::db();
        $sql = "UPDATE ElevateValidationHashes ";
        $sql .= "SET token=?, dateCreated=CURRENT_TIMESTAMP() ";
        $sql .= "WHERE uID=$uID;";
        $result = $db->query($sql, array($token));
        return $result;
    }
}
