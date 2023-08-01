<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

class CupGoUserSubscriptionList extends Object
{

    protected $db = null;
    protected $user_id = null;
    protected $subscriptions = array();

    function __construct()
    {

        global $u;
        $this->user_id = $u->getUserID();
        $this->db = Loader::db();

    }

    public function fetchAllUserSubscriptionsForDisplay()
    {
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
        $sql .= "WHERE UserID = ? ";
        $sql .= "AND (Archive IS NULL OR Archive != 'Y') AND cgus.S_ID IS NOT NULL  ";

        $sql .= "UNION ALL ";

        $sql .= "SELECT ";
        $sql .= "cgeu.ID AS UserSubscriptionID, cgeu.CreationDate as USubCreationDate, null as USubEndDate, ";
        $sql .= "null as ISBN_13, null as AccessCode, null as Active, null as DaysRemaining, ";
        $sql .= "null as CreationDate,null as StartDate, null as EndDate,null as Duration, null as Type, 0 as SubscriptionAvailID, ";
        $sql .= "null as Name, null as Description,null as CMS_Name,0 as SubscriptionID, 'HOTMATHS' as Source ";
        $sql .= "FROM CupGoExternalUser cgeu ";
        $sql .= "INNER JOIN CupGoBrandCodeTitles cgbct ON FIND_IN_SET(brandCode, cgeu.brandCodes) ";
        $sql .= "WHERE uID = ? ";

        $sql .= ") AS a ";
        $sql .= "ORDER BY ISBN_13, USubCreationDate desc ";
        $sql .= ") AS b GROUP BY CMS_Name,UserSubscriptionID ";
        $sql .= "ORDER BY USubCreationDate DESC ";

        $results = $this->db->GetAll($sql, array($this->user_id, $this->user_id));

        $result = array();

        foreach ($results as $rKey => $rVal) {

            $hotmathsSwitch = false;
            if ($rVal['Source'] == "HOTMATHS") {
                $hotmathsSwitch = true;
            }

            $subTabs = $this->getSubscriptionTabsBySAID($rVal, $this->user_id, $arrange, $rVal['SubscriptionAvailID'],
                $hotmathsSwitch);

            foreach ($subTabs as $stKey => $stVal) {
                $result[$stKey][] = array_merge($stVal, $rVal);
            }

        }

        $this->subscriptions = $result;

        return $result;

    }

    public function sortSubscriptions($arrange, $result = false)
    {
        $arrange = str_replace(' ', '', strtoupper($arrange));

        if (!$result) {
            $result = $this->subscriptions;
        }

        switch ($arrange) {
            case 'A-Z':
                uasort($result, array("CupGoUserSubscriptionList", "sortSubscriptionByDisplayNameASC"));
                break;
            case 'Z-A':
                uasort($result, array("CupGoUserSubscriptionList", "sortSubscriptionByDisplayNameDESC"));
                break;
            case 'OLDEST-NEWEST':
                uasort($result, array("CupGoUserSubscriptionList", "sortSubscriptionByDateASC"));
                break;
            default:
                uasort($result, array("CupGoUserSubscriptionList", "sortSubscriptionByDateDESC"));
                break;
        }

        $this->subscriptions = $result;

        return $result;
    }

    public function getPaginatedSubscriptions($offset, $limit)
    {
        return array_slice($this->subscriptions, $offset, $limit, true);
    }

    public static function fetchAllBySubscription($s_id)
    {

        $sql = "SELECT cgus.StartDate, cgus.EndDate, cgus.Duration, cgsa.Type, cgus.AccessCode, cgus.Active, cgus.DaysRemaining ";
        $sql .= "FROM CupGoSubscriptionAvailability cgsa LEFT JOIN CupGoUserSubscription cgus ON cgsa.ID=cgus.SA_ID ";
        $sql .= "WHERE cgus.S_ID = ?, cgsa.Active='Y', cgsa.Demo='N', cgus.Archive=NULL";

        // ANZGO-3764 modified by jbernardez 20181011
        return $this->db->getAll($sql, array($s_id));
    }

    public function __get($property)
    {
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

    private function getSubscriptionTabsBySAID($result, $userID, $arrange, $said, $hotmaths = false)
    {

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
        $tabs = $this->db->GetAll($sql, array($userID, $said));

        $tab_arr = array();

        foreach ($tabs as $t) {
            $tab_arr[$t['titleID']] = $t;
        }

        if ($hotmaths) {
            $sql = "SELECT cct.id AS titleID, cct.name, cct.isbn13, cct.displayName, cct.prettyURL, cgeu.authToken, cgeu.externalID, cgeu.brandCodes, cgeu.tokenExpiryDate ";
            $sql .= "FROM CupContentTitle cct ";
            $sql .= "INNER JOIN CupGoBrandCodeTitles cgbct ON cct.id = cgbct.titleID ";
            $sql .= "INNER JOIN CupGoExternalUser cgeu ON cgbct.brandCode = cgeu.brandCodes ";
            $sql .= "WHERE cgeu.uID = ? ";
            $sql .= "AND cgeu.ID = ? ";

            // ANZGO-3764 modified by jbernardez 20181011
            $hms = $this->db->GetAll($sql, array($userID, $result['UserSubscriptionID']));

            foreach ($hms as $hm) {
                $tab_arr[$hm['titleID']] = $hm;
            }
        }

        return $tab_arr;
    }

    public static function sortSubscriptionByDateASC($a, $b)
    {
        if (strtotime($a[0]['USubCreationDate']) == strtotime($b[0]['USubCreationDate'])) {
            return strcmp($a[0]['displayName'], $b[0]['displayName']);
        }

        return (strtotime($a[0]['USubCreationDate']) < strtotime($b[0]['USubCreationDate'])) ? -1 : 1;
    }

    public static function sortSubscriptionByDateDESC($a, $b)
    {
        if (strtotime($a[0]['USubCreationDate']) == strtotime($b[0]['USubCreationDate'])) {
            return strcmp($a[0]['displayName'], $b[0]['displayName']);
        }

        return (strtotime($a[0]['USubCreationDate']) > strtotime($b[0]['USubCreationDate'])) ? -1 : 1;
    }

    public static function sortSubscriptionByDisplayNameASC($a, $b)
    {
        $a_bin = strtolower($a[0]['displayName']);
        $b_bin = strtolower($b[0]['displayName']);
        if ($a_bin == $b_bin) {
            return 0;
        }

        return ($a_bin < $b_bin) ? -1 : 1;
    }

    public static function sortSubscriptionByDisplayNameDESC($a, $b)
    {
        $a_bin = strtolower($a[0]['displayName']);
        $b_bin = strtolower($b[0]['displayName']);
        if ($a_bin == $b_bin) {
            return 0;
        }

        return ($a_bin > $b_bin) ? -1 : 1;
    }
}
