<?php

Loader::library('hub-sdk/autoload');
Loader::library('Activation/hub_activation');

use HubEntitlement\Models\Activation;
use HubEntitlement\Models\Permission;

class ProvisioningHotmathsModel
{
    const STATE_ADD_SUBSCRIPTION = 'Adding subscription.';
    const STATE_ADD_SUBSCRIPTION_ERR = 'Adding Subscription error';
    const STATE_SUBSCRIPTION_ERR = 'Subscription error';

    const COLUMN_EMAIL = 0;
    const COLUMN_PASSWORD = 1;
    const COLUMN_FIRST_NAME = 2;
    const COLUMN_LAST_NAME = 3;
    const COLUMN_SCHOOL = 4;
    const COLUMN_STATE = 5;
    const COLUMN_POST_CODE = 6;
    const COLUMN_USER_ROLE = 7;
    const COLUMN_SCHOOL_ID = 8;

    // SB-675 Added by mtanada 20200812
    const COLUMN_CLASS_NAME_INT_MATH = 9;
    const COLUMN_CLASS_NAME_HUMANITIES = 10;
    const COLUMN_CLASS_NAME_ICEEM = 11;

    public $fileRecordID;

    private $db;

    public function __construct()
    {
        Loader::library('Activation/library');
        $this->db = Loader::db();
    }

    public function insertFileRecord($fileId, $fileName)
    {
        $u = new User();
        $sql = 'INSERT INTO ProvisioningFiles (FileID,FileName,DateUploaded,StaffID) VALUES(?,?,NOW(),?)';
        $this->db->Execute($sql, array($fileId, $fileName, $u->uID));

        return $this->db->Insert_ID('ProvisioningFiles');
    }

    /**
     * ANZGO-3258 Added by John Renzo S. Sunico, October 13, 2017
     * Returns provisioning file uploaded information
     *
     * @param int $fileId
     * @return bool|array
     */
    public function getFileRecord($fileId)
    {
        return $this->db->GetRow('SELECT * FROM ProvisioningFiles WHERE ID = ?', array($fileId));
    }

    public function insertProvisioningUsers($data, $fileId)
    {
        $sql = 'INSERT INTO ProvisioningUsers (FirstName,LastName,Email,FileID,Type,DateUploaded) VALUES ';
        $params = array();
        $params[] = htmlspecialchars($data[static::COLUMN_FIRST_NAME], ENT_QUOTES);
        $params[] = htmlspecialchars($data[static::COLUMN_LAST_NAME], ENT_QUOTES);
        $params[] = $data[static::COLUMN_EMAIL];
        $params[] = $fileId;
        $params[] = ucfirst($data[static::COLUMN_USER_ROLE]);
        $sql .= "('" . implode("','", $params) . "',NOW())";
        $this->db->Execute($sql);

        return $this->db->Insert_ID();
    }

    public function updateUserStatusByEmail($email, $prevStatus, $fileId, $newStatus, $remarks, $options = [])
    {
        $sql = 'UPDATE ProvisioningUsers SET Status = ?, Remarks = ?, DateModified = NOW() ';
        $params = array();
        $params[] = $newStatus;
        $params[] = $remarks;
        if ($options) {
            foreach ($options as $key => $value) {
                $sql .= ', ' . $key . ' = ?';
                $params[] = $value;
            }
        }

        if (is_array($prevStatus)) {
            $sql .= ' WHERE (Email = ? AND (Status = ? OR Status = ?)) AND FileID = ?';
            $params[] = $email;
            $params[] = $prevStatus[0];
            $params[] = $prevStatus[1];
            $params[] = $fileId;
        } else {
            $sql .= ' WHERE (Email = ? AND Status = ?) AND FileID = ?';
            $params[] = $email;
            $params[] = $prevStatus;
            $params[] = $fileId;
        }
        $this->db->Execute($sql, $params);
    }

