<?php
/**
 * Description of global_content
 *
 * @author paulbalila
 */
class GlobalContentModel {
    private $db;
    public function __construct() {
        $this->db = Loader::db();
    }
    
    // Modified by Paul Balila, 2016-04-13
    // For ticket ANZUAT-112
    public function getGlobalContentsTabs() {
        $query = "select ID,CMS_Name,ContentHeading,ContentData from CupGoContent where Global ='Y' AND ID NOT IN(SELECT RecordID FROM CupGoArchive WHERE TableName IN('CMS_Content','CupGoContent'))";
        $result = $this->db->Execute($query);
        return ($result->RowCount() > 0) ? $result->GetAll() : FALSE;
    }
    
    public function getContentDetails($id) {
        $query = "select ID,ContentData,Global from CupGoContent where ID = ?";
        $result = $this->db->Execute($query,array($id));
        return ($result->RowCount() > 0) ? $result->FetchRow() : FALSE;
    }
    
    public function updateGlobalContent($id,$content) {
        $query = "update CupGoContent set ContentData = ? where ID = ?";
        $result = $this->db->Execute($query,array($content['ContentData'],$id));
        return ($result->RowCount() >= 0);
    }
}
