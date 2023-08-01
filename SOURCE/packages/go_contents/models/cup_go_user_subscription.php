<?php
/*
 * ANZGO-3764 tagged by jbernardez 20181011
 * tagged for deletion, cup_go_user_subscription_list is now used
 */
defined('C5_EXECUTE') or die(_("Access Denied."));

class CupGoUserSubscription extends Object {

    protected $id = false;
    protected $user_id = false;
    protected $sa_id = false;
    protected $creation_date = false;
    protected $start_date = false;
    protected $end_date = false;
    protected $duration = false;
    protected $active = false;
    protected $access_Code = false;
    protected $s_id = false;
    protected $days_remaining = false;
    protected $activation_message = false;
    protected $exisiting_result = false;
    protected $db = false;

    function __construct($id = false)
    {
        global $u;
        $user_id = $u->getUserID();
        $this->user_id = $user_id;
        $this->db = Loader::db();

        if ($id) {

            $db = Loader::db();

            $sql = "SELECT * FROM CupGoUserSubscription WHERE id = ?";

            $result = $db->getRow($sql, array($id));

            if ($result) {

                $this->id = $result['ID'];
                $this->sa_id = $result['SA_ID'];
                $this->creation_date = $result['CreationDate'];
                $this->start_date = $result['StartDate'];
                $this->end_date = $result['EndDate'];
                $this->duration = $result['Duration'];
                $this->active = $result['Active'];
                $this->access_Code = $result['AccessCode'];
                $this->s_id = $result['S_ID'];
                $this->days_remaining = $result['DaysRemaining'];

                $this->exisiting_result = $result;
            }
        }
    }

    public static function deleteByID($id)
    {
        // ANZGO-3325 added by jbernardez 20180222
        // 1. query the id that is given
        // 2. get the SID of the result, use this as basis for the delete
        // 3. why? because you need to delete all instances of the user subscription SID
        $db = Loader::db();
        $sql = "SELECT UserID, S_ID FROM CupGoUserSubscription WHERE id = ?";
        $result = $db->GetRow($sql, array($id));

        // ANZGO-3325 modified by jbernardez 20180222
        $sql = "UPDATE CupGoUserSubscription SET Archive='Y', ArchiveDate=now() WHERE S_ID = ? AND UserID = ?";
        // ANZGO-3325 modified by jbernardez 20180226
        $updateResult = $db->Execute($sql, array($result['S_ID'], $result['UserID']));
        $updateFlag = $db->Affected_Rows('CupGoUserSubscription') > 0;

        $tabSql = "UPDATE CupGoTabAccess SET Active = 'N' WHERE US_ID = ?";
        $db->Execute($tabSql, array($id));
        $tabUpdateFlag = $db->Affected_Rows('CupGoTabAccess');

        // ANZGO-3325 added by jbernardez 20180226
        if (count($updateResult) > 0) {
            return $updateFlag;
        } else {
            return false;
        }
    }

    public static function fetchByID($id)
    {
        $object = new CupGoUserSubscription($id);
        if ($object->id === false) {
            return false;
        } else {
            return $object;
        }
    }

    public static function fetchSubscriptionTitleByISBN($isbn13 = '')
    {
        if ($isbn13) {
            $object = new CupGoUserSubscription();
            $db = Loader::db();
            $sql = "SELECT isbn13, displayName, prettyUrl FROM CupContentTitle WHERE isbn13=?";
            $result = $db->GetRow($sql, array($isbn13));
        }

        if ($result === false) {
            return false;
        } else {
            return $result;
        }
    }

    public static function fetchSubscriptionTitleByID($id)
    {
        $object = new CupGoUserSubscription();
        $db = Loader::db();
        $sql = "SELECT isbn13, displayName, prettyUrl FROM CupContentTitle WHERE id=?";
        $result = $db->GetRow($sql, array($id));
        if ($result === false) {
            return false;
        } else {
            return $result;
        }
    }

