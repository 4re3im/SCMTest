<?php

/**
 * ANZGO-3488 Added by jbernardez 20170905
 */

defined('C5_EXECUTE') || die(_('Access Denied.'));

class AnalyticsSessionInfo
{
    public static function getUniqueActiveTeacher($form, $subscriptions)
    {
        $year = $form['year'];
        $month = $form['month'];
        $results = array();

        foreach ($subscriptions as $subscription) {
            $id = $subscription['id'];
            $name = $subscription['name'];

            $sessioninfo = self::getUniqueActivePerTitle($year, $month, 5, $name, $id);
            $results[$id] = $sessioninfo['uniqueActive'];
        }

        $result = array (
            "totalUniqueActiveTeacher" => $results,
        );

        return $result;
    }

    public static function getUniqueActiveStudent($form, $subscriptions)
    {
        $year = $form['year'];
        $month = $form['month'];
        $results = array();

        foreach ($subscriptions as $subscription) {
            $id = $subscription['id'];
            $name = $subscription['name'];

            $sessioninfo = self::getUniqueActivePerTitle($year, $month, 4, $name, $id);
            $results[$id] = $sessioninfo['uniqueActive'];
        }

        $result = array (
            "totalUniqueActiveStudent" => $results,
        );

        return $result;
    }

    private function getUniqueActivePerTitle($year, $month, $gID, $info, $pid = 0)
    {
        $db = Loader::db();
        $sql = "SELECT count(*) AS uniqueActive ";
        $sql .= "FROM ( ";
        $sql .= "SELECT UserID ";
        $sql .= "FROM CupGoLogUser CGLU ";
        $sql .= "INNER JOIN UserGroups UG ON UG.uID = CGLU.UserID ";
        $sql .= "WHERE CreatedDate LIKE ? ";
        $sql .= "AND Action = 'View Title' ";
        $sql .= "AND UG.gID = ? /* set this to 5, for teacher */ ";
        if ($pid > 0) {
            $sql .= "AND CGLU.PID = ? ";
            $pidinfo = $pid;
        } else {
            $sql .= "AND CGLU.info LIKE ? ";
            $pidinfo = "%".$info."%";
        }
        $sql .= "GROUP BY UserID ";
        $sql .= ") AS CGLUR ";

        $result = $db->getRow($sql, array('%'.$year.'-'.str_pad($month, 2, "0", STR_PAD_LEFT).'%', $gID, $pidinfo));

        return $result;
        // '%'.$year.'-'.str_pad($month, 2, "0", STR_PAD_LEFT).'%'
    }

    public static function getRepeatActiveTeacher($form, $subscriptions)
    {
        $year = $form['year'];
        $month = $form['month'];
        $results = array();

        foreach ($subscriptions as $subscription) {
            $id = $subscription['id'];
            $name = $subscription['name'];

            $sessioninfo = self::getRepeatActivePerTitle($year, $month, 5, $name, $id);
            $results[$id] = $sessioninfo['repeatActive'];
        }

        $result = array (
            "totalRepeatActiveTeacher" => $results,
        );

        return $result;
    }

    public static function getRepeatActiveStudent($form, $subscriptions)
    {
        $year = $form['year'];
        $month = $form['month'];
        $results = array();

        foreach ($subscriptions as $subscription) {
            $id = $subscription['id'];
            $name = $subscription['name'];

            $sessioninfo = self::getRepeatActivePerTitle($year, $month, 4, $name, $id);
            $results[$id] = $sessioninfo['repeatActive'];
        }

        $result = array (
            "totalRepeatActiveStudent" => $results,
        );

        return $result;
    }

