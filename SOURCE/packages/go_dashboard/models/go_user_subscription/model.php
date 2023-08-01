<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

// Loader::model('go_user_subscription/model', 'go_dashboard');
Loader::model('go_user_subscription/list', 'go_dashboard');

class GoDashboardGoUserSubscription extends Object {

    protected $id;

    public function __construct($id = null, $row = null) {
        $this->id = $id;
        if ($row == null) {
            $db = Loader::db();
            $row = $db->GetRow("SELECT * FROM CupGoUserSubscription WHERE ID=?", array($id));
        }

        $this->setPropertiesFromArray($row);
    }

    public function getUserSubscriptionID() {
        return $this->id;
    }

    public function update($data) {
        $db = Loader::db();
        $db->AutoExecute('CupGoUserSubscription', $data, 'UPDATE', "id='{$this->id}'");
    }

    public static function add($data) {
        $db = Loader::db();
        $db->AutoExecute('CupGoUserSubscription', $data, 'INSERT');
        return new GoDashboardGoUserSubscription($db->Insert_ID(), $data);
    }

    public function remove() {
        $db = Loader::db();
        $db->Execute('DELETE FROM CupGoUserSubscription WHERE id=?', array($this->id));
    }

    public function getLastInsertId() {
        $db = Loader::db();
        return $db->Insert_ID();
    }

    // public function addUserNotes($user_id, $notes) {
    //     $db = Loader::db();
    //     $q = "INSERT INTO CupGoUserNotes (UserID, NoteText) VALUES (?, ?)";
    //     $r = $db->prepare($q);
    //     $res = $db->Execute($r, array($user_id, $notes));
    //     return $res;
    // }
    // public function getUserNotes($user_id) {
    //     $db = Loader::db();
    //     $query = "SELECT * FROM CupGoUserNotes WHERE UserID=$user_id";
    //     $rows = $db->GetAll($query);
    //     return $rows;
    // }
    // public function getUserSubscriptions($user_id) {
    //     $db = Loader::db();
    //     $query = "SELECT * FROM CupGoUserSubscription WHERE UserID=$user_id";
    //     $rows = $db->GetAll($query);
    //     return $rows;
    // }
    // public function getUserTrackingGeneral($user_id) {
    //     $db = Loader::db();
    //     $query = "SELECT CreatedDate,PageName, Action,
    //             (SELECT CASE WHEN(ProductName IS NOT NULL AND TabName IS NOT NULL) THEN CONCAT_WS(' > ',ProductName,TabName) ELSe Info END) as Info,
    //             (SELECT CASE WHEN(Action in ('View Tab','') )
    //             THEN
    //                     (SELECT CASE WHEN(AccessLevel >= 1) THEN 'Full' ELSE 'Guest' END)
    //             ELSE
    //                     NULL
    //             END) as Access_Level
    //             FROM CupGoLogUser
    //             WHERE UserID = $user_id
    //             UNION ALL
    //             SELECT CreatedDate,PageName,Action,Info,NULL as Access_Level
    //             FROM CupGoLogAccessCode
    //             WHERE UserID = $user_id
    //             UNION ALL
    //             SELECT d.CreateDate,'Resources' as PageName
    //             ,'Download' + (SELECT CASE WHEN(d.Complete='Y') THEN ' (OK)' ELSE ' (Incomplete)' END) as Action
    //             ,cd.FileName as Info, NULL as Access_Level
    //             FROM CupGoDownloadTrack d, CupGoContentDetail cd, CupGoContent c
    //             WHERE d.UserID = $user_id
    //             AND d.FileID = cd.ID
    //             AND cd.ContentID = c.id
    //             ORDER BY 1 DESC";
    //     $rows = $db->GetAll($query);
    //     return $rows;
    // }
    // public function getUserActivationErrors($user_id) {
    //     $db = Loader::db();
    //     $query = "SELECT CreatedDate, Info FROM CupGoLogAccessCode WHERE Action = 'fail' AND UserID = $user_id ORDER BY 1 DESC";
    //     $rows = $db->GetAll($query);
    //     return $rows;
    // }
    // public function checkUserSubscription($user_id, $sa_id) // is it suppose to be the Subscription Availability ID
    // {
    //     $db = Loader::db();
    //     $row = $db->GetRow("SELECT * FROM CupGoUserSubscription WHERE UserID=? AND SA_ID=? AND Active='Y'", array($user_id, $sa_id));
    //     if (count($row) > 0) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }
    // pass the entire data? or just the product
    // or subscription availability
    // public function addUserSubscription($user_id, $product_id)
    // public function addUserSubscription($user_id, $sa_id, $s_id) // is it suppose to be the Subscription Availability ID
    // {
    //     $db = Loader::db();
    //     // Inser into UserSubscription
    //     $query = "INSERT INTO CupGoUserSubscription (UserID,
    //                                             SA_ID,
    //                                             S_ID,
    //                                             StartDate,
    //                                             EndDate,
    //                                             Duration,
    //                                             Active,
    //                                             PurchaseType)
    //                 (SELECT $user_id,ID,S_ID,StartDate,EndDate,Duration,'Y','CMS'
    //                     FROM CupGoSubscriptionAvailability
    //                     WHERE ID= ? )";
    //     $r = $db->prepare($query);
    //     $db->Execute($r, array($sa_id));
    //     $userSubscriptionLastInserId = $db->Insert_ID();
    //     $tab_query = "SELECT $user_id,TabID,S_ID,$sa_id,'Y', $userSubscriptionLastInserId FROM CupGoSubscriptionTabs WHERE S_ID=$s_id";
    //     $rows = $db->GetAll($tab_query);
    //     if (count($rows) > 0 ) {
    //         // loop insert
    //         foreach ($rows as $row) {
    //             $query = "INSERT INTO CupGoTabAccess(UserID,TabID,S_ID,SA_ID,Active,US_ID)
    //                 (SELECT $user_id,TabID,S_ID,$sa_id,'Y', $userSubscriptionLastInserId FROM CupGoSubscriptionTabs WHERE S_ID = ? )";
    //             $r = $db->prepare($query);
    //             $db->Execute($r, array($s_id));
    //         }
    //     }
    // }


