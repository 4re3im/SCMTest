<?php

/**
 * HOTMATHS API USER COLLECTION
 * ANZGO-3914 Added by Shane Camus 11/12/18
 */

Loader::library('HotMaths/apiv2');

class HMUser extends NewHotMathsAPI
{
    protected $username;
    protected $email;
    protected $firstName;
    protected $lastName;
    protected $countryCode;
    protected $subscriberType;
    protected $uID;

    public function __construct($params, $customUserType = null, $isGlobalGoUser = false)
    {
        parent::__construct($params, $isGlobalGoUser);
        if ($isGlobalGoUser) {
            $this->setGlobalGoUserDetails($params);
        } else {
            $this->setUserDetails($customUserType);
        }
    }

    public function setUserDetails($customUserType)
    {
        $u = User::getByUserID($this->userID);
        $ui = UserInfo::getByID($this->userID);

        // Username checks for SubscriberType as Admin/CupStaff requires two users
        $this->username = isset($customUserType) ? $ui->uEmail . $customUserType : $ui->uEmail;
        $this->email = $ui->uEmail;
        $this->firstName = $ui->getAttribute('uFirstName');
        $this->lastName = $ui->getAttribute('uLastName');
        $this->countryCode = ' ';

        if (is_null($customUserType)) {
            $this->subscriberType = array_search('Teacher', $u->getUserGroups()) ? 'TEACHER' : 'STUDENT';
        } else {
            $this->subscriberType = strtoupper($customUserType);
        }
    }

    public function setGlobalGoUserDetails($provisioningUser)
    {
        $this->uID = $provisioningUser['user']['uID'];
        $this->username = $provisioningUser['user']['Email'];
        $this->email = $provisioningUser['user']['Email'];
        $this->firstName = $provisioningUser['user']['FirstName'];
        $this->lastName = $provisioningUser['user']['LastName'];
        $this->countryCode = ' ';
        $this->subscriberType = strtoupper($provisioningUser['user']['Type']);
    }

    /**
     * @param null $additionalUserDetails array ['classCode'] ['productActivations']
     * @return mixed
     */
    public function create($additionalUserDetails = null)
    {
        $userDetails = $requiredUserDetails = array(
            'email' => $this->email,
            'username' => $this->username,
            'externalId' => $this->userID,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'countryCode' => $this->countryCode,
            'subscriberType' => $this->subscriberType
        );

        if (!is_null($additionalUserDetails)) {
            $userDetails = array_merge($requiredUserDetails, $additionalUserDetails);
        }

        $url = $this->curlLink . '/api/user/createUser' . $this->tokenURL;
        $this->requestHMAPI($url, static::HTTP_METHOD_POST, json_encode($userDetails));

        return $this->getHMAPIResponse();
    }

    public function createGlobalGoHmUser($additionalUserDetails = null)
    {
        // Build user details for Edjin Hotmaths consumption.
        $userDetails = $requiredUserDetails = array(
            "countryCode" => ' ',
            "email" => $this->email,
            "userUuid" => $this->uID,
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "subscriberType" => $this->subscriberType,
            "username" => $this->email
        );

        if (!is_null($additionalUserDetails)) {
            $userDetails = array_merge($requiredUserDetails, $additionalUserDetails);
        }

        // Parse to json, send, and get response.
        $jsonDetails = json_encode($userDetails);
        $url = $this->curlLink . '/api/user/createUser' . $this->tokenURL;

        $this->requestHMAPI($url, static::HTTP_METHOD_POST, $jsonDetails);

        return $this->getHMAPIResponse();
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        $url = $this->curlLink . '/api/user/external/' . $this->userID . $this->tokenURL;

        $this->requestHMAPI($url);

        return $this->getHMAPIResponse();
    }

    public function getUserByUsername()
    {
        $url = $this->curlLink . '/api/user/username/' . $this->tokenURL;

        $data = array(
            "username" => $this->username
        );
        $this->requestHMAPI($url, '', $data);

        return $this->getHMAPIResponse();
    }

    /**
     * @param $userID
     * @param $productID
     * @param $additionalProductDetails array ['productExpiryDate'] ['limitedProduct'] ['productIds']
     * @return mixed
     */
    public function subscribeProductToUser($userID, $productID, $additionalProductDetails)
    {
        $productDetails = $requiredProductDetails = array(
            'productId' => $productID,
            'userIds' => array($userID)
        );

        if (!is_null($additionalProductDetails)) {
            $productDetails = array_merge($requiredProductDetails, $additionalProductDetails);
        }

        $url = $this->curlLink . '/api/user/addProductToUsers' . $this->tokenURL;
        $this->requestHMAPI($url, static::HTTP_METHOD_POST, json_encode($productDetails));

        return $this->getHMAPIResponse();
    }

    /**
     * @param $userID
     * @param $classCode
     * @return mixed
     */
    public function addUserToClass($userID, $classCode)
    {
        $requiredClassDetails = array(
            'classCode' => $classCode,
            'userIds' => array($userID)
        );

        $url = $this->curlLink . '/api/user/connectUsersWithClass' . $this->tokenURL;
        $this->requestHMAPI($url, static::HTTP_METHOD_POST, json_encode($requiredClassDetails));

        return $this->getHMAPIResponse();
    }

    /**
     * SB-660 Added by mtanada 20200729
     * @param $userID
     * @param $schoolId
     * @return mixed
     */
    public function addUserToSchool($userID, $schoolId)
    {
        $requiredSchoolDetails = array(
            'schoolId' => $schoolId,
            'userIds' => array($userID)
        );

        $url = $this->curlLink . '/api/school/addUsersToSchool' . $this->tokenURL;
        $this->requestHMAPI($url, static::HTTP_METHOD_POST, json_encode($requiredSchoolDetails));

        return $this->getHMAPIResponse();
    }

    /**
     * SB-675 Added by mtanada 20200812
     * SB-696 modified by mtanada 20201008
     * @param array $classNames
     * @param integer $schoolId
     * @return mixed
     */
    public function getClassCode($classNames, $schoolId)
    {
        $result = [];

        if (is_null($schoolId)) {
            return null;
        }

        foreach ($classNames as $brandCode => $name) {
            if (is_null($name)) {
                continue;
            }
            // Create class else use existing class
            $classInfo = $this->createClassByName($brandCode, $name, $schoolId);
            $result[]  = $classInfo->classCode;
        }
        return $result;
    }

    /**
     * SB-675 Added by mtanada 20200814
     * @param integer $userID
     * @param array $classCodes
     * @return mixed
     */
    public function addUserToClasses($userID, $classCodes)
    {
        $requiredClassDetails = array(
            'classCodes' => $classCodes,
            'userId'     => $userID
        );

        $url = $this->curlLink . '/api/user/connectUserWithClasses' . $this->tokenURL;
        $this->requestHMAPI($url, static::HTTP_METHOD_POST, json_encode($requiredClassDetails));

        return $this->getHMAPIResponse();
    }
}
