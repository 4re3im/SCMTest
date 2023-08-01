<?php

defined('C5_EXECUTE') || die(_("Access Denied."));

class GoDashboardGoUsers extends Object
{
    protected $id;

    public function on_start()
    {
        Loader::model('go_users/model', 'go_dashboard');
        Loader::model('go_users/list', 'go_dashboard');
    }

    public function __construct($id = null, $row = null)
    {
        $this->id = $id;
        if ($row == null) {
            $db = Loader::db();
            $row = $db->GetRow('SELECT * FROM Users WHERE uID=?', array($id));
        }

        $this->setPropertiesFromArray($row);
    }

    public function getUserID()
    {
        return $this->id;
    }

    public function getUserInfo($userID)
    {

        $db = Loader::db();

        $sql = <<<sql
            SELECT u.*, usi.*, sfc.*,ug.gID FROM Users u
                JOIN UserSearchIndexAttributes usi ON usi.uID = u.uID
                LEFT JOIN UserSalesforces usf ON usf.uID = u.uID
                LEFT JOIN SalesforceContacts sfc ON sfc.sID = usf.sID
                LEFT JOIN UserGroups ug ON ug.uID = u.uID
            WHERE u.uID=?
sql;

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
        $db->Execute('DELETE FROM Users WHERE uID=?', array($this->id));
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

        $q = 'INSERT INTO CupGoUserNotes (UserID, NoteText, CreationDate) VALUES (?, ?, ?)';
        $r = $db->prepare($q);
        return $db->Execute($r, array($userID, $notes, $date));
    }

    public function getUserNotes($userID)
    {
        $db = Loader::db();
        $query = 'SELECT * FROM CupGoUserNotes WHERE UserID = ?';

        return $db->GetAll($query, [$userID]);
    }

    /**
     * ANZGO-3722 Modified by Shane Camus 05/23/18
     * @param $userID
     * @return mixed
     */
    public function getUserSubscriptions($userID)
    {
        $db = Loader::db();
        $query = <<<sql
            SELECT us.*, st.TitleID, s.Name as SubType, s.CMS_Name as Subscription,
            sa.`Type`, us.CreatedBy as CreatedBy
            FROM CupGoUserSubscription us
            LEFT JOIN CupGoSubscription s ON us.S_ID = s.ID
            LEFT JOIN CupGoSubscriptionTabs st ON us.S_ID = st.S_ID
            JOIN CupGoSubscriptionAvailability AS sa ON us.SA_ID = sa.ID
            WHERE UserID=? GROUP BY us.ID ORDER BY us.CreationDate DESC
sql;
        return $db->GetAll($query, array($userID));
    }

    /**
     * ANZGO-3722 Added by Shane Camus 05/23/18
     * @param $sID
     * @return mixed
     */
    public function getSubscriptionDetails($sID)
    {
        $db = Loader::db();
        $query = "SELECT * FROM CupGoSubscription s JOIN CupGoSubscriptionTabs st ON s.ID = st.S_ID WHERE s.ID=?";
        return $db->GetRow($query, array($sID));
    }

    public function getUserTrackingGeneral($userID)
    {
        $db = Loader::db();
        $query = <<<sql
                SELECT CreatedDate,PageName, Action,
                    (SELECT CASE
                        WHEN(ProductName IS NOT NULL AND TabName IS NOT NULL) THEN CONCAT_WS(' > ',ProductName,TabName)
                        ELSE Info
                    END) as Info,
                    (SELECT CASE
                        WHEN(Action in ('View Tab','') ) THEN
                            (SELECT CASE WHEN(AccessLevel >= 1) THEN 'Full' ELSE 'Guest' END)
                        ELSE NULL
                    END) as Access_Level
                FROM CupGoLogUser
                WHERE UserID = ?

                UNION ALL

                SELECT CreatedDate,PageName,Action,Info,NULL as Access_Level
                FROM CupGoLogAccessCode
                WHERE UserID = ?

                UNION ALL

                SELECT d.CreateDate, 'Resources' as PageName,
                'Download' + (SELECT CASE WHEN(d.Complete='Y') THEN ' (OK)' ELSE ' (Incomplete)' END) as Action,
                cd.FileName as Info, NULL as Access_Level
                FROM CupGoDownloadTrack d, CupGoContentDetail cd, CupGoContent c
                WHERE d.UserID = ? AND d.FileID = cd.ID AND cd.ContentID = c.id
                ORDER BY 1 DESC
sql;

        return $db->GetAll($query, [$userID, $userID, $userID]);
    }

