<?php

/**
 * Provisioned Users Model
 * ANZGO-3595 Added by Shane Camus 01/25/2018
 */

defined('C5_EXECUTE') || die(_("Access Denied."));

class GoDashboardProvisionedUsers extends Object
{
    protected $id;

    public function __construct($id = null, $row = null)
    {
        $this->id = $id;
        if ($row == null) {
            $db = Loader::db();
            $row = $db->GetRow('SELECT * FROM Users WHERE uID=?', array($id));
        }
        $this->setPropertiesFromArray($row);
    }

    public function on_start()
    {
        Loader::model('provisioned_users/model', 'go_dashboard');
        Loader::model('provisioned_users/list', 'go_dashboard');
    }

    public function getUserID()
    {
        return $this->id;
    }

    public function getUserInfo($userID)
    {
        $db = Loader::db();

        $sql = 'SELECT u.*, usi.*, sfc.*, ug.gID FROM Users u ';
        $sql .= 'JOIN UserSearchIndexAttributes usi ON usi.uID = u.uID ';
        $sql .= 'LEFT JOIN UserSalesforces usf ON usf.uID = u.uID ';
        $sql .= 'LEFT JOIN SalesforceContacts sfc ON sfc.sID = usf.sID ';
        $sql .= 'LEFT JOIN UserGroups ug ON ug.uID = u.uID ';
        $sql .= 'WHERE u.uID=?';

        return $db->GetRow($sql, array($userID));
    }

    public function update($data)
    {
        $db = Loader::db();
        $db->AutoExecute('Users', $data, 'UPDATE', "id='{$this->id}'");
    }

    public static function add($data)
    {
        $db = Loader::db();
        $db->AutoExecute('Users', $data, 'INSERT');

        return new GoDashboardGoUsers($db->Insert_ID(), $data);
    }

    public function remove()
    {
        $db = Loader::db();
        $db->Execute('DELETE FROM Users WHERE id=?', array($this->id));
    }

    public function getLastInsertId()
    {
        $db = Loader::db();

        return $db->Insert_ID();
    }

    public function addUserNotes($userID, $notes)
    {
        $db = Loader::db();
        $dh = Loader::helper('date');
        $date = $dh->getSystemDateTime();

        $query = 'INSERT INTO CupGoUserNotes (UserID, NoteText, CreationDate) VALUES (?, ?, ?)';
        $request = $db->prepare($query);

        return $db->Execute($request, array($userID, $notes, $date));
    }

    public function getUserNotes($userID)
    {
        $db = Loader::db();
        $query = 'SELECT * FROM CupGoUserNotes WHERE UserID=?';

        return $db->GetAll($query, array($userID));
    }

    // ANZGO-3634 Modified by John Renzo Sunico, 02/12/2018
    public function getUserSubscriptions($userID)
    {
        $creator = new User();

        $db = Loader::db();
        $query = 'SELECT us.*, s.Name as SubType, s.CMS_Name as Subscription, cgsa.`Type` ';
        $query .= 'FROM CupGoUserSubscription us LEFT JOIN CupGoSubscription s ON us.S_ID = s.ID ';
        $query .= 'JOIN CupGoSubscriptionAvailability AS cgsa ON us.SA_ID = cgsa.ID ';
        $query .= 'WHERE UserID=? AND CreatedBy = ? ORDER BY us.CreationDate DESC';

        return $db->GetAll($query, array($userID, $creator->getUserID()));
    }