    public function updateUserStatusById($id, $prevStatus, $fileId, $newStatus, $remarks, $options = [])
    {
        $sql = 'UPDATE ProvisioningUsers SET Status = ?, Remarks = ?, DateModified = NOW() ';
        $params = array();
        $params[] = $newStatus;
        $params[] = $remarks;
        if ($options) {
            foreach ($options as $key => $value) {
                $sql .= ", " . $key . " = ?";
                $params[] = $value;
            }
        }

        if (is_array($prevStatus)) {
            $sql .= ' WHERE (uID = ? AND (Status = ? OR Status = ?)) AND FileID = ?';
            $params[] = $id;
            $params[] = $prevStatus[0];
            $params[] = $prevStatus[1];
            $params[] = $fileId;
        } else {
            $sql .= ' WHERE (uID = ? AND Status = ?) AND FileID = ?';
            $params[] = $id;
            $params[] = $prevStatus;
            $params[] = $fileId;
        }
        $this->db->Execute($sql, $params);
    }

    // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
    public function updateProvisioningUserStatusByID($id, $status = 'Status', $remarks = 'Remarks')
    {
        $sql = 'UPDATE ProvisioningUsers SET Status = ?, Remarks = ? WHERE ID = ?';
        $this->db->Execute($sql, [$status, $remarks, $id]);

        return $this->db->hasAffectedRows;
    }

    /**
     * ANZGO-3525 Added by John Renzo S. Sunico October 23, 2017
     * Simple update of Remarks
     *
     * @param $id
     * @param $remarks
     * @return RecordSet
     */
    public function updateProvisionedUsersRemarks($id, $remarks)
    {
        $sql = 'UPDATE ProvisioningUsers SET Remarks = ?, DateModified = NOW() WHERE id = ?';

        return $this->db->Execute($sql, array($remarks, $id));
    }

    public function getProvisionedUsers($fileId, $page = 0)
    {
        $limit = 10;
        $sql = 'SELECT uID, FirstName, LastName, Email, Type, Status, Remarks, in_gigya ';
        $sql .= 'FROM ProvisioningUsers WHERE FileID = ? ORDER BY LastName ';
        $sql .= 'LIMIT ' . $limit;

        if ($page > 0) {
            $offset = (($limit * $page) - $limit);
            $sql .= ' OFFSET ' . $offset;
        }

        return $this->db->GetAll($sql, array($fileId));
    }

    public function getAllProvisionedUsers($fileId)
    {
        $sql = 'SELECT uID, FirstName, LastName, Email, Type, Status, Remarks, ';
        $sql .= 'CASE WHEN in_gigya IS NULL THEN "Pending" ';
        $sql .= 'WHEN in_gigya = 0 THEN "Failed" ';
        $sql .= 'WHEN in_gigya = 1 THEN "Succeeded" ';
        $sql .= 'END AS "In Gigya"';
        $sql .= 'FROM ProvisioningUsers WHERE FileID = ? ORDER BY LastName';

        return $this->db->GetAll($sql, array($fileId));
    }

    public function getAllNewProvisionedUsersInfo($fileId)
    {
        $sql = <<<SQL
            SELECT u.uID uID, u.uEmail email, u.uPassword password,
                   u.uIsValidated isValidated, u.uIsActive isActive,
                   usia.ak_uFirstName firstName, usia.ak_uLastName lastName,
                   usia.ak_uStateAU state, usia.ak_uPostcode postCode,
                   usia.ak_uSchoolName schoolName, g.gName role
            FROM Users u
            JOIN UserSearchIndexAttributes usia ON u.uID = usia.uID
            JOIN UserGroups ug ON u.uID = ug.uID
            JOIN Groups g ON ug.gID = g.gID
            WHERE u.uID IN (
                SELECT uID FROM ProvisioningUsers WHERE FileID = ?
            );
SQL;

        return $this->db->GetAll($sql, [$fileId]);
    }

    public function getTotalProvUsers($fileId)
    {
        $sql = 'SELECT COUNT(*) total ';
        $sql .= 'FROM ProvisioningUsers WHERE FileID = ? ORDER BY LastName';
        $result = $this->db->GetRow($sql, array($fileId));

        return $result ? $result['total'] : 0;
    }

    public function getProvisionedUser($uId)
    {
        $sql = 'SELECT uID, FirstName, LastName, Email, Type, Status, Remarks FROM ProvisioningUsers WHERE uID= ?';

        return $this->db->GetRow($sql, array($uId));
    }

    /**
     * ANZGO-3525 Modified by John Renzo S. Sunico October 23, 2017
     * Return provisioning user information by id (primary key)
     *
     * @param $id
     * @return array|bool
     */
    public function getProvisioningUserByID($id)
    {
        $sql = 'SELECT * FROM ProvisioningUsers WHERE id = ?';

        return $this->db->GetRow($sql, array($id));
    }

    // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
    public function isProvisioned($email)
    {
        $sql = 'SELECT ID FROM ProvisioningUsers WHERE FileID = ? AND Email = ? AND completed = 1';
        $result = $this->db->GetRow($sql, [$this->fileRecordID, $email]);

        return count($result) > 0;
    }

    // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
    public function getProvisionedUserProgressCount()
    {
        $sql = 'SELECT COUNT(*) FROM ProvisioningUsers WHERE FileID = ? AND completed = 1';
        return $this->db->GetOne($sql, [$this->fileRecordID]);
    }

    // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
    public function markProvisionUserCompletedByID($id)
    {
        $sql = 'UPDATE ProvisioningUsers SET completed = 1 WHERE ID = ?';
        $this->db->Execute($sql, [$id]);

        return $this->db->hasAffectedRows;
    }

    // ANZGO-3642 Modified by John Renzo Sunico, 02/22/2018
    // ANZGO-3654 Modified by Shane Camus, 04/26/2018
    // ANZGO-3914 Modified by Shane Camus, 11/12/2018
    // SB-2 Modified by Michael Abrigos, 01/16/2018
    public function processUserSubscription($provisioningUser, $saIDs, $limitedEndDate = null)
    {
        $userID = $provisioningUser['uID'];
        $hmMetaData = array();

        foreach ($saIDs as $saID) {
            $status = 'Provisioned';
            $remark = 'Subscription added';

            // HUB-153 modified by Carl Lewi R. Godoy 08/28/2018
            $u = new User();
            $createdBy = $u->uID;

            // Get user activations
            $userActivations = Activation::where([
                'user_id' => $userID,
                'is_paginated' => 0,
            ]);

            //SB-103 added by machua 20190320
            $limitedProductDaysRemaining = 0;
            $limitedDateDeactivated = null;
            // SB-178 modified by machua 20190521
            if ($limitedEndDate !== null || $limitedEndDate !== '') {
                $limitedDate = new DateTime($limitedEndDate);
                $currentDate = new DateTime();
                if ($currentDate > $limitedDate) {
                    $limitedProductDaysRemaining = 0;
                } else {
                    $limitedProductDaysRemaining = date_diff($currentDate, $limitedDate);
                    $limitedProductDaysRemaining = (int)($limitedProductDaysRemaining->format('%a'));
                }
            }

            foreach ($userActivations as $userActivation) {
                $userPermission = $userActivation->permission;

                $daysRemaining = $userActivation->daysRemaining;

                // Compares fetched permission's entitlement id to current selected entitlement id
                // SB-178 modified by machua 20190523 to properly check if user have the same subscription
                if ((int)$userPermission->entitlement_id === (int)$saID &&
                    $userActivation->DateDeactivated === null &&
                    $daysRemaining > 0) {

                    // SB-178 modified by machua 20190521
                    // SB-103 added by machua 20190320 change behavior if limited product is provisioned
                    if ($limitedEndDate === null || $limitedEndDate === ''
                        || ($limitedEndDate !== null
                            && $limitedProductDaysRemaining > $daysRemaining)) {
                        // Update/Deactivate current subscription
                        $userActivation->DateDeactivated = date('Y-m-d H:i:s');

                        try {
                            $userActivation->save();
                            $remark = 'Subscription renewed';
                        } catch (Exception $e) {
                            $status = 'TNGProvisionError';
                            $remark = 'Unable to renew subscription.';
                            $this->updateProvisioningUserStatusByID($provisioningUser['ID'], $status, $remark);
                            continue;
                        }
                    } elseif ($limitedEndDate !== null 
                            && $limitedProductDaysRemaining < $daysRemaining) {
                        $status = 'Provisioned';
                        $remark = 'Active subscription already exists';
                        $this->updateProvisioningUserStatusByID($provisioningUser['ID'], $status, $remark);
                        $limitedDateDeactivated = date('Y-m-d H:i:s');
                        continue;
                    }
                }
            }

            // Create permission without proof
            $permission = new Permission([
                'entitlement_id' => (int)$saID,
                'released_at' => date('Y-m-d H:i:s'),
                'expired_at' => null,
                'limit' => 1,
                'is_active' => 1
            ]);

            $permission->save();

            $activationLib = new HubActivation([
                'accessCode' => '0000-0000-0000-0000',
                'terms' => 'true'
            ]);

            $activationLib->setActivationOwner($userID);
            $activationLib->setPermission($permission);
            $activationLib->activateInGo = false;
            $activationLib->setPurchaseType(HubActivation::PURCHASE_TYPE_PROVISION);
            $activationLib->setCreatedBy($createdBy);
            $result = $activationLib->provisionProductInGo(
                $provisioningUser['uID'],
                $provisioningUser['Type'],
                $provisioningUser['Email'],
                $limitedEndDate,
                $limitedDateDeactivated
            );

            $this->updateProvisioningUserStatusByID($provisioningUser['ID'], $result['action'], $result['message']);

            if (!$result['success']) {
                throw new UnexpectedValueException($remark);
            }

            if (!empty($result['meta'])) {
                //SB-14 modified by machua 20190110 to accommodate multiple hmIDs
                $hmMetaData = array_merge($hmMetaData, $result['meta']);
            }
        }

        if (!empty($hmMetaData)) {
            return $hmMetaData;
        }
    }

