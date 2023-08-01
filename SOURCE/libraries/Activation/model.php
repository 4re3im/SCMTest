<?php

/**
 * Activating of Access Code Model
 * ANZGO-3495 Added by Shane Camus 9/20/2017
 * ANZGO-3764 tagged by jbernardez 20181008
 * ActivationModel is now tagged for deletion as we are now using PEAS API for activation
 */

class ActivationModel
{
    private $db;

    public function __construct()
    {
        $this->db = Loader::db();
    }

    /**
     * @param $accessCode
     * @return mixed
     */
    public function getAccessCodeDetails($accessCode)
    {
        $db = Loader::db();
        $sql = "SELECT cgac.ID AS id, DATE(cgac.DateActivated) AS dateActivated, cgac.Active AS active, ";
        // ANZGO-3757 modified by jbernardez 20180620
        $sql .= "cgac.Usable as usable, cgac.UsageCount as usageCount, cgac.UsageMax as usageMax, cgacb.EOL as eol, cgsa.ID as saID, ";
        $sql .= "cgsa.EndDate as endDate, cgsa.Duration as duration, cgsa.Type as type, cgsa.EndOfYearBreakpoint as eoyBreakpoint, ";
        $sql .= "cgsa.LimitActivation as limitActivation, cgsa.EndOfYearOffSet as eoyOffset, cgsa.HmID as hmID, ";
        // ANZGO-3758 modified by jbernardez 20180629
        $sql .= "cgs.ID as sID, cgs.Activate_Page_Message as message, cgs.edumar_titleID as edumarTitleID ";
        $sql .= "FROM CupGoAccessCodes cgac ";
        $sql .= "JOIN CupGoAccessCodeBatch cgacb ON cgac.BatchID = cgacb.ID ";
        $sql .= "JOIN CupGoSubscriptionAvailability cgsa ON cgsa.ID = cgac.SA_ID ";
        $sql .= "JOIN CupGoSubscription cgs ON cgs.ID = cgsa.S_ID ";
        $sql .= "WHERE cgac.AccessCode = ?";

        return $db->GetRow($sql, $accessCode);
    }

    /**
     * @param $accessCode
     * @return mixed
     */
    public function getTabName($accessCode)
    {
        $db = Loader::db();
        $sql = "SELECT cgt.TabName ";
        $sql .= "FROM CupGoAccessCodes cgac ";
        $sql .= "JOIN CupGoSubscriptionAvailability cgsa ON cgac.SA_ID = cgsa.ID ";
        $sql .= "JOIN CupGoSubscription cgs ON cgsa.S_ID = cgs.ID ";
        $sql .= "JOIN CupGoSubscriptionTabs cgst ON cgst.S_ID = cgs.ID ";
        $sql .= "JOIN CupGoTabs cgt ON cgst.TabID = cgt.ID ";
        $sql .= "WHERE cgac.AccessCode = ?";

        return $db->GetAll($sql, $accessCode);
    }

    /**
     * @param $sID
     * @return mixed
     */
    public function getTabIDs($sID)
    {
        $db = Loader::db();
        $sql = "SELECT TabID AS tabID FROM CupGoSubscriptionTabs WHERE S_ID=?";

        return $db->GetAll($sql, $sID);
    }

    /**
     * @param $userID
     * @param $todayDate
     * @param $ipAddress
     * @param $agentInfo
     * @param $isUsable
     * @param $accessCodeID
     * @return mixed
     */
    public function assignAccessCodeToUser($userID, $todayDate, $ipAddress, $agentInfo, $isUsable, $accessCodeID)
    {
        $db = Loader::db();
        $sql = "UPDATE CupGoAccessCodes SET UserID=?, DateActivated = ?, IPAddress=?, ";
        $sql .= "UserAgent=?, Usable=?, UsageCount=(UsageCount+1) ";
        $sql .= "WHERE ID=?";
        $db->Execute($sql, array($userID, $todayDate, $ipAddress, $agentInfo, $isUsable, $accessCodeID));

        return $db->Affected_Rows();
    }

    /**
     * @param $endDate
     * @param $subscriptionAvailabilityID
     * @return mixed
     */
    public function setEndDateSubscriptionAvailability($endDate, $subscriptionAvailabilityID)
    {
        $db = Loader::db();
        $sql = "UPDATE CupGoSubscriptionAvailability SET EndDate=? WHERE ID=?";
        $db->Execute($sql, array($endDate, $subscriptionAvailabilityID));

        return $db->Affected_Rows();
    }

