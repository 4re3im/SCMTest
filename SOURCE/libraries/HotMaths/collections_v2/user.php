<?php

/**
 * HOTMATHS API USER COLLECTION
 * ANZGO-3914 Added by Shane Camus 11/12/18
 */

Loader::library('HotMaths/apiv3');

class HMUser extends NewerHotMathsAPI
{
    /**
     * @param $userID
     * @return mixed
     */
    public function getUserByGoID($userID)
    {
        $url = $this->curlLink . '/api/user/external/' . $userID . $this->tokenURL;

        $this->requestHMAPI($url);

        return $this->getHMAPIResponse();
    }

    /**
     * @param $params
     * @param null $additionalUserDetails array ['classCode'] ['productActivations']
     * @return mixed
     */
    public function create($params, $additionalUserDetails = null)
    {
        try {
            $userDetails = $this->setRequiredUserDetails($params);
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => 'Missing required user detail: ' . $e->getMessage()
            );
        }

        if (!is_null($additionalUserDetails)) {
            $userDetails = array_merge($userDetails, $additionalUserDetails);
        }


        $url = $this->curlLink . '/api/user/createUser' . $this->tokenURL;
        $this->requestHMAPI($url, static::HTTP_METHOD_POST, json_encode($userDetails));

        return $this->getHMAPIResponse();
    }

    /**
     * @param $params
     * @return array | Exception
     */
    public function setRequiredUserDetails($params)
    {
        $missingUserDetail = [];

        if (!array_key_exists('email', $params)) {
            $missingUserDetail[] = 'email';
        }

        if (!array_key_exists('userID', $params)) {
            $missingUserDetail[] = 'userID';
        }

        if (!array_key_exists('firstName', $params)) {
            $missingUserDetail[] = 'firstName';
        }

        if (!array_key_exists('lastName', $params)) {
            $missingUserDetail[] = 'lastName';
        }

        if (!array_key_exists('countryCode', $params)) {
            $missingUserDetail[] = 'countryCode';
        }

        if (!array_key_exists('subscriberType', $params)) {
            $missingUserDetail[] = 'subscriberType';
        }

        if (count($missingUserDetail) === 0) {
           return array(
                'email' => $params['email'],
                'username' => $params['email'],
                'externalId' => $params['userID'],
                'firstName' => $params['firstName'],
                'lastName' => $params['lastName'],
                'countryCode' => $params['countryCode'],
                'subscriberType' => $params['subscriberType']
            );
        } else {
            throw new Exception(join(', ', $missingUserDetail));
        }
    }

    /**
     * @param $userIDs
     * @param array | int $productIDs 
     * @param null $additionalProductDetails array ['productExpiryDate'] ['limitedProduct'] ['productIds']
     * @return mixed
     */
    public function addProductToUser($userIDs, $productIDs, $additionalProductDetails = null)
    {
        $productDetails['userIds'] = $userIDs;

        if (gettype($userIDs) === 'integer') {
            $productDetails['userIds'] = array($userIDs);
        }

        $parameter = gettype($productIDs) === 'integer' ? 'productId' : 'productIds';
        $productDetails[$parameter] = $productIDs;

        if (!is_null($additionalProductDetails)) {
            $productDetails = array_merge($productDetails, $additionalProductDetails);
        }

        $url = $this->curlLink . '/api/user/addProductToUsers' . $this->tokenURL;
        $this->requestHMAPI($url, static::HTTP_METHOD_POST, json_encode($productDetails));

        return $this->getHMAPIResponse();
    }


    /**
     * @param $userID
     * @param $classId
     * @return mixed
     */
    public function addUsersToClass($classCode, $userIDs)
    {
        if (gettype($userIDs) === 'integer') {
            $userIDs = array($userIDs);
        }

        $requiredClassDetails = array(
            'classCode' => $classCode,
            'userIds' => $userIDs
        );

        $url = $this->curlLink . '/api/user/connectUsersWithClass' . $this->tokenURL;
        $this->requestHMAPI($url, static::HTTP_METHOD_POST, json_encode($requiredClassDetails));

        return $this->getHMAPIResponse();
    }

    /**
     * @param $userID
     * @param $classCode
     * @return mixed
     */
    public function addUserToClasses($classIDs, $userID)
    {
        if (gettype($classIDs) === 'integer') {
            $classIDs = array($classIDs);
        }

        $requiredClassDetails = array(
            'classIds' => $classIDs,
            'userId' => $userID
        );

        $url = $this->curlLink . '/api/user/updateUserClasses' . $this->tokenURL;
        $this->requestHMAPI($url, static::HTTP_METHOD_POST, json_encode($requiredClassDetails));

        return $this->getHMAPIResponse();
    }

    public function addUsersWithClass($classIDs, $userID)
    {
        if (gettype($classIDs) === 'integer') {
            $classIDs = array($classIDs);
        }

        $requiredClassDetails = array(
            'classIds' => $classIDs,
            'userId' => $userID
        );

        $url = $this->curlLink . '/api/user/updateUserClasses' . $this->tokenURL;
        $this->requestHMAPI($url, static::HTTP_METHOD_POST, json_encode($requiredClassDetails));

        return $this->getHMAPIResponse();
    }

    public function extendUsersProducts($userIDs, $productIDs, $additionalProductDetails = null)
    {

        $productDetails['userIds'] = $userIDs;
        $productDetails['productIds'] = $productIDs;

        if (!is_null($additionalProductDetails)) {
            $productDetails = array_merge($productDetails, $additionalProductDetails);
        }

        $url = $this->curlLink . '/api/user/extendUsersProducts' . $this->tokenURL;
        $this->requestHMAPI($url, static::HTTP_METHOD_POST, json_encode($productDetails));

        return $this->getHMAPIResponse();
    }
}