    public function getUserActivationErrors($userID)
    {
        $db = Loader::db();
        $sql = "SELECT CreatedDate, Info FROM CupGoLogAccessCode WHERE Action='fail' AND UserID=? ORDER BY 1 DESC";

        return $db->GetAll($sql, array($userID));
    }

    public function checkUserSubscription($userID, $saID)
    {
        $db = Loader::db();
        $sql = "SELECT * FROM CupGoUserSubscription WHERE UserID=? AND SA_ID=? AND Active='Y'";
        $row = $db->GetRow($sql, array($userID, $saID));

        return count($row) > 0;
    }

    public function addUserSubscription($userID, $saID, $sID)
    {
        $db = Loader::db();

        $q = 'SELECT *  FROM CupGoSubscriptionAvailability WHERE ID= ?';
        $row = $db->GetRow($q, array($saID));

        $query = <<<sql
        INSERT INTO CupGoUserSubscription
            (UserID, SA_ID, S_ID, StartDate, EndDate, Duration, Active, PurchaseType, CreatedBy)
            (
                SELECT $userID, ID, S_ID, StartDate, EndDate, Duration, 'Y', 'CMS', ?
                FROM CupGoSubscriptionAvailability WHERE ID = ?
            )
sql;

        $admin = new User();
        $r = $db->prepare($query);
        $db->Execute($r, array($admin->getUserID(), $saID));

        $userSubscriptionLastInsertId = $db->Insert_ID();

        // ANZGO-3748 modified by mtanada 2018/07/03 added reactivation type
        switch ($row['Type']) {
            case 'duration':
                $query = 'UPDATE CupGoUserSubscription SET EndDate = DATE_ADD(CreationDate, INTERVAL Duration DAY)';
                $query .= ' WHERE ID=? AND UserID=? LIMIT 1;';

                $r = $db->prepare($query);
                $db->Execute($r, array($userSubscriptionLastInsertId, $userID));

                $this->setDaysRemaining($row['Type'], $userSubscriptionLastInsertId);
                break;
            case 'end-of-year':
            case 'reactivation':
                $currentMonth = date("n");
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

                $query = 'UPDATE CupGoUserSubscription SET EndDate = ? WHERE ID=? AND UserID=? LIMIT 1;';
                $r = $db->prepare($query);
                $db->Execute($r, array($endDate, $userSubscriptionLastInsertId, $userID));

                $this->setDaysRemaining($row['Type'], $userSubscriptionLastInsertId);
                break;
            case 'start-end':
                $saEndDate = $row['EndDate'];

                $query = 'UPDATE CupGoUserSubscription SET EndDate = ? WHERE ID=? AND UserID=? LIMIT 1;';
                $r = $db->prepare($query);
                $db->Execute($r, array($saEndDate, $userSubscriptionLastInsertId, $userID));

                $this->setDaysRemaining($row['Type'], $userSubscriptionLastInsertId);
                break;
            default:
                break;
        }

        $query = 'INSERT INTO CupGoTabAccess(UserID,TabID,S_ID,SA_ID,Active,US_ID)
            (SELECT ?, TabID, S_ID, ?, "Y", ? FROM CupGoSubscriptionTabs WHERE S_ID = ? )';
        $db->Execute($query, array($userID, $saID, $userSubscriptionLastInsertId, $sID));

        // ANZGO-3572 added by jbernardez 20171214
        // created function call to all in one the return of the endDate
        return $this->computeEndDate(
            $row['EndDate'],
            $row['Type'],
            $row['Duration'],
            $row['EndOfYearBreakPoint'],
            $row['EndOfYearOffset']
        );
    }

