<?php
defined('C5_EXECUTE') || die(_("Access Denied."));

class CupGoContentDetail extends Object
{

    protected $id = false;
    protected $file_name = false;
    protected $file_path = false;
    protected $public_description = false;
    protected $public_name = false;
    protected $visibility = false;

    protected $exisiting_result = array();

    function __construct($id = false)
    {
        if ($id) {
            $db = Loader::db();
            $sql = "SELECT * FROM CupGoContentDetail WHERE id = ?";
            $result = $db->getRow($sql, array($id));

            if ($result) {

                $this->id               = $result['id'];
                $this->file_name        = $result['FileName'];
                $this->file_size        = $result['FileSize'];
                $this->file_path        = $result['FilePath'];
                $this->public_description = $result['PublicDescription'];
                $this->public_name      = $result['PublicName'];
                $this->visibility       = $result['Visibility'];

                $this->exisiting_result = $result;
            }
        }
    }

    public static function fetchByID($id)
    {
        $object = new CupGoContentDetail($id);
        if ($object->id === false) {
            return false;
        } else {
            return $object;
        }
    }

    public static function fetchAllByContentID($contentId, $tabId = false)
    {
        $object = new CupGoContentDetail();

        $db = Loader::db();
        $params = array();

        // Modified by Shane Camus 2017-03-21
        // ANZGO-3167 Sorting Issue on Content-Details
        // sort order should be queried from table CupGoContentDetail
        // Modified by Paul Balila 2017-05-07 ANZGO-3094
        // Added TabID to avoid duplicate entries
        $sql  = "SELECT cgcd.*, cgtc.DemoOnly AS ContentDemo ";
        $sql .= "FROM CupGoTabContent cgtc JOIN CupGoContentDetail cgcd ON cgtc.ContentID = cgcd.ContentID ";
        if ($tabId) {
          $sql .= "WHERE (cgtc.ContentID = ? AND cgtc.Active = 'Y' AND cgtc.TabID = ?) AND ";
          $params = array($contentId, $tabId);
        } else {
          $sql .= "WHERE (cgtc.ContentID = ? AND cgtc.Active = 'Y') AND ";
          $params = array($contentId);
        }

        // Modified by J.Tanada 2017-12-05 ANZGO-3538
        // add cgcd.Active = 'Y' for Active Yes/No functionality
        $sql .= "cgcd.Active = 'Y' AND cgcd.ID NOT IN (SELECT RecordID FROM CupGoArchive ";
        $sql .= "WHERE TableName IN ('CMS_ContentDetail','CupGoContentDetails')) ";
        $sql .= "ORDER BY cgcd.SortOrder;";
        $result = $db->getAll($sql, $params);

        if ($result === false) {
            return false;
        } else {
            return $result;
        }
    }

    public static function getActiveSubscriptionContents($userId)
    {
        $db = Loader::db();
        $sql = "SELECT DISTINCT(cgcd.`ID`) FROM `CupGoTabAccess` cgta ";
        $sql .= "JOIN `CupGoTabContent` cgtc ON cgta.`TabID` = cgtc.`TabID` ";
        $sql .= "JOIN `CupGoContentDetail` cgcd ON cgtc.`ContentID` = cgcd.`ContentID` ";
        $sql .= "WHERE (cgta.`UserID` = ? AND cgta.`Active` = 'Y' AND cgcd.Active = 'Y') AND cgcd.`TypeID` = 1005";
        $result = $db->GetAll($sql, array($userId));

        if (empty($result)) {
            return false;
        }

        $temp = array();
        foreach ($result as $rKey => $rVal) {
            $temp[] = $rVal['ID'];
        }
        return $temp;
    }

    public static function getActiveSubscriptionContentsByTabIds(array $tabIds)
    {
        if (!$tabIds) {
            return [];
        }

        $tabIds = implode(',', $tabIds);

        $db = Loader::db();
        $sql = <<<SQL
          SELECT cgcd.ID FROM CupGoContentDetail cgcd
          INNER JOIN CupGoTabContent cgtc ON cgcd.ContentID = cgtc.ContentID
          INNER JOIN CupGoTabs cgt ON cgt.ID = cgtc.TabID
          WHERE cgcd.Active = 'Y' AND cgcd.TypeID = 1005 AND cgt.ID IN ($tabIds)
SQL;

        $result = $db->GetAll($sql);

        return array_column($result, 'ID');
    }

    public static function isTabBlockAndContentPublic($contentId)
    {
        $db = Loader::db();
        $sql = "SELECT cgtc.`Visibility` AS contentBlockVisibility, cgcd.`Visibility` AS contentDetailVisibility ";
        $sql .= "FROM `CupGoContentDetail` cgcd JOIN `CupGoContent` cgc ON cgcd.`ContentID` = cgc.`ID` ";
        $sql .= "JOIN `CupGoTabContent` cgtc ON cgc.`ID` = cgtc.`ContentID` WHERE cgcd.`ID` = ?";
        $result = $db->GetRow($sql, array($contentId));
        return ($result['contentBlockVisibility'] === 'Public' && $result['contentDetailVisibility'] === 'Public');
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
