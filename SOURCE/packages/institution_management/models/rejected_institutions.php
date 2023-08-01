<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 07/07/2021
 * Time: 1:57 PM
 */

class RejectedInstitutionsModel
{
    const TABLE = 'rejected_institutions';
    private $db;

    public function __construct()
    {
        $this->db = Loader::db();
    }

    public function insertRejectedInstitution($data)
    {
        $sql = "INSERT INTO " . self::TABLE . " (oid, uid, remarks, school_code, admin_id) VALUES (?,?,?,?, ?)";
        $this->db->Execute($sql, [
            $data['oid'],
            $data['uid'],
            $data['remarks'],
            $data['schoolCode'],
            $data['admin_id']
        ]);
    }

    public function insertMultipleInstitutions($data)
    {
        global $u;
        foreach ($data as $oid => $datum) {
            $params = [
                'oid' => $oid,
                'uid' => $datum['uid'],
                'remarks' => $datum['remarks'],
                'schoolCode' => $datum['schoolCode'],
                'admin_id' => $u->getUserID()
            ];
            $this->insertRejectedInstitution($params);
        }
    }

    public function getByOids($oids)
    {
        $sql = "SELECT oid, remarks FROM " . self::TABLE;
        $sql .= " WHERE oid IN('" . implode("','", $oids) . "')";

        return $this->db->GetAssoc($sql);
    }

	// SB-1117 added by timothy.perez - Users who have been rejected from a school registration are unable to join another school
    public function deleteRejectedInstitutionByOid($oid) 
    {
        $sql = "DELETE FROM " . self::TABLE;
        $sql .= " WHERE oid = \"" . $oid . "\";";

        $result = $this->db->Execute($sql);

        if (!$result) {
            $this->errors[] = "Cannot delete institution";
            return false;
        }

        return true;
    }
}