    public function fetchAllByUserID($arrange = null)
    {
        $object = new CupGoUserSubscription();
        $u = new User();
        $result = array();
        $db = Loader::db();

        $sql = "SELECT * FROM (SELECT * FROM(";
        $sql .= "SELECT ";
        $sql .= "cgus.ID AS UserSubscriptionID, cgus.CreationDate as USubCreationDate, cgus.EndDate AS USubEndDate, ";
        $sql .= "cgs.ISBN_13, cgus.AccessCode, cgus.Active, cgus.DaysRemaining, ";
        $sql .= "cgsa.CreationDate, cgsa.StartDate, cgsa.EndDate, cgsa.Duration,cgsa.Type, ";
        $sql .= "cgsa.ID AS SubscriptionAvailID, cgs.Name, cgs.Description,cgs.CMS_Name, cgs.ID AS SubscriptionID, ";
        $sql .= "'Go' as Source FROM CupGoSubscription cgs ";
        $sql .= "LEFT JOIN 	CupGoSubscriptionAvailability 	cgsa ON cgs.ID=cgsa.S_ID ";
        $sql .= "LEFT JOIN 	CupGoUserSubscription 			cgus ON cgsa.ID=cgus.SA_ID ";
        // ANZGO-3045
        $sql .= "WHERE UserID = ? AND (Archive IS NULL OR Archive!='Y') AND cgus.S_ID IS NOT NULL";

        $sql .= " UNION ALL ";

        $sql .= "SELECT ";
        $sql .= "cgus.ID AS UserSubscriptionID, cgus.CreationDate as USubCreationDate, null as USubEndDate, ";
        $sql .= "null as ISBN_13, null as AccessCode, null as Active, null as DaysRemaining, null as CreationDate ";
        $sql .= "null as StartDate, null as EndDate,null as Duration, null as Type, 0 as SubscriptionAvailID, ";
        $sql .= "null as Name, null as Description,null as CMS_Name,0 as SubscriptionID, 'HOTMATHS' as Source ";
        $sql .= "FROM CupGoExternalUser cgus ";
        $sql .= "LEFT JOIN CupGoBrandCodeTitles cgbct ON FIND_IN_SET(brandCode,cgus.brandCodes) ";
        $sql .= "WHERE uID = ?";
        // ANZGO-3045
        $sql .= ") AS a ORDER BY ISBN_13, USubCreationDate desc) AS b GROUP BY CMS_Name,UserSubscriptionID ";

        // ANZGO-3764 modified by jbernardez 20181011
        $results = $db->GetAll($sql, array($u->getUserID(), $u->getUserID()));

        $subTabs = $this->getSubscriptionTabs($results, $u->getUserID(), $arrange);

        $result = array();

        // ANZGO-3043
        foreach ($results as $rKey => $rVal) {
            foreach ($subTabs as $stKey => $stVal) {
                // ANZGO-3158
                if (in_array($stVal['SA_ID'], $rVal) || ($stVal['SA_ID'] == $rVal['SubscriptionAvailID'])) {
                    $result[$stKey][] = array_merge($stVal, $rVal);

                    // ANZGO-3158
                    // ANZGO-3043
                    // remove already merged for hotmaths only
                    if ($rVal['Source'] == 'HOTMATHS') {
                        unset($subTabs[$stKey]);
                    }
                }
            }
        }


        if ($arrange == "Oldest - newest") {
            uasort($result, array("CupGoUserSubscription", "sortSubscriptionByDateASC"));
        } elseif ($arrange == "A - Z" || $arrange == "Z - A") {
            ;
        } else {
            uasort($result, array("CupGoUserSubscription", "sortSubscriptionByDateDESC"));
        }

        return ($result) ? $result : FALSE;
    }

    public function getGoSubscriptionByAccessCode($access_code)
    {
        /*
         * ANZGO-3326 Added by John Renzo S. Sunico, 04/20/17
         */

        $u = new User();
        $db = Loader::db();
        $object = new CupGoUserSubscription();
        $arrange = 'Arrange';
        $result = array();

        // ANZGO-3383 Modified by John Renzo Sunico, May 12, 2017
        // Modified to include all tabs in new activate display

        $subscriptions = $this->getSubscriptionsWithTitleOfAccessCode($access_code);
        $sql = "SELECT * FROM (SELECT * FROM(";
        $sql .= "SELECT ";
        $sql .= "cgus.ID AS UserSubscriptionID, cgus.CreationDate as USubCreationDate, cgus.EndDate AS USubEndDate, ";
        $sql .= "cgs.ISBN_13, cgus.AccessCode, cgus.Active, cgus.DaysRemaining, ";
        $sql .= "cgsa.CreationDate, cgsa.StartDate, cgsa.EndDate, cgsa.Duration,cgsa.Type,cgsa.ID AS SubscriptionAvailID, ";
        $sql .= "cgs.Name, cgs.Description,cgs.CMS_Name, cgs.ID AS SubscriptionID, 'Go' as Source ";
        $sql .= "FROM 		CupGoSubscription 				cgs ";
        $sql .= "LEFT JOIN 	CupGoSubscriptionAvailability 	cgsa ON cgs.ID=cgsa.S_ID ";
        $sql .= "LEFT JOIN 	CupGoUserSubscription 			cgus ON cgsa.ID=cgus.SA_ID ";
        $sql .= "WHERE UserID = ? AND (Archive IS NULL OR Archive!='Y') AND cgus.S_ID IS NOT NULL ";

        if ($subscriptions) {
            $subscriptions = implode(',', array_column($subscriptions, "USubscriptionID"));
            $sql .= " AND cgus.ID IN ( ? ) ";
        }

        $sql .= ") AS a ORDER BY ISBN_13, USubCreationDate desc) AS b GROUP BY CMS_Name,UserSubscriptionID ";

        // ANZGO-3764 modified by jbernardez 20181011
        $results = $db->GetAll($sql, array($u->getUserID(), $subscriptions));
        $result = array();

        foreach ($results as $rKey => $rVal) {

            $hotmathsSwitch = false;
            if ($rVal['Source'] == "HOTMATHS") {
                $hotmathsSwitch = true;
            }

            $subTabs = $this->getSubscriptionTabsBySAID(
                $rVal,
                $u->getUserID(),
                $arrange,
                $rVal['SubscriptionAvailID'],
                $hotmathsSwitch
            );

            foreach ($subTabs as $stKey => $stVal) {
                $result[$stKey][] = array_merge($stVal, $rVal);
            }

        }
        return ($result) ? $result : false;
    }

    public function getSubscriptionsWithTitleOfAccessCode($accessCode)
    {
        $sql = "SELECT DISTINCT ocgus.ID as USubscriptionID FROM CupGoUserSubscription ocgus ";
        $sql .= "INNER JOIN CupGoTabAccess ocgta ON ocgus.ID = ocgta.US_ID ";
        $sql .= "INNER JOIN CupGoTabs ocgt ON ocgta.TabID = ocgt.ID ";
        $sql .= "INNER JOIN ( ";
        $sql .= "SELECT DISTINCT titleID as USubscription FROM CupGoTabs cgt ";
        $sql .= "INNER JOIN CupGoTabAccess cgta ON cgt.ID = cgta.TabID ";
        $sql .=	"INNER JOIN CupGoUserSubscription cgus ON cgta.US_ID = cgus.ID ";
        $sql .= "WHERE cgta.UserID = ? AND cgus.AccessCode = ?) as r ON ocgt.titleID = r.USubscription ";
        $sql .= "WHERE ocgus.UserID = ?;";
        return $this->db->GetAll($sql, array($this->user_id, $accessCode, $this->user_id));
    }