    public function getUserTrackingGeneral($userID)
    {
        $db = Loader::db();
        $query = 'SELECT CreatedDate,PageName, Action, ';
        $query .= '(SELECT CASE WHEN(ProductName IS NOT NULL AND TabName IS NOT NULL) ';
        $query .= 'THEN CONCAT_WS(" > ",ProductName,TabName) ELSE Info END) as Info, ';
        $query .= '(SELECT CASE WHEN(Action in ("View Tab","")) ';
        $query .= 'THEN ';
        $query .= '(SELECT CASE WHEN(AccessLevel >= 1) THEN "Full" ELSE "Guest" END) ';
        $query .= 'ELSE ';
        $query .= 'NULL ';
        $query .= 'END) as Access_Level ';
        $query .= 'FROM CupGoLogUser ';
        $query .= 'WHERE UserID = ? ';
        $query .= 'UNION ALL ';
        $query .= 'SELECT CreatedDate,PageName,Action,Info,NULL as Access_Level ';
        $query .= 'FROM CupGoLogAccessCode ';
        $query .= 'WHERE UserID = ? ';
        $query .= 'UNION ALL ';
        $query .= 'SELECT d.CreateDate,"Resources" as PageName ';
        $query .= ',"Download" + (SELECT CASE WHEN(d.Complete="Y") THEN " (OK)" ELSE " (Incomplete)" END) as Action ';
        $query .= ',cd.FileName as Info, NULL as Access_Level ';
        $query .= 'FROM CupGoDownloadTrack d, CupGoContentDetail cd, CupGoContent c ';
        $query .= 'WHERE d.UserID = ? ';
        $query .= 'AND d.FileID = cd.ID ';
        $query .= 'AND cd.ContentID = c.id ';
        $query .= 'ORDER BY 1 DESC';

        return $db->GetAll($query, array($userID, $userID, $userID));
    }

    public function getUserActivationErrors($userID)
    {
        $db = Loader::db();
        $query = 'SELECT CreatedDate, Info FROM CupGoLogAccessCode WHERE Action="fail" AND UserID = ? ORDER BY 1 DESC';

        return $db->GetAll($query, array($userID));
    }

    public function checkUserSubscription($userID, $saID)
    {
        $db = Loader::db();
        $row = $db->GetRow(
            'SELECT * FROM CupGoUserSubscription WHERE UserID=? AND SA_ID=? AND Active="Y"',
            array($userID, $saID)
        );

        return (count($row) > 0);
    }

    public function addUserSubscription($userID, $saID, $sID)
    {
        $db = Loader::db();

        $query1 = 'SELECT *  FROM CupGoSubscriptionAvailability WHERE ID= ?';
        $row = $db->GetRow($query1, array($saID));

        $query2 = 'INSERT INTO CupGoUserSubscription (UserID, ';
        $query2 .= 'SA_ID, ';
        $query2 .= 'S_ID, ';
        $query2 .= 'StartDate, ';
        $query2 .= 'EndDate, ';
        $query2 .= 'Duration, ';
        $query2 .= 'Active, ';
        $query2 .= 'PurchaseType, ';
        $query2 .= 'CreatedBy) ';
        $query2 .= '(SELECT ' . $userID . ', ID, S_ID, StartDate, EndDate, Duration, "Y", "CMS", ? ';
        $query2 .= 'FROM CupGoSubscriptionAvailability ';
        $query2 .= 'WHERE ID= ? )';

        $admin = new User();
        $request1 = $db->prepare($query2);
        $db->Execute($request1, array($admin->getUserID(), $saID));

        $userSubscriptionLastInsertID = $db->Insert_ID();

        switch ($row['Type']) {
            case 'duration':
                $query3 = 'UPDATE CupGoUserSubscription SET EndDate = DATE_ADD(CreationDate, INTERVAL Duration DAY) ';
                $query3 .= 'WHERE ID=? AND UserID=? LIMIT 1';
                $request2 = $db->prepare($query3);
                $db->Execute($request2, array($userSubscriptionLastInsertID, $userID));
                $this->setDaysRemaining($row['Type'], $userSubscriptionLastInsertID);
                break;
            case 'end-of-year':
                $currentMonth = date('n');
                $currentYear = date('Y');
                $breakpoint = $row['EndOfYearBreakPoint'];
                $breakpointOffset = $row['EndOfYearOffset'];
                $breakpointOffset -= 1;

                if ($breakpointOffset < 0) {
                    $breakpointOffset = 0;
                }

                if ($currentMonth <= $breakpoint) {
                    $endDate = ($currentYear + $breakpointOffset) . '-12-31 12:00:00';
                } else {
                    if ($currentMonth > $breakpoint) {
                        $endDate = ($currentYear + 1 + $breakpointOffset) . '-12-31 12:00:00';
                    }
                }

                $query3 = 'UPDATE CupGoUserSubscription SET EndDate = ? WHERE ID=? AND UserID=? LIMIT 1';
                $request2 = $db->prepare($query3);
                $db->Execute($request2, array($endDate, $userSubscriptionLastInsertID, $userID));
                $this->setDaysRemaining($row['Type'], $userSubscriptionLastInsertID);
                break;
            case 'start-end':
                $endDate = $row['EndDate'];
                $query3 = 'UPDATE CupGoUserSubscription SET EndDate = "' .
                    $endDate . '" WHERE ID=? AND UserID=? LIMIT 1';
                $request2 = $db->prepare($query3);
                $db->Execute($request2, array($userSubscriptionLastInsertID, $userID));
                $this->setDaysRemaining($row['Type'], $userSubscriptionLastInsertID);
                break;
            default:
                break;
        }

        $query4 = 'INSERT INTO CupGoTabAccess(UserID, TabID, S_ID, SA_ID, Active, US_ID) ';
        $query4 .= '(SELECT ' . $userID . ', TabID, S_ID, ' . $saID . ', "Y", ' . $userSubscriptionLastInsertID . ' ';
        $query4 .= "FROM CupGoSubscriptionTabs ";
        $query4 .= 'WHERE S_ID = ? )';
        $db->Execute($query4, array($sID));

        return $this->computeEndDate(
            $row['EndDate'],
            $row['Type'],
            $row['Duration'],
            $breakpoint = $row['EndOfYearBreakPoint'],
            $breakpointOffset = $row['EndOfYearOffset']
        );
    }

