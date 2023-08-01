<?php

/**
 * Model for the Go title.
 * @author gerardbalila
 */

define('TAB_ICON_FOLDER',
    DIR_BASE . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'cup_content' . DIRECTORY_SEPARATOR .
    'images' . DIRECTORY_SEPARATOR . 'formats' . DIRECTORY_SEPARATOR
);

class GoProductEditorModel extends Model
{
    const CUP_GO_CONTENT = 'CupGoContent';
    const CUP_GO_FOLDER_CONTENT = 'CupGoFolderContent';
    const CUP_GO_TAB_CONTENT = 'CupGoTabContent';
    const GLOBAL_STRING = 'Global';
    const SORT_ORDER = 'SortOrder';
    const WINDOW_SIZE = 'WindowSize';
    const FILE_UPLOAD_DATE = 'FileUploadDate';
    const ACTIVE = 'Active';
    const TO_DELETE = 'ToDelete';
    const DEMO_ONLY = 'DemoOnly';
    const FOLDER_ID = 'FolderID';
    const FOLDER_NAME = 'FolderName';
    const FOLDER_CONTENT_ID = 'folderContentID';
    const NEW_SORT_NUM = 'newSortNum';

    private $db;

    public function __construct()
    {
        $this->db = Loader::db();
    }

    private function clean($data)
    {
        return addslashes($data);
    }

    public function getGoTitle($id)
    {
        // SB-313 Added prettyUrl to generate links - mabrigos 20190903
        $sql = "SELECT isbn13, name, prettyUrl FROM CupContentTitle WHERE id = ?";
        return $this->db->GetRow($sql, $id);
    }

    public function getFolders($id)
    {
        $sql = "SELECT cgcf.ID, cgcf.FolderName,cgcf.titleID, cgc.ID AS folderContentID, cgc.ContentHeading, ";
        $sql .= "cgc.CMS_Name, cgc.ContentTypeID, cgc.Global, cgc.ContentData, cgc.CMS_Notes ";
        $sql .= "FROM CupGoContentFolders cgcf ";
        $sql .= "LEFT JOIN CupGoFolderContent cgFolc ON cgcf.ID = cgFolc.FolderID ";
        $sql .= "LEFT JOIN CupGoContent cgc ON cgFolc.ContentID = cgc.ID ";
        $sql .= "WHERE cgcf.titleID = ? ";
        $sql .= "AND cgcf.ID NOT IN(SELECT `RecordID` FROM CupGoArchive WHERE TableName = 'CupGoContentFolders')";
        $sql .= "ORDER BY cgcf.ID ";
        $folders = $this->db->GetAll($sql, array($id));

        $tmp = array();
        foreach ($folders as $folder) {
            if ($folder[static::FOLDER_CONTENT_ID]) {
                $tmp[$folder['ID']][$folder[static::FOLDER_NAME]][] =
                    array(
                        'content_id' => $folder[static::FOLDER_CONTENT_ID],
                        'heading' => $folder['ContentHeading'],
                        'cms_name' => $folder['CMS_Name'],
                        'contents' => $this->getSubFolderContentHeadings($folder[static::FOLDER_CONTENT_ID])
                    );
            } else {
                $tmp[$folder['ID']][$folder[static::FOLDER_NAME]][] = false;
            }

        }
        return $tmp;
    }

    public function getSubFolderDetails($id)
    {
        $q = "SELECT * FROM CupGoContent WHERE ID = ?";
        return $this->db->GetRow($q, array($id));
    }

    public function getGlobalContents()
    {
        $query = "SELECT ID, CMS_Name, ContentHeading ";
        $query .= "FROM CupGoContent ";
        $query .= "WHERE Global ='Y' ";
        $query .= "AND ID NOT IN(SELECT RecordID FROM `CupGoArchive` WHERE TableName IN('CMS_Content','CupGoContent'))";
        return $this->db->GetAll($query);
    }

