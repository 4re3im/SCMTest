<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 14/05/2020
 * Time: 3:34 PM
 */

class GigyaPasswordModel
{
    private $db;
    const FILE_TABLE = 'GigyaResetPasswordFiles';
    const USER_TABLE = 'GigyaResetPasswordUsers';
    const STATUS_TABLE = 'GigyaResetPasswordStatus';

    public function __construct()
    {
        $this->db = Loader::db();
    }

    // FILES
    public function insertFileRecord($fileID, $fileName)
    {
        global $u;
        $sql = 'INSERT INTO ' . static::FILE_TABLE . ' (FileID, FileName, DateUploaded, StaffID)';
        $sql .= 'VALUES (?,?,NOW(),?)';
        $this->db->Execute($sql, [$fileID, $fileName, $u->getUserID()]);

        return $this->db->Insert_ID(static::FILE_TABLE);
    }

    public function getFileRecord($fileRecordID)
    {
        $sql = 'SELECT * FROM ' . static::FILE_TABLE . ' WHERE ID = ?';
        return $this->db->GetRow($sql, [$fileRecordID]);
    }

    // USERS
    public function insertUserRecord($data)
    {
        $fieldsToUpdate = $qMarks = $params = [];

        foreach ($data as $index => $datum) {
            $fieldsToUpdate[] = $index;
            $qMarks[] = '?';
            $params[] = $datum;
        }

        $sql = 'INSERT INTO ' . static::USER_TABLE . ' (' . implode(',', $fieldsToUpdate) . ')';
        $sql .= ' VALUES(' . implode($qMarks, ',') . ')';
        $this->db->Execute($sql, $params);
        return $this->db->Insert_ID(static::USER_TABLE);
    }

    public function getUsersByFileID($fileID, $page, $limit)
    {
        $offset = ($limit * $page) - $limit;
        $sql = 'SELECT * FROM ' . static::USER_TABLE . ' WHERE FileID = ?';
        $sql .= 'LIMIT ' . $offset . ', ' . $limit;
        $results = $this->db->GetAll($sql, [$fileID]);
        return $results;
    }

    public function getAllValidUsersByFileID($fileID)
    {
        $sql = 'SELECT * FROM ' . static::USER_TABLE . ' WHERE FileID = ? AND IsValidForChange = 1';
        return $this->db->GetAll($sql, [$fileID]);
    }

    public function getAllUsersByFileID($fileID)
    {
        $sql = 'SELECT ID, FirstName, LastName, Email, TempPassword, Status FROM ' . static::USER_TABLE;
        $sql .= ' WHERE FileID = ?';
        return $this->db->GetAll($sql, [$fileID]);
    }

    public function getUserCountByFileID($fileID)
    {
        $results = $this->getAllUsersByFileID($fileID);
        return count($results);
    }

    public function updateUser($id, $data)
    {
        $fieldsToUpdate = $params = [];
        foreach ($data as $index => $datum) {
            $fieldsToUpdate[] = $index . ' = ?';
            $params[] = $datum;
        }
        $fieldsToUpdate[] = 'DateModified = NOW()';
        $params[] = $id;

        $sql = 'UPDATE ' . static::USER_TABLE . ' SET ';
        $sql .= implode(',', $fieldsToUpdate);
        $sql .= ' , DateModified = NOW()';
        $sql .= ' WHERE ID = ?';

        $this->db->Execute($sql, $params);
    }

    public function updateUserByEmail($email, $fileID, $data)
    {
        $fieldsToUpdate = $params = [];
        foreach ($data as $index => $datum) {
            $fieldsToUpdate[] = $index . ' = ?';
            $params[] = $datum;
        }
        $fieldsToUpdate[] = 'DateModified = NOW()';

        $params[] = $email;
        $params[] = $fileID;

        $sql = 'UPDATE ' . static::USER_TABLE . ' SET ';
        $sql .= implode(',', $fieldsToUpdate);
        $sql .= ' , DateModified = NOW()';
        $sql .= ' WHERE Email = ? AND FileID = ?';

        $this->db->Execute($sql, $params);
    }

