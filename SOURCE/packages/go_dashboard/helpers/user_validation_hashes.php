<?php defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Created by: jsunico@cambridge.org
 * 7/26/2017 9:21 AM
 * Extends UserValidationHash to add functionality
 */



class UserValidationHashesHelper extends UserValidationHash
{
    public static function getHashByUserID($uID, $type)
    {
        $db = Loader::db();
        $uHash = $db->getOne("SELECT uHash FROM UserValidationHashes WHERE uID = ? AND type = ?", array($uID, $type));
        if($uHash) {
            return $uHash;
        }

        return false;
    }
}