    public function getUserSubscription($userId, $saId)
    {
        $sql = 'SELECT ID FROM CupGoUserSubscription WHERE (UserID = ? AND SA_ID = ?) AND Active = "Y"';

        return $this->db->GetRow($sql, array($userId, $saId));
    }

    public function deactivateSubscription($userId, $usId)
    {
        $flag = true;
        $sql = 'UPDATE CupGoUserSubscription SET Active = "N", DateDeactivated = now() WHERE UserID = ? AND ID = ?';
        $this->db->Execute($sql, array($userId, $usId));

        if ($this->db->Affected_Rows('CupGoUserSubscription') > 0) {
            $this->updateUserStatusById(
                $userId,
                static::STATE_ADD_SUBSCRIPTION,
                $this->fileRecordID,
                static::STATE_ADD_SUBSCRIPTION,
                'Previous subscription deactivated'
            );

            $tabSql = 'UPDATE CupGoTabAccess SET Active = "N", DateDeactivated = now() WHERE UserID = ? AND US_ID = ?';
            $this->db->Execute($tabSql, array($userId, $usId));
            if ($this->db->Affected_Rows() > 0) {
                $this->updateUserStatusById(
                    $userId,
                    static::STATE_ADD_SUBSCRIPTION,
                    $this->fileRecordID,
                    static::STATE_ADD_SUBSCRIPTION,
                    'Previous subscription tab access deactivated'
                );
            } else {
                $this->updateUserStatusById(
                    $userId,
                    static::STATE_ADD_SUBSCRIPTION,
                    $this->fileRecordID,
                    static::STATE_ADD_SUBSCRIPTION_ERR,
                    'Previous subscription tab access deactivation error'
                );
                $flag = false;
            }
        } else {
            $this->updateUserStatusById(
                $userId,
                static::STATE_ADD_SUBSCRIPTION,
                $this->fileRecordID,
                static::STATE_ADD_SUBSCRIPTION_ERR,
                'Previous subscription deactivation error'
            );
            $flag = false;
        }

        return $flag;
    }