    // ANZGO-3300
    // added by: James Bernardez
    // date: 20170420
    // added group by USubCreationDate to join multiple hotmaths with the same creation date
    public function fetchBatchByUserID($arrange=null, $offset=0, $limit=8)
    {
        $db = Loader::db();
        $object = new CupGoUserSubscription();
        $subscription_ids = $this->fetchUSubscriptionsBasedOnTitle($arrange, $offset, $limit);
        $subscription_ids = array_column($subscription_ids, 'USubscriptionID');
        $subscription_ids = implode($subscription_ids, ',');

        /**
         * ANZGO-3300 Modified by John Renzo S. Sunico, 04/27/2017
         * Added USubscriptionID filter in WHERE clause; This is to ensure
         * that only needed titles are loaded. Important for sliced resources sorting.
         */

        $sql = "SELECT * FROM (";
        $sql .= "SELECT * FROM (";
        $sql .= "SELECT ";
        $sql .= "cgus.ID AS UserSubscriptionID, cgus.CreationDate as USubCreationDate, cgus.EndDate AS USubEndDate, ";
        $sql .= "cgs.ISBN_13, cgus.AccessCode, cgus.Active, cgus.DaysRemaining, ";
        $sql .= "cgsa.CreationDate, cgsa.StartDate, cgsa.EndDate, cgsa.Duration,cgsa.Type,cgsa.id AS SubscriptionAvailID, ";
        $sql .= "cgs.Name, cgs.Description,cgs.CMS_Name, cgs.ID AS SubscriptionID, 'Go' as Source ";
        $sql .= "FROM       CupGoSubscription               cgs ";
        $sql .= "LEFT JOIN  CupGoSubscriptionAvailability   cgsa ON cgs.ID=cgsa.S_ID ";
        $sql .= "LEFT JOIN  CupGoUserSubscription           cgus ON cgsa.ID=cgus.SA_ID ";
        $sql .= "WHERE UserID = ? AND cgus.ID IN (?) ";
        $sql .= "AND (Archive IS NULL OR Archive != 'Y') AND cgus.S_ID IS NOT NULL  ";

        $sql .= "UNION ALL ";

        $sql .= "SELECT ";
        $sql .= "cgeu.ID AS UserSubscriptionID, cgeu.CreationDate as USubCreationDate, null as USubEndDate, ";
        $sql .= "null as ISBN_13, null as AccessCode, null as Active, null as DaysRemaining, ";
        $sql .= "null as CreationDate,null as StartDate, null as EndDate,null as Duration, null as Type, 0 as SubscriptionAvailID, ";
        $sql .= "null as Name, null as Description,null as CMS_Name,0 as SubscriptionID, 'HOTMATHS' as Source ";
        $sql .= "FROM CupGoExternalUser cgeu ";
        $sql .= "INNER JOIN CupGoBrandCodeTitles cgbct ON FIND_IN_SET(brandCode, cgeu.brandCodes) ";
        $sql .= "WHERE uID = ? AND cgeu.ID IN (?)";

        $sql .= ") AS a ";
        $sql .= "ORDER BY ISBN_13, USubCreationDate desc ";
        $sql .= ") AS b GROUP BY CMS_Name,UserSubscriptionID ";
        $sql .= "ORDER BY USubCreationDate DESC ";

        // ANZGO-3764 modified by jbernardez 20181011
        $results = $db->GetAll($sql, array($this->user_id, $subscription_ids, $this->user_id, $subscription_ids));

        $result = array();

        foreach ($results as $rKey => $rVal) {

            $hotmathsSwitch = false;
            if ($rVal['Source'] == "HOTMATHS") {
                $hotmathsSwitch = true;
            }

            $subTabs = $this->getSubscriptionTabsBySAID(
                $rVal,
                $this->user_id,
                $arrange,
                $rVal['SubscriptionAvailID'],
                $hotmathsSwitch
            );

            foreach ($subTabs as $stKey => $stVal) {
                $result[$stKey][] = array_merge($stVal, $rVal);
            }
        }

        /**
         * ANZGO-3300 Modified by John Renzo S. Sunico, 04/27/2017
         * Separated the sorting to a new function.
         */
        $result = $this->sortSubscriptions($arrange, $result);
        return ($result) ? $result : false;
    }

    /**
     * ANZGO-3300 Added by John Renzo S. Sunico, 04/27/2017
     * Sorts array returned by fetchBatchByUserID
     * @param arrange str from My Resources
     * @param result array created by fetchBatchByUserID
     */

    public function sortSubscriptions($arrange, $result)
    {
        $arrange = str_replace(' ', '', strtoupper($arrange));
        switch ($arrange) {
            case 'A-Z':
                uasort($result, array("CupGoUserSubscription", "sortSubscriptionByDisplayNameASC"));
                break;
            case 'Z-A':
                uasort($result, array("CupGoUserSubscription", "sortSubscriptionByDisplayNameDESC"));
                break;
            case 'OLDEST-NEWEST':
                uasort($result, array("CupGoUserSubscription", "sortSubscriptionByDateASC"));
                break;
            default:
                uasort($result, array("CupGoUserSubscription", "sortSubscriptionByDateDESC"));
                break;
        }
        return $result;
    }

