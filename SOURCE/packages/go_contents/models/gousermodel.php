<?php
/**
 * Description of password
 *
 * @author paulbalila
 */
class GoUserModel {
    private $db;
    public function __construct() {
        $this->db = Loader::db();
    }

    public function compare_pass($username, $raw_pass) {
        $query = "SELECT * FROM Users AS u WHERE u.uEmail = ?";
        $result = $this->db->GetRow($query,array($username));

        // Added by Paul Balila, 2016-04-21
        // For ticket ANZUAT-146
        // Checks if imported user's spassword has been changed by the admin.
        $this->checkPasswordChangedByAdmin($result);

        // Trigger the query again to override the previous result set.
        $result = $this->db->GetRow($query,array($username));

        if(is_null($result['oldPasswordTested'])) {
            $update_query = "UPDATE `Users` SET `oldPasswordTested` = 1 WHERE `uID` = ?";
            $update_result = $this->db->Execute($update_query,array($result['uID']));
            $flag = ($this->db->Affected_Rows() > 0);
        } else if(!$result['oldPasswordTested']) {
            // Encrypt raw password to old style...
            $old_enc = $this->encrypt_pass('old',$raw_pass);

            // and check if they are equal.
            if($old_enc == $result['uPassword']) {
                // If they are, encrypt entered password to new style
                $new_enc = $this->encrypt_pass('new', $raw_pass);
                $update_query = "UPDATE `Users` SET `oldPasswordTested` = 1, `uPassword` = ? WHERE `uID` = ?";
                $update_result = $this->db->Execute($update_query,array($new_enc,$result['uID']));
                $flag = ($this->db->Affected_Rows() > 0);
            }
        } else {
            $flag = $result['oldPasswordTested'];
        }
        return $flag;
    }

    // hack to set promotion by email
    public function setPromoByEmail($uID,$value) {
        $query = "UPDATE `UserSearchIndexAttributes` SET `ak_uPMByEmail` = ? WHERE uID = ?";
        $result = $this->db->Execute($query,array($value,$uID));
        return $result;
    }

    public function setOldPasswordAsTested($uID) {
        $query = "UPDATE `Users` SET `oldPasswordTested` = 1 WHERE uID = ?";
        $result = $this->db->Execute($query,array($uID));
        return ($this->db->Affected_Rows('Users') > 0);
    }

    private function checkPasswordChangedByAdmin($result) {
        // Check if uPassword is already hashed by c5 and oldPasswordTested flag is still 0.
        if(!ctype_alnum($result->uPassword) && $result->oldPasswordTested == 0) {
            // If it is, set oldPasswordTested to 1.
            return $this->setOldPasswordAsTested($result->uID);
        }
    }

    private function encrypt_pass($style,$input) {
        switch ($style) {
            case 'old':
                $returnpassword = $input."ZX3D56";
                $returnpassword = md5($returnpassword);
                break;
            case 'new':
                $u = new User();
                $returnpassword = $u->encryptPassword($input);
                break;
            default:
                break;
        }
        return $returnpassword;
    }
    /**
     * Added by Paul Balila, 2017-05-05, ANZGO-3338
     */
    public function removeDuplicateHashes($uId)
    {
        $params = array($uId, UVTYPE_LOGIN_FOREVER);
        $sql = "SELECT * FROM UserValidationHashes WHERE uID = ? AND type = ?";
        $result = $this->db->GetRow($sql, $params);

        if(!empty($result)) {
            $this->db->Execute("DELETE FROM UserValidationHashes WHERE uID = ? AND type = ?", $params);
        }
    }

    public function updateUserHMPendings($uId) {
        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $params = array($now, $uId);
        $paramsRetry = 5;

        $incRetry = "UPDATE `PendingHM` SET `retryCount` = retryCount + 1 WHERE retryCount < ? AND processedAt IS NULL";
        $this->db->Execute($incRetry, $paramsRetry);

        $sql = "UPDATE `PendingHM` SET `processedAt` = ? WHERE goUID = ? AND processedAt IS NULL";
        return $this->db->Execute($sql, $params);
    }

    public function updateAllUserHMPendings($failedUids, $failedClassCodes, $failedHMPIds) {
        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $paramsRetry = 5;
        $params = array($now, $paramsRetry = 5);

        $incRetry = "UPDATE `PendingHM` SET `retryCount` = retryCount + 1 WHERE retryCount < ? AND processedAt IS NULL";
        $this->db->Execute($incRetry, $paramsRetry);

        $sql = "UPDATE `PendingHM` SET `processedAt` = ? WHERE retryCount < ?";

        if (!empty($failedUids)) {
            $sql .= " AND goUID NOT IN (" . implode(',', array_map('intval', $failedUids)) . ")";
        }

        if (!empty($failedClassCodes)) {
            $sql .= " AND classCode NOT IN (" . implode(',', array_map('intval', $failedClassCodes)) . ")";
        }

        if (!empty($failedHMPIds)) {
            $sql .= " AND hmPID NOT IN (" . implode(',', array_map('intval', $failedHMPIds)) . ")";
        }

        var_dump($sql);
        $this->db->Execute($sql, $params);
        return $now;
    }

