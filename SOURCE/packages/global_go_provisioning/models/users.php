<?php
/**
 * User: jbernardez
 * Date: 20200623
 */
class UsersModel
{
    private $db;
    const PACKAGE_HANDLE = 'global_go_provisioning';
    private $bulkActionsHelper;
    
    public function __construct()
    {
        $this->db = Loader::db();
        $this->bulkActionsHelper = Loader::helper('bulk_actions', static::PACKAGE_HANDLE);
    }

    public function changePasswordByBulk($userRecords)
    {
        $bulkUpdateQuery = '
            UPDATE Users 
            SET uPassword = CASE uEmail 
        ';

        $inQuery = '';
        foreach ($userRecords as $userRecord) {
            $encryptedPassword = $this->bulkActionsHelper->encryptPassword($userRecord['TempPassword']);
            $bulkUpdateQuery .= "WHEN '" . $userRecord['Email'] . "' THEN '" . $encryptedPassword . "' ";
            $inQuery .= "'". $userRecord['Email'] ."', ";
        }

        $inQuery = substr($inQuery, 0, -2);
        $bulkUpdateQuery .= 'END WHERE uEmail IN ('. $inQuery .')';

        $updateResult = $this->db->Execute($bulkUpdateQuery);
    }
}