    /**
     * @param $userID
     * @param $todayDate
     * @param $endDate
     * @param $accessCode
     * @param $saID
     * @param $purchaseType
     * @return mixed
     * Modified by: Jeszy Tanada 10/06/2017
     * added params in Execute and modify DaysRemaining
     * modified by: Jeszy Tanada 2018/07/02 Added Reactivation type
     */
    public function createUserSubscription($userID, $todayDate, $endDate, $accessCode, $saID, $purchaseType = 'CODE')
    {
        $db = Loader::db();

        $sql = 'INSERT INTO CupGoUserSubscription (UserID, SA_ID, CreationDate, StartDate, EndDate, Duration, ';
        $sql .= 'Active, AccessCode, PurchaseType, S_ID, DaysRemaining) ';
        $sql .= 'SELECT ? AS "UserID", ID, ? AS "CreationDate", StartDate, ? AS "EndDate", ';
        $sql .= 'CASE ';
        $sql .= 'WHEN Type = "end-of-year" THEN DATEDIFF(DATE(?), DATE(?)) ';
        $sql .= 'WHEN Type = "reactivation" THEN DATEDIFF(DATE(?), DATE(?)) ';
        $sql .= 'WHEN Type = "start-end" THEN DATEDIFF(EndDate, StartDate) ';
        $sql .= 'WHEN Type = "duration" THEN Duration ';
        $sql .= 'END AS Duration, "Y" AS "Active", ? AS "AccessCode", ? AS "PurchaseType", ';
        $sql .= 'S_ID, CASE ';
        $sql .= 'WHEN Type = "end-of-year" THEN DATEDIFF(DATE(?), DATE(?)) ';
        $sql .= 'WHEN Type = "reactivation" THEN DATEDIFF(DATE(?), DATE(?)) ';
        $sql .= 'WHEN Type = "start-end" THEN DATEDIFF(EndDate, StartDate) ';
        $sql .= 'WHEN Type = "duration" THEN Duration ';
        $sql .= 'END AS DaysRemaining ';
        $sql .= 'FROM CupGoSubscriptionAvailability WHERE ID=?';
        $db->Execute($sql, array(
            $userID,
            $todayDate,
            $endDate,
            $endDate,
            $todayDate,
            $endDate,
            $todayDate,
            $accessCode,
            $purchaseType,
            $endDate,
            $todayDate,
            $endDate,
            $todayDate,
            $saID
        ));

        return $db->Insert_ID('CupGoUserSubscription');
    }

    /**
     * @param $userID
     * @param $tabID
     * @param $saID
     * @param $endDate
     * @param $lastUSID
     * @param $daysRemainingInfo
     * @return mixed
     * Modified by: Jeszy Tanada 10/06/2017
     * Insert DaysRemaining Field from UserSubscription
     */
    public function assignTabsToUser($userID, $tabID, $sID, $saID, $endDate, $lastUSID, $daysRemainingInfo)
    {
        $db = Loader::db();
        $sql = "INSERT INTO CupGoTabAccess (UserID, TabID, S_ID, SA_ID, Active, EndDate, US_ID, DaysRemaining) ";
        $sql .= "VALUES (?,?,?,?,'Y',?,?,?) ";
        $db->Execute($sql, array($userID, $tabID, $sID, $saID, $endDate, $lastUSID, $daysRemainingInfo));

        return $db->Insert_ID();
    }

    /**
     * @param $tabID
     * @param $saID
     * @param $accessCode
     * @param $subscriptionID
     * @return mixed
     * Modified by: Jeszy Tanada 10/06/2017
     * Get DaysRemaining data from UserSubscription
     */
    public function getDaysRemaining($userSubscriptionID)
    {
        $db = Loader::db();
        // ANZGO-3774 modified by jbernardez 20180711
        $sql = "SELECT DaysRemaining FROM CupGoUserSubscription WHERE ID = ?";

        return $db->GetRow($sql, array($userSubscriptionID));
    }

    // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
    public function getUserSubscriptionDaysRemainingByID($id)
    {
        $sql = 'SELECT DaysRemaining FROM CupGoUserSubscription WHERE ID = ?';

        return $this->db->GetOne($sql, [$id]);
    }

    // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
    public function getSubscriptionAvailabilityByID($id)
    {
        $sql = 'SELECT * FROM CupGoSubscriptionAvailability WHERE ID = ?';
        return $this->db->GetRow($sql, [$id]);
    }

    // ANZGO-3642 Added by John Renzo Sunico, 02/23/2018
    public function updateUserSubscriptionCreatorByID($id, $userID)
    {
        $sql = 'UPDATE CupGoUserSubscription SET CreatedBy = ? WHERE ID = ?';
        $this->db->Execute($sql, [$userID, $id]);

        return $this->db->Affected_Rows();
    }

    // ANZGO-3642 Added by John Renzo Sunico, 02/26/2018
    public function getTabNamesBySAID($said)
    {
        $sql = 'SELECT TabName FROM CupGoTabs cgt ';
        $sql .= 'INNER JOIN CupGoSubscriptionTabs cgst ON cgt.ID = cgst.TabID ';
        $sql .= 'INNER JOIN CupGoSubscription cgs ON cgst.S_ID = cgs.ID ';
        $sql .= 'INNER JOIN CupGoSubscriptionAvailability cgsa ON cgs.ID = cgsa.S_ID ';
        $sql .= 'WHERE cgsa.ID = ?;';

        return $this->db->GetAll($sql, [$said]);
    }

