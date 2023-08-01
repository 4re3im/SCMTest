<?php

/**
 * SSO Go Model
 */
class SsoGoModel
{

    private $db;

    public function __construct()
    {
        $this->db = Loader::db();
    }

    // Used for DB Session
    // not going to used, but will keep, historically
    public function insert()
    {
        $u = new User();
        $u->subscriptions = $this->getSubscriptions();
        $sql = "INSERT INTO CupGoPhpSession(sesskey,expiry,expireref,created,modified,sessdata,sessdataTNG) VALUES (?,DATE_ADD(NOW(), INTERVAL 2 HOUR),'',NOW(),NOW(),'',?)";

        $values = array(session_id(), json_encode($u));

        return $this->db->Execute($sql, $values);
    }

    // Used for DB Session
    // not going to used, but will keep, historically
    public function update()
    {
        $u = new User();
        $u->subscriptions = $this->getSubscriptions();

        $sql = "UPDATE CupGoPhpSession SET sessdataTNG = ?, modified = NOW() WHERE sesskey = ?";
        $values = array(json_encode($u), session_id());

        return $this->db->Execute($sql, $values);
    }

    public function getSubscriptions()
    {
        $u = new User();
        $subscriptions = array();

        if (in_array('Student', $u->uGroups) || in_array('Teacher', $u->uGroups)) {
            $sql = "SELECT Private_TabText FROM CupGoTabs cgt "
                . "WHERE cgt.ID IN (SELECT DISTINCT (TabID) FROM CupGoTabAccess cgta WHERE cgta.UserID = ?) "
                . "AND (cgt.Private_TabText IS NOT NULL AND cgt.Private_TabText > '') AND Active = 'Y'";
            $subscriptions = $this->db->GetAll($sql, array($u->uID));
        }

        return $subscriptions;
    }

    public function getRequestUrl()
    {
        $sql = "SELECT requestUrl FROM CupGoPhpSession WHERE sesskey = ?";
        $result = $this->db->GetRow($sql, array(session_id()));

        if (empty($result)) {
            $result = $this->db->GetRow($sql, array($_COOKIE['CONCRETE5']));
        }

        return $result;
    }

    // HUB-159 Modified by John Renzo S. Sunico, 09/03/2018
    public function getTabByPrivateTab($keyword)
    {
        $sql = 'SELECT ID FROM CupGoTabs WHERE Private_TabText LIKE ? AND Active = \'Y\'';

        return $this->db->GetCol($sql, ["%$keyword%"]);
    }
}
