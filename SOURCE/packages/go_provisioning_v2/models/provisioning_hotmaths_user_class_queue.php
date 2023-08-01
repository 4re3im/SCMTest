<?php

/**
 * ANZGO-3525 Modified by John Renzo S. Sunico October 23, 2017
 * Queueing system for adding HotMaths classes to users
 */

class ProvisioningHotMathsUserClassesQueue
{
    private $db;
    private $queue;
    private $api;
    private $fileID;
    private $provisioning;
    private $hmModel;
    private $processLimit = '18446744073709551615';

    /**
     * Initializes database handler
     * @param ProvisioningModel $provisioningModel
     */
    public function __construct(ProvisioningModel $provisioningModel)
    {
        Loader::library('HotMaths/collections_v2/user');
        Loader::library('HotMaths/model');
        $this->db = Loader::db();
        $this->provisioning = $provisioningModel;
        $this->hmModel = new NewHotMathsModel();
    }

    /**
     * Takes a comma separated list of class codes and add
     * them to queue for later processing
     *
     * @param $userProvisioningID
     * @param $classKeys
     * @param $fileID
     * @return mixed
     */
    public function addMultipleHotMathsUserClassToQueue($userProvisioningID, $classKeys, $fileID)
    {
        if (!$classKeys) {
            return false;
        }

        $classCodes = explode(',', trim($classKeys));

        foreach ($classCodes as $classKey) {
            $query = <<<sql
                INSERT INTO `ProvisioningHotMathsUserClassesQueue` (`ProvisioningUsersID`, `classCode`, `fileID`)
                SELECT * FROM (SELECT ?, ?, ?) AS tmp
                WHERE NOT EXISTS(
                    SELECT id FROM ProvisioningHotMathsUserClassesQueue WHERE ProvisioningUsersID = ?
                    AND classCode = ?
                    AND fileID = ?
                );
sql;
            $this->db->Execute($query, array(
                $userProvisioningID,
                $classKey,
                $fileID,
                $userProvisioningID,
                $classKey,
                $fileID
            ));
        }
    }

    /**
     * Returns list of unprocessed items in queue
     *
     * @param $fileID
     * @return mixed
     */
    public function getUnprocessedUserClassFromQueueByFileID($fileID)
    {
        $limit = intval($this->processLimit);

        $sql = <<<sql
            SELECT * FROM ProvisioningHotMathsUserClassesQueue
            WHERE fileID = ? AND completed = 0
            LIMIT $limit;
sql;
        $this->queue = $this->db->GetAll($sql, array($fileID));
        $this->fileID = $fileID;
        return $this->queue;
    }

    public function getUnprocessedUserClassFromQueueByProvisioningUserID($provisioningUserID)
    {
        $limit = intval($this->processLimit);

        $sql = <<<sql
            SELECT * FROM ProvisioningHotMathsUserClassesQueue
            WHERE provisioningUsersID = ? AND completed = 0
            LIMIT $limit;
sql;
        $this->queue = $this->db->GetAll($sql, array($provisioningUserID));
        return $this->queue;
    }

    /**
     * ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
     * Processes the contents of queue property which is set by
     * the dependent method.
     *
     * @depends getUnprocessedUserClassFromQueueByFileID
     * @return int
     */
    public function processQueue()
    {
        foreach ($this->queue as $item) {
            $provisioningUserId = $item['provisioningUsersID'];
            $provisionUserInfo = $this->provisioning->getProvisioningUserByID($provisioningUserId);
            $classCode = $item['classCode'];

            $user = $this->provisioning->getUserByProvisioningUsersID($provisioningUserId);

            if (!$user) {
                $this->markQueueItemCompleted($item['id']);
                continue;
            }

            if (!$this->createHotMathsUserIfNotExist($user)) {
                $this->provisioning->updateProvisionedUsersRemarks(
                    $provisioningUserId,
                    $provisionUserInfo['Remarks'] . ". Unable to create HotMaths user."
                );
                $this->markQueueItemCompleted($item['id']);
                continue;
            }

            $response = $this->addUserToClassByClassCode($user, $classCode);

            if (isset($response->success) && $response->success) {
                $this->provisioning->updateProvisioningUserStatusByID(
                    $provisioningUserId,
                    'AddedClass',
                    "User has been added to class $classCode."
                );
            } else {
                $this->provisioning->updateProvisioningUserStatusByID(
                    $provisioningUserId,
                    'AddClassError',
                    $response->message
                );
            }

            $this->markQueueItemCompleted($item['id']);
        }

        return count($this->queue);
    }

    /**
     * Adds HotMaths User to HotMaths class
     *
     * @depends on HotMaths API Library
     * @param User $user
     * @param $classCode
     * @return mixed
     */
    public function addUserToClassByClassCode(User $user, $classCode)
    {
        $this->setHotMathsAPI($user->getUserID(), '', '', 'JSON');
        return $this->api->addUserToHotMathsClass($classCode);
    }

