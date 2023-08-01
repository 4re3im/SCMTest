<?php  defined('C5_EXECUTE') or die(_('Access Denied.'));

// ANZGO-3451 Added by Shane Camus 7/25/17

class AnalyticsSupport
{
    public static function getEnquiryCountByMonth($month, $year)
    {
        $db = Loader::db();
        $sql = "SELECT YEAR(CreatedDate) as y, ";
        $sql .= "MONTH(CreatedDate) as m, ";
        $sql .= "COUNT(DISTINCT SessionID) as c ";
        $sql .= "FROM CupGoLogUser ";
        $sql .= "WHERE PageName='Contact' AND Action='Enquiry' ";
        $sql .= "AND MONTH(CreatedDate)= ? ";
        $sql .= "AND YEAR(CreatedDate)= ? ";
        $result = $db->getRow($sql, array($month, $year));
        return $result;
    }

    public static function getSupportPageVisitCountByMonth($month, $year)
    {
        $db = Loader::db();
        $sql = "SELECT YEAR(CreatedDate) as y, ";
        $sql .= "MONTH(CreatedDate) as m, ";
        $sql .= "COUNT(DISTINCT SessionID) as c ";
        $sql .= "FROM CupGoLogUser ";
        $sql .= "WHERE PageName='Support' AND Action='View' ";
        $sql .= "AND MONTH(CreatedDate)= ? ";
        $sql .= "AND YEAR(CreatedDate)= ? ";
        $result = $db->getRow($sql, array($month, $year));
        return $result;
    }

    public static function getSupportTabClickCountByMonth($month, $year)
    {
        $db = Loader::db();
        $sql = "SELECT Info as tabName, ";
        $sql .= "YEAR(CreatedDate) as y,  ";
        $sql .= "MONTH(CreatedDate) as m,  ";
        $sql .= "COUNT(DISTINCT SessionID) as c ";
        $sql .= "FROM CupGoLogUser ";
        $sql .= "WHERE PageName='Support' ";
        $sql .= "AND Action='View Tab' ";
        $sql .= "AND MONTH(CreatedDate) = ? ";
        $sql .= "AND YEAR(CreatedDate) = ? ";
        $sql .= "GROUP BY Info ";
        $sql .= "ORDER BY Info ASC ";
        $result = $db->GetAll($sql, array($month, $year));
        return $result;
    }
}