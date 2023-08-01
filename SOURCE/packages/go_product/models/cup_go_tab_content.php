<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

class CupGoTabContent extends Object {

    protected $id = FALSE;
    protected $tab_id = FALSE;
    protected $content_id = FALSE;

    protected $exisiting_result = array();

    function __construct($id = false) {

        if($id){
            $db = Loader::db();
            $sql = "SELECT * FROM CupGoTabContent WHERE id = ?";
            $result = $db->getRow($sql, array($id));

            if($result){

                $this->id 			= $result['id'];
                $this->tab_id			= $result['TabID'];
                $this->content_id		= $result['ContentID'];

                $this->exisiting_result = $result;
            }
        }
    }

    public static function fetchByID($id){
        $object = new CupGoTabContent($id);
        if($object->id === FALSE){
            return FALSE;
        }else{
            return $object;
        }
    }

    /**
     * @author : Ariel Tabag
     * @editedBy : Paul Balila
     * Added tab content details in the query
     */
    public static function fetchAllByTabID($tab_id)
    {
        $object = new CupGoTabContent();

        $db = Loader::db();
        // SB-293 modified by machua 20190808 to add the necessary columns for tab text
        $sql  = "SELECT
                c.*,
                cct.name,
                tc.Visibility as TabGoContentVisibility,tc.ColumnNumber,tc.Active AS IsTabContentActive,tc.DemoOnly,
                ct.TabName,ct.TabIcon, ct.UserTypeIDRestriction,ct.`ContentAccess`, ct.AlwaysUsePublicText,
                ct.Public_TabText, ct.Private_TabText "; // Added "tc.Active AS IsTabContentActive" by Paul Balila
        $sql .= "FROM CupGoTabs ct LEFT JOIN CupGoTabContent tc ON tc.TabID=ct.ID ";
        $sql .= "LEFT JOIN CupGoContent c ON tc.ContentID=c.ID ";
        $sql .= "LEFT JOIN CupContentTitle cct ON ct.titleID=cct.ID WHERE ct.ID = ? AND ct.Active='Y' ";
        $sql .= "AND (ct.ID NOT IN (SELECT RecordID FROM CupGoArchive WHERE TableName IN ('CMS_Tabs','CupGoTabs')) AND c.ID NOT IN(SELECT RecordID FROM CupGoArchive WHERE TableName IN ('CMS_Content','CupGoContent')))";

        // $sql .= "ORDER BY tc.SortOrder, ColumnNumber";
        $sql .= "ORDER BY tc.SortOrder";

        $result = $db->getAll($sql, array($tab_id));

        if($result === FALSE){
            return FALSE;
        }else{
          return $result;
        }
    }

    public static function checkContentDetailAccess($tab_id)
    {
        $db = Loader::db();
        $sql = "SELECT cgcd.Visibility FROM CupGoTabContent ctc JOIN CupGoContentDetail cgcd ON ctc.ContentID = cgcd.ContentID WHERE TabID = ?";
        $result = $db->GetAll($sql,array($tab_id));
        $override_access = FALSE;
        foreach ($result as $r) {
            if($r['Visibility'] == 'Public') {
                $override_access = TRUE;
                break;
            }
        }
        return $override_access;
    }

    /**
     * ANZGO-3990 Added by Shane Camus 01/04/19
     * @param $tabID
     * @return array|bool
     */
    public static function fetchByTabID($tabID)
    {
        $db = Loader::db();
        $sql = <<<sql
        SELECT * 
        FROM CupGoTabContent AS cgtc
        JOIN CupGoContent AS cgc ON cgtc.ContentID = cgc.ID 
        WHERE cgtc.TabID = ? 
        AND cgc.`ID` NOT IN (SELECT RecordID FROM `CupGoArchive` WHERE `TableName` IN('CupGoContent','CMS_Content')) 
sql;
        $result = $db->GetRow($sql, array($tabID));

        if ($result === false) {
            return false;
        } else {
            return $result;
        }
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }

}
