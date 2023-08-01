<?php defined('C5_EXECUTE') or die(_('Access Denied.'));

/**
 * ANZGO- Added by: jbernardez 20171120
 * Analytics Provisioning
 */
class AnalyticsProvisioning
{
    /**
     * @param string $type
     * @param $month
     * @param $year
     * @return array|bool
     */
    public static function getProvisionCountPerMonthYear($month, $year, $type = 'teacher')
    {
        $db = Loader::db();
        $sql = "SELECT YEAR(DateUploaded) AS year, ";
        $sql .= "MONTH(DateUploaded) AS month, ";
        $sql .= "COUNT(ID) AS count ";
        $sql .= "FROM ProvisioningUsers ";
        //ANZGO-3806 modified by jdchavez 08/08/2018
        if ($type === 'teacher') {
            $sql .= "WHERE Type = 'Teacher' ";
        } else {
            $sql .= "WHERE Type = 'Student' ";
        }
        $sql .= "AND MONTH(DateUploaded)= ? ";
        $sql .= "AND YEAR(DateUploaded)= ? ";
        $result = $db->getRow($sql, array($month, $year));
        return $result;
    }
}