    /**
     * Creates HotMathsUser
     * Then save user details to database
     *
     * @param User $user
     * @return bool|stdClass
     */
    public function createHotMathsUserIfNotExist(User $user)
    {
        $this->setHotMathsAPI($user->getUserID());
        $this->api->createHmUser();
        $this->api->getHmUser();

        if (isset($this->api->hmUser->userId)) {
            $this->api->saveUserToTngHm();
            return $this->api->hmUser;
        }

        return false;
    }

    /**
     * Mark item in queue as complete
     *
     * @param $queueID
     * @return RecordSet
     */
    public function markQueueItemCompleted($queueID)
    {
        $sql = 'UPDATE ProvisioningHotMathsUserClassesQueue SET completed = 1 WHERE id = ?';
        return $this->db->Execute($sql, array($queueID));
    }

    /**
     * @param string $userID
     * @param string $hmID
     * @param string $responseType
     * @param null $customUserType
     */
    public function setHotMathsAPI($userID = '', $hmID = '', $responseType = '', $customUserType = null)
    {
        $this->api = new HMUser(
            array(
                'userID' => $userID,
                'hmProductID' => $hmID,
                'responseType' => $responseType
            ),
            $customUserType
        );
    }

    /**
     * @param $userID
     * @param null $classCode
     * @param null $products
     * @return mixed
     */
    public function provisionUserInHotMaths($userID, $classCode = null, $products = null)
    {
        $additionalUserDetails = $this->prepareHMParameters($classCode, $products);

        $this->setHotMathsAPI($userID, '', 'JSON');

        return $this->api->create($additionalUserDetails);
    }

    /**
     * @param $email
     * @param $products
     * @return bool|null
     */
    public function provisionSubscriptionInHotMaths($email, $products)
    {
        $hmUser = $this->api->getUser();
        if ($email !== $hmUser->username) {
            return false;
        } else {
            $result = null;
            foreach ($products as $product) {
                $productID = $product['productId'];
                unset($product['productId']);
                $result = $this->api->subscribeProductToUser($hmUser->userId, $productID, $product);

                if (isset($result->success) && !$result->success) {
                    return $result;
                }
            }
            return $result;
        }
    }

    /**
     * @param $email
     * @param $classCode
     * @return bool|null
     */
    public function provisionUserToClassCodeInHotMaths($email, $classCode)
    {
        $hmUser = $this->api->getUser();
        if ($email !== $hmUser->username) {
            return false;
        } else {
            return $this->api->addUserToClass($hmUser->userId, $classCode);
        }
    }

    /**
     * @param $classCode
     * @param $products
     * @return array
     */
    public function prepareHMParameters($classCode, $products)
    {
        $additionalUserDetails = array();

        if (!is_null($classCode)) {
            $additionalUserDetails['classCode'] = $classCode;
        }

        if (!is_null($products)) {
            $additionalUserDetails['productActivations'] = $products;
        }

        return $additionalUserDetails;
    }

    /**
     * @param $user
     */
    public function storeTokens($user)
    {
        if (count($user->products) > 0) {
            foreach ($user->products as $product) {
                $this->hmModel->storeAuthorizationTokenPerUser(
                    $user->externalId,
                    $user->accessToken,
                    $user->userId,
                    $user->accessTokenExpiresIn,
                    $product->brandCode,
                    $product->schoolYear,
                    $product->subscriberType
                );
            }
        } else {
            $this->hmModel->storeAuthorizationTokenPerUser(
                $user->externalId,
                $user->accessToken,
                $user->userId,
                $user->accessTokenExpiresIn,
                $user->brandCode
            );
        }
    }

    /**
     * Returns total number of items in queue of specific
     * provisioning fileID.
     *
     * @param $fileID
     * @return array|bool
     */
    public function getTotalNumberInQueuePerFileID($fileID)
    {
        return $this->db->GetRow(
            'SELECT COUNT(*) c FROM ProvisioningHotMathsUserClassesQueue WHERE fileID = ?',
            array($fileID)
        );
    }

    /**
     * Returns total number of remaining items in queue of
     * specific provisioning fileID
     * @param $fileID
     * @return array|bool
     */
    public function getTotalRemainingItemsInQueuePerFileID($fileID)
    {
        return $this->db->GetRow(
            'SELECT COUNT(*) c FROM ProvisioningHotMathsUserClassesQueue WHERE fileID = ? AND completed = 0',
            array($fileID)
        );
    }

    /**
     * Sets the limit process
     *
     * @param $limit
     */
    public function setProcessLimit($limit)
    {
        $this->processLimit = $limit;
    }

    /**
     * SB-71 added by jbernardez 20190221
     * just returns the HM User as this is needed for saving tokens
     *
     * @return object hmUser
     */
    public function getHmUserOnly() 
    {
        return $this->api->getUser();
    }
}
