<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('code_check/model', 'go_dashboard');
Loader::model('code_check/list', 'go_dashboard');


class GoDashboardCodeCheck extends Object
{

    protected $id;

    public function __construct($id = null, $row = null)
    {
        $this->id = $id;
    }

    public function getSubscriptionID()
    {
        return $this->id;
    }

    public function update($data)
    {
        $db = Loader::db();
        $db->AutoExecute('CupGoAccessCodes', $data, 'UPDATE', "id='{$this->id}'");
    }

    public static function add($data)
    {
        $db = Loader::db();
        $db->AutoExecute('CupGoAccessCodes', $data, 'INSERT');
        return new GoDashboardCodeCheck($db->Insert_ID(), $data);
    }

    public function remove()
    {
        $db = Loader::db();
        $db->Execute('DELETE FROM CupGoAccessCodes WHERE id=?', array($this->id));
    }


    public function filterBySubscriberName($name)
    {
        $db = Loader::db();

        $query = "SELECT  u.ID, u.Firstname, u.Lastname, s.*
                FROM CupGoAccessCodes s
                JOIN  CambridgeGO_Live.User u ON u.ID = s.subscriber_id
                WHERE u.Firstname LIKE  '%$name%' OR u.Firstname LIKE '%$name%'
                ";

        $rows = $db->GetAll($query);

        return $rows;

    }


