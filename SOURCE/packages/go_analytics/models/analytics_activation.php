<?php

/**
 * Analytics Model for Activation
 */

defined('C5_EXECUTE') || die(_('Access Denied.'));

class AnalyticsActivation
{

    public static function getMonthlyTotalNewSubscriptions($year, $month)
    {
        $db = Loader::db();

        $itbSql = "SELECT count(*) as count ";
        $itbSql .= "FROM CupGoSubscription cgs ";
        $itbSql .= "INNER JOIN CupGoSubscriptionAvailability cgsa ON cgs.ID = cgsa.S_ID ";
        $itbSql .= "INNER JOIN CupGoUserSubscription cgus ON cgsa.ID = cgus.SA_ID ";
        $itbSql .= "WHERE (Archive IS NULL OR Archive!='Y') AND cgus.S_ID IS NOT NULL ";
        $itbSql .= "AND cgus.CreationDate like ? ";

        return $db->GetRow($itbSql, array('%'.$year.'-'.str_pad($month, 2, "0", STR_PAD_LEFT).'%'));
    }

    public static function getMonthlyITBNewSubscriptions($year, $month)
    {
        $db = Loader::db();

        $itbSql = "SELECT count(*) as count ";
        $itbSql .= "FROM CupGoSubscription cgs ";
        $itbSql .= "INNER JOIN CupGoSubscriptionAvailability cgsa ON cgs.ID = cgsa.S_ID ";
        $itbSql .= "INNER JOIN CupGoUserSubscription cgus ON cgsa.ID = cgus.SA_ID ";
        $itbSql .= "INNER JOIN CupGoSubscriptionTabs cgst ON cgst.S_ID = cgs.ID ";
        $itbSql .= "INNER JOIN CupGoTabs cgt ON cgt.ID = cgst.TabID ";
        $itbSql .= "WHERE (Archive IS NULL OR Archive!='Y') AND cgus.S_ID IS NOT NULL ";
        $itbSql .= "AND TabName = 'Interactive Textbook' ";
        $itbSql .= "AND cgus.CreationDate like ? ";

        return $db->GetRow($itbSql, array('%'.$year.'-'.str_pad($month, 2, "0", STR_PAD_LEFT).'%'));
    }

    public static function getAverageSubscriptionsPerAccount()
    {
        $db = Loader::db();

        $userSql = "SELECT count(*) as userCount FROM Users WHERE uIsActive = 1 AND uIsValidated = 1";
        $userResultCount = $db->GetRow($userSql);

        $subscriptionSql = "SELECT count(*) as subscriptionCount FROM ( ";
        $subscriptionSql .= "SELECT * FROM ( ";
        $subscriptionSql .= "SELECT * FROM ( ";
        $subscriptionSql .= "SELECT ";
        $subscriptionSql .= "cgus.ID AS UserSubscriptionID, cgus.CreationDate as USubCreationDate, ";
        $subscriptionSql .= "cgus.EndDate AS USubEndDate, cgs.ISBN_13, cgus.AccessCode, cgus.Active, ";
        $subscriptionSql .= "cgus.DaysRemaining, cgsa.CreationDate, cgsa.StartDate, cgsa.EndDate, cgsa.Duration, ";
        $subscriptionSql .= "cgsa.Type, cgsa.ID AS SubscriptionAvailID, cgs.Name, cgs.Description, cgs.CMS_Name, ";
        $subscriptionSql .= "cgs.ID AS SubscriptionID, 'Go' as Source ";
        $subscriptionSql .= "FROM CupGoSubscription cgs ";
        $subscriptionSql .= "LEFT JOIN CupGoSubscriptionAvailability cgsa ON cgs.ID=cgsa.S_ID ";
        $subscriptionSql .= "LEFT JOIN CupGoUserSubscription cgus ON cgsa.ID=cgus.SA_ID ";
        $subscriptionSql .= "WHERE (Archive IS NULL OR Archive!='Y') AND cgus.S_ID IS NOT NULL ";
        $subscriptionSql .= ") AS a ORDER BY ISBN_13, USubCreationDate desc ";
        $subscriptionSql .= ") AS b GROUP BY CMS_Name, UserSubscriptionID ) as test ";
        $subsResultCount = $db->GetRow($subscriptionSql);

        $hmSql = "SELECT count(ID) as hmCount FROM Hotmaths";
        $hmResultCount = $db->GetRow($hmSql);

        $allSubs = $subsResultCount['subscriptionCount'] + $hmResultCount['hmCount'];
        $average = $allSubs / $userResultCount['userCount'];

        return number_format((float)$average, 2, '.', '');
    }