    public function fetchAllUnsuccesfulRowsCron($failedUids, $failedClassCodes, $failedHMPIds) {
        $processedAt = null;
        $paramsRetry = 5;
        $params = array($processedAt, $paramsRetry);
        $sql = "UPDATE `PendingHM` SET `processedAt` = ? WHERE retryCount < ?";

        if (!empty($failedUids)) {
            $sql .= " AND goUID NOT IN (" . implode(',', array_map('intval', $failedUids)) . ")";
        }

        if (!empty($failedClassCodes)) {
            $sql .= " AND classCode NOT IN (" . implode(',', array_map('intval', $failedClassCodes)) . ")";
        }

        if (!empty($failedHMPIds)) {
            $sql .= " AND hmPID NOT IN (" . implode(',', array_map('intval', $failedHMPIds)) . ")";
        }

        var_dump($sql);
        return $this->db->Execute($sql, $params);


        $sql = "SELECT * FROM PendingHM WHERE processedAt` = ? WHERE retryCount < ?";
        
        return $this->db->GetAll($sql, $params);
    }


    public function updateAllUserProductExpiryPendingsSingles($uIds) {
        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $params = array($now);
        $paramsRetry = 5;

        $incRetry = "UPDATE `PendingHMProductDetails` SET `retryCount` = retryCount + 1 WHERE retryCount < ? AND processedAt IS NULL";
        $this->db->Execute($incRetry, $paramsRetry);

        if (empty($uIds)) {
            return;
        }
        
        $sql = "UPDATE `PendingHMProductDetails` SET `processedAt` = ? WHERE goUID IN (" . implode(',', array_map('intval', $uIds)) . ")";

        return $this->db->Execute($sql, $params);
    }

    // public function updateAllUserProductExpiryPendings($uIds) {
    //     $now = (new \DateTime())->format('Y-m-d H:i:s');
    //     $params = array($now);
        
    //     $sql = "UPDATE `PendingHMProductDetails` SET `processedAt` = ? WHERE goUID IN (" . implode(',', array_map('intval', $uIds)) . ")";

    //     return $this->db->Execute($sql, $params);
    // }

    public function updateHotMaths($response) {
        $params = array(
            $response->externalId,
            $response->accessToken,
            $response->userId,
            $response->accessTokenExpiresIn,
            $response->brandCode,
            $response->schoolYear,
            $response->subscriberType,
            null,
            $response->createDate
        );

        $sql = "INSERT INTO Hotmaths (userId, authorizationToken, externalID, tokenExpiryDate, brandCodes, schoolYear, subscriberType, trial, dateCreated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->db->Execute($sql, $params);
    }

    public function getHMUIDbyGOUID($goUID) {
        $sql = "SELECT externalID FROM Hotmaths WHERE UserID = ?";

        return $this->db->GetRow($sql, array($goUID));
    }

    public function fetchUserHMPendings($uId) {
        $params = array($uId);
        $sql = "SELECT GROUP_CONCAT(DISTINCT classCode) AS classCodes, GROUP_CONCAT(DISTINCT HMPID) AS HMPIDs, externalID, firstName, lastName, UPPER(subscriberType) as subscriberType, limitedProduct FROM PendingHM WHERE goUID = ? AND processedAt IS NULL GROUP BY limitedProduct";
        
        return $this->db->GetAll($sql, $params);
    }

    public function fetchAllUserHMPendings() {
        $sql = "SELECT classCode, GROUP_CONCAT(HMPID) AS HMPIDs, goUID, externalID, email, firstName, lastName, UPPER(subscriberType) as subscriberType, limitedProduct FROM PendingHM WHERE processedAt IS NULL AND retryCount < 5 GROUP BY goUID, classCode, limitedProduct ORDER BY createdAt ASC";
        return $this->db->GetAll($sql);
    }

    public function fetchAllPendingProductExpiryDateSingles() {
        $sql = "SELECT hmPID, goUID, limitedProduct, productExpiryDate FROM PendingHMProductDetails WHERE processedAt IS NULL AND retryCount < 5 ORDER BY createdAt ASC";
        return $this->db->GetAll($sql);
    }

    // public function fetchAllPendingProductExpiryDate() {
    //     $sql = "SELECT GROUP_CONCAT(DISTINCT hmPID) AS hmPIDs, GROUP_CONCAT(goUID) AS goUIDs, limitedProduct, productExpiryDate FROM PendingHMProductDetails WHERE processedAt IS NULL AND retryCount < 5 GROUP BY productExpiryDate, limitedProduct";
    //     return $this->db->GetAll($sql);
    // }
}