    public function getTabs($titleID)
    {
        $sql = "SELECT * FROM CupGoTabs WHERE (titleID = ? AND TabLevel = 1 AND TabType = 'TAB') ";
        $sql .= "AND ID NOT IN(SELECT RecordID FROM CupGoArchive WHERE TableName IN ('CupGoTabs','CMS_Tabs')) ";
        $sql .= "ORDER BY SortOrder";
        return $this->db->GetAll($sql, array($titleID));
    }

    /**
     * Added by gxbalila
     * GCAP-790
     * @return bool
     */
    public function getAllTabs()
    {
        $sql = "SELECT * FROM CupGoTabs WHERE (TabLevel = 1 AND TabType = 'TAB') ";
        $sql .= "AND ID NOT IN(SELECT RecordID FROM CupGoArchive WHERE TableName IN ('CupGoTabs','CMS_Tabs')) ";
        return $this->db->GetAll($sql);
    }

    // GCAP-845 Modifed by JSulit 2020/06/23 | SB-662 modified by mtanada 20201023
    public function getTabDetails($tabID)
    {
        $sql = "SELECT gt.ID, gt.CreationDate, gt.ParentID, gt.TitleID, gt.TabName, gt.Public_TabText, gt.Private_TabText,";
        $sql .= "gt.SortOrder, gt.Active, gt.Visibility, gt.DefaultTab, gt.TabLevel, gt.Columns, gt.TabType, gt.Indent,";
        $sql .= "gt.UserTypeIDRestriction, gt.CatalogueField, gt.ContentVisibility, gt.WatermarkImage, gt.ContentType,";
        $sql .= "gt.CustomAccessMessage, gt.AlwaysUsePublicText, gt.AllowSearch, gt.TabColour, gt.MyResourcesLink,";
        $sql .= "gt.ElevateProduct, gt.HMProduct, gt.ComingSoon, gt.KnowledgeCheck, gt.Cogbooks, gt.Saras, gt.isDPSApp, gt.ContentAccess, gt.ResourceURL,";
        $sql .= "gt.HmID, gt.hm_test_url, gt.hm_prod_url, gt.TabIcon, gt.TabTitle2, gt.group_id, gt.thumbnail_url,";
        $sql .= "cgt.group_name FROM CupGoTabs gt ";
        $sql .= "INNER JOIN CupGoTabGroup cgt on gt.group_id = cgt.ID WHERE (gt.`ID` = ?) ";
        $sql .= "AND gt.`ID` NOT IN(SELECT RecordID FROM CupGoArchive WHERE TableName IN ('CupGoTabs','CMS_Tabs'))";

        $result = $this->db->getRow($sql, array($tabID));

        if (is_null($result) || sizeof($result) == 0) {
            $sql = "SELECT * FROM CupGoTabs WHERE (`ID` = ?) ";
            $sql .= "AND `ID` NOT IN(SELECT RecordID FROM CupGoArchive WHERE TableName IN ('CupGoTabs','CMS_Tabs'))";
            return $this->db->getRow($sql, array($tabID));
        }
        return $this->db->getRow($sql, array($tabID));
    }

    public function getContentAdded($tabID)
    {
        $query = "SELECT cgtc.*,cgc.CMS_Name,cgc.ContentHeading ";
        $query .= "FROM CupGoTabContent AS cgtc JOIN CupGoContent AS cgc ON cgtc.ContentID = cgc.ID ";
        $query .= "WHERE cgtc.TabID = ? ";
        $query .= "AND cgc.`ID` NOT IN(";
        $query .= "SELECT RecordID FROM `CupGoArchive` WHERE `TableName` IN('CupGoContent','CMS_Content')) ";
        $query .= "ORDER BY cgtc.`SortOrder`";
        return $this->db->GetAll($query, array($tabID));
    }

    public function getGroups()
    {
        $groups = array();
        $query = "SELECT * FROM Groups";
        $results = $this->db->GetAll($query);

        if (is_array($results)) {
            foreach ($results as $eachRow) {
                $groups[$eachRow['gID']] = $eachRow['gName'];
            }
        }
        return $groups;
    }

