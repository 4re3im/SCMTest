<?php  defined('C5_EXECUTE') or die(_('Access Denied.'));

class AnalyticsUsage
{
    public static function getPeakUsageTimeByMonthYear($month, $year, $timezone=0)
    {
        $db = Loader::db();
        $sql = "SELECT Created as Month, BusiestTime, COUNT(*) as NumOfUsersSession ";
        $sql .= "FROM ";
        $sql .= "(SELECT DATE_FORMAT(DATE_ADD(CreatedDate, INTERVAL $timezone HOUR), '%M %Y') as Created, ";
        $sql .= "DATE_FORMAT(DATE_ADD(CreatedDate, INTERVAL $timezone HOUR), '%h:00 %p - %h:59 %p') as BusiestTime ";
        $sql .= "FROM CupGoLogUser ";
        $sql .= "WHERE DATE_FORMAT(DATE_ADD(CreatedDate, INTERVAL $timezone HOUR), '%M %Y') = ? ";
        $sql .= "GROUP BY UserID, BusiestTime) as t_traffic ";
        $sql .= "GROUP BY BusiestTime ";
        $sql .= "ORDER BY NumOfUsersSession DESC ";
        $sql .= "LIMIT 1;";
        $result = $db->getRow($sql, array("$month $year"));
        return $result;
    }

    public static function getPeakUsageDayByMonthYear($month, $year, $timezone=0)
    {
        $db = Loader::db();
        $sql = "SELECT Created as Month, BusiestDate, COUNT(*) as NumOfUsersSession ";
        $sql .= "FROM ";
        $sql .= "(SELECT DATE_FORMAT(DATE_ADD(CreatedDate, INTERVAL $timezone HOUR), '%M %Y') as Created, ";
        $sql .= "DATE_FORMAT(DATE_ADD(CreatedDate, INTERVAL $timezone HOUR), '%d') as BusiestDate ";
        $sql .= "FROM CupGoLogUser ";
        $sql .= "WHERE DATE_FORMAT(DATE_ADD(CreatedDate, INTERVAL $timezone HOUR), '%M %Y') = ? ";
        $sql .= "GROUP BY UserID, BusiestDate) as t_traffic ";
        $sql .= "GROUP BY BusiestDate ";
        $sql .= "ORDER BY NumOfUsersSession DESC ";
        $sql .= "LIMIT 1;";
        $result = $db->getRow($sql, array("$month $year"));
        return $result;
    }
}
