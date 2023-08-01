<?php
/**
 * CupGoTabHmIds Model Class
 * SB-16 Added by MTanada 20190108
 */

defined('C5_EXECUTE') or die(_("Access Denied."));

class CupGoTabHmIds extends Object
{
    protected $id = false;
    protected $exisiting_result = array();

    function __construct($id = false)
    {
        if ($id) {
            $db = Loader::db();
            $sql = "SELECT * FROM CupGoTabHmIds WHERE id = ?";
            $result = $db->getRow($sql, array($id));

            if ($result) {
                $this->id 			    = $result['ID'];
                $this->entitlementId    = $result['EntitlementId'];
                $this->tabId		    = $result['TabId'];
                $this->hmId             = $result['HmId'];
                $this->exisiting_result = $result;
            }
        }
    }

    public static function getHmIdByEntitlementIdAndTabId($entitlementId, $tabId)
    {
        $db = Loader::db();

        $sql = "SELECT HmId FROM CupGoTabHmIds WHERE EntitlementId = ? AND TabId = ?";
        $result = $db->getRow($sql, array($entitlementId, $tabId));

        if (!$result) {
            return false;
        } else {
            return $result['HmId'];
        }
    }

    // SB-233 added by machua 20190704
    public static function getFormattedDetailsByTabId($tabId)
    {
        $db = Loader::db();

        $sql = 'SELECT EntitlementId, HmId FROM CupGoTabHmIds WHERE TabId = ? ORDER BY ID DESC';
        $result = $db->getAll($sql, array($tabId));

        if (!$result) {
            return false;
        } else {
            $formattedResult = [];
            foreach ($result as $row) {
                $entitlementId = $row['EntitlementId'];
                $formattedResult[$entitlementId] = $row['HmId'];
            }
            return $formattedResult;
        }
    }


    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }
}