    private function getSubFolderContentHeadings($id)
    {
        $q = "SELECT ID, Public_Name FROM `CupGoContentDetail` ";
        $q .= "WHERE `ContentID` = ? AND `ID` NOT IN(";
        $q .= "SELECT `RecordID` FROM CupGoArchive WHERE TableName IN('CupGoContentDetails','CMS_ContentDetails')) ";
        $q .= "ORDER BY SortOrder ASC ";
        return $this->db->GetAll($q, array($id));
    }

    public function getSubFolderContents($id)
    {
        $q = "SELECT * FROM CupGoContentDetail WHERE ID = ?";
        return $this->db->GetRow($q, array($id));
    }

    public function getSubFolderHeading($id)
    {
        $query = "SELECT ContentHeading, CMS_Name FROM CupGoContent WHERE ID = ?";
        return $this->db->GetRow($query, array($id));
    }

    public function saveNewTab($data, $titleID)
    {
        $newSortSql = "SELECT MAX(SortOrder) + 1 AS newSortNum FROM CupGoTabs WHERE titleID = ?";
        $result = $this->db->GetRow($newSortSql, array($titleID));
        $sortNum = ($result[static::NEW_SORT_NUM]) ? $result[static::NEW_SORT_NUM] : 1;

        // Add the SortOrder in the $data array
        $data[static::SORT_ORDER] = $sortNum;
        $data['TabLevel'] = 1;
        $data['titleID'] = $titleID;

        if ($data['group_id'] == 0 || $data['group_id'] == 'NULL') {
            $data['group_id' == NULL];
        }

        // Added by gxbalila
        // GCAP-790
        if ((int)$data['TabIcon'] > 0) {
            $data['thumbnail_url'] = $this->getTabThumbnailURL($data['TabIcon']);
        }

        $tmp = array();
        $qMarks = array();
        $keys = array();

        foreach ($data as $k => $d) {
            $keys[] = $this->clean($k);
            $tmp[] = $d;
            $qMarks[] = '?';
        }
        $sql = "INSERT INTO CupGoTabs (" . implode(",", array_keys($data)) . ") ";
        $sql .= "VALUES (" . implode(",", $qMarks) . ")";
        $this->db->Execute($sql, $tmp);
        return $this->db->Insert_ID('CupGoTabs');
        }

    /**
     * Added by gxbalila
     * GCAP-790
     * Returns tab format location
     * @param $iconID
     * @return string
     */
    public function getTabThumbnailURL($iconID)
    {
        $sourcePath = TAB_ICON_FOLDER . $iconID . '.png';
        $path = 'files/cup_content/images/formats/' . $iconID . '.png';

        if (!file_exists($sourcePath)) {
            $path = null;
        }

        return $path;
    }

    public function saveNewFolder($titleID, $data)
    {
        $q = "INSERT INTO CupGoContentFolders (FolderName,titleID) VALUES (?, ?)";
        $this->db->Execute($q, array($data[static::FOLDER_NAME], $titleID));
        return $this->db->Insert_ID('CupGoContentFolders');
    }

    // ANZGO-3363 Modified by Shane Camus 02/22/18
    public function saveNewContentDetail($data)
    {
        $newSortSql = "SELECT MAX(SortOrder) + 1 AS newSortNum FROM CupGoContentDetail WHERE ContentID = ?";
        $result = $this->db->GetRow($newSortSql, array($data['ContentID']));

        $sortNum = ($result[static::NEW_SORT_NUM]) ? $result[static::NEW_SORT_NUM] : 1;
        $windowSize = $data[static::WINDOW_SIZE];
        $sizeArr = explode("x", $windowSize);

        // Added by Paul Balila (ANZGO-3167)
        $data[static::SORT_ORDER] = $sortNum;
        $data['WindowWidth'] = $sizeArr[0];
        $data['WindowHeight'] = $sizeArr[1];
        $data[static::FILE_UPLOAD_DATE] = date('Y-m-d H:i:s', $data[static::FILE_UPLOAD_DATE]);

        unset($data['folder_id']);
        unset($data[static::WINDOW_SIZE]);

        $params = array();
        $qMarks = array();
        $keys = array();

        if (!isset($data[static::ACTIVE])) {
            $data[static::ACTIVE] = 'N';
        }

        foreach ($data as $k => $d) {
            $keys[] = $this->clean($k);
            $params[] = $d;
            $qMarks[] = "?";
        }

        $q = "INSERT INTO CupGoContentDetail(" . implode(',', $keys);
        $q .= ") VALUES (" . implode(",", $qMarks) . ")";
        $this->db->Execute($q, $params);
        return $this->db->Insert_ID('CupGoContentDetail');
    }