    /**
     * ANZGO-3300 Added by John Renzo S. Sunico, 04/27/2017
     * Get order by.
     * @param arrange str from My Resources
     * @param tableName str name of table to sort
     */

    public function getOrderBy($arrange, $tableName="result")
    {
        $arrange = str_replace(' ', '', strtoupper($arrange));
        $order_by = "ORDER BY %s ";
        switch ($arrange) {
            case 'A-Z':
                $order_by = sprintf($order_by, "$tableName.Title ASC ");
                break;
            case 'Z-A':
                $order_by = sprintf($order_by, "$tableName.Title DESC ");
                break;
            case 'OLDEST-NEWEST':
                $order_by = sprintf($order_by, "$tableName.Created ASC ");
                break;
            default:
                $order_by = sprintf($order_by, "$tableName.Created DESC ");
                break;
        }
        return $order_by;
    }

    /**
     * ANZGO-3300 Added by John Renzo S. Sunico, 04/27/2017
     * @param arrange str from My Resources
     * @param offset int starting rows
     * @param limit int number of rows to get
     * @return array(array('USubscriptionID, isbn13, Title, Created'))
     */

    public function fetchUSubscriptionsBasedOnTitle($arrange = null, $offset = 0, $limit = 8)
    {
        $sql = "SELECT ocgus.ID as USubscriptionID, result.isbn13, result.Title, result.Created FROM ( ";
        $sql .= "SELECT cgs.ISBN_13 as isbn13, cct.displayName as Title, UNIX_TIMESTAMP(cgus.CreationDate) as Created ";
        $sql .= "FROM CupGoSubscription cgs ";
        $sql .= "INNER JOIN CupGoTabAccess cgta ON cgs.ID = cgta.S_ID ";
        $sql .= "INNER JOIN CupGoTabs cgt ON cgt.ID = cgta.TabID ";
        $sql .= "INNER JOIN CupContentTitle cct ON cct.id = cgt.titleID ";
        $sql .= "INNER JOIN CupGoUserSubscription cgus ON cgus.ID = cgta.US_ID ";
        $sql .= "WHERE cgta.UserID = ? AND cgus.CreationDate IS NOT NULL AND cgus.Active = 'Y' ";
        $sql .= "AND (cgus.Archive IS NULL OR cgus.Archive != 'Y') AND cgus.S_ID IS NOT NULL ";
        $sql .= "GROUP BY ISBN_13 ";
        $sql .= ") as result ";
        $sql .= "LEFT JOIN CupGoSubscription ocgs ON ocgs.ISBN_13 = result.isbn13 ";
        $sql .= "LEFT JOIN CupGoUserSubscription ocgus ON ocgus.S_ID = ocgs.ID ";
        $sql .= "WHERE ocgs.ISBN_13 = result.isbn13 AND ocgus.UserID = ? ";
        $sql .= $this->getOrderBy($arrange);
        $sql .= "LIMIT $offset, $limit;";
        $results = $this->db->GetAll($sql, array($this->user_id, $this->user_id));

        if (!$results) {
            return array(array("USubscriptionID" => -1));
        }
        return $results;
    }

    public static function fetchByTabID($tabId)
    {
        $object = new CupGoUserSubscription();
        $db = Loader::db();
        $u = new User();

        $result = array();
        $db = Loader::db();
        $sql = "SELECT cgsa.Type, cgsa.CreationDate, cgsa.StartDate, cgsa.EndDate, cgsa.Duration,";
        $sql .= "cgus.CreationDate as USubCreationDate, cgus.EndDate AS USubEndDate ";
        $sql .= "FROM CupGoUserSubscription cgus LEFT JOIN CupGoSubscriptionAvailability cgsa ON cgus.SA_ID=cgsa.ID ";
        $sql .= "LEFT JOIN CupGoSubscriptionTabs cgst ON cgus.S_ID=cgst.S_ID ";
        $sql .= "WHERE TabID=? AND UserID=" . $u->getUserID() . " ORDER BY cgus.CreationDate DESC";
        $result = $db->getRow($sql, array($tabId));

        if ($result === false) {
            return false;
        } else {
            return $result;
        }
    }

    public static function fetchAllBySubscription($sId)
    {
        $object = new CupGoUserSubscription();

        $db = Loader::db();

        $sql = "SELECT cgus.StartDate, cgus.EndDate, cgus.Duration, cgsa.Type, cgus.AccessCode, cgus.Active, ";
        $sql .= "cgus.DaysRemaining ";
        $sql .= "FROM CupGoSubscriptionAvailability cgsa LEFT JOIN CupGoUserSubscription cgus ON cgsa.ID=cgus.SA_ID ";
        $sql .= "WHERE cgus.S_ID=?, cgsa.Active='Y', cgsa.Demo='N', cgus.Archive=NULL";

        $result = $db->getAll($sql, array($sId));

        if ($result === false) {
            return false;
        } else {
            return $result;
        }
    }

    // Added by Paul Balila, 03/03/2017
    // For ticket ANZGO-3259
    public static function processEmSmCode($accesscode)
    {
      $length = strlen($accesscode);
      $message = "redirect";

      if ($length == 3) {
        $message = 'Please enter access code';
        CupGoLogs::trackUser("Activate Code", "Fail", $message . " : " . $accesscode);
      } elseif ($length != 19) {
        $message = 'Access code is incomplete 2';
        CupGoLogs::trackUser("Activate Code", "Fail", $message . " : " . $accesscode);
      } elseif (preg_match('/[^A-Za-z0-9-]/', $accesscode)) {
        $message = 'Access code contains invalid characters';
        CupGoLogs::trackUser("Activate Code", "Fail", $message . " : " . $accesscode);
      }
      return $message;
    }