    public function getAccessCodeDetails($accesscode)
    {
        $db = Loader::db();
        /** ANZGO-3658 modified by Maryjes Tanada 03/22/2018
         * Added a condition for specific access code, since we increase the usage Max of BJVB-K939-EUEA-TYLG to 4
         * We can remove this condition once the access code have been purchased & used for the 4th time
         */
        if ($accesscode === 'BJVB-K939-EUEA-TYLG') {
            $queryUsageMax = 'ac.UsageMax';
        } else {
            $queryUsageMax = 'sa.UsageMax';
        }
        return $db->GetRow("SELECT ac.*, us.ID as US_ID, sa.Duration, sa.Type, sa.Description,
                          sa.Active AS 'subscription_active', sa.EndOfYearBreakPoint, $queryUsageMax,
                          s.Name, s.CMS_Name, s.ISBN_13
                          FROM CupGoAccessCodes ac
                          JOIN CupGoSubscriptionAvailability sa ON sa.ID = ac.SA_ID
                          JOIN CupGoSubscription s ON s.ID = sa.S_ID
                          LEFT JOIN CupGoUserSubscription us ON us.AccessCode = ac.AccessCode
                          WHERE ac.AccessCode=? ORDER BY us.CreationDate DESC", array($accesscode));
    }


    public function getactivatedBy($accesscode)
    {
        $db = Loader::db();

        $query = "SELECT u.uID AS 'UserID', u.uEmail, us.ID as US_ID,
                    usia.ak_uFirstName AS 'FirstName',usia.ak_uLastName AS 'LastName',
                    ac.DateActivated,
                    us.EndDate,
                    us.PurchaseType,
                    us.Active, ac.AccessCode,
                    (CASE WHEN sa.Type='duration' AND sa.Duration=0 THEN 'perpetual'
                            WHEN us.DaysRemaining IS NULL THEN 'To be calculated'
                            ELSE us.DaysRemaining
                        END) AS 'DaysRemaining'
                FROM CupGoAccessCodes ac
                LEFT JOIN Users u ON u.uID = ac.UserID
                LEFT JOIN UserSearchIndexAttributes AS usia ON u.uID = usia.uID
                LEFT JOIN CupGoUserSubscription us ON us.AccessCode = ac.AccessCode
                LEFT JOIN CupGoSubscriptionAvailability sa ON sa.ID = ac.SA_ID
                WHERE ac.AccessCode = ? AND ac.UserID IS NOT NULL ORDER BY us.CreationDate DESC";

        return $db->GetRow($query, array($accesscode));
    }


    public function previouslyActivatedBy($accesscode)
    {
        $db = Loader::db();

        $query = "SELECT us.Active,
            u.uID AS 'UserID', us.ID as US_ID,
            usia.ak_uFirstName AS 'FirstName',
            usia.ak_uLastName AS 'LastName',
            u.uEmail,
            us.CreationDate AS 'DateActivated',
            us.EndDate,
            us.PurchaseType,
            (CASE WHEN sa.Type='duration' AND sa.Duration=0 THEN 'perpetual'
                    WHEN us.DaysRemaining IS NULL THEN 'To be calculated'
                    ELSE us.DaysRemaining
                END) AS 'DaysRemaining'
            FROM CupGoUserSubscription us
            LEFT JOIN Users u ON u.uID = us.UserID
            LEFT JOIN CupGoSubscriptionAvailability sa ON sa.ID = us.SA_ID
            LEFT JOIN UserSearchIndexAttributes AS usia ON u.uID = usia.uID
            WHERE us.AccessCode = ?";

        return $db->GetAll($query, array($accesscode));
    }



    public function previousReleaseDates($accesscode)
    {

        $db = Loader::db();
        // GCAP-875 Modified by machua 20200514 revert to old query as admin info are in C5
        $query="SELECT a.ReleaseDate,
        (SELECT CASE  WHEN (u.uName IS NOT NULL) THEN u.uName ELSE 'System' END) as 'ReleasedBy'
        FROM CupGoAccessCodesReleasedArchive a
        LEFT OUTER JOIN Users u on a.StaffID = u.uID
        where a.AccessCode = ?";

        return $db->GetAll($query, array($accesscode));

    }

    public function codeErrors($accesscode)
    {
        $db = Loader::db();

        // GCAP-844 Modified by machua 20200511 
        $query = "SELECT ac.UserID, ac.CreatedDate AS 'ErrorDate', ac.Info FROM Log_AccessCode ac "
                . "WHERE ac.Action='Fail' AND Info LIKE '%" . trim($accesscode) . "%'
                ORDER BY ac.CreatedDate DESC";

        return $db->GetAll($query);
    }

    public function getLastInsertId()
    {
        $db = Loader::db();
        return $db->Insert_ID();
    }

    // Code actions
    // Redeem, deactivate, release
    public function toggleCode($codeID)
    {
        $db = Loader::db();

        $query="update CupGoAccessCodes set Active = IF(Active = 'Y','N','Y') where ID=$codeID";

        return $db->Execute($query);

    }

    public function search($term)
    {
        $result_arr = array();
        $db = Loader::db();
        $query = "SELECT u.uID, u.uEmail, usia.ak_uFirstName, usia.ak_uLastName
                  FROM Users AS u JOIN UserSearchIndexAttributes AS usia ON u.uID = usia.uID WHERE "
                  . "usia.ak_uFirstName LIKE '%" . $term . "%' OR usia.ak_uLastName LIKE '%" . $term . "%'
                    OR u.uEmail LIKE '%" . $term . "%'";
        $result = $db->Execute($query);
        foreach ($result as $row) {
            $name = '(' . $row['uID'] . ') ' . $row['uEmail'];
            $result_arr[] = array('id'=>$row['uID'],'value'=>$row['uEmail'],'label'=>$name);
        }

        return (count($result_arr) > 0) ? $result_arr : array("No matches found");
    }

    public function redeem($data)
    {
        // ANZGO-3168
        // use class CupGoUserSubscription method processCode() to simplify accesscode processing
        $db = Loader::db();
        // Hotmaths API
        Loader::library('HotMaths/api');

        $code = $data['code'];
        $curruserid = $data['id'];

        // Check for HM products.
        // ANZGO-3183 by Paul Balila, 2017-01-26
        $hmApiParams = array('userId' => $curruserid, 'accessCode' => $code, 'response' => 'STRING');
        $hmApi = new HotMathsApi($hmApiParams);
        $apiReturn = '';
        if ($hmApi->getError()) {
            $apiReturn = $hmApi->getError();
            return $apiReturn['message'];
        } else {
            $hmProdCheck = $hmApi->isHmProduct();

            if ($hmProdCheck) {
                $result = $hmApi->activationHmSubscription();

                if ($result === false) {
                    $apiReturn = $hmApi->getError();
                    return $apiReturn['message'];
                }

                $process_code = CupGoUserSubscription::processCode($code, $curruserid);
                if ($process_code == 'successful') {
                    $finalStep = $hmApi->resumeActivationHmSubscription();
                    if (!$finalStep) {
                        $apiReturn = $hmApi->getError();
                        return $apiReturn['message'];
                    } else {
                        return "Subscription successful";
                    }
                }
            }
        }

        $process_code = CupGoUserSubscription::processCode($code, $curruserid);

        if ($process_code == 'successful') {
            return "Subscription successful";
        } else {
            return $process_code;
        }

        // Remove commented codes, to see back-up ask Maryjes, Tickets > 2018 > Sprint-6 > filename commented_code.php
    }

    public function activateCode($ac_id, $accesscode, $sa_id, $s_id, $setenddate, $limitActivation, $curruserid)
    {
        $db = Loader::db();
        $userid = $curruserid;
        $today = date("Y-m-d H:i:s");

    	$ipaddress = $_SERVER['REMOTE_ADDR'];
    	$agentinfo = $_SERVER["HTTP_USER_AGENT"];

    	$usable = 'Y';
    	if ($limitActivation == 'Y') {
    		$usable = 'N';
    	}

        $sql = "UPDATE CupGoAccessCodes
                SET UserID='$userid', DateActivated=CURDATE(), IPAddress='$ipaddress', UserAgent='$agentinfo',
                Usable='$usable', UsageCount=(UsageCount+1) where ID='$ac_id'";
        $result = $db->Execute($sql);

        if (!$result) {
            return "An error occurred while updating your Access Code";
        } else {
          if ($setenddate) {
            $ins_sql = "INSERT INTO CupGoUserSubscription (UserID, SA_ID, CreationDate, StartDate, EndDate, Duration,
                      Active, AccessCode, PurchaseType, S_ID)
      				SELECT '$userid' as 'UserID', ID, '$today' AS 'CreationDate', StartDate, $setenddate AS 'EndDate',
      				Duration,'Y' as 'Active', '$accesscode' as 'AccessCode', 'CMS-Admin' as 'PurchaseType', S_ID
      				FROM CupGoSubscriptionAvailability
      				WHERE id='$sa_id'";
          } else {
            $ins_sql = "INSERT INTO CupGoUserSubscription (UserID, SA_ID, CreationDate, StartDate, EndDate, Duration,
                    Active, AccessCode, PurchaseType, S_ID)
    				SELECT '$userid' as 'UserID', ID, '$today' AS 'CreationDate', StartDate, DATE_ADD(StartDate,
    				INTERVAL Duration DAY) AS 'EndDate', Duration,'Y' as 'Active', '$accesscode' as 'AccessCode',
    				'CMS-Admin' as 'PurchaseType', S_ID
    				FROM CupGoSubscriptionAvailability WHERE id='$sa_id'";
          }

            $ins_result = $db->Execute($ins_sql);
            if (!$ins_result) {
                return "An error occurred while activating your subscription";
            } else {
                $sel_sql = "SELECT max(ID) AS 'Identity' FROM CupGoUserSubscription LIMIT 1";
                $selResult = $db->GetRow($sel_sql);
                if (!$selResult) {
                    $_SESSION['aacerror'] = 'Access code is incomplete';
                    return "Access code is incomplete";
                } else {
                    $lastUsId = $selResult['Identity'];
                }

                $tabSql = "SELECT TabID from CupGoSubscriptionTabs where S_ID='$s_id'";
                $tabResult = $db->GetRow($tabSql);
                if (!$tabResult) {
                    return "Access code is incomplete";
                } else {
                    $tabid = $tabResult['TabID'];
                    $sql_ins = "INSERT INTO CupGoTabAccess (UserID,TabID,S_ID,SA_ID,Active,EndDate,US_ID)
                                VALUES ($userid, $tabid, $s_id, $sa_id, 'Y', NULL, $lastUsId)";
                    $result_ins = $db->Execute($sql_ins);
                }

                $prodidsql = "SELECT DISTINCT st.titleID
			    FROM CupGoAccessCodes ac, CupGoSubscriptionAvailability sa,CupGoSubscriptionTabs st
			    WHERE ac.AccessCode='$accesscode'
			    AND sa.ID = ac.SA_ID
			   AND st.S_ID = sa.S_ID";

                $prodidresult = $db->Execute($prodidsql);
                $_SESSION['lastProductSubscribedTo'] = $prodidresult->ProductID;
                $_SESSION['lastSubSubscribedTo'] = $s_id;

                // Check if this AccessCode has an App Bundle
                $this->accessCodeHasAppBundle($accesscode, $curruserid);

                return "Subscription successful";
            }
        }
    }

    public function accessCodeHasAppBundle($accesscode, $curruserid)
    {
        $db = Loader::db();
        $query = "SELECT ST.titleID
        	FROM CupGoAccessCodes AC
        		JOIN CupGoSubscriptionAvailability SA ON SA.ID = AC.SA_ID
        		JOIN CupGoSubscription S ON S.ID = SA.S_ID

        		JOIN  CupGoSubscriptionTabs ST ON ST.S_ID = S.ID
        		JOIN CupGoTabs T ON T.ID = ST.TabID
        	WHERE AC.AccessCode='$accesscode'
        	AND T.TabName = 'App' LIMIT 1";

        $result = $db->GetRow($query);

        if ($result) {
            $productId = $result['ProductID'];
            $bundleQuery = "SELECT TitleBundleId FROM CupGoAccessCodesBundle WHERE product_id = $productId";
            $bundleResult = $db->GetRow($bundleQuery);

            if ($bundleResult) {
                $titleId = $bundleResult['TitleBundleId'];
                $_datetime_today = date('Y-m-d H:i:s');
                $_datetime_enddate = date('Y-m-d H:i:s', strtotime('+1 year'));

                // 1) Insert to DPSEntitlementSubscriptions
                $insert_subscription_query = "INSERT INTO DPSEntitlementSubscriptions
								(subscriber_id, startDate, endDate, title_id )
							VALUES ('$curruserid', '$_datetime_today', '$_datetime_enddate', '$titleId')";

                $result_insert_subscription_query = $db->Execute($insert_subscription_query);

                // 2) Insert to DPSEntitlementCoupons
                $insert_coupon_query = "INSERT INTO DPSEntitlementCoupons(name, subscriber_id )
							VALUES ('$accesscode', '$curruserid')";

                $result_insert_subscription_query = $db->Execute($insert_coupon_query);

                $coupon_last_insert_id = $db->lastInsID();

                // To Insert into DPSEntitlementEditions
                // You need to get the Chapters of the Titles first from DPSEntitlementChapters

                $queryChaptersSql = "SELECT * FROM DPSEntitlementChapters WHERE title_id = $titleId";

                $queryChaptersResult = $db->GetRow($queryChaptersSql);

                $title = $queryChaptersResult['folio_name'];
                $productId = $queryChaptersResult['productId'];
                $publicationDate = $queryChaptersResult['publicationDate'];

                $insertEditionQuery = "INSERT INTO DPSEntitlementEditions
                                (title, productId, publicationDate, coupon_id)
                                VALUES ('$title', '$productId', '$publicationDate', '$coupon_last_insert_id')";

                $db->Execute($insertEditionQuery);
            }
        }
    }

    public function release($id)
    {
        $db = Loader::db();
        $codeID = $id;
        $u = new User();
        $staffID = $u->getUserID();

        // https://jira.cambridge.org/browse/ANZUAT-39
        // as per discussion with Carina UsageCount should remain when the code is released
        $query = "UPDATE CupGoAccessCodes
    		SET Usable = 'Y', UserID = NULL, DateActivated = NULL, IPAddress = NULL, UserAgent = NULL
    		WHERE ID='$codeID'";

        $db->Execute($query);

		$query = "INSERT INTO CupGoAccessCodesReleasedArchive(AccessCode, CreationDate, UserID, SA_ID, DateActivated,
                StaffID, IPAddress, Browser, OperatingSystem, Active, UserAgent, BatchID, UsageMax, UsageCount, Usable,
                ReleaseDate)
                (SELECT AccessCode, CreationDate, UserID, SA_ID, DateActivated, $staffID AS 'StaffID', IPAddress,
                Browser,OperatingSystem, Active, UserAgent, BatchID, UsageMax, UsageCount, Usable,NOW() AS 'ReleaseDate'
                FROM CupGoAccessCodes WHERE ID = '$codeID')";

        return ($db->Execute($query)) ? "Code Released" : "Error releasing code";
    }

    // GCAP-848 added by mtanada 20200504
    public function getTitleIds($tabs)
    {
        $db = Loader::db();
        $tabIds = array_column($tabs, 'id');

        if (!empty($tabs)) {
            $query = 'SELECT TitleID, id as tabId FROM CupGoTabs
                      WHERE ID IN ('. implode(",", $tabIds) .')';
            return $db->GetAll($query);
        }
        return false;
    }

    // GCAP-844 Added by machua 20200511
    public function getSeriesId($titleID)
    {
        $db = Loader::db();

        $query = 'SELECT ccs.ID as seriesId FROM CupContentTitle cct
              INNER JOIN CupContentSeries ccs ON cct.series = ccs.name
              WHERE cct.id = '. $titleID;
        return $db->GetOne($query);
    }


}