    // ANZGO-3363 Modified by Shane Camus 02/22/18
    public function updateContentDetail($data)
    {
        $id = $data['ID'];
        $windowSize = $data[static::WINDOW_SIZE];

        $sizeArr = explode("x", $windowSize);
        $data['WindowWidth'] = $sizeArr[0];
        $data['WindowHeight'] = $sizeArr[1];
        $data[static::FILE_UPLOAD_DATE] = date('Y-m-d H:i:s', $data[static::FILE_UPLOAD_DATE]);

        unset($data['subfolder_id']);
        unset($data['folder_id']);
        unset($data['ID']);
        unset($data[static::WINDOW_SIZE]);

        if (!isset($data[static::ACTIVE])) {
            $data[static::ACTIVE] = 'N';
        }

        $sqlArr = array();
        $params = array();

        foreach ($data as $field_name => $value) {
            $sqlArr[] = $this->clean($field_name) . " = ?";
            $params[] = $value;
        }

        $query = "UPDATE CupGoContentDetail SET " . implode(",", $sqlArr) . " WHERE ID = ?";
        $params[] = $id;
        $this->db->Execute($query, $params);
        return $this->db->Affected_Rows();
    }

    public function updateTab($data)
    {
        $id = $data['ID'];
        unset($data['ID']);

        $sqlArr = array();
        $params = array();


        if (!isset($data['AlwaysUsePublicText'])) {
            $data['AlwaysUsePublicText'] = 'N';
        }
        if (!isset($data[static::ACTIVE])) {
            $data[static::ACTIVE] = 'N';
        }
        if (!isset($data['MyResourcesLink'])) {
            $data['MyResourcesLink'] = 'N';
        }
        if (!isset($data['ElevateProduct'])) {
            $data['ElevateProduct'] = 'N';
        }
        if (!isset($data['HMProduct'])) {
            $data['HMProduct'] = 'N';
        }

        // SB-249 added by mabrigos
        if (!isset($data['ComingSoon'])) {
            $data['ComingSoon'] = 'N';
        }

        // GCAP-1734 added by jdchavez
        if (!isset($data['KnowledgeCheck'])) {
            $data['KnowledgeCheck'] = 'N';
        }

        // CGO-233 added by mmascarinas
        if (!isset($data['Cogbooks'])) {
            $data['Cogbooks'] = 'N';
        }

        // CGO-343 added by tperez
        if (!isset($data['Saras'])) {
            $data['Saras'] = 'N';
        }

        // GCAP-845 added by JSulit
        if ($data['group_id'] === 0 || $data['group_id'] === 'NULL' || $data['group_id'] === '0') {
            $data['group_id'] = NULL;
        }

        // Added by gxbalila
        // GCAP-790
        if ((int)$data['TabIcon'] > 0) {
            $data['thumbnail_url'] = $this->getTabThumbnailURL($data['TabIcon']);
        }
        foreach ($data as $field_name => $value) {
            $sqlArr[] = $this->clean($field_name) . " = ?";
            $params[] = $value;
        }
        $sql = "UPDATE CupGoTabs SET " . implode(",", $sqlArr) . " WHERE ID = ?";
        $params[] = $id;
        $this->db->Execute($sql, $params);
        return $this->db->Affected_Rows();
    }