    // ANZGO-3168
    // modified to accept userID so that it can be used by other modules
    public function activateCode($ac_id, $accesscode, $sa_id, $s_id, $setenddate, $limitActivation, $passedUserID = null)
    {
        global $u;
        $object = new CupGoUserSubscription();
        $db = Loader::db();

        // ANZGO-3168/
        // modified to accept userID
        if (is_null($passedUserID)) {
            $userid = $u->uID;
        } else {
            $userid = $passedUserID;
        }

        $today = date("Y-m-d H:i:s");
        $ipaddress = $_SERVER['REMOTE_ADDR'];
        $agentinfo = $_SERVER["HTTP_USER_AGENT"];
        $usable = $limitActivation == 'Y' ? 'N' : 'Y';

        $sql = "UPDATE CupGoAccessCodes set UserID=?,DateActivated=CURDATE(),IPAddress=?,";
        $sql .= "UserAgent=?,Usable=?,UsageCount=(UsageCount+1) where ID=?";
        $params = array($userid,$ipaddress,$agentinfo,$usable,$ac_id);

        if (!$db->Execute($sql, $params)) {
            $activation_message = 'An error occurred while updating your Access Code';
        } else {
            //Traverse SubscriptionTabs for S_ID = $subid and for each entry add tab access record
            $sql = "SELECT TabID from CupGoSubscriptionTabs where S_ID='$s_id'";

            $result_tabs = $db->getAll($sql);

            if (!$result_tabs) {
                $activation_message = 'Access code is incomplete 4';
                CupGoLogs::trackUser("Activate Code", "Fail-sqlerror", $activation_message . " : " . $accesscode);
            } else {
                /**
                 * Modified by: Jeszy Tanada 10/06/2017
                 * Populate DaysRemaining Field from UserSubscription
                 * ANZGO-3748 modified by mtanada 2018/07/03 added reactivation type
                 */
                $sql = "INSERT INTO CupGoUserSubscription (UserID, SA_ID, CreationDate, StartDate, EndDate, Duration,
                    Active, AccessCode, PurchaseType, DaysRemaining, S_ID)
                    SELECT ? as 'UserID', ID, ? AS 'CreationDate', StartDate, ? AS 'EndDate',
                    CASE Type
                      WHEN 'end-of-year' THEN DATEDIFF(?, ?)
                      WHEN 'reactivation' THEN DATEDIFF(?, ?)
                      WHEN 'start-end' THEN DATEDIFF(EndDate, StartDate)
                      WHEN 'duration' THEN Duration
                    END AS Duration,'Y' as 'Active',
                    ? as 'AccessCode', 'CODE' as 'PurchaseType',
                    CASE Type
                      WHEN 'end-of-year' THEN DATEDIFF(?, ?)
                      WHEN 'reactivation' THEN DATEDIFF(?, ?)
                      WHEN 'start-end' THEN DATEDIFF(EndDate, StartDate)
                      WHEN 'duration' THEN Duration
                    END AS DaysRemaining, S_ID FROM CupGoSubscriptionAvailability WHERE id=?"; //ANZGO-3546

                $result = $db->Execute($sql, array($userid, $today, $setenddate, $setenddate, $today, $setenddate,
                        $today,$accesscode, $setenddate, $today, $setenddate, $today, $sa_id)
                );

                $last_us_id = $db->Insert_ID('CupGoUserSubscription');

                if (!$result) {
                    $activation_message = 'An error occurred while activating your subscription';
                    CupGoLogs::trackUser("Activate Code", "Fail", $activation_message . " : " . $accesscode);
                } elseif (!$last_us_id) {
                    $activation_message = 'Access code is incomplete 5';
                    CupGoLogs::trackUser("Activate Code", "Fail-sqlerror", $activation_message . " : " . $accesscode);
                } else {
                    foreach ($result_tabs as $row) {
                        $tabid = $row['TabID'];

                        /**
                         * Added by: Jeszy Tanada 10/06/2017
                         * Store DaysRemaining in $daysRemainingInfo from UserSubscription
                         * to pass to TabAccess DaysRemaining field
                         */
                        $daysRemaining = $this->getDaysRemaining_US($tabid, $sa_id, $accesscode, $s_id);
                        $daysRemainingInfo = $daysRemaining['DaysRemaining'];

                        // Modified by Shane Camus 2017-03-16
                        // ANZGO-3287 Delete functionality not working
                        // Simplified previous code
                        $sql = "INSERT INTO CupGoTabAccess (UserID,TabID,S_ID,SA_ID,Active,EndDate,US_ID,DaysRemaining) ";
                        $sql .= "VALUES ('$userid','$tabid','$s_id','$sa_id','Y','$setenddate',$last_us_id, $daysRemainingInfo)";
                        $db->Execute($sql);
                    }

                    $activation_message = 'successful';
                }
                CupGoLogs::trackUser("Activate Code", "Success", "Subscription successful : " . $accesscode);
            }

            // Check if this AccessCode has an App Bundle
            return $activation_message;
        }
    }

    /**
     * Modified by: Jeszy Tanada 10/06/2017
     * Get DaysRemaining data from UserSubscription
     */
    public function getDaysRemaining_US($tabID, $saID, $accessCode, $subscriptionID)
    {
        $db = Loader::db();
        $sql = "SELECT DaysRemaining FROM CupGoUserSubscription cgus ";
        $sql .= "JOIN CupGoSubscriptionTabs cgst ON cgst.S_ID = cgus.S_ID ";
        $sql .= "WHERE cgst.TabID = ? AND cgst.S_ID = ? AND cgus.SA_ID = ? AND cgus.AccessCode = ?";

        // ANZGO-3764 modified by jbernardez 20181011
        return $db->GetRow($sql, array($tabID, $subscriptionID, $saID, $accessCode));
    }

    // ANZGO-2856
    // check the subcription of the accesscode being used
    // if it has a custom message
    // it it does, return that message, else return blank
    public function accessCodeSubscriptionMessage($accesscode)
    {
        global $u;

        $db = Loader::db();

        $sql = "SELECT AC.AccessCode, S.Activate_Page_Message
            FROM CupGoAccessCodes AC
                JOIN CupGoSubscriptionAvailability SA ON SA.ID = AC.SA_ID
                JOIN CupGoSubscription S ON S.ID = SA.S_ID
            WHERE AC.AccessCode = ?";

        $message = '';

        // ANZGO-3764 modified by jbernardez 20181011
        if ($result = $db->getRow($sql, array($accesscode))) {
            $message = $result['Activate_Page_Message'];
        }

        return $message;
    }

    public function accessCodeHasAppBundle($accesscode)
    {

        global $u;

        $db = Loader::db();

        $curruserid = $u->getUserID();

        // 1) Check if the Subscription Availability has App tab on it
        $sql = "SELECT ST.titleID
        FROM CupGoAccessCodes AC
                JOIN CupGoSubscriptionAvailability SA ON SA.ID = AC.SA_ID
                JOIN CupGoSubscription S ON S.ID = SA.S_ID
                JOIN CupGoSubscriptionTabs ST ON ST.S_ID = S.ID
                JOIN CupGoTabs T ON T.ID = ST.TabID
        WHERE AC.AccessCode = ?
        AND T.TabName = 'App' LIMIT 1";

        // If there is an App Tab
        // ANZGO-3764 modified by jbernardez 20181011
        if ($result = $db->getRow($sql, array($accesscode))) {

            $product_id = $result['titleID'];

            // search if it has AccessCodesBundle
            $sql = "SELECT TitleBundleId FROM CupGoAccessCodesBundle WHERE product_id = $product_id";

            $result = $db->getRow($sql);

            if (count($result)) {

                // if there is insert into DPSEntitlementSubscriptions
                // do the normal App Redeem Code from the Device
                // Steps are:
                // 1) Insert to DPSEntitlementSubscriptions
                // 2) Insert to DPSEntitlementCoupons
                // 3) Insert to DPSEntitlementEditions

                $title_id = $result['TitleBundleId'];
                $_datetime_today = date('Y-m-d H:i:s');
                $_datetime_enddate = date('Y-m-d H:i:s', strtotime('+1 year'));

                // 1) Insert to DPSEntitlementSubscriptions
                $insert_subscription_query = "INSERT INTO DPSEntitlementSubscriptions
                                              (subscriber_id, startDate, endDate, title_id ) VALUES (?,?,?,?)";

                $result_insert_subscription_query = $db->Execute(
                    $insert_subscription_query,
                    array($curruserid, $_datetime_today, $_datetime_enddate, $title_id)
                );

                // 2) Insert to DPSEntitlementCoupons
                $insert_coupon_query = "INSERT INTO DPSEntitlementCoupons (name, subscriber_id ) VALUES (?,?)";

                $result_insert_subscription_query = $db->Execute($insert_coupon_query, array($accesscode, $curruserid));

                $coupon_last_insert_id = $db->Insert_ID();

                // To Insert into DPSEntitlementEditions
                // You need to get the Chapters of the Titles first from DPSEntitlementChapters

                $query_chapters = "SELECT * FROM DPSEntitlementChapters WHERE title_id = ?";

                $r = $db->getAll($conn, $query_chapters, array($title_id));

                if (count($r)) {

                    // 3) Insert to DPSEntitlementEditions
                    foreach ($r as $row) {

                        $title = $row['folio_name'];
                        $productId = $row['productId'];
                        $publicationDate = $row['publicationDate'];

                        $insert_edition_query = "INSERT INTO DPSEntitlementEditions
                                                (title, productId, publicationDate, coupon_id ) VALUES (?,?,?,?)";

                        $result_insert_edition_query = $db->Execute(
                            $insert_edition_query,
                            array($title, $productId, $publicationDate, $coupon_last_insert_id)
                        );
                    }
                }
            }
        } // End Check App
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }

    /**
     */
    private function getSubscriptionTabs($result, $userID, $arrange)
    {
        $db = Loader::db();

        $sql = "SELECT
                cgta.TabID, cct.`id` AS titleID,
                cct.name, cct.`isbn13`,cct.`isbn13`,cct.`displayName`,cct.`prettyUrl`,
                cgta.US_ID, cgta.SA_ID
                FROM CupGoTabAccess AS cgta
                JOIN `CupGoTabs` AS cgt ON cgta.`TabID` = cgt.ID
                JOIN CupContentTitle AS cct ON cgt.`TitleID` = cct.ID
                WHERE cgta.UserID = ? ";

        // ANZGO-3045
        // changing group by to US_ID, as tab_id doesn't load all US_ID
        // which is needed to see newest access id
        // $sql .= "GROUP BY cgst.TabID ";

        // ANZGO-3059 by Paul Balila, 10-01-17
        $sql .= "GROUP BY US_ID, cct.`id`";

        if ($arrange == "A - Z") {
            $sql .= "ORDER BY cct.displayName";
        } elseif ($arrange == "Z - A") {
            $sql .= "ORDER BY cct.displayName DESC";
        }

        $tabs = $db->GetAll($sql, array($userID));

        $tab_arr = array();

        foreach ($tabs as $t) {
            $tab_arr[$t['titleID']] = $t;
        }

        // ANZGO-3043
        // ADD HOTMATHS
        $sql = "select cct.id AS titleID, cct.name, cct.isbn13, cct.displayName, cct.prettyURL, cgeu.authToken, ";
        $sql .= "cgeu.externalID, cgeu.brandCodes, cgeu.tokenExpiryDate from CupContentTitle cct ";
        $sql .= "inner join CupGoBrandCodeTitles cgbct ON cct.id = cgbct.titleID ";
        $sql .= "inner join CupGoExternalUser cgeu ON cgbct.brandCode = cgeu.brandCodes ";
        $sql .= "where cgeu.uID = ?";

        // ANZGO-3764 modified by jbernardez 20181011
        $hms = $db->GetAll($sql, array($userID));

        foreach ($hms as $hm) {
            $tab_arr[$hm['titleID']] = $hm;
        }

        return $tab_arr;
    }

    // ANZGO-3300
    // added by: James Bernardez
    // date: 20170420
    // get subscription tab per SA_ID
    private function getSubscriptionTabsBySAID($result, $userID, $arrange, $said, $hotmaths = false)
    {
        $db = Loader::db();

        $sql = "SELECT
                cgta.TabID, cct.`id` AS titleID,
                cct.name, cct.`isbn13`,cct.`isbn13`,cct.`displayName`,cct.`prettyUrl`,
                cgta.US_ID, cgta.SA_ID
                FROM CupGoTabAccess AS cgta
                JOIN `CupGoTabs` AS cgt ON cgta.`TabID` = cgt.ID
                JOIN CupContentTitle AS cct ON cgt.`TitleID` = cct.ID
                WHERE cgta.UserID = ?
                AND cgta.SA_ID = ? ";
        $sql .= "GROUP BY US_ID, cct.`id`";

        if ($arrange == "A - Z") {
            $sql .= "ORDER BY cct.displayName";
        } elseif ($arrange == "Z - A") {
            $sql .= "ORDER BY cct.displayName DESC";
        }

        // ANZGO-3764 modified by jbernardez 20181011
        $tabs = $db->GetAll($sql, array($userID, $said));

        $tab_arr = array();

        foreach ($tabs as $t) {
            $tab_arr[$t['titleID']] = $t;
        }

        if ($hotmaths) {
            $sql = "SELECT cct.id AS titleID, cct.name, cct.isbn13, cct.displayName, cct.prettyURL, cgeu.authToken, ";
            $sql .= "cgeu.externalID, cgeu.brandCodes, cgeu.tokenExpiryDate ";
            $sql .= "FROM CupContentTitle cct ";
            $sql .= "INNER JOIN CupGoBrandCodeTitles cgbct ON cct.id = cgbct.titleID ";
            $sql .= "INNER JOIN CupGoExternalUser cgeu ON cgbct.brandCode = cgeu.brandCodes ";
            $sql .= "WHERE cgeu.uID = ? ";
            $sql .= "AND cgeu.ID = ? ";

            // ANZGO-3764 modified by jbernardez 20181011
            $hms = $db->GetAll($sql, array($userID, $result['UserSubscriptionID']));

            foreach ($hms as $hm) {
                $tab_arr[$hm['titleID']] = $hm;
            }
        }

        return $tab_arr;
    }

    private static function sortSubscriptionByDateASC($a, $b)
    {
        if (strtotime($a[0]['USubCreationDate']) == strtotime($b[0]['USubCreationDate'])) {
            return 0;
        }
        return (strtotime($a[0]['USubCreationDate']) < strtotime($b[0]['USubCreationDate'])) ? -1 : 1;
    }

    private static function sortSubscriptionByDateDESC($a, $b)
    {
        if (strtotime($a[0]['USubCreationDate']) == strtotime($b[0]['USubCreationDate'])) {
            return 0;
        }
        return (strtotime($a[0]['USubCreationDate']) > strtotime($b[0]['USubCreationDate'])) ? -1 : 1;
    }

    private static function sortSubscriptionByDisplayNameASC($a, $b)
    {
        $a_bin = strtolower($a[0]['displayName']);
        $b_bin = strtolower($b[0]['displayName']);
        if ($a_bin == $b_bin) {
            return 0;
        }
        return ($a_bin < $b_bin) ? -1 : 1;
    }

    private static function sortSubscriptionByDisplayNameDESC($a, $b)
    {
        $a_bin = strtolower($a[0]['displayName']);
        $b_bin = strtolower($b[0]['displayName']);
        if ($a_bin == $b_bin) {
            return 0;
        }
        return ($a_bin > $b_bin) ? -1 : 1;
    }

    // ANZGO-3045
    /**
     * @param $tabid
     * @param $usID
     * @return mixed
     */
    public function subscriptionsByTabAccess($tabID, $usID)
    {

        $db = Loader::db();
        $u = new User();

        $sql = "select cgus.CreationDate as USubCreationDate, cgus.EndDate as USubEndDate, cgus.DaysRemaining, ";
        // ANZGO-3055
        $sql .= "cgsa.StartDate as StartDate, cgsa.EndDate as EndDate, cgus.Duration as Duration, cgus.AccessCode ";
        $sql .= "from CupGoTabAccess cgta ";
        $sql .= "inner join CupGoUserSubscription cgus on cgta.US_ID=cgus.ID ";
        $sql .= "inner join CupGoSubscriptionAvailability cgsa ON cgus.SA_ID=cgsa.ID ";
        $sql .= "where cgus.UserID =  ? ";
        $sql .= "and cgta.TabID = ? ";
        // ANZGO-3601 Modified by Shane Camus 01/17/18
        $sql .= "and cgta.US_ID = ? ";
        $sql .= "ORDER BY USubCreationDate DESC ";

        // ANZGO-3764 modified by jbernardez 20181011
        return $db->GetAll($sql, array($u->getUserID(), $tabID, $usID));
    }

    // ANZGO-3600 added by jbernardez 20180212
    public function tabAccessUpdateBySID($subscriptionID, $userID, $usID)
    {
        // 1. check if there was an update in new table CupGoTabAccessUpdates
        // 2. if there is a flag on this table, check if this has not been updated yet
        // by checking table CupGoTabAccessUpdated
        // 3. if not yet updated in CupGoTabAccessUpdated, then run the update for
        // CupGoTabAccess

        $updatesResult = $this->tabAccessUpdates($subscriptionID);
        if ($updatesResult) {
            $cgtauID = $updatesResult['ID'];
            $updatedResult = $this->tabAccessUpdated($userID, $subscriptionID, $cgtauID);

            if (!$updatedResult) {
                $this->tabAccessUpdate($subscriptionID, $userID, $usID);
                $this->addTabAccessUpdated($userID, $subscriptionID, $cgtauID);
            }
        }
    }

    // ANZGO-3600 added by jbernardez 20180212
    private function tabAccessUpdates($subscriptionID)
    {
        $db = Loader::db();

        $sql = "SELECT * FROM CupGoTabAccessUpdates WHERE S_ID = ? ORDER BY ID DESC LIMIT 1 ";

        $result = $db->GetRow($sql, array($subscriptionID));

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // ANZGO-3600 added by jbernardez 20180212
    private function tabAccessUpdated($subscriptionID, $userID, $cgtauID)
    {
        $db = Loader::db();

        $sql = "SELECT * FROM CupGoTabAccessUpdated WHERE User_ID = ? AND S_ID = ? AND CGTAU_ID = ? ";

        $result = $db->GetRow($sql, array($userID, $subscriptionID, $cgtauID));

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // ANZGO-3600 added by jbernardez 20180212
    private function addTabAccessUpdated($subscriptionID, $userID, $cgtauID)
    {
        $db = Loader::db();

        $sql = "INSERT INTO CupGoTabAccessUpdated (User_ID, S_ID, CGTAU_ID) VALUES (? ,? ,?) ";

        $result = $db->Execute($sql, array($userID, $subscriptionID, $cgtauID));

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // ANZGO-3600 added by jbernardez 20180212
    private function tabAccessUpdate($subscriptionID, $userID, $usID)
    {
        // 1. disable all
        // 2. enable when ==
        // 3. add when !=
        $db = Loader::db();

        $sql = "SELECT * FROM CupGoTabAccess WHERE S_ID = ? AND UserID = ? AND US_ID = ? ORDER BY ID";

        $resultTabAccessByUserBySubsID = $db->GetAll($sql, array($subscriptionID, $userID, $usID));

        $sql = "SELECT * FROM CupGoSubscriptionTabs WHERE S_ID = ?";

        $resultTabAccessUpdates = $db->GetAll($sql, array($subscriptionID));

        // 1. update all record of cupgotabaccess to Active = N
        $sql = "UPDATE CupGoTabAccess SET Active = 'N' WHERE s_id = ? AND userid = ?";

        $db->Execute($sql, array($subscriptionID, $userID));

        foreach ($resultTabAccessUpdates as $resultTabAccessUpdate) {

            $insert = false;
            foreach ($resultTabAccessByUserBySubsID as $resultTABUSID) {

                // 2. enable record when they are equal
                if ($resultTabAccessUpdate['TabID'] == $resultTABUSID['TabID']) {

                    // check days remaining, if already at 0, then don't apply update
                    if ($resultTABUSID['DaysRemaining'] > 0) {
                        $sql = "UPDATE CupGoTabAccess SET Active = 'Y' WHERE s_id = ? AND userid = ? AND TabID = ? ";
                        $db->Execute($sql, array($subscriptionID, $userID, $resultTABUSID['TabID']));
                        $insert = true;
                    }
                }
            }

            // 3. if it got here, it means record didn't have any equal, so add that record
            if (!$insert) {
                $sql = "INSERT INTO CupGoTabAccess (UserID, TabID, S_ID, SA_ID, Active, EndDate, US_ID, ";
                $sql .= "DaysRemaining, DateDeactivated) VALUES (?, ?, ?, ?, 'Y', ?, ?, ?, ?)";
                $db->Execute($sql, array(
                    $resultTABUSID['UserID'],
                    $resultTabAccessUpdate['TabID'],
                    $resultTABUSID['S_ID'],
                    $resultTABUSID['SA_ID'],
                    $resultTABUSID['EndDate'],
                    $resultTABUSID['US_ID'],
                    $resultTABUSID['DaysRemaining'],
                    $resultTABUSID['DateDeactivated']
                ));
            }
        }
    }

    // ANZGO-3600 added by jbernardez 20180219
    public function checkActiveUserTabs($subscriptionID, $userID, $usID)
    {
        $db = Loader::db();

        $sql = "SELECT * FROM CupGoTabAccess WHERE S_ID = ? AND UserID = ? AND US_ID = ? AND Active = 'Y' ORDER BY ID";

        $results = $db->GetAll($sql, array($subscriptionID, $userID, $usID));

        if (count($results > 0)) {
            return true;
        } else {
            return false;
        }
    }
}
