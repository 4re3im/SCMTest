<?php

/**
 * All logs
 *
 * @author atabag <atabag@cambridge.org>
 */
class CupGoLogs 
{
    public static function trackUser($pageName, $action, $info = "", $userID = 0, $pid = 0) 
    {
        global $u;
        $db = Loader::db();

        // ANZGO-3451 Modified by Shane Camus 7/27/2017
        // To make sure anonymous user logs are recorded as user 0
        if ($u->getUserID()) {
            $userID = $u->getUserID();
        } else {
            $userID = 0;
        }

        $version = '5.6.3.1';
        $insertProductName = '';

        // ANZGO-3488 modified by James Bernardez 20170824
        // $pid = '';
        $userAgent      = $_SERVER['HTTP_USER_AGENT'];
        $ipAddress      = $_SERVER['REMOTE_ADDR'];
        $queryString    = $_SERVER['QUERY_STRING'];
        $scriptName     = $_SERVER['SCRIPT_NAME'];
        $sessionID      = session_id();

        // If a user is logged on, write the entry the Log_User table.
        $logTable = 'CupGoLogUser';

        // If the action is a code activation, write the entry the Log_AccessCode table.
        if ($pageName === 'Activate Code') {
            $logTable = 'CupGoLogAccessCode';
        // If the action is within an Interactive Book, write the entry the Log_InteractiveBook table.
        } elseif ($pageName === 'Interactive Textbook') {
            $logTable = 'CupGoLogInteractiveBook';
        }

        // Set up the insert query to write to Log_User or Log_Anonymous, if PID is required to be written to the able.
        if ($logTable === 'CupGoLogUser') {
            $query = 'INSERT INTO ' . $logTable . '(UserID, IPAddress, QueryString, Action, PageName, 
                        UserAgent, Version, ScriptName, Info, SessionID, PID, ProductName) ';
            $query .= 'VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $params = array(
                $userID,
                $ipAddress,
                $queryString,
                $action,
                $pageName,
                $userAgent,
                $version,
                $scriptName,
                $info,
                $sessionID,
                $pid,
                $insertProductName);
        // Otherwise write to one of the other tables.
        } else {
            $query = 'INSERT INTO ' . $logTable . '(UserID, IPAddress, QueryString, Action, PageName, 
                        UserAgent, Version, ScriptName, Info, SessionID) ';
            $query .= 'VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $params = array(
                $userID,
                $ipAddress,
                $queryString,
                $action,
                $pageName,
                $userAgent,
                $version,
                $scriptName,
                $info,
                $sessionID);
        }

        return $db->Execute($query, $params);
    }

    /**
     * SB-251 added by jbernardez 20190711
     * sends back the count of how many times My Resources of a user was logged
     * @param $userid
     * @return int/false
     */
    public function myResoucesLogCount($userID = 0)
    {
        $db = Loader::db();
        $query = 'SELECT COUNT(UserID) AS MyResoucesCount FROM CupGoLogUser 
                    INNER JOIN Users ON CupGoLogUser.UserID = Users.uID 
                    WHERE CupGoLogUser.UserID = ? 
                    AND CupGoLogUser.Action = "View my resources" 
                    AND Users.uDateAdded > "2019-02-01 00:00:00" 
                    AND CURDATE() < "2019-09-01 00:00:00" ';
        $params = array($userID);

        $result = $db->GetRow($query, $params);

        if (count($result) > 0) {
            return $result['MyResoucesCount'];
        } else {
            return false;
        }
    }
}