    public static function getTotalSubscriptionPerGOLife()
    {
        $db = Loader::db();

        $subscriptionSql = "SELECT count(*) as subscriptionCount FROM ( ";
        $subscriptionSql .= "SELECT * FROM ( ";
        $subscriptionSql .= "SELECT * FROM ( ";
        $subscriptionSql .= "SELECT ";
        $subscriptionSql .= "cgus.ID AS UserSubscriptionID, cgus.CreationDate as USubCreationDate, ";
        $subscriptionSql .= "cgus.EndDate AS USubEndDate, cgs.ISBN_13, cgus.AccessCode, cgus.Active, ";
        $subscriptionSql .= "cgus.DaysRemaining, cgsa.CreationDate, cgsa.StartDate, cgsa.EndDate, cgsa.Duration, ";
        $subscriptionSql .= "cgsa.Type, cgsa.ID AS SubscriptionAvailID, cgs.Name, cgs.Description, cgs.CMS_Name, ";
        $subscriptionSql .= "cgs.ID AS SubscriptionID, 'Go' as Source ";
        $subscriptionSql .= "FROM CupGoSubscription cgs ";
        $subscriptionSql .= "LEFT JOIN CupGoSubscriptionAvailability cgsa ON cgs.ID=cgsa.S_ID ";
        $subscriptionSql .= "LEFT JOIN CupGoUserSubscription cgus ON cgsa.ID=cgus.SA_ID ";
        $subscriptionSql .= "WHERE (Archive IS NULL OR Archive!='Y') AND cgus.S_ID IS NOT NULL ";
        $subscriptionSql .= ") AS a ORDER BY ISBN_13, USubCreationDate desc ";
        $subscriptionSql .= ") AS b GROUP BY CMS_Name, UserSubscriptionID ) as test ";
        $subsResultCount = $db->GetRow($subscriptionSql);

        $hmSql = "SELECT count(ID) as hmCount FROM Hotmaths";
        $hmResultCount = $db->GetRow($hmSql);

        return $subsResultCount['subscriptionCount'] + $hmResultCount['hmCount'];
    }

    public static function getTotalNewSubscriptionsISBNTitle($year, $month, $value, $type = "ISBN")
    {
        $db = Loader::db();

        $subscriptionSql = "SELECT count(*) as count ";
        $subscriptionSql .= "FROM CupGoSubscription cgs ";
        $subscriptionSql .= "LEFT JOIN CupGoSubscriptionAvailability cgsa ON cgs.ID=cgsa.S_ID ";
        $subscriptionSql .= "LEFT JOIN CupGoUserSubscription cgus ON cgsa.ID=cgus.SA_ID ";
        $subscriptionSql .= "WHERE (Archive IS NULL OR Archive != 'Y') AND cgus.S_ID IS NOT NULL ";

        if ($type == "ISBN") {
            $subscriptionSql .= "AND ISBN_13 = ? ";
        } else {
            $subscriptionSql .= "AND CMS_Name = ? ";
        }

        $subscriptionSql .= "AND cgus.CreationDate like ? ";

        return $db->GetRow($subscriptionSql, array($value, '%'.$year.'-'.str_pad($month, 2, "0", STR_PAD_LEFT).'%'));
    }

    public static function getAllSubscriptions()
    {
        $db = Loader::db();

        // ANZGO-3488 modified by jbernardez 20170905
        // added ID
        $subscriptionsSql = "SELECT ID, ISBN_13, CMS_Name FROM CupGoSubscription";

        return $db->GetAll($subscriptionsSql);
    }