    public function updateUserAfterPwChange($fileID)
    {
        $sql = 'UPDATE ' . static::USER_TABLE . ' SET TempPassword = null, DateModified = NOW()';
        $sql .= 'WHERE FileID = ? AND IsValidForChange = 1';

        return $this->db->GetRow($sql, [$fileID]);
    }

    public function updateSuccessfulPwChangedUsers($emails, $fileID, $data)
    {
        $emails = array_filter($emails);

        $paramString = $params = [];

        foreach ($data as $index => $datum) {
            $paramString[] = $index .= ' = ?';
            $params[] = $datum;
        }

        $paramString[] = 'DateModified = NOW()';

        $sql = 'UPDATE ' . static::USER_TABLE . ' SET ' . implode(',', $paramString);
        $sql .= ' WHERE Email IN(\'' . implode("','", $emails) . '\') AND FileID = ?';

        $params[] = $fileID;
        $this->db->Execute($sql, $params);
    }

    public function updateUnfoundEmails($emails, $fileID)
    {
        $sql = 'UPDATE ' . static::USER_TABLE;
        $sql .= ' SET Status = \'Details: User may not be in Gigya or uses a different email\'';
        $sql .= 'WHERE Email IN(\'' . implode("','", $emails) . '\') AND FileID = ?';

        $this->db->Execute($sql, [$fileID]);
    }

    // STATUS
    public function setStatusRecord($data)
    {
        $fileRecordId = $data['FileRecordID'];
        $sql = 'SELECT ID FROM ' . static::STATUS_TABLE . ' WHERE FileRecordID = ?';
        $entry = $this->db->GetRow($sql, [$fileRecordId]);

        if(!$entry) {
            $this->insertStatus($data);
        } else {
            $this->updateStatus($data);
        }
    }

    public function insertStatus($data)
    {
        $keys = $values = $qMarks = [];
        foreach ($data as $index => $datum) {
            $keys[] = $index;
            $values[] = $datum;
            $qMarks[] = '?';
        }
        $sql = 'INSERT INTO ' . static::STATUS_TABLE;
        $sql .= ' (' . implode(',', $keys) . ')';
        $sql .= ' VALUES(' . implode(',', $qMarks) . ')';
        $this->db->Execute($sql, $values);
    }

    public function updateStatus($data)
    {
        $paramString = [];
        $params = [];
        foreach ($data as $index => $datum) {
            if ($index === 'FileRecordID') {
                continue;
            }
            $paramString[] = $index . ' = ?';
            $params[] = $datum;
        }
        $paramString[] = 'DateUpdated = NOW()';
        $sql = 'UPDATE ' . static::STATUS_TABLE . ' SET ' . implode(', ', $paramString);
        $sql .= ' WHERE FileRecordID = ?';

        $params[] = $data['FileRecordID'];
        $this->db->Execute($sql, $params);
    }

    public function getStatus($fileRecordID)
    {
        $sql = 'SELECT Status, Message, IsFinished, Data, FileRecordID FROM ' . static::STATUS_TABLE;
        $sql .= ' WHERE FileRecordID = ?';

        return $this->db->GetRow($sql, [$fileRecordID]);
    }

    public function updateAllUsers($fileRecordID, $data)
    {
        $params = [];
        $paramString = [];
        foreach ($data as $index => $datum) {
            $paramString[] = $index . ' = ?';
            $params[] = $datum;
        }

        $sql = 'UPDATE ' . static::USER_TABLE . ' SET ' . implode(', ', $paramString);
        $sql .= ', DateModified = NOW() WHERE FileID = ?';

        $params[] = $fileRecordID;

        return $this->db->GetRow($sql, $params);
    }
}