    public function setDaysRemaining($type, $saID)
    {
        $db = Loader::db();
        switch ($type) {
            case 'end-of-year':
                $sql = 'UPDATE CupGoUserSubscription SET `DaysRemaining` = DATEDIFF(EndDate, Now()) WHERE ID = ?';
                break;
            case 'start-end':
                $sql = 'UPDATE CupGoUserSubscription SET `DaysRemaining` = DATEDIFF(EndDate, StartDate) WHERE ID = ?';
                break;
            case 'duration':
                $sql = 'UPDATE CupGoUserSubscription SET `DaysRemaining` = Duration WHERE ID = ?';
                break;
            default:
                $sql = "";
                break;
        }
        $db->Execute($sql, array($saID));
    }

    public function toggleSubscriptionActiveStatus($usID, $userID)
    {
        $db = Loader::db();
        $query = 'SELECT Active FROM CupGoUserSubscription WHERE UserID = ? AND ID = ?';
        $request = $db->prepare($query);

        return $db->GetRow($request, array($userID, $usID));
    }

    public function saveUserGeneralInfo($data)
    {
        $db = Loader::db();
        foreach ($data as $table => $dbData) {
            if ($table != 'SalesforceContacts') {
                $db->AutoExecute($table, $dbData, 'UPDATE', "uID='{$this->id}'");
            } else {
                $query = 'UPDATE SalesforceContacts SET accountID = ? ';
                $query .= 'WHERE sID IN (SELECT sID FROM UserSalesforces WHERE uID=?)';
                $r = $db->prepare($query);
                $db->Execute($r, array($dbData['accountID'], $this->id));
            }
        }
    }

    public function archiveUser()
    {
        $db = Loader::db();
        $query = 'UPDATE Users SET uIsActive=0 WHERE uID=?;';
        $request = $db->prepare($query);
        $db->Execute($request, array($this->id));
    }


    private function computeEndDate($endDate, $type, $duration = false, $breakpoint = 0, $breakpointOffset = 0)
    {
        switch ($type) {
            case 'start-end':
                $endDate = date('Y-m-d', strtotime($endDate));
                if (date('Y-m-d') < $endDate) {
                    $setEndDate = $endDate . ' 12:00:00 AM';
                } else {
                    $setEndDate = $endDate;
                }
                break;
            case 'end-of-year':
                $currMonth = date('n');
                $currYear = date('Y');
                $breakpointOffset -= 1;
                $breakpointOffset = ($breakpointOffset < 0) ? 0 : $breakpointOffset;
                if ($currMonth <= $breakpoint) {
                    $setEndDate = ($currYear + $breakpointOffset) . '-12-31 12:00';
                } else {
                    $setEndDate = ($currYear + 1 + $breakpointOffset) . '-12-31 12:00';
                }
                break;
            case 'duration':
                $dateToConvert = date('Y-m-d');
                $setEndDate = date('Y-m-d H:i:s', strtotime($dateToConvert . '+' . $duration . ' days'));
                break;
            default:
                $setEndDate = '';
                break;
        }

        $newEndDate = date('Y-m-d', strtotime($setEndDate));

        return array('endDate' => $newEndDate);
    }
}