    public function addUserSubscription($userId, $saId, $sId)
    {
        $creator = new User();
        $flag = false;

        // Get the SubscriptionAvailability Detail
        $q = 'SELECT * FROM CupGoSubscriptionAvailability WHERE ID = ?';
        $row = $this->db->GetRow($q, array($saId));

        // Create new user subscription and get record ID.
        $latestUserSubId = $this->insertUserSubscription($saId, $userId, $creator->getUserID());
        if (!$latestUserSubId) {
            $this->updateUserStatusById(
                $userId,
                static::STATE_ADD_SUBSCRIPTION,
                $this->fileRecordID,
                static::STATE_SUBSCRIPTION_ERR,
                'Unable to create user subscription'
            );
        } else {
            $this->updateUserStatusById(
                $userId,
                static::STATE_ADD_SUBSCRIPTION,
                $this->fileRecordID,
                static::STATE_ADD_SUBSCRIPTION,
                'User subscription created'
            );
        }

        // Update end date for newly created user subscription.
        if (!$this->updateEndDate($latestUserSubId, $userId, $row)) {
            $this->updateUserStatusById(
                $userId,
                static::STATE_ADD_SUBSCRIPTION,
                $this->fileRecordID,
                static::STATE_SUBSCRIPTION_ERR,
                'End date setting'
            );
        } else {
            $this->updateUserStatusById(
                $userId,
                static::STATE_ADD_SUBSCRIPTION,
                $this->fileRecordID,
                static::STATE_ADD_SUBSCRIPTION,
                'End date set'
            );
        }

        // Update DaysRemaining of the newly inserted subscription
        if (!$this->updateDaysRemaining($latestUserSubId)) {
            $this->updateUserStatusById(
                $userId,
                static::STATE_ADD_SUBSCRIPTION,
                $this->fileRecordID,
                static::STATE_SUBSCRIPTION_ERR,
                'Days remaining setting'
            );
        } else {
            $this->updateUserStatusById(
                $userId,
                static::STATE_ADD_SUBSCRIPTION,
                $this->fileRecordID,
                static::STATE_ADD_SUBSCRIPTION,
                'Days remaining set'
            );
        }

        // Add user subscription tabs.
        if (!$this->addTabAccess($userId, $saId, $latestUserSubId, $sId)) {
            $this->updateUserStatusById(
                $userId,
                static::STATE_ADD_SUBSCRIPTION,
                $this->fileRecordID,
                static::STATE_SUBSCRIPTION_ERR,
                'Tab access setting'
            );
        } else {
            $this->updateUserStatusById(
                $userId,
                static::STATE_ADD_SUBSCRIPTION,
                $this->fileRecordID,
                static::STATE_ADD_SUBSCRIPTION,
                'Tab access given'
            );
        }

        if (!$this->updateTabAccessEndDate($latestUserSubId)) {
            $this->updateUserStatusById(
                $userId,
                static::STATE_ADD_SUBSCRIPTION,
                $this->fileRecordID,
                static::STATE_SUBSCRIPTION_ERR,
                'Tab access end date setting'
            );
        } else {
            $this->updateUserStatusById(
                $userId,
                static::STATE_ADD_SUBSCRIPTION,
                $this->fileRecordID,
                static::STATE_ADD_SUBSCRIPTION,
                'Tab access end date set'
            );
            $flag = true;
        }

        return $flag;
    }

    public function insertUserSubscription($saId, $userId, $creatorID)
    {
        $query = <<<'sql'
            INSERT INTO CupGoUserSubscription
            (UserID, SA_ID, S_ID, StartDate, EndDate, Duration, Active, PurchaseType, CreatedBy)
            (SELECT ?, ID, S_ID, StartDate, EndDate, Duration, "Y", "PROVISION", ?
                FROM CupGoSubscriptionAvailability WHERE ID = ?)
sql;
        $this->db->Execute($query, array($userId, $creatorID, $saId));

        return $this->db->Insert_ID('CupGoUserSubscription');
    }

    // ANZGO-3624 Modified by John Renzo Sunico, 02/08/2018
    public function updateEndDate($userSubsId, $userId, $userSubscription)
    {
        $params = array();
        $currentMonth = date('n');
        $currentYear = date('Y');
        $breakpoint = $userSubscription['EndOfYearBreakPoint'];
        $breakpointOffset = ((int)$userSubscription['EndOfYearOffset']) - 1;
        $breakpointOffset = ($breakpointOffset < 0) ? 0 : $breakpointOffset;

        switch ($userSubscription['Type']) {
            case 'duration':
                break;
            case 'end-of-year':
                if ($currentMonth <= $breakpoint) {
                    $endDate = ($currentYear + $breakpointOffset) . '-12-31 12:00';
                } else {
                    $endDate = ($currentYear + 1 + $breakpointOffset) . '-12-31 12:00';
                }
                break;
            case 'start-end':
                $endDate = date('Y-m-d H:i:s', strtotime($userSubscription['EndDate']));
                break;
            default:
                break;
        }

        if (strcmp($userSubscription['Type'], 'duration') === 0) {
            $sql = 'UPDATE CupGoUserSubscription SET EndDate = DATE_ADD(CreationDate, INTERVAL Duration  DAY) ';
            $sql .= 'WHERE ID = ? AND UserID = ? LIMIT 1;';
            $params[] = $userSubsId;
            $params[] = $userId;
        } else {
            $sql = 'UPDATE CupGoUserSubscription SET EndDate = ? WHERE ID = ? AND UserID = ? LIMIT 1';
            $params[] = $endDate;
            $params[] = $userSubsId;
            $params[] = $userId;
        }

        return $this->db->Execute($sql, $params);
    }