    public function updateTabContents($data)
    {
        $tabID = $data['tab_id'];
        unset($data['tab_id']);
        foreach ($data as $id => $contents) {
            if (strpos($id, 'temp') !== false) {
                // insert
                $this->addContentToTab($tabID, $contents);
                continue;
            }

            if ($contents[static::TO_DELETE] == 'Y') {
                $this->deleteAddedContent($id);
                continue;
            }

            unset($contents[static::TO_DELETE]);

            $sqlArr = array();
            $params = array();
            $sql = "";

            // Check if there is no 'Active' field in $contents.
            // That means that 'Active' is set to 'N' so we explicitly set that.
            // Same goes with 'DemoOnly'.
            if (!isset($contents[static::ACTIVE])) {
                $contents[static::ACTIVE] = 'N';
            }
            if (!isset($contents[static::DEMO_ONLY])) {
                $contents[static::DEMO_ONLY] = 'N';
            }
            foreach ($contents as $field_name => $value) {
                $sqlArr[] = $this->clean($field_name) . " = ?";
                $params[] = $value;
            }

            $sql = "UPDATE CupGoTabContent SET " . implode(",", $sqlArr) . " WHERE ID = ?";
            $params[] = $id;
            $this->db->Execute($sql, $params);
        }
        return $this->db->Affected_Rows(static::CUP_GO_TAB_CONTENT);
    }

    public function updateFolder($data)
    {
        $sql = "UPDATE CupGoContentFolders SET `FolderName` = ? WHERE `ID` = ?";
        $this->db->Execute($sql, array($data[static::FOLDER_NAME], $data['ID']));
        return $this->db->Affected_Rows();
    }

    public function updateSubFolder($data)
    {
        unset($data['SubcontentFiles']);
        unset($data[static::FOLDER_ID]);
        $id = $data['ID'];
        unset($data['ID']);

        $sqlArr = array();
        $params = array();

        if (!isset($data[static::GLOBAL_STRING])) {
            $data[static::GLOBAL_STRING] = 'N';
        }

        foreach ($data as $fieldName => $value) {
            $sqlArr[] = $this->clean($fieldName) . " = ?";
            $params[] = $value;
        }

        $sql = "UPDATE CupGoContent SET " . implode(",", $sqlArr) . " WHERE ID = ?";
        $params[] = $id;

        $this->db->Execute($sql, $params);
        return $this->db->Affected_Rows(static::CUP_GO_CONTENT);
    }

    public function addSubFolder($data, $titleID)
    {
        $folderID = $data[static::FOLDER_ID];
        unset($data[static::FOLDER_ID]);
        // ANZGO-2904
        unset($data['SubcontentFiles']);

        $params = array();
        $qMarks = array();
        $keys = array();

        if (!isset($data[static::GLOBAL_STRING])) {
            $data[static::GLOBAL_STRING] = 'N';
        }

        $data['titleID'] = $titleID;
        foreach ($data as $k => $v) {
            $keys[] = $this->clean($k);
            $qMarks[] = "?";
            $params[] = $v;
        }

        $insQuery = "INSERT INTO CupGoContent(CreationDate," . implode(",", $keys);
        $insQuery .= ") VALUES (NOW()," . implode(",", $qMarks) . ")";
        $this->db->Execute($insQuery, $params);
        $contentID = $this->db->Insert_ID("CupGoContent");

        $recordQuery = "INSERT INTO CupGoFolderContent(FolderID,ContentID) VALUES(?,?)";
        $this->db->Execute($recordQuery, array($folderID, $contentID));
        $this->db->Insert_ID(static::CUP_GO_FOLDER_CONTENT);

        return $contentID;
    }

    public function addContentToTab($tabID, $data)
    {
        $newSortSql = "SELECT MAX(SortOrder) + 1 AS newSortNum FROM CupGoTabContent WHERE TabID = ?";
        $result = $this->db->GetRow($newSortSql, array($tabID));
        $sortNum = ($result[static::NEW_SORT_NUM]) ? $result[static::NEW_SORT_NUM] : 1;

        $params = array();
        $qMarks = array();
        $keys = array();

        if (!isset($data[static::ACTIVE])) {
            $data[static::ACTIVE] = 'N';
        }

        if (!isset($data[static::DEMO_ONLY])) {
            $data[static::DEMO_ONLY] = 'N';
        }

        unset($data[static::TO_DELETE]);
        $data[static::SORT_ORDER] = $sortNum;
        $data['TabID'] = $tabID;

        foreach ($data as $k => $v) {
            $keys[] = $this->clean($k);
            $qMarks[] = "?";
            $params[] = $v;
        }

        $query = "INSERT INTO CupGoTabContent(CreationDate," . implode(",", $keys);
        $query .= ") VALUES (NOW()," . implode(",", $qMarks) . ")";

        $this->db->Execute($query, $params);
        return $this->db->Insert_ID(static::CUP_GO_TAB_CONTENT);
    }

    public function deleteAddedContent($id)
    {
        $query = "DELETE FROM CupGoTabContent WHERE ID = ?";
        $this->db->Execute($query, array($id));
        return $this->db->Affected_Rows();
    }

    public function deleteTab($tabID)
    {
        $deleteContentAddedQuery = "DELETE FROM CupGoTabContent WHERE TabID = ?";
        $deleteTabQuery = "DELETE FROM CupGoTabs WHERE ID = ?";

        $args = array($tabID);
        $this->db->Execute($deleteContentAddedQuery, $args);
        $this->db->Execute($deleteTabQuery, $args);

        $contentAddedDeleteResult = $this->db->Affected_Rows(static::CUP_GO_TAB_CONTENT);
        $tabDeleteResult = $this->db->Affected_Rows(CUP_GO_TAB_CONTENT);

        return $contentAddedDeleteResult && $tabDeleteResult;
    }

    public function deleteContentDetail($id)
    {
        if (is_array($id)) {
            if (!empty($id)) {
                $sql = "DELETE FROM CupGoContentDetail WHERE ID IN (" . implode(",", $id) . ")";
                $this->db->Execute($sql);
            }
        } else {
            $sql = "DELETE FROM CupGoContentDetail WHERE ID = ?";
            $this->db->Execute($sql, array($id));
        }
        return $this->db->Affected_Rows('CupGoContentDetail');
    }

    public function deleteSubFolder($id, $folderID)
    {
        $sql = "SELECT ID FROM CupGoContentDetail WHERE ContentID = ?";
        $result = $this->db->GetAll($sql, array($id));

        $subFolderContentIDs = array();
        foreach ($result as $r) {
            $subFolderContentIDs[] = $r['ID'];
        }

        $subFolderContentDelFlag = $this->deleteContentDetail($subFolderContentIDs);

        $delSQL = "DELETE FROM CupGoFolderContent WHERE FolderID = ? AND ContentID = ?";
        $this->db->Execute($delSQL, array($folderID, $id));
        $delResult = $this->db->Affected_Rows(static::CUP_GO_FOLDER_CONTENT);

        $delTabContent = "DELETE FROM CupGoTabContent WHERE ContentID = ?";
        $this->db->Execute($delTabContent, array($id));
        $delTabContentResult = $this->db->Affected_Rows(static::CUP_GO_TAB_CONTENT);

        $subFolderDelSQL = "DELETE FROM CupGoContent WHERE ID = ?";
        $this->db->Execute($subFolderDelSQL, array($id));
        $subFolderDelResult = $this->db->Affected_Rows(static::CUP_GO_CONTENT);

        return ($subFolderContentDelFlag || $delResult || $subFolderDelResult || $delTabContentResult);
    }