    // ANZGO-3488 Added by jbernardez 20170907
    // ANZGO-3526 Modified by Shane Camus 10/05/17
    public static function getITBAndEPubTitlesCount()
    {
        $db = Loader::db();

        $sql = "SELECT COUNT(*) as titlesCount FROM CupContentTitle ";
        $sql .= "WHERE id IN ( ";
        $sql .= "SELECT DISTINCT(TitleID) ";
        $sql .= "FROM CupGoTabs WHERE Private_TabText LIKE '%preview_content%' ";
        $sql .= "OR Private_TabText LIKE '%interactive_book%' ";
        $sql .= "GROUP BY TitleID ORDER BY TitleID ";
        $sql .= ")";

        $result = $db->GetRow($sql);
        return $result['titlesCount'];
    }

    // ANZGO-3488 Added by jbernardez 20170907
    // ANZGO-3526 Modified by Shane Camus 10/05/17
    // batch is current batch to return
    // batches is number of batch to breakdown to
    public static function getBatchTitles($batch = 1, $batches = 1)
    {
        // this should not happen as maximum number of batch is always equal to batches
        if ($batch > $batches) {
            return false;
        }

        $subsCount = static::getITBAndEPubTitlesCount();

        if ($batches > 1) {
            $limit = ceil($subsCount / $batches);
            $multiplier = $batch - 1;
            $start = $multiplier * ($limit);
        } else {
            $start = 0;
            $limit = $subsCount;
        }

        return static::getTitlesPerBatch($start, $limit);
    }

    // ANZGO-3488 Added by jbernardez 20170907
    public static function getTitlesPerBatch($start, $limit)
    {
        $db = Loader::db();

        $sql = "SELECT id, isbn13, name FROM CupContentTitle ";
        $sql .= "WHERE id IN ( ";
        $sql .= "SELECT DISTINCT(TitleID) ";
        $sql .= "FROM CupGoTabs WHERE Private_TabText LIKE '%preview_content%' ";
        $sql .= "OR Private_TabText LIKE '%interactive_book%' ";
        $sql .= "GROUP BY TitleID ORDER BY TitleID) ";
        $sql .= "LIMIT ? OFFSET ? ";

        return $db->GetAll($sql, array((int)$limit, $start));
    }

    /**
     * ANZGO-3487 Added by John Renzo Sunico, Aug 18, 2017
     * @param $s_id int Subscription ID
     * @param $month int Month to fetch
     * @param $year int Year to fetch
     * @return array|bool
     * Update: Aug. 24, 2017 Change function names and queries
     */
    public static function getSubscriptionActivationPerMonth($month, $year)
    {
        $db = Loader::db();
        $sql = "SELECT cgsa.S_ID as SID, COUNT(*) as NumberOfActivation FROM CupGoUserSubscription cgus ";
        $sql .= "JOIN CupGoSubscriptionAvailability cgsa ON cgus.SA_ID = cgsa.ID ";
        $sql .= "WHERE cgus.Active = 'Y'  AND cgus.Archive IS NULL AND ";
        $sql .= "MONTH(cgus.CreationDate) = ? AND YEAR(cgus.CreationDate) = ? ";
        $sql .= "GROUP BY SID";
        return $db->getAll($sql, array($month, $year));
    }

    /**
     * ANZGO-3487 Added by John Renzo Sunico, Aug 18, 2017
     * @param $s_id int Subscription ID
     * @param $month int Month to fetch
     * @param $year int Year to fetch
     * @return array|bool
     * Update: Aug. 24, 2017 Change function names and queries
     */
    public static function getExpiredSubscriptionPerMonth($month, $year)
    {
        $db = Loader::db();
        $sql = "SELECT cgsa.S_ID as SID, COUNT(*) as NumberOfExpired FROM CupGoUserSubscription cgus ";
        $sql .= "JOIN CupGoSubscriptionAvailability cgsa ON cgus.SA_ID = cgsa.ID ";
        $sql .= "WHERE cgus.Active = 'N' ";
        $sql .= "AND ( ";
        $sql .= "(MONTH(cgus.DateDeactivated) = ? AND YEAR(cgus.DateDeactivated) = ?) OR ";
        $sql .= "(MONTH(cgus.EndDate) = ? AND YEAR(cgus.EndDate) = ?) ";
        $sql .= ") ";
        $sql .= "GROUP BY SID;";
        return $db->getAll($sql, array($month, $year, $month, $year));
    }
}