    public function updateDaysRemaining($userSubsId)
    {
        $sql = 'UPDATE CupGoUserSubscription SET `DaysRemaining` = DATEDIFF(EndDate, Now()) WHERE ID = ?';

        return $this->db->Execute($sql, array($userSubsId));
    }

    public function addTabAccess($userId, $saId, $userSubsId, $subsId)
    {
        $query = 'INSERT INTO CupGoTabAccess(UserID,TabID,S_ID,SA_ID,Active,US_ID)
        (SELECT ?,TabID,S_ID,?, "Y",? FROM CupGoSubscriptionTabs WHERE S_ID = ?)';

        return $this->db->Execute($query, array($userId, $saId, $userSubsId, $subsId));
    }

    public function updateTabAccessEndDate($userSubsId)
    {
        $sql = 'UPDATE CupGoTabAccess cgta JOIN CupGoUserSubscription cgus ON cgus.ID = cgta.US_ID ';
        $sql .= 'SET cgta.EndDate = cgus.EndDate WHERE us_id = ?';

        return $this->db->Execute($sql, array($userSubsId));
    }

    /**
     * ANZGO-3525 Modified by John Renzo S. Sunico October 23, 2017
     * Returns User object
     *
     * @param $provisioningUsersID
     * @return bool|null|User
     */
    public function getUserByProvisioningUsersID($provisioningUsersID)
    {
        $sql = <<<sql
            SELECT * FROM Users u
            INNER JOIN ProvisioningUsers pu ON u.uID = pu.uID
            WHERE pu.ID = ?
            LIMIT 1;
sql;
        $user = $this->db->GetRow($sql, array($provisioningUsersID));

        if ($user) {
            return User::getByUserID($user['uID']);
        }

        return false;

    }

    /**
     * ANZGO-3525 Modified by John Renzo S. Sunico October 23, 2017
     *
     * Returns the last ID from the last
     * query that was executed.
     */
    public function getLastInsertedID()
    {
        return $this->db->Insert_ID();
    }

    public function getAvailableRecord()
    {
        $query = "SELECT pf.ID, pf.FileID FROM ProvisioningFiles pf WHERE pf.is_migrated = 0 LIMIT 1";
        return $this->db->GetRow($query);
    }

    public function markRecordAsDone($id)
    {
        $query = "UPDATE ProvisioningFiles pf SET pf.is_migrated = ? WHERE pf.ID = ?";
        $this->db->Execute($query, [1, $id]);
    }

    // GCAP-530 Modified by Shane Camus 10/25/19
    public function markSuccessfulMigrationOfUser($ids)
    {
        $query = "UPDATE ProvisioningUsers SET in_gigya = 1 WHERE uID IN('" . implode("','", $ids) . "')";
        $this->db->Execute($query);
    }

    // GCAP-530 Modified by Shane Camus 11/06/19
    public function markUnsuccessfulMigrationOfUser($fileId)
    {
        $query = "UPDATE ProvisioningUsers SET in_gigya = 0 WHERE in_gigya IS NULL AND FileID = ?";
        $this->db->Execute($query, [$fileId]);
    }

    // GCAP-541 Campion added by machua/mtanada 20191004
    public function markLiteUsersInGigya($gigyaUsers)
    {
        $ids = [];
        foreach ($gigyaUsers as $gigyaUser) {
            foreach ($gigyaUser->data->systemIDs as $systemId) {
                if ($systemId->idType  === 'GO' || $systemId->idType  === 'go') {
                    $ids[] = $systemId->idValue;
                    break;
                }
            }
        }
        $query = "UPDATE ProvisioningUsers SET in_gigya = 1 WHERE uID IN('" . implode("','", $ids) . "')";
        $this->db->Execute($query);
    }

    // SB-626 added by mabrigos 20200708
    public function checkSelfRegisteredUsers($emails)
    {
        $query = "SELECT uEmail FROM Users WHERE uEmail IN ('" . implode("','", $emails) . "') ";
        $query .= "AND (uPassword IS NULL OR uPassword = '')";
        return $this->db->GetAll($query);
    }

}