    /**
     * Modified by: Jeszy Tanada and John Renzo Sunico 10/06/2017
     * Insert DaysRemaining from UserSubscription
     * to populate dashboard admin subscription tab days remaining
     * ANZGO-3748 modified by mtanada 2018/07/03 added reactivation type
     */
    public function setDaysRemaining($type, $saID)
    {
        $db = Loader::db();
        $sql = "";
        switch ($type) {
            case 'end-of-year':
            case 'reactivation':
                $sql = 'UPDATE CupGoUserSubscription SET `DaysRemaining` = DATEDIFF(EndDate, Now()) WHERE ID = ?';
                break;
            case 'start-end':
                $sql = 'UPDATE CupGoUserSubscription SET `DaysRemaining` = DATEDIFF(EndDate, StartDate) WHERE ID = ?';
                break;
            case 'duration':
                $sql = 'UPDATE CupGoUserSubscription SET `DaysRemaining` = Duration WHERE ID = ?';
                break;
            default:
                break;
        }
        $db->Execute($sql, array($saID));

    }

    public function toggleSubscriptionActiveStatus($usid, $userID)
    {
        $db = Loader::db();
        $q = 'SELECT Active FROM CupGoUserSubscription WHERE UserID = ? AND ID = ?';

        $r = $db->prepare($q);
        $db->GetRow($r, array($userID, $usid));
    }

    public function saveUserGeneralInfo($data)
    {
        $db = Loader::db();

        foreach ($data as $table => $dbData) {
            if ($table != 'SalesforceContacts') {
                $db->AutoExecute($table, $dbData, 'UPDATE', "uID='{$this->id}'");
            } else {
                $query = <<<'sql'
                    UPDATE SalesforceContacts SET accountID=?
                    WHERE sID IN (SELECT sID FROM UserSalesforces WHERE uID=?);
sql;

                $r = $db->prepare($query);
                $db->Execute($r, array($dbData['accountID'], $this->id));
            }
        }
    }

    public function archiveUser()
    {

        $db = Loader::db();

        $query = "UPDATE Users SET uIsActive=0 WHERE uID=?;";

        $r = $db->prepare($query);
        $db->Execute($r, array($this->id));
    }

    /* ANZGO-3572 added by jbernardez 20171214
     * added new method to compute for endDate used for return
     * ANZGO-3748 modified by mtanada 2018/07/03 added reactivation type
     */
    private function computeEndDate($endDate, $type, $duration = false, $breakpoint = 0, $breakpointOffset = 0)
    {
        switch ($type) {
            case 'start-end':
                $endDate = date('Y-m-d', strtotime($endDate));

                if (date('Y-m-d') < $endDate) {
                    $setEndDate = $endDate . " 12:00:00 AM";
                } else {
                    $setEndDate = $endDate;
                }
                break;
            case 'end-of-year':
            case 'reactivation':
                $currMonth = date('n');
                $currYear = date('Y');
                $breakpointOffset -= 1;
                $breakpointOffset = ($breakpointOffset < 0) ? 0 : $breakpointOffset;

                if ($currMonth <= $breakpoint) {
                    $setEndDate = ($currYear + $breakpointOffset) . "-12-31 12:00";
                } else {
                    $setEndDate = ($currYear + 1 + $breakpointOffset) . "-12-31 12:00";
                }
                break;
            case 'duration':
                $dateToConvert = date('Y-m-d');
                $setEndDate = date('Y-m-d H:i:s', strtotime($dateToConvert . "+" . $duration . " days"));
                break;
            default:
                break;
        }

        $newEndDate = date('Y-m-d', strtotime($setEndDate));

        return array(
            'endDate' => $newEndDate
        );;
    }

    public function getGigyaIDFromUID($id)
    {
        $db = Loader::db();
        $query = "SELECT gUID FROM Users WHERE uID = ?";
        return $db->GetRow($query, array($id));
    }
}