    private static function getRepeatActivePerTitle($year, $month, $gID, $info, $pid = 0)
    {
        $db = Loader::db();
        $sql = "SELECT count(*) as repeatActive ";
        $sql .= "FROM ( ";
        $sql .= "SELECT count(UserID) as usercount, UserID, Info ";
        $sql .= "FROM CupGoLogUser CGLU ";
        $sql .= "INNER JOIN UserGroups UG ON UG.uID = CGLU.UserID ";
        $sql .= "WHERE CreatedDate LIKE ? ";
        $sql .= "AND CGLU.Action = 'View Title' ";
        $sql .= "AND UG.gID = ? /*set this to 5, for teacher */ ";
        if ($pid > 0) {
            $sql .= "AND CGLU.PID = ? ";
            $pidinfo = $pid;
        } else {
            $sql .= "AND CGLU.info LIKE ? ";
            $pidinfo = "%".$info."%";
        }
        $sql .= "GROUP BY UserID, Info ";
        $sql .= "having usercount > 1 ";
        $sql .= ") AS CGLUR";

        $result = $db->getRow($sql, array('%'.$year.'-'.str_pad($month, 2, "0", STR_PAD_LEFT).'%', $gID, $pidinfo));

        return $result;
    }

    public static function getIndividualSessionsTeacher($form, $subscriptions)
    {
        $year = $form['year'];
        $month = $form['month'];
        $results = array();

        foreach ($subscriptions as $subscription) {
            $id = $subscription['id'];
            $name = $subscription['name'];

            $sessioninfo = self::getIndividualSessionsTeacherPerTitle($year, $month, 5, $name, $id);
            $results[$id] = $sessioninfo['individualSessions'];
        }

        $result = array (
            "totalIndividualSessionsTeacher" => $results,
        );

        return $result;
    }

    public static function getIndividualSessionsStudent($form, $subscriptions)
    {
        $year = $form['year'];
        $month = $form['month'];
        $results = array();

        foreach ($subscriptions as $subscription) {
            $id = $subscription['id'];
            $name = $subscription['name'];

            $sessioninfo = self::getIndividualSessionsTeacherPerTitle($year, $month, 4, $name, $id);
            $results[$id] = $sessioninfo['individualSessions'];
        }

        $result = array (
            "totalIndividualSessionsStudent" => $results,
        );

        return $result;
    }

    private function getIndividualSessionsTeacherPerTitle($year, $month, $gID, $info, $pid = 0)
    {
        $db = Loader::db();
        $sql = "SELECT SUM(usercount) AS individualSessions ";
        $sql .= "FROM ( ";
        $sql .= "SELECT count(UserID) as usercount, UserID, Info ";
        $sql .= "FROM CupGoLogUser CGLU ";
        $sql .= "INNER JOIN UserGroups UG ON UG.uID = CGLU.UserID ";
        $sql .= "WHERE CreatedDate LIKE ? ";
        $sql .= "AND CGLU.Action = 'View Title' ";
        $sql .= "AND UG.gID = ? /*set this to 5, for teacher */ ";
        if ($pid > 0) {
            $sql .= "AND CGLU.PID = ? ";
            $pidinfo = $pid;
        } else {
            $sql .= "AND CGLU.info LIKE ? ";
            $pidinfo = "%".$info."%";
        }
        $sql .= ") AS CGLUR";

        $result = $db->getRow($sql, array('%'.$year.'-'.str_pad($month, 2, "0", STR_PAD_LEFT).'%', $gID, $pidinfo));
        return $result;
    }

    /**
     * ANZGO-3472 Added by John Renzo S. Sunico, October 17, 2017
     * Returns count of unique users on specified month and group
     * @param $month
     * @param $year
     * @param $groupName
     * @return array|bool
     */
    public static function getUniqueSessionCountPerMonthAndGroupName($month, $year, $groupName)
    {
        $db = Loader::db();

        $month = intval($month);
        $year = intval($year);

        $from = date('Y-m-d', strtotime("$year-$month-01"));
        $to = date('Y-m-t', strtotime("$year-$month-01"));

        $userGroup = Group::getByName($groupName);
        if (!$userGroup) {
            throw new InvalidArgumentException("Group $groupName not found.");
        }

        $sql = <<<SQL
            SELECT COUNT(*) as count FROM (
                SELECT DISTINCT log.UserID as Count FROM CupGoLogUser log
                INNER JOIN UserGroups ugroup ON log.UserID = ugroup.uID
                WHERE log.CreatedDate BETWEEN ? AND ?
                AND ugroup.gID = ?
                GROUP BY UserID
            ) as DistinctUser;
SQL;
        return $db->GetRow($sql, array($from, $to, $userGroup->getGroupID()));
    }



}