    public function deactivateSubscriptions($user_id) {
        $db = Loader::db();
        $q = "UPDATE CupGoUserSubscription SET   (UserID, NoteText) VALUES (?, ?)";
        $r = $db->prepare($q);
        $res = $db->Execute($r, array($user_id, $notes));
        return $res;
    }

    public function toggleUserSubscription($user_id, $usid) {
        $db = Loader::db();

        $row = $db->GetRow("SELECT Active FROM CupGoUserSubscription WHERE UserID=? AND ID=?", array($user_id, $usid));

        // TabAccess
        if ($row['Active'] == 'Y') {
            //-- Deactivate TabAccess
            $sql = "UPDATE CupGoTabAccess
                   SET Active = 'N', DateDeactivated = now()
                   WHERE UserID = ?
                   AND US_ID = ?;";
        } else {
            //-- Reactivate TabAccess
            $sql = "UPDATE CupGoTabAccess
                   SET Active = 'Y', DateDeactivated = NULL
                   WHERE UserID = ?
                   AND US_ID = ?;";
        }

        $r = $db->prepare($sql);
        $res = $db->Execute($r, array($user_id, $usid));


        // UserSubscription
        if ($row['Active'] == 'Y') {
            //-- Deactivate a UserSubscription
            $sql = "UPDATE CupGoUserSubscription
                   SET Active = 'N', DateDeactivated = now()
                   WHERE UserID = ?
                   AND ID = ?";
        } else {
            //-- Reactivate UserSubscription
            $sql = "UPDATE CupGoUserSubscription
                   SET Active = 'Y', DateDeactivated = NULL
                   WHERE UserID = ?
                   AND ID = ?";
        }

        $r = $db->prepare($sql);
        $res = $db->Execute($r, array($user_id, $usid));
    }
    
}