    /** ANZGO-3721 Added by Maryjes Tanada, 05/22/2018
     *  Get title ID and Taba name to be added, using access code
     */
    public function getTitleIdByAccessCode($accessCode)
    {
        $tabs = array();
        $sql = <<<SQL
            SELECT cgst.TitleID, cgt.TabName FROM CupGoSubscriptionTabs cgst
            INNER JOIN CupGoSubscription cgs ON cgst.S_ID = cgs.ID
            INNER JOIN CupGoSubscriptionAvailability cgsa ON cgs.ID = cgsa.S_ID
            INNER JOIN CupGoAccessCodes cgac ON cgsa.ID = cgac.SA_ID
            INNER JOIN CupGoTabs cgt ON cgst.TabID = cgt.ID
            WHERE cgac.AccessCode = ?
SQL;
        $tabs = $this->db->GetAll($sql, $accessCode);

        // ANZGO-3723 modified by jbernardez 20180524
        // added online teacher edition
        foreach ($tabs as $tab) {
            if ((strtolower($tab['TabName']) == 'online teacher resource') || (strtolower($tab['TabName']) == 'online resource')
                || (strtolower($tab['TabName']) == 'online teacher edition')) {
                return $tab;
            }
        }

        return false;
    }

    /** ANZGO-3721 Added by Maryjes Tanada, 05/22/2018
     * Count current active subscription and get current active Tab name
     */
    public function countSubscriptionByTitleId($userId, $titleId)
    {
        $sql = <<<SQL
            SELECT count(cgus.ID) as subscriptionCount, cgt.TabName FROM CupGoUserSubscription cgus
            INNER JOIN CupGoSubscriptionTabs cgst ON cgus.S_ID = cgst.S_ID
            INNER JOIN CupGoTabs cgt ON cgst.TabID = cgt.ID
            WHERE cgus.UserID = ?
            AND cgst.TitleID = ? AND cgus.Active = 'Y'
            GROUP BY cgus.ID DESC;
SQL;
        return $this->db->GetRow($sql, array($userId, $titleId));
    }

    // ANZGO-3723 added by jbernardez 20180523
    public function getTitleIdBySaID($saID)
    {
        $tabs = array();
        $sql = <<<SQL
            SELECT cgst.TitleID, cgt.TabName FROM CupGoSubscriptionTabs cgst
            INNER JOIN CupGoSubscription cgs ON cgst.S_ID = cgs.ID
            INNER JOIN CupGoSubscriptionAvailability cgsa ON cgs.ID = cgsa.S_ID
            INNER JOIN CupGoTabs cgt ON cgst.TabID = cgt.ID
            WHERE cgsa.ID = ?
SQL;
        $tabs = $this->db->GetAll($sql, $saID);

        foreach ($tabs as $tab) {
            if ((strtolower($tab['TabName']) == 'online teacher resource') || (strtolower($tab['TabName']) == 'online resource')
                || (strtolower($tab['TabName']) == 'online teacher edition')) {
                return $tab;
            }
        }

        return false;
    }

    public function getHotMathsTabsByTabIds($tabIds)
    {
        if (!$tabIds) {
            return [];
        }

        $tabs = implode(',', $tabIds);
        $sql = <<<SQL
            SELECT *
            FROM CupGoTabs
            WHERE ID IN ($tabs) AND
            LOWER(TabName) IN (
                'online teacher resource',
                'online resource',
                'online teacher edition'
            )
SQL;
        return $this->db->GetAll($sql);
    }

    public function getTabsFromTabIdsWithTitleId($tabIds, $titleId)
    {
        if (!$tabIds) {
            return [];
        }

        $tabIds = implode(',', $tabIds);

        $sql = <<<SQL
            SELECT *
            FROM CupGoTabs
            WHERE TitleID = ? AND
            ID IN ($tabIds);
SQL;
        return $this->db->GetAll($sql, [(int)$titleId]);
    }

    // GCAP-416 Added by mtanada, 05/20/2019
    public function getTabNameById($tabIDs)
    {
        $db = Loader::db();
        $sql = "SELECT TabName ";
        $sql .= "FROM CupGoTabs WHERE ID IN (".implode(',', $tabIDs).")";

        return $db->GetAll($sql);
    }

    /*
     * GCAP-848 modified by mtanada 20200504
     * Get title IDs and tab IDs
     * @param array tabs
     */
    public function getTitleIds($tabs)
    {
        if (!empty($tabs)) {
            $query = 'SELECT TitleID, id AS tabId FROM CupGoTabs
                      WHERE ID IN ('. implode(",", $tabs) .')';
            return $this->db->GetAll($query);
        }
        return false;
    }

    /*
     * GCAP-848 modified by mtanada 20200504
     * Get series ID for every unique title IDs
     * @param array titles with tabs
     */
    public function getSeriesIds($titleIds)
    {
        $tmpTitles = array_column($titleIds, 'TitleID');
        $query = 'SELECT cct.id AS titleId, ccs.ID AS seriesId FROM CupContentTitle cct
              INNER JOIN CupContentSeries ccs ON cct.series = ccs.name
              WHERE cct.id IN ('. implode(',', $tmpTitles) .') GROUP BY titleId';

        return $this->db->GetAll($query);
    }
}
