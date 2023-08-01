<?php 
/**
 * Model for SeriesAnswers 
 * @author Ariel Tabag <atabag@cambridge.org>
 * ANZGO0-3764 tagged by jbernardez 20181011
 * tagged for deletion
 */
class Products extends Model
{
    var $staff_id; var $db;
    var $file_location = "";
    
    public function __construct()
    {
        $u = new User();
        $this->staff_id = $u->getUserID();
        $this->dbGo();
    }

    public function __destruct()
    {
        $this->dbConcrete();
    }
    
    private function dbConcrete()
    {
        $this->db = Loader::db(null, null, null, null, true);
    }
    
    private function dbGo()
    {
        $this->db = Loader::db(DB_SERVER, DB_USERNAME, DB_PASSWORD, GO_DATABASE, true);
    }

    /**
     * user subscription availability search
     **/
    public function getAllProductByname($product_name)
    {
        $sql  = "SELECT CONCAT( IFNULL(s.CMS_Name,''), ' : ', IFNULL(s.Name,''), ' : ', ";
        $sql .= "IFNULL(sa.Description,''), ' / ', IFNULL(cast(s.ISBN_13 as char(13)),''), ' (', ";
        $sql .= "IFNULL((SELECT CASE WHEN(sa.Type='duration' AND sa.Duration=0) then 'perpetual' ";
        $sql .= "WHEN(sa.Type='duration' AND sa.duration>0) THEN CONCAT(cast(sa.Duration as char(10)), ' days') ";
        $sql .= "WHEN(sa.Type='start-end') THEN CONCAT(cast(sa.StartDate as char(25)), ' to ', CAST(sa.EndDate as char(25))) ";
        $sql .= "ELSE 'school year' end),''), IFNULL((SELECT CASE WHEN(sa.Demo='Y') THEN ' / Demo' end),''), ')') AS 'search_result', ";
        $sql .= "sa.ID as 'sa_id', s.ID as 's_id', CONCAT(s.ID,'_',sa.ID) as search_id from SubscriptionAvailability sa, Subscription s ";
        // ANZGO-3764 modified by jbernardez 20181011
        $sql .= "WHERE sa.S_ID = s.id and sa.Active='Y' AND s.CMS_Name LIKE ? ORDER BY 1 DESC";

        $products = $this->db->getAll($sql, array("%$product_name%"));

        return $products;
    }
    
    /**
     * insert user subscription
     */
    public function insertUserSubscription($user_id, $s_id, $sa_id)
    {
        $sql  = "INSERT INTO UserSubscription(UserID,SA_ID,S_ID,StartDate,EndDate,Duration,Active,PurchaseType) ";
        $sql .= "(SELECT ?,ID,S_ID,StartDate,EndDate,Duration,'Y','PROVISION' FROM SubscriptionAvailability WHERE ID=?)";
        $this->db->Execute($sql, array($user_id, $sa_id));
        $u_sub_id = $this->db->Insert_ID();
        $sql  = "INSERT INTO TabAccess(UserID,TabID,S_ID,SA_ID,Active,US_ID) ";
        // ANZGO-3764 modified by jbernardez 20181011
        $sql .= "(SELECT ?,TabID,S_ID,?,'Y',? from CMS_SubscriptionTabs where S_ID=?)";

        $this->db->Execute($sql, array($user_id, $sa_id, $u_sub_id, $s_id));

        $Provision = new Provision();
        $Provision->insertProvisionByUserSubscription($user_id, $u_sub_id);
        
        return true;
    }
}