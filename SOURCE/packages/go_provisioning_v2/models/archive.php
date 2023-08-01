<?php

Loader::library('hub-sdk/autoload');
Loader::library('Activation/hub_activation_v2');

use HubEntitlement\Models\Activation;
use HubEntitlement\Models\Permission;

class ArchivingModel
{
    const COLUMN_EMAIL = 0;
    public $fileRecordID;
    private $db;

    public function __construct()
    {
        Loader::library('Activation/library');
        $this->db = Loader::db();
    }

    public function insertFileRecord($fileId, $fileName)
    {
        $u = new User();
        $sql = 'INSERT INTO ArchivingFiles (FileID,FileName,DateUploaded,StaffID) VALUES(?,?,NOW(),?)';
        $this->db->Execute($sql, array($fileId, $fileName, $u->uID));

        return $this->db->Insert_ID('ArchivingFiles');
    }

    public function getFileRecord($fileId)
    {
        return $this->db->GetRow('SELECT * FROM ArchivingFiles WHERE ID = ?', array($fileId));
    }

    public function getUserId($row)
    {
        return $this->db->GetRow('SELECT uID FROM Users WHERE uEmail = ? LIMIT 1', array($row[static::COLUMN_EMAIL]));
    }

    public function updateActivation ($activationId)
    {
        foreach ($activationId as $act) {
            $userActivation = Activation::find($act);
            $userActivation->Archive = 'Y';
            $userActivation->ArchivedDate = (new DateTime())->format('Y-m-d H:i:s');
            $userActivation->DateDeactivated = (new DateTime())->format('Y-m-d H:i:s');
            try {
                $userActivation->save();
            } catch(Exception $e) {
                throw new Exception($e);
            }
        }
    }
}