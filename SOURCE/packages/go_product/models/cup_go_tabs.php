<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

class CupGoTabs extends Object
{
    protected $id = false;
    public $existing_result = array();

    function __construct($id = false)
    {
        if ($id) {
            $db = Loader::db();
            $sql = "SELECT * FROM CupGoTabs WHERE id = ?";
            $result = $db->getRow($sql, array($id));

            if ($result) {
                $this->id 			= $result['id'];
                $this->tab_id			= $result['TabID'];
                $this->content_id		= $result['ContentID'];
                $this->userTypeIDRestriction    = $result['UserTypeIDRestriction'];
                $this->existing_result = $result;
            }
        }
    }

    public static function fetchByID($id)
    {
        $object = new CupGoTabContent($id);
        if ($object->id === false) {
            return false;
        } else {
            return $object;
        }
    }

    public static function fetchTabByID($id)
    {
        $object = new CupGoTabs($id);
        return ($object) ? $object : false;
    }

    public static function fetchByTitleID($titleId)
    {
        $db = Loader::db();

        $sql = "SELECT * FROM CupGoTabs WHERE titleID = ?";
        $result = $db->getRow($sql, array($titleId));

        if (!$result) {
            return false;
        } else {
            return $result;
        }
    }

    public static function fetchByParentID($id)
    {
        $db = Loader::db();

        $sql = "SELECT * FROM CupGoTabs WHERE ParentID = ? AND Active='Y'";
        $result = $db->getAll($sql, array($id));

        if (!$result) {
            return false;
        } else {
            return $result;
        }
    }

    /* ANZGO-3439 added by Maryjes Tanada 04/03/2018
     * Checking of Content Access ===  Free, moved from cgcd to cgt
     * similar checking for Login only access
     */
    public static function isContentAccessFree($contentId)
    {
        $db = Loader::db();
        $sql = "SELECT cgt.ContentAccess FROM CupGoContentDetail cgcd
        JOIN CupGoTabContent cgtc ON cgcd.ContentID = cgtc.ContentID
        JOIN CupGoTabs cgt ON cgtc.TabID = cgt.ID WHERE cgcd.ID = ? AND cgt.ContentAccess IS NOT NULL";
        $result = $db->GetRow($sql, array($contentId));
        return ($result['ContentAccess'] === 'Free');
    }

    public static function isLoginOnlyAccess($contentId)
    {
      $db = Loader::db();
      $sql = "SELECT cgt.ContentAccess, cgt.UserTypeIDRestriction AS GroupType FROM CupGoContentDetail cgcd
        JOIN CupGoTabContent cgtc ON cgcd.ContentID = cgtc.ContentID
        JOIN CupGoTabs cgt ON cgtc.TabID = cgt.ID WHERE cgcd.ID = ? AND cgt.ContentAccess IS NOT NULL";

      return $db->GetRow($sql, array($contentId));
    }

    /**
     * ANZGO-3376 Added by John Renzo S. Sunico, July 31, 2017
     * Checks if tab has content such as documents, pdfs;
     */

    public static function hasContent($tabID)
    {
        $db = Loader::db();
        $sql = "SELECT ID FROM CupGoTabContent WHERE TabID = ? LIMIT 1;";
        return $db->GetRow($sql, array($tabID));
    }

    /**
     * ANZGO-3529 Added by Jeszy Tanada Oct. 19, 2017
     * To get tab name for logging
     */
    public static function getTabName($contentID)
    {
        $db = Loader::db();
        $sql = <<<SQL
              Select cgt.TabName from CupGoTabs cgt
              Inner join CupGoTabContent cgtc ON cgt.ID = cgtc.TabID
              Inner join CupGoContent cgc ON cgtc.ContentID = cgc.ID
              Inner join CupGoContentDetail cgcd ON cgc.ID = cgcd.ContentID
              WHERE cgcd.ID = ?;
SQL;
        return $db->GetRow($sql, array($contentID));
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
