<?php  defined('C5_EXECUTE') or die(_('Access Denied.'));

// ANZGO-3433 Added by Shane Camus 7/10/17

class AnalyticsReactivation
{
    public static function getTotalReactivationCount()
    {
      $db = Loader::db();
      $sql = "SELECT count(*) as c FROM CupGoAccessCodes ";
      $sql .= "WHERE UsageCount > 1;";
      $result = $db->getRow($sql);
      return $result['c'];
    }

    public static function getAllTitles()
    {
      $db = Loader::db();
      $sql = "SELECT ID, ISBN_13, CMS_Name, Name ";
      $sql .= "FROM CupGoSubscription WHERE CMS_NAME NOT LIKE '%test%' ";
      $sql .= "AND CMS_NAME<>'' ORDER BY ID ASC;";
      $result = $db->GetAll($sql);
      return $result;
    }

    public static function getReactivationCountByMonth($month, $year)
    {
      $db = Loader::db();
      $sql = "SELECT s.ID as id, YEAR(us.CreationDate) as y, MONTH(us.CreationDate) as m, COUNT(*) as count ";
      $sql .= "FROM CupGoAccessCodes ac ";
      $sql .= "LEFT JOIN CupGoUserSubscription us ON us.AccessCode = ac.accessCode ";
      $sql .= "JOIN CupGoSubscriptionAvailability sa ON sa.ID = ac.SA_ID ";
      $sql .= "JOIN CupGoSubscription s ON s.ID = sa.S_ID ";
      $sql .= "WHERE ac.UsageCount > 1 ";
      $sql .= "AND MONTH(us.CreationDate) = ? AND YEAR(us.CreationDate) = ? ";
      $sql .= "GROUP BY MONTH(us.CreationDate), id ";
      $sql .= "ORDER BY id;";
      $result = $db->GetAll($sql, array($month, $year));
      return $result;
    }

}