    public function deleteFolder($id)
    {
        $subFoldersSQL = "SELECT ContentID FROM CupGoFolderContent WHERE FolderID = ?";
        $subFoldersResult = $this->db->GetAll($subFoldersSQL, array($id));

        $subFolderIDs = array();
        foreach ($subFoldersResult as $sf_id) {
            $subFolderIDs[] = $sf_id['ContentID'];
        }

        if (!empty($subFolderIDs)) {
            $subFolderContentsSQL = "SELECT ID FROM CupGoContentDetail WHERE ContentID IN (";
            $subFolderContentsSQL .= implode(",", $subFolderIDs) . ")";
            $subFolderContentsResult = $this->db->GetAll($subFolderContentsSQL);

            $subFolderContentIDs = array();
            foreach ($subFolderContentsResult as $sfc_id) {
                $subFolderContentIDs[] = $sfc_id['ID'];
            }

            $subFolderContentDel = $this->deleteContentDetail($subFolderContentIDs);

            $delSQL = "DELETE FROM CupGoFolderContent WHERE FolderID = ? AND ContentID IN (";
            $delSQL .= implode(",", $subFolderIDs) . ")";
            $this->db->Execute($delSQL, array($id));
            $delResult = $this->db->Affected_Rows(static::CUP_GO_FOLDER_CONTENT);

            $delTabContentSQL = "DELETE FROM CupGoTabContent WHERE ContentID IN (";
            $delTabContentSQL .= implode(",", $subFolderIDs) . ")";
            $this->db->Execute($delTabContentSQL);
            $delTabContentResult = $this->db->Affected_Rows(static::CUP_GO_TAB_CONTENT);

            $subFolderDelSQL = "DELETE FROM CupGoContent WHERE ID IN (" . implode(",", $subFolderIDs) . ")";
            $this->db->Execute($subFolderDelSQL);
            $subFolderDelResult = $this->db->Affected_Rows(static::CUP_GO_CONTENT);
        }

        $folderDelSQL = "DELETE FROM CupGoContentFolders WHERE ID = ?";
        $this->db->Execute($folderDelSQL, array($id));
        $folderDelResult = $this->db->Affected_Rows('CupGoContentFolders');

        return ($subFolderContentDel || $delResult || $subFolderDelResult || $folderDelResult || $delTabContentResult);
    }

    public function getIcons()
    {
        $sql = "SELECT id, name FROM CupContentFormat ORDER BY name";
        return $this->db->GetAll($sql);
    }

    // ANZGO-2899
    public function updateTabSorting($data)
    {
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

    // ANZGO-2910
    public function updateContentDetailSortable($data)
    {
        $keys = array();
        $sql = "UPDATE CupGoContentDetail SET `SortOrder` = CASE `ID` ";
        foreach ($data as $dKey => $dValue) {
            $sql .= " WHEN " . $dKey . " THEN " . $dValue;
            $keys[] = $dKey;
        }
        $sql .= " END WHERE `ID` IN (" . implode(",", $keys) . ")";
        $this->db->Execute($sql);
        return ($this->db->Affected_Rows() > 0);
    }

    // ANZGO-2902
    // get from HM
    public function loadHotMathsAPITrialProducts($env = 'testing')
    {
        $sql = "SELECT access_token FROM Hotmaths_API where env='$env'";
        $result = $this->db->GetRow($sql);

        $accessToken = $result['access_token'];
        $curl = curl_init();
        $url = "https://api.edjin.com/api/product/products?access_token=$accessToken";

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        // ANZGO-3154
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);

        $hmProducts = curl_exec($curl);
        curl_close($curl);

        $results = json_decode($hmProducts);

        $products = array();

        // ANZGO-3154
        if ($results) {
            foreach ($results as $result) {
                $availability = $result->subscriptionDays;
                $productID = $result->productId;
                $subscriberType = $result->subscriberType;
                $label = $result->name . ' / ' . $productID . ' / ' . $subscriberType . ' ( ' . $availability . ' Days ) ';
                $products[$productID] = $label;
            }
        }

        return $products;

    }

     // GCAP-845 Added by JSulit 2020/05/08
    public function getGroupTabList()
    {
        $sql = "SELECT ID, group_name FROM CupGoTabGroup"; 
        return $this->db->GetAll($sql);
    }
}
