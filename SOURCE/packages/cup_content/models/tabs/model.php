<?php
/**
 * Description of model
 *
 * @author paulbalila
 */
class CupContentTabs {
    private $db;
        
    public function __construct() {
        $this->db = Loader::db();
    }
    
    /**
     * Gets all the tabs related to a title ID
     * @author Paul Balila
     * 
     * @param int $titleID
     */
    public function getTitleTabs($titleID) {
        $sql = "SELECT * FROM CupGoTabs WHERE (titleID = ? AND TabLevel = 1 AND TabType = 'TAB') "
                . "AND ID NOT IN(SELECT RecordID FROM CupGoArchive WHERE TableName IN ('CupGoTabs','CMS_Tabs')) ORDER BY SortOrder";
        $result = $this->db->Execute($sql,array($titleID));
        return ($result) ? $result : FALSE;
    }

    /*    
    public function getTabID($tabName,$titleID) {
        $query = "SELECT ID FROM CupGoTabs WHERE ((TabName = ? AND titleID = ?) AND (TabType = 'Tab' AND TabLevel=1)) "
                . "AND ID NOT IN(SELECT RecordID FROM CupGoArchive WHERE TableName = 'CupGoTabs')";
        $result = $this->db->Execute($query,array($tabName,$titleID));
        return $result->FetchRow();
    }
    */
    
    public function getTabGlobalContents($tabID) {
        $this->db = Loader::db();
        $query = "SELECT cgtc.*,cgc.CMS_Name,cgc.ContentHeading "
                . "FROM CupGoTabContent AS cgtc JOIN CupGoContent AS cgc ON cgtc.ContentID = cgc.ID ";
        $query .= "WHERE cgtc.TabID = ? ";
        $query .= "AND cgc.`ID` NOT IN(SELECT RecordID FROM `CupGoArchive` WHERE `TableName` IN('CupGoContent','CMS_Content')) ";
        $query .= "ORDER BY cgtc.`SortOrder`";
        $result = $this->db->Execute($query, array($tabID));
        return ($result) ? $result->GetAll() : FALSE;
    }

    public function getTabGlobalContentsByContentID($id) {
        
        $this->db = Loader::db();
        $query = "SELECT cgtc.ID, cgtc.SortOrder, cgc.CMS_Name,cgc.ContentHeading "
                . "FROM CupGoTabContent AS cgtc JOIN CupGoContent AS cgc ON cgtc.ContentID = cgc.ID ";
        $query .= "WHERE cgtc.id = ? ";
            $query .= "ORDER BY cgtc.`SortOrder`";
            $result = $this->db->GetRow($query, array($id));
        return ($result) ? $result : FALSE;
    }
    
    
    public function getTabFolders($titleID) {
        $query = "SELECT ID,FolderName FROM CupGoContentFolders WHERE titleID = ? AND `ID` NOT IN(SELECT `RecordID` FROM CupGoArchive WHERE TableName IN('CupGoContentFolders','CMS_folders'))";
        $result = $this->db->Execute($query,array($titleID));
        return $result->GetAll();
    }
    
    public function getTabFolderContents($folderID) {
        $query = "SELECT cgc.* "
                . "FROM CupGoFolderContent AS cgfc "
                . "JOIN CupGoContent AS cgc ON cgfc.ContentID = cgc.ID "
                . "WHERE cgfc.FolderID = ? AND (cgc.ContentHeading IS NOT NULL OR cgc.CMS_Name IS NOT NULL)"
                . "AND cgc.`ID` NOT IN (SELECT RecordID FROM `CupGoArchive` WHERE `TableName` IN('CupGoContent','CMS_Content'))";
        $result = $this->db->Execute($query,array($folderID));
        return ($result) ? $result->GetAll() : FALSE;
    }
    
    public function getLinkedFolderContents($folderID,$tabID) {
        $query = "SELECT cgtc.* "
                . "FROM CupGoFolderContent AS cgfc "
                . "JOIN CupGoTabContent AS cgtc ON cgfc.ContentID = cgtc.ContentID "
                . "WHERE cgtc.`TabID` = ?";
        $result = $this->db->Execute($query,array($tabID));
        $contentIDs = array();
        foreach ($result->GetAll() as $r) {
            $contentIDs[] = $r['ContentID'];
        }
        return $contentIDs;
    }
    
    public function getTitleIDFromFolderID($folderID) {
        $query = "SELECT titleID FROM CupGoContentFolders WHERE ID = ?";
        $result = $this->db->Execute($query,array($folderID));
        return $result->FetchRow();
    }
    
    public function insertTabContent($tabID,$contentID) {
        // Sort fixes
        $newSortSql = "SELECT MAX(SortOrder) + 1 AS newSortNum FROM CupGoTabContent WHERE TabID = ?";
        $result = $this->db->GetRow($newSortSql, array($tabID));
        $sortNum = ($result['newSortNum']) ? $result['newSortNum'] : 1;
        
        $query = "INSERT INTO CupGoTabContent(CreationDate,TabID,ContentID,SortOrder) VALUES (NOW(),?,?,?)";
        $result = $this->db->Execute($query,array($tabID,$contentID,$sortNum));
        
        // Modified by Paul Balila, 2016-04-06
        // For ticket ANZUAT-63
        // default: return $this->getTabGlobalContents($this->db->Insert_ID('CupGoTabContent'));
        return $this->getTabGlobalContents($tabID);
    }
    
    public function updateTabContent($columnName,$columnValue,$tabContentID) {
        $query = "UPDATE CupGoTabContent SET $columnName = ? WHERE ID = ?";
        $result = $this->db->Execute($query,array($columnValue,$tabContentID));
        return $result;
    }
    
    public function updateTabSorting($data) {
        $keys = array();
        $sql = "UPDATE CupGoTabs SET `SortOrder` = CASE `ID` ";
        foreach ($data as $dKey => $dValue) {
            $sql .= " WHEN " . $dKey . " THEN " . $dValue;
            $keys[] = $dKey;
        }
        $sql .= " END WHERE `ID` IN (" . implode(",", $keys) . ")";
        $this->db->Execute($sql);
        return $this->db->Affected_Rows();
    }
    
    public function updateTabContentSorting($data) {
        $keys = array();
        $sql = "UPDATE CupGoTabContent SET `SortOrder` = CASE `ID` ";
        foreach ($data as $dKey => $dValue) {
            $sql .= " WHEN " . $dKey . " THEN " . $dValue;
            $keys[] = $dKey;
        }
        $sql .= " END WHERE `ID` IN (" . implode(",", $keys) . ")";
        $this->db->Execute($sql);
        return $this->db->Affected_Rows("CupGoTabContent");
    }
    
    public function deleteTabContent($tabContentID) {
        $query = "DELETE FROM CupGoTabContent WHERE ID = ?";
        $result = $this->db->Execute($query,array($tabContentID));
        return $result;
    }   
    
    public function deleteTabContentAfterFolderArchive($folderID) {        
        $query = "DELETE FROM CupGoTabContent WHERE `ContentID` IN(SELECT `ContentID` FROM `CupGoFolderContent` WHERE `FolderID` = ?)";
        $result = $this->db->Execute($query,array($folderID));
        return $result;
    }
}
