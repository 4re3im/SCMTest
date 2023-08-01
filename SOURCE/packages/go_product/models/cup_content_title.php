<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

class CupContentTitle extends Object
{
    protected $id = false;
    protected $name = false;
    protected $isbn13 = false;
    protected $region = false;
    protected $content = false;
    protected $createdAt = false;
    protected $prettyUrl = false;
    protected $modifiedAt = false;
    protected $longDescription = false;
    protected $shortDescription = false;
    protected $series = false;

    protected $exisiting_result = array();

    function __construct($id = false)
    {
        if ($id) {
            $db = Loader::db();
            $sql = "select * from CupContentTitle where id = ?";
            $result = $db->getRow($sql, array($id));

            if ($result) {
                $this->id = $result['id'];
                $this->name = $result['name'];
                $this->prettyUrl = $result['prettyUrl'];
                $this->displayName = $result['displayName'];
                $this->shortDescription = $result['shortDescription'];
                $this->longDescription = $result['longDescription'];
                $this->content = $result['content'];
                $this->modifiedAt = $result['modifiedAt'];
                $this->series = $result['series'];

                $this->exisiting_result = $result;
            }
        }
    }

    public static function fetchByID($id)
    {
        $object = new CupContentTitle($id);
        if ($object->id === false) {
            return false;
        } else {
            return $object;
        }
    }

    public static function fetchByPrettyUrl($prettyUrl)
    {
        $object = new CupContentTitle();
        $object->loadByPrettyUrl($prettyUrl);

        if ($object->id === false) {
            return false;
        } else {
            return $object;
        }
    }

    public static function fetchDetailByPrettyUrl($prettyUrl)
    {

        $object = new CupContentTitle();

        $db = Loader::db();
        $sql = "SELECT cct.id,cct.isbn13,cct.name, GROUP_CONCAT(author separator ', ') as author,cct.showBuyNow ";
        $sql .= "FROM CupContentTitle as cct ";
        $sql .= "LEFT JOIN CupContentTitleAuthors ccta ON cct.id=ccta.titleID ";
        $sql .= "WHERE prettyUrl like ? AND isGoTitle=1 AND isEnabled=1";
        $row = $db->getRow($sql, array($prettyUrl));

        if ($row) {
            return $row;
        } else {
            return false;
        }
    }

    public static function getEduPrettyUrlByISBN($isbn)
    {
        $db = Loader::db();
        $sql = "SELECT prettyUrl FROM education_c5.CupContentTitle WHERE isbn13=$isbn";
        $result = $db->getRow($sql);
        return $result['prettyUrl'];
    }

    /*
     * ANZGO-3492 Modified by John Renzo Sunico, June 12, 2017
     * ANZGO-3601 Modified by Shane Camus 01/17/18
     * Update sql to include HMProduct field
     * SB-249 modified by mabrigos added ComingSoon field 20190710
     */
    public static function fetchContentByPrettyUrl($prettyUrl, $tabID = 0, $usID = null)
    {
        $u = new User();

        $object = new CupContentTitle();

        $db = Loader::db();
        $params = array();
        $sql = "SELECT cct.id, cct.isbn13, cct.name, cct.goName, cct.goSubTitle, cgt.ID as tabID, TabName,
        Public_TabText, MyResourcesLink, HMProduct, ComingSoon, ";
        $sql .= "Private_TabText, AlwaysUsePublicText, cgt.Visibility, cgt.ContentVisibility, cgt.Columns,
        cgt.UserTypeIDRestriction,cgt.ResourceURL, cgt.ContentAccess, cgt.Active, cgt.TabIcon,";
        // ANZGO-2902
        $sql .= "cgt.HmID as sampleHmid, cgt.hm_test_url as hmTestUrl, cgt.hm_prod_url as hmProdUrl ";

        //if logged in check tab access
        if ($u->isLoggedIn()) {

            $userId = $u->getUserID();

            $sql .= ",(SELECT Active FROM CupGoTabAccess WHERE TabID=cgt.ID AND UserID=? ";
            $params[] = $userId;
            if ($usID) {
                $sql .= "AND US_ID = ? ";
                $params[] = $usID;
            } else {
                $sql .= "AND Active='Y' ";
            }
            $sql .= "LIMIT 1) as TabAccess ";
            $sql .= ",(SELECT cgsa.`HmID` FROM CupGoTabAccess cgta JOIN `CupGoSubscriptionAvailability` cgsa
            ON cgta.SA_ID = cgsa.ID WHERE TabID=cgt.ID AND cgta.Active='Y' AND UserID=? LIMIT 1) AS HMProductId ";
            $params[] = $userId;
        }

        $sql .= "FROM CupContentTitle as cct ";
        $sql .= "LEFT JOIN CupGoTabs cgt ON cct.id=cgt.titleID ";
        $sql .= "WHERE prettyUrl like ? AND cgt.Active='Y' AND TabType='Tab' AND TabLevel=1 ";
        $params[] = $prettyUrl;
        $sql .= "AND cgt.ID NOT IN (SELECT RecordID FROM CupGoArchive WHERE TableName IN ('CMS_Tabs','CupGoTabs')) ";

        if ($tabID) {
            $sql .= "AND cgt.ID = ? ";
            $params[] = $tabID;
        }

        $sql .= "ORDER BY cgt.SortOrder,ID";

        $row = $db->getAll($sql, $params);

        if ($row) {
            return $row;
        } else {
            return false;
        }
    }

    public static function getActiveContentsById($titleId)
    {
        $sql = <<<SQL
            SELECT cgt.*, cct.id, cct.isbn13 FROM CupGoTabs cgt
            JOIN CupContentTitle cct ON cgt.TitleID = cct.id
            WHERE
            cgt.TitleID = ? AND
            cgt.Active = 'Y' AND
            cgt.TabType = 'Tab' AND cgt.TabLevel = 1
            ORDER BY cgt.SortOrder
SQL;
        $db = Loader::db();

        return $db->GetAll($sql, [(int)$titleId]);
    }

    public static function getTitleAndTabInfoByTabId($tabId)
    {
        $db = Loader::db();
        $sql = <<<QUERY
            SELECT
                cct.id,
                cct.isbn13,
                cct.name,
                cct.goName,
                cct.goSubTitle,
                cgt.ID as tabID,
                TabName,
                Public_TabText,
                MyResourcesLink,
                HMProduct,
                Private_TabText,
                AlwaysUsePublicText,
                cgt.Visibility,
                cgt.ContentVisibility,
                cgt.Columns,
                cgt.UserTypeIDRestriction,
                cgt.ResourceURL,
                cgt.ContentAccess,
                cgt.Active,
                cgt.TabIcon,
                cgt.HmID as sampleHmid,
                cgt.hm_test_url as hmTestUrl,
                cgt.hm_prod_url as hmProdUrl
            FROM CupContentTitle cct
            INNER JOIN CupGoTabs cgt ON cct.id = cgt.TitleID
            WHERE cgt.ID = ? AND cgt.Active = 'Y'
QUERY;
        return $db->GetRow($sql, [$tabId]);
    }

    public function getActiveTabs()
    {
        $db = Loader::db();
        $sql = <<<SQL
          SELECT *, ID as tabID
          FROM CupGoTabs
          WHERE TitleID = ?
            AND Active = 'Y'
            AND TabType = 'Tab'
            AND TabLevel = 1
          ORDER BY SortOrder
SQL;

        return $db->GetAll($sql, [(int)$this->id]);
    }

    public function loadByPrettyUrl($prettyUrl)
    {
        $db = Loader::db();
        $sql = "SELECT * FROM CupContentTitle WHERE prettyUrl = ?";

        $result = $db->getRow($sql, array($prettyUrl));

        if ($result) {
            $this->id = $result['id'];
            $this->name = $result['name'];
            $this->isbn13 = $result['isbn13'];
            $this->prettyUrl = $result['prettyUrl'];
            $this->displayName = $result['displayName'];
            $this->shortDescription = $result['shortDescription'];
            $this->longDescription = $result['longDescription'];
            $this->content = $result['content'];
            $this->modifiedAt = $result['modifiedAt'];
            $this->series = $result['series'];

            $this->exisiting_result = $result;

        } else {
            return false;
        }
    }

    public static function getTitleSubjects($id)
    {
        $db = Loader::db();
        $sql = "SELECT ccs.`name` AS subject,ccs.`prettyUrl` FROM `CupContentTitleSubjects` AS ccts
        JOIN `CupContentSubject` AS ccs ON ccts.`subject` = ccs.`name` WHERE ccts.`titleID` = ?";

        return $db->getAll($sql, array($id));
    }

    /* ANZGO-3687 Added by Maryjes Tanada 04/10/2018
     * Get Hm ID and SA_ID
     * @param int $titleId
     */
    public static function getStudentHmIdSaId($titleId)
    {
        $db = Loader::db();
        $sql = <<<SQL
            SELECT cgsa.HmID, cgsa.ID FROM CupGoSubscriptionAvailability cgsa
            INNER JOIN CupGoSubscription cgs ON cgsa.S_ID = cgs.ID
            INNER JOIN CupGoSubscriptionTabs cgst ON cgs.ID = cgst.S_ID
            INNER JOIN CupGoTabs cgt ON cgst.TabID = cgt.ID
            INNER JOIN CupContentTitle cct ON cgt.titleID = cct.id
            WHERE cct.id = ? AND cgt.UserTypeIDRestriction = 4 AND cgsa.HmID IS NOT NULL LIMIT 1;
SQL;
        return $db->GetRow($sql, array($titleId));
    }

    public static function getStudentHMTabsByTitleId($titleId)
    {
        $db = Loader::db();
        $sql = <<<SQL
            SELECT ID FROM CupGoTabs
            WHERE TitleID = ? AND UserTypeIDRestriction = 4 AND HMProduct = 'Y';
SQL;
        return $db->GetAll($sql, [$titleId]);
    }

    /* ANZGO-3687 Added by Maryjes Tanada 04/10/2018
     * Get SA_ID
     * @param int $titleId, int $hmId
     */
    public static function getHmTeacherSaId($titleId, $hmId)
    {
        $db = Loader::db();
        $sql = <<<SQL
            SELECT cgsa.ID as SaID FROM CupGoSubscriptionAvailability cgsa
            INNER JOIN CupGoSubscription cgs ON cgsa.S_ID = cgs.ID
            INNER JOIN CupGoSubscriptionTabs cgst ON cgs.ID = cgst.S_ID
            INNER JOIN CupGoTabs cgt ON cgst.TabID = cgt.ID
            INNER JOIN CupContentTitle cct ON cgt.titleID = cct.id
            WHERE cct.id = ? AND cgsa.HmID = ? LIMIT 1;
SQL;
        $result = $db->GetRow($sql, array($titleId, $hmId));
        return $result['SaID'];
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
