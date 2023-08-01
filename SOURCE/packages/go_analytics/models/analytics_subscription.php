<?php defined('C5_EXECUTE') or die(_('Access Denied.'));
/**
 * ANZGO- Added by: John Renzo S. Sunico, 8/18/2017 8:08 AM
 * Analytics Subscription
 * Update: Aug. 24, 2017 Change function names and queries
 */

class AnalyticsSubscription
{
    public static function getTitlesFromSubscription($exclude_ids = array())
    {
        $db = Loader::db();
        $sql = "SELECT ID, CMS_Name as Title, ISBN_13 as ISBN FROM CupGoSubscription";

        if ($exclude_ids) {
            $exclude_ids = mysql_escape_string($exclude_ids);
            $sql .= " WHERE ID NOT IN ($exclude_ids)";
        }

        $results = $db->GetAll($sql);
        return $results;
    }

    public static function getSubscriptionAccessCodePerMonth($month, $year)
    {
        $db = Loader::db();

        $sql = "SELECT cgsa.S_ID as SID, COUNT(*) as NumberOfCodes FROM CupGoAccessCodes as codes
                JOIN CupGoSubscriptionAvailability as cgsa ON codes.SA_ID = cgsa.ID
                WHERE MONTH(codes.CreationDate) = ? AND YEAR(codes.CreationDate) = ?
                GROUP BY SID";

        $result = $db->GetAll($sql, array($month, $year));
        return $result;
    }
}
