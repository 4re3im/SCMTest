<?php

// ANZGO-3430 Added by Shane Camus 6/30/17

defined('C5_EXECUTE') || die(_('Access Denied.'));

class AnalyticsUsers
{
     // ANZGO-3472 Added by John Renzo S. Sunico, October 17, 2017
     // Added constants
    const GROUP_STUDENTS = 'Student';
    const GROUP_TEACHERS = 'Teacher';

    public static function getTotalStudentUsersCount()
    {
        $db = Loader::db();
        $sql = "SELECT count(*) as c FROM UserGroups ";
        $sql .= "WHERE gID = 4;";
        $result = $db->getRow($sql);

        return $result['c'];
    }

    public static function getTotalTeacherUsersCount()
    {
        $db = Loader::db();
        $sql = "SELECT count(*) as c FROM UserGroups ";
        $sql .= "WHERE gID = 5;";
        $result = $db->getRow($sql);

        return $result['c'];
    }

    public static function getTotalUsersCount()
    {
        $db = Loader::db();
        $sql = "SELECT count(*) as c FROM Users;";
        $result = $db->getRow($sql);

        return $result['c'];
    }

    public static function getTotalUsersCountByMonth()
    {
        // SB-265 modified by machua 20190724
        $db = Loader::db();
        $sql = "SELECT YEAR(uc.uDateAdded) as year, MONTH(uc.uDateAdded) as month, count(*) as c FROM ";
        $sql .= "(SELECT u.uDateAdded, u.uID FROM Users as u ";
        $sql .= "INNER JOIN UserGroups as ug ON u.uID = ug.uID ";
        $sql .= "WHERE u.uDateAdded > '2015-12-31 23:59:59') as uc ";
        $sql .= "GROUP BY month(uc.uDateAdded), year(uc.uDateAdded) ";
        $sql .= "ORDER BY year(uc.uDateAdded), month(uc.uDateAdded) ASC; ";

        return $db->GetAll($sql);
    }

    public static function getStudentCountByMonth()
    {
        // SB-265 modified by machua 20190724
        $db = Loader::db();
        $sql = "SELECT YEAR(uc.uDateAdded) as year, MONTH(uc.uDateAdded) as month, count(*) as c FROM ";
        $sql .= "(SELECT u.uDateAdded, u.uID FROM Users as u ";
        $sql .= "INNER JOIN UserGroups as ug ON u.uID = ug.uID ";
        $sql .= "WHERE ug.gID=4 AND u.uDateAdded > '2015-12-31 23:59:59') as uc ";
        $sql .= "GROUP BY month(uc.uDateAdded), year(uc.uDateAdded) ";
        $sql .= "ORDER BY year(uc.uDateAdded), month(uc.uDateAdded) ASC; ";

        return $db->GetAll($sql);
    }

    public static function getTeacherCountByMonth()
    {
        // SB-265 modified by machua 20190724
        $db = Loader::db();
        $sql = "SELECT YEAR(uc.uDateAdded) as year, MONTH(uc.uDateAdded) as month, count(*) as c FROM ";
        $sql .= "(SELECT u.uDateAdded, u.uID FROM Users as u ";
        $sql .= "INNER JOIN UserGroups as ug ON u.uID = ug.uID ";
        $sql .= "WHERE ug.gID=5 AND u.uDateAdded > '2015-12-31 23:59:59') as uc ";
        $sql .= "GROUP BY month(uc.uDateAdded), year(uc.uDateAdded) ";
        $sql .= "ORDER BY year(uc.uDateAdded), month(uc.uDateAdded) ASC; ";

        return $db->GetAll($sql);
    }

}
