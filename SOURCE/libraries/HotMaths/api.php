<?php

/* This is the object helper for all HotMaths function.
 * @author gerardbalila
 * @date 2016-09-22
 */

define('TEST_API', "https://api-test.edjin.com");
define('PROD_API', "https://api.edjin.com");

define('DS_TEST_POINTER', "https://testscience.hotmaths.com.au/cambridgeLogin");
define('DE_TEST_POINTER', "https://testenglish.hotmaths.com.au/cambridgeLogin");

define('DS_PROD_POINTER',
    "https://dynamicscience.cambridge.edu.au/cambridgeLogin");
define('DE_PROD_POINTER',
    "https://dynamicenglish.cambridge.edu.au/cambridgeLogin");

class HotMathsApi
{
    const HTTP_METHOD_POST = 'post';
    const SEVERITY_NOTICE = 'notice';
    const STUDENT = 'STUDENT';
    const TEACHER = 'TEACHER';
    const MESSAGE = 'message';
    const SUCCESS = 'success';
    const ACTION = 'action';
    const ACCESS_CODE = 'accessCode';
    const FUNCTION_TYPE = 'function';
    const ID = 'ID';
    const USER_ID = 'userID';
    const AUTHORIZATION_TOKEN = 'authorizationToken';
    const TIMESTAMP = 'timestamp';
    const BRAND_CODES = 'brandCodes';
    const HTTP_FORBIDDEN = 403;

    private $url = TEST_API;
    private $deAccessLink = DE_TEST_POINTER;
    private $dsAccessLink = DS_TEST_POINTER;
    private $tokenUrl;
    private $hmDb;
    private $token;
    private $userId = false;
    // SB-424 added by jbernardez 20200128
    private $externalId = false;
    private $accessCode = false;
    private $saId = false;
    private $hmId = false;
    private $responseType;
    private $error = false;
    private $response = false;
    // ANZGO-3572 added by jbernardez 20171208
    private $endDate = false;

    // ANZGO-3721 added by Maryjes Tanada 20180516 for Teacher activating student product
    public $teacherProduct = null;
    public $limitedProduct = false;

    // HotMaths responses
    private $curlResponse;
    private $curlRespObj;

    // HotMaths objects
    public $hmProduct;
    public $hmUser;

    // TNG objects
    private $tngHmUser;

    // ANZGO-3654 Modified by Shane Camus, 04/24/2018
    public function __construct($params)
    {
        $this->hmDb = new HotMathsModel();
        // ANZGO-3891 added and modified by jdchavez 10/24/18
        Loader::library('FileLog/FileLogger');
        $this->extractParams($params);
        if ($this->error !== false) {
            return false;
        }

        $this->buildUrls();
    }

    private function buildUrls()
    {
        $environment = 'testing';

        // Check our environment. If we are in a development server, use the test HM API pointer.
        // Else, use the production HM API pointer.
        // ANZGO-3418, modified by James Bernardez, 2017/06/23
        // Added referrer UAT for UAT testing
        // ANZGO-3524, modified by jbernardez 20170915
        // moved from referer to serverName
        if (defined('PRODUCTION_MODE') && PRODUCTION_MODE) {
            $environment = 'production';
            $this->url = PROD_API;
            $this->dsAccessLink = DS_PROD_POINTER;
            $this->deAccessLink = DE_PROD_POINTER;
        }

        // Get the access token...
        $this->token = $this->hmDb->getAccessToken($environment);
        // ...and build the token URL parameter.
        $this->tokenUrl = "?access_token=" . $this->token;
    }

    // ANZGO-3654 Modified by Shane Camus, 04/24/2018
    private function extractParams($params)
    {
        // ANZGO-3630 Modified by Maryjes Tanada 02/12/2018
        // If HmID is passed then assign access code immediately
        if (isset($params['hmID'])) {
            $this->hmId = $params['hmID'];
            $this->accessCode = $params[static::ACCESS_CODE];
            return;
        }

        if (!isset($params['userId'])) {
            $this->buildError(false, "No user ID specified.");
            return;
        }

        if (!isset($params[static::ACCESS_CODE]) && !isset($params['hmProductId']) && !isset($params['saId'])) {
            $this->buildError(false,
                "No access code, HotMaths product ID, or subscription availability ID specified.");
            return;
        }

        if (!isset($params['response'])) {
            $this->buildError(false, "No response type specified.");
            return;
        }

        extract($params);
        $this->userId = $userId;
        if (isset($params[static::ACCESS_CODE])) {
            $this->accessCode = $accessCode;
        }
        if (isset($params['hmProductId'])) {
            $this->hmId = $hmProductId;
        }
        if (isset($params['saId'])) {
            $this->saId = $saId;
        }
        // SB-424 added by jbernardez 20200128
        if (isset($params['externalId'])) {
            $this->externalId = $externalId;
        } 
        $this->responseType = $response;
    }

    /**
     * ANZGO-3654 Modified by Shane Camus, 04/24/2018
     * General use cURL caller.
     *
     * @param string $url The URL to be used in the cURL call.
     * @param string $curl_action The type of action we want to perform. "POST"
     *     if we are posting. This is defaulted to "" or GET.
     * @return mixed The expected result from the cURL call in array form.
     *     False in failure.
     */
    private function getCurlResponse($url, $action = "", $data = false)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        if (strtolower($action) == static::HTTP_METHOD_POST) {
            // Append post details and set return data type.
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data)
                )
            );
        }

        $this->curlResponse = curl_exec($curl);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($this->curlResponse, 0, $headerSize);
        $body = substr($this->curlResponse, $headerSize);
        $this->curlRespObj = json_decode($body);
        curl_close($curl);

        $u = new User();
        // ANZGO-3899 Added by Shane Camus 10/19/18
        // ANZGO-3895 POC by John Renzo Sunico 10/19/18
        FileLogger::log(
            array(
                static::TIMESTAMP => date('r'),
                static::USER_ID => $u->getUserID(),
                'info' => 'HM API Function: ' . debug_backtrace()[1][static::FUNCTION_TYPE],
                'meta' => array(
                    'url' => $url,
                    'method' => (strtolower($action) == static::HTTP_METHOD_POST) ? 'POST' : 'GET',
                    'status' => explode(' ', $header)[1],
                    'requestData' => json_decode($data),
                    'response' => $this->curlRespObj
                )
            )
        );
    }

    /**
     * ANZGO-3654 Modified by Shane Camus, 04/24/2018
     * Builds the response.
     *
     * @param string $status
     * @param string $message
     * @param string $accessCode
     * @param string $action
     * @return
     */
    private function buildResponse($status, $message, $accessCode, $action)
    {
        $this->response = array(
            static::SUCCESS => $status,
            static::MESSAGE => $message,
            'accesscode' => $accessCode,
            static::ACTION => $action
        );

        $u = new User();
        // ANZGO-3899 Added by Shane Camus 10/23/18
        FileLogger::log(
            array(
                static::TIMESTAMP => date('r'),
                static::USER_ID => $u->getUserID(),
                'info' => 'HM API Function: ' . debug_backtrace()[1][static::FUNCTION_TYPE],
                'meta' => $this->response
            )
        );

        if (strtoupper($this->responseType) == 'JSON') {
            $this->response = json_encode($this->response);
        }
    }

    /**
     * ANZGO-3654 Modified by Shane Camus, 04/24/2018
     *
     * refer to RFC5425 (https://tools.ietf.org/html/rfc5424#page-11)
     * for more info on logger message severity
     */
    private function buildError(
        $status,
        $message,
        $statusCode = 400,
        $severity = 'error'
    ) {
        $u = new User();

        $this->error = array(
            static::SUCCESS => $status,
            static::MESSAGE => $message,
            'status' => $statusCode,
            'severity' => $severity
        );

        // ANZGO-3899 Added by Shane Camus 10/23/18
        FileLogger::log(
            array(
                static::TIMESTAMP => date('r'),
                static::USER_ID => $u->getUserID(),
                'info' => 'HM API Function: ' . debug_backtrace()[1][static::FUNCTION_TYPE],
                'meta' => $this->error
            )
        );

        if (strtoupper($this->responseType) == 'JSON') {
            $this->error = json_encode($this->error);
        }
    }

    public function getError()
    {
        return $this->error;
    }

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * This is the starting point of Hotmaths validation.
     * $params should have the TNG User ID and either access code or Hotmaths
     * product ID.
     *
     * @param bool $forceRenew , string $userType
     * @return boolean $status
     * ANZGO-3687 modified by Maryjes Tanada 04/10/2018 added user type params
     *     to get user with specific type
     */
    public function addHmSubscription($forceRenew = false, $userType = null)
    {
        $this->isHmProduct();
        if ((isset($this->curlRespObj->success) && $this->curlRespObj->success === false) || !$this->curlRespObj) {
            $this->buildResponse(false, $this->curlRespObj->message, '',
                'HotMaths activation');
            return false;
        } else {
            $this->hmProduct = $this->curlRespObj;
        }

        if (isset($userType)) {
            $this->getHmUserByType($userType);
        } else {
            $this->isHmUser();
        }

        if (isset($this->curlRespObj->success) && $this->curlRespObj->success === false) {
            $this->createHmUser();
            if (isset($this->curlRespObj->success) && $this->curlRespObj->success === false) {
                $this->buildResponse($this->curlRespObj->success,
                    $this->curlRespObj->message, '', '');
                return false;
            } else {
                $this->hmUser = $this->curlRespObj;
                // We get the user again because the return from the user creation
                // lacks some needed data for saving in TNG Hotmaths table.
                $this->userId = $this->hmUser->externalId;
                $this->isHmUser();
                $this->hmUser = $this->curlRespObj;
            }
        } else {
            $this->hmUser = $this->curlRespObj;
        }

        // ANZGO-3524 Modified by jbernardez 20170918
        // ANZGO-3622 Modfied by John Renzo Sunico 01/26/2018
        if ($forceRenew) {
            $this->forceRenewHmUser();
        }

        /*
        * ANZGO-3364 J.Tanada Nov. 29, 2017
        * HM: User check for Teacher activating a Student code (vice versa)
        * Prev: Check for user to HM product compatibility.
        */
        $hmCompatibilityMessage = $this->checkHmUserCompatibility(
            $this->hmProduct->subscriberType,
            $this->hmUser->subscriberType
        );

        // ANZGO-3721, modified by Maryjes Tanada 05/16/2018
        // to handle teacher activating/adding student product
        if ($hmCompatibilityMessage === 'Teacher to activate Student') {
            $this->teacherProduct = $this->hmProduct->teacherProductId;
            $this->getHmProduct($this->teacherProduct);
            $this->limitedProduct = true;
        } elseif (!empty($hmCompatibilityMessage) && $hmCompatibilityMessage !== 'Teacher to activate Student') {
            $this->buildError(false, $hmCompatibilityMessage,
                static::HTTP_FORBIDDEN, static::SEVERITY_NOTICE);
            return false;
        }

        $this->addHmProductToUser();
        if (isset($this->curlRespObj->success) && $this->curlRespObj->success === false) {
            $this->buildError(false, $this->curlRespObj->message);
            return false;
        }

        // At this point, products have been added to the user successfully.
        // So update our TNG tables.
        $tngHmUser = $this->isTngHotmathsUser();

        // ANZGO-3524 modified by jbernardez 20170918
        // force save everytime if forceRenew
        if ($tngHmUser <= 0) {
            if ($this->saveUserToTngHm()) {
                $this->buildResponse(true,
                    'HotMaths user added to TNG HotMaths table.', '', '');
                return true;
            } else {
                $this->buildError(false,
                    "Error in linking user to TNG HotMaths.");
                return false;
            }
        }
    }

    /**
     * HotMaths validation for Activation
     * $params should have the TNG User ID and either access code or Hotmaths
     * product ID.
     *
     * @param array $params
     * @param string $respType The kind of response the user wants from the
     *     API.
     * @return boolean $status
     */
    public function activationHmSubscription()
    {
        $this->isHmProduct();
        if ((isset($this->curlRespObj->success) && $this->curlRespObj->success === false) || !$this->curlRespObj) {
            $this->buildResponse(false, $this->curlRespObj->message, '',
                'HotMaths activation');

            return false;
        } else {
            $this->hmProduct = $this->curlRespObj;
        }

        $this->isHmUser();
        if (isset($this->curlRespObj->success) && $this->curlRespObj->success === false) {
            $this->createHmUser();
            if (isset($this->curlRespObj->success) && $this->curlRespObj->success === false) {
                $this->buildResponse($this->curlRespObj->success,
                    $this->curlRespObj->message, '', '');

                return false;
            }
            $this->hmUser = $this->curlRespObj;
        } else {
            $this->hmUser = $this->curlRespObj;
        }

        /*
         * ANZGO-3364 J.Tanada Nov. 29, 2017
         * HM: User check for Teacher activating a Student code (vice versa)
         * Prev: Check for user to HM product compatibility.
         */
        $hmCompatibilityMessage = $this->checkHmUserCompatibility(
            $this->hmProduct->subscriberType,
            $this->hmUser->subscriberType
        );
        // ANZGO-3721, modified by Maryjes Tanada 05/16/2018
        // to handle teacher activating/adding student product
        if ($hmCompatibilityMessage === 'Teacher to activate Student') {
            $this->teacherProduct = $this->hmProduct->teacherProductId;
            $this->getHmProduct($this->teacherProduct);
            $this->limitedProduct = true;
        } elseif (!empty($hmCompatibilityMessage) && $hmCompatibilityMessage !== 'Teacher to activate Student') {
            $this->buildError(false, $hmCompatibilityMessage,
                static::HTTP_FORBIDDEN, static::SEVERITY_NOTICE);

            return false;
        }
    }

    public function resumeActivationHmSubscription()
    {
        // ANZGO-3572 moved by jbernardez 20171208
        // moved here as we need to save the information on our user subscription
        // first so we can get the endDate
        $this->addHmProductToUser();
        if (isset($this->curlRespObj->success) && $this->curlRespObj->success === false) {
            $this->buildError(false, $this->curlRespObj->message);

            return false;
        }

        // At this point, products have been added to the user successfully.
        // So update our TNG tables.
        $tngHmUser = $this->isTngHotmathsUser();
        if ($tngHmUser <= 0) {
            if ($this->saveUserToTngHm()) {
                $this->buildResponse(true,
                    'HotMaths user added to TNG HotMaths table.', '', '');

                return true;
            } else {
                $this->buildError(false,
                    'Error in linking user to TNG HotMaths.');

                return false;
            }
        }

        return true;
    }

    /**
     * Check if user is an Edjin HotMaths user.
     *
     * @global User $u The C5 user.
     * @param string $accesscode
     * @return mixed JSON in success, boolean otherwise.
     */
    public function isHmUser()
    {
        // Build the URL and execute cURL call.
        $url = $this->url . "/api/user/external/" . $this->userId . $this->tokenUrl;
        $this->getCurlResponse($url);

        // ANZGO-3659 added by jbernardez 20180312
        return $this->curlRespObj;
    }

    /* ANZGO-3687 Added by Maryjes Tanada 04/10/2018
    * User type parameters, if set, meaning admin/cup staff, get specific Student/Teacher in HM
    */
    public function getHmUserByType($userType = null)
    {
        // Build the URL and execute cURL call.
        $url = $this->url . "/api/user/external/" . $this->userId . "/" . $userType . $this->tokenUrl;
        $this->getCurlResponse($url);
        return $this->curlRespObj;
    }

    /* ANZGO-3687 Added by Maryjes Tanada 04/10/2018
    * User type parameters, if set, meaning admin/cup staff, get specific Student/Teacher in HM
    */
    public function isHmUserByType($userType)
    {
        // Build the URL and execute cURL call.
        $url = $this->url . "/api/user/external/userExists/" . $this->userId . "/" . $userType . $this->tokenUrl;
        $this->getCurlResponse($url);
        return $this->curlRespObj;
    }

    /**
     * ANZGO-3524 modified by jbernardez 20170918
     * ANZGO-3622 Modfied by John Renzo Sunico 01/26/2018
     * Forces a renew on the authorizationToken
     */
    public function forceRenewHmUser()
    {
        if (!$this->hmProduct) {
            $this->getHmProduct();
        }

        $renewUrl = $this->url . "/api/user/external/renewAccessToken/" . $this->userId . $this->tokenUrl;
        $this->getCurlResponse($renewUrl);

        $this->isHmUser();
        $this->hmUser = $this->curlRespObj;

        if (!isset($this->hmUser->externalId)) {
            return false;
        }

        $brandCodes = $this->hmDb->getUserBrandCodes($this->hmUser->externalId);

        if (!$brandCodes) {
            $this->saveUserToTngHm();
        }

        foreach ($brandCodes as $brandCode) {
            $this->hmDb->insertUserToTngHm($this->hmUser, $brandCode);
        }

        return true;
    }

    /* ANZGO-3687 modified by Maryjes Tanada 04/10/2018
    * User type parameters, if set, meaning admin/cup staff, get specific Student/Teacher in HM
    */
    public function createHmUser($userType = null)
    {
        $u = User::getByUserID($this->userId);
        $ui = UserInfo::getByID($this->userId);

        $tempSuburb = $ui->getAttribute('uSuburb');
        $tempSchoolAddress = $ui->getAttribute('uSchoolAddress');

        $suburb = ($tempSuburb) ? $tempSuburb : "";
        $schoolAddress = ($tempSchoolAddress) ? $tempSchoolAddress : "";

        $group = "Student";
        foreach ($u->uGroups as $g) {
            if ($g == "Teacher") {
                $group = $g;
                break;
            }
        }
        if (isset($userType)) {
            $group = strtoupper($userType);
        }

        // Build user details for Edjin Hotmaths consumption.
        $userDetails = array(
            "accessToken" => "",
            "brandCode" => $this->hmProduct->brandCode,
            "password" => "",
            "schoolYear" => $this->hmProduct->schoolYear,
            "countryCode" => " ",
            "countryName" => " ",
            "email" => $ui->uEmail,
            "externalId" => $ui->uID,
            "firstName" => $ui->getAttribute('uFirstName'),
            "lastAccessedDate" => date('Y-m-d', $ui->uLastOnline),
            "lastName" => $ui->getAttribute('uLastName'),
            "postcode" => $ui->getAttribute('uPostcode'),
            "regionCode" => $suburb,
            "regionName" => $suburb,
            "schoolName" => $schoolAddress,
            "subscriberType" => strtoupper($group),
            // ANZGO-3687 Append usertype to username in HM, username is unique and admin/cupstaff needs 2 usertype
            "username" => ($userType ? $ui->uEmail . $userType : $ui->uEmail)
        );

        // Parse to json, send, and get response.
        $jsonDetails = json_encode($userDetails);
        $url = $this->url . "/api/user/createUser" . $this->tokenUrl;
        $this->getCurlResponse($url, static::HTTP_METHOD_POST, $jsonDetails);
    }

    public function getHmUser()
    {
        $url = $this->url . "/api/user/external/" . $this->userId . $this->tokenUrl;
        $this->getCurlResponse($url);
        $this->hmUser = $this->curlRespObj;
    }

    /**
     * SB-424 added by jbernardez 20200127
     * @return mixed JSON in success, boolean otherwise.
     */
    public function getHmUserExternalID()
    {
        $url = $this->url . '/api/user/' . $this->externalId . $this->tokenUrl;
        $this->getCurlResponse($url);
        
        return $this->curlRespObj;
    }

    /**
     * Save Hotmaths user in TNG Hotmaths table.
     * @param $curl_resp
     * @return boolean
     */
    public function saveUserToTngHm()
    {
        return $this->hmDb->insertUserToTngHm($this->hmUser,
            $this->hmProduct->brandCode);
    }

    // ANZGO-3572 added by jbernardez 20171208
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    public function addHmProductToUser()
    {
        // ANZGO-3721, modified by Maryjes Tanada 05/16/2018
        // adding limitedProduct params for HM reference Teacher activating/adding student product
        $temp = array(
            'limitedProduct' => $this->limitedProduct,
            'productId' => $this->hmProduct->productId,
            'userIds' => array($this->hmUser->userId)
        );

        // ANZGO-3572 added by jbernardez 20171207
        // addition of expiration date for recording on HM side
        // modified 20171214
        // only set this up when endDate is set, as this will cause
        // errors on other functionality which will not use expiryDate
        if ($this->endDate !== false) {
            $temp['productExpiryDate'] = $this->endDate;
        }

        $url = $this->url . "/api/user/addProductToUsers" . $this->tokenUrl;

        $this->getCurlResponse($url, static::HTTP_METHOD_POST,
            json_encode($temp));
    }

    /**
     * Gets the User record from the TNG Hotmaths table.
     * @param object $hm_user
     * @return mixed PDO result array or boolean in failure.
     */
    public function isTngHotmathsUser()
    {
        return $this->hmDb->getTngHotmathsUserCount(
            $this->hmUser->externalId,
            $this->hmProduct->brandCode,
            $this->hmProduct->schoolYear
        );
    }

    /**
     * A 2-step process to delete existing User subscription and to
     * reset a previously activated code.
     *
     * @param $accesscode
     */
    public function rollback_hm_accesscode($accesscode)
    {
        $deleteFlag = $this->hm_db->deleteUserSubscription($accesscode);
        $rollbackFlag = $this->hm_db->rollbackAccesscode($accesscode);
        $resp = array();
        if ($deleteFlag || $rollbackFlag) {
            $message = 'We are unable to give you access to HotMaths Resource.';
            $message .= 'Please contact our customer service for details';
            CupGoLogs::trackUser('Activate Code',
                'HM - Add User to HM Product : ' . $accesscode);
            $resp = $this->build_response(false, $message, $accesscode,
                'add_user_to_hm_product');
        }

        return $resp;
    }

    /**
     * Check if product is an Edjin Hotmaths product.
     *
     * @param string $accesscode
     * @return array
     *
     * GCAP-564 Modified by Shane Camus 11/18/19
     */
    public function isHmProduct()
    {
        if ($this->hmId) {
            $tngHmProd['HmID'] = $this->hmId;
        } elseif ($this->saId) {
            $tngHmProd = $this->hmDb->getTngHmProductBySAId($this->saId);
        }

        if (count($tngHmProd) <= 0 || ($tngHmProd['HmID'] == null || $tngHmProd['HmID'] == '')) {
            // Not an HM product.
            $messageNotHm = 'The product is not a TNG HotMaths product. ';
            $messageNotHm .= 'Please check access code or tab settings of this product.';
            $this->buildResponse(false, $messageNotHm, '',
                'HotMaths Subscription');

            return false;
        } else {
            // Verify if product is Edjin Hotmaths product.
            $url = $this->url . '/api/product/' . $tngHmProd['HmID'] . $this->tokenUrl;
            $this->getCurlResponse($url);

            return true;
        }
    }

    // ANZGO-3721, modified by Maryjes Tanada 05/16/2018
    // get teacherProduct id for teacher activating/adding student product
    public function getHmProduct($teacherProductId = null)
    {
        if (!$this->hmId) {
            $this->isHmProduct();
            $this->hmProduct = $this->curlRespObj;
            $this->hmId = $this->hmProduct->productId;
        }
        if ($teacherProductId) {
            $url = $this->url . '/api/product/' . $teacherProductId . $this->tokenUrl;
        } else {
            $url = $this->url . '/api/product/' . $this->hmId . $this->tokenUrl;
        }
        $this->getCurlResponse($url);
        $this->hmProduct = $this->curlRespObj;

        return $this->hmProduct;
    }

    /* ANZGO-3687 Added by Maryjes Tanada 04/10/2018
    * User type parameters, if set, meaning admin/cup staff, get specific Student/Teacher in HM
    * ANZGO-3721, modified by Maryjes Tanada 05/16/2018
    * get equivalent teacher product id when teacher activating/adding student product
    */
    public function getHmAccessLink($runAdd = false, $userType = null)
    {
        if ($this->teacherProduct) {
            $this->getHmProduct($this->teacherProduct);
        } else {
            $this->getHmProduct();
        }
        if (isset($userType)) {
            $this->getHmUserByType($userType);
        } else {
            $this->getHmUser();
        }

        if (isset($this->hmUser->success) && !$this->hmUser->success) {
            $this->createHmUser();
            if (isset($this->curlRespObj->success) && $this->curlRespObj->success === false) {
                $this->buildError($this->curlRespObj->success,
                    $this->curlRespObj->message);

                return false;
            } else {
                if (isset($userType)) {
                    $this->isHmUserByType($userType);
                } else {
                    $this->isHmUser();
                }
                $this->hmUser = $this->curlRespObj;
            }
        } else {
            $this->hmUser = $this->curlRespObj;
        }

        // ANZGO-3572 modified by jbernardez
        // we can actually skip parts in here as all activation of HM
        // now happens on Code Activation, Provisioning on My Resources,
        // and through CMS. this only makes it slower as we access everything again with HM
        // DO NOT REMOVE
        if ($runAdd) {
            $this->addHmProductToUser();
        }

        if (isset($this->curlRespObj->success) && $this->curlRespObj->success === false) {
            $this->buildError(false,
                $this->curlRespObj->message . " add product to user");

            return false;
        }

        // ANZGO-3418, added by James Bernardez, 2017/06/28
        $this->saveUserToTngHm();

        $this->tngHmUser = $this->hmDb->getTngHotmathsUser($this->userId,
            $this->hmProduct->brandCode);
        // Check if user has access to link.
        $compFlag = (strcmp($this->hmProduct->subscriberType,
                $this->hmUser->subscriberType) === 0);
        if ($compFlag === false) {
            $this->buildError(false, 'Subscriber type mismatch.',
                static::HTTP_FORBIDDEN, static::SEVERITY_NOTICE);

            return false;
        }

        /*
         * ANZGO-3501 Added by John Renzo Sunico
         * Added productId in parameters to redirect to
         * correct product.
         */
        // ANZGO-3659 added by jbernardez 20180309
        /* ANZGO-3687 modified by Maryjes Tanada 04/10/2018
         * User type parameters, if set, meaning admin/cup staff, get specific token by user type
         */
        if (isset($userType)) {
            $result = $this->hmDb->getRecentAuthorizationTokenByUserType($this->userId,
                $userType);
        } else {
            $result = $this->hmDb->getRecentAuthorizationToken($this->userId);
        }

        // SB-154 added by machua to compare the access tokens in HM and TNG
        $hotmathsID = (int)$result[static::ID];
        $tngAuthToken = $result[static::AUTHORIZATION_TOKEN];
        $hmAccessToken = $this->hmUser->accessToken;
        if ($tngAuthToken !== $hmAccessToken) {
            $this->hmDb->updateRecentAuthorizationToken($hotmathsID, $hmAccessToken);
            $tngAuthToken = $hmAccessToken;
        }

        if ($this->hmProduct->brandCode == "DYNAMICENGLISH") {
            $url = $this->deAccessLink
                . "?brandCode="
                . $this->tngHmUser[static::BRAND_CODES]
                . "&access_token="
                . $tngAuthToken
                . "&productId=" . $this->hmProduct->productId;
        } else {
            $url = $this->dsAccessLink
                . "?brandCode="
                . $this->tngHmUser[static::BRAND_CODES]
                . "&access_token="
                . $tngAuthToken
                . "&productId=" . $this->hmProduct->productId;
        }

        return $compFlag ? $url : $compFlag;
    }

    // ANZGO-3389, added by James Bernardez, 05/31/2017
    // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
    // barebones calls on product and user to create hmlink
    // ANZGO-3721, modified by Maryjes Tanada 05/16/2018
    // retrive equivalent teacher product id when teacher activating/adding student product
    public function getHmAccessLinkModified($teacherProduct = null)
    {
        if ($teacherProduct) {
            $this->getHmProduct($teacherProduct);
        } else {
            $this->getHmProduct();
        }
        $this->tngHmUser = $this->hmDb->getTngHotmathsUser($this->userId,
            $this->hmProduct->brandCode);

        // ANZGO-3521 added by jbernardez 20170919
        // ANZGO-3622 Modified by John Renzo Sunico 01/26/2018
        
        if (!$this->isAccessTokenValid()) {
            $this->forceRenewHmUser();
            $this->tngHmUser = $this->hmDb->getTngHotmathsUser($this->userId,
                $this->hmProduct->brandCode);
        }

        // ANZGO-3659 added by jbernardez 20180309
        $result = $this->hmDb->getRecentAuthorizationToken($this->userId);
        
        // SB-154 added by machua to compare the access tokens in HM and TNG
        $hotmathsID = (int)$result[static::ID];
        $tngAuthToken = $result[static::AUTHORIZATION_TOKEN];

        if ($this->hmUser === null) {
            $this->getHmUser();
        }

        $hmAccessToken = $this->hmUser->accessToken;
        if ($tngAuthToken !== $hmAccessToken) {
            $this->hmDb->updateRecentAuthorizationToken($hotmathsID, $hmAccessToken);
            $tngAuthToken = $hmAccessToken;
        }
        
        /*
         * ANZGO-3501 Added by John Renzo Sunico
         * Added productId in parameters to redirect to
         * correct product.
         */
        // ANZGO-3659 modified by jbernardez 20180308
        if ($this->hmProduct->brandCode == "DYNAMICENGLISH") {
            $url = $this->deAccessLink
                . "?brandCode="
                . $this->tngHmUser[static::BRAND_CODES]
                . "&access_token="
                . $tngAuthToken
                . "&productId=" . $this->hmProduct->productId;
        } else {
            $url = $this->dsAccessLink
                . "?brandCode="
                . $this->tngHmUser[static::BRAND_CODES]
                . "&access_token="
                . $tngAuthToken
                . "&productId=" . $this->hmProduct->productId;
        }

        return $url;
    }

    /*
     * ANZGO-3490 Added by John Renzo Sunico, Sept. 05, 2017
     * Validate API for HM
     */
    public function validateAccessCode()
    {
        $url = $this->url . '/api/activationCode/validate/' . $this->accessCode . $this->tokenUrl;
        $this->getCurlResponse($url);

        return $this->curlRespObj;
    }

    // ANZGO-3521, added by jbernardez, 20170919
    // ANZGO-3622 Modified by John Renzo Sunico 01/26/2018
    public function isAccessTokenValid()
    {
        return $this->hmDb->checkHMTokenValidity($this->userId);
    }

    // ANZGO-3554 Added by Shane Camus 10/26/17
    public function findAccessCodes()
    {
        $url = $this->url . '/api/activationCode/find/' . $this->accessCode . $this->tokenUrl;
        $this->getCurlResponse($url);

        $codes = array();
        foreach ($this->curlRespObj as $result) {
            $codes[] = $result->code;
        }

        return $codes;
    }

    /**
     * ANZGO-3525 Added by John Renzo S. Sunico October 23, 2017
     * Returns HotMaths Class Information using $classCode
     *
     * @param $classCode
     * @return mixed
     */
    public function getHotMathsClassByKey($classCode)
    {
        $findCodeByClassCodeAPI = $this->url . "/api/class/find/code/$classCode/" . $this->tokenUrl;
        $this->getCurlResponse($findCodeByClassCodeAPI);

        return $this->curlRespObj;
    }

    /**
     * ANZGO-3525 Added by John Renzo S. Sunico October 23, 2017
     * Uses HotMaths API /api/user/connectUsersWithClass
     * to add user to a class using classCode.
     *
     * @param $classCode
     * @return array
     */
    public function addUserToHotMathsClass($classCode)
    {
        $this->getHmUser();

        if (!isset($this->hmUser->userId)) {
            return array(
                'errors' => array(
                    'Error while adding user to class. User is not yet created in HotMaths.',
                    'Please make sure you add products before provisioning'
                )
            );
        }

        $addUserToClassAPI = $this->url . '/api/user/connectUsersWithClass' . $this->tokenUrl;
        $data = array(
            'userIds' => array($this->hmUser->userId),
            'classCode' => $classCode
        );
        $this->getCurlResponse($addUserToClassAPI, static::HTTP_METHOD_POST,
            json_encode($data));

        return $this->curlRespObj;
    }

    /**
     * ANZGO-3525 Added by John Renzo S. Sunico October 23, 2017
     * Returns list of unprocessed HM provisioned product of user
     * Done this since hmDB is private
     *
     * @param $userID
     * @param string $limit
     * @return mixed
     */
    public function getUnprocessedProvisionedHotMathsSubscription(
        $userID,
        $limit = '18446744073709551615'
    ) {
        return $this->hmDb->getUnprocessedProvisionedHotMathsSubscription($userID,
            $limit);
    }

    public function checkHmUserCompatibility($hmProductUserType, $hmUserType)
    {
        $messageHmCode = '';
        if ($hmProductUserType === static::STUDENT && $hmUserType === static::TEACHER) {
            // ANZGO-3721, modified by Maryjes Tanada 05/16/2018
            // Remove error message when teacher activating/adding student product and changed message code
            $messageHmCode = 'Teacher to activate Student';
        }
        if ($hmProductUserType === static::TEACHER && $hmUserType === static::STUDENT) {
            $messageHmCode = 'You are trying to activate a teacher product on a student account.';
            $messageHmCode .= 'You need a student product. <br>To purchase visit ';
            $messageHmCode .= '<a target="_blank" href="https://www.cambridge.edu.au/education">';
            $messageHmCode .= 'www.cambridge.edu.au/education</a> or contact your Education resource consultant.';
        }

        return $messageHmCode;
    }

    public function searchUser(array $parameters)
    {
        $searchUrl = $this->url . "/api/user/search/" . $this->tokenUrl;
        $this->getCurlResponse($searchUrl, static::HTTP_METHOD_POST,
            json_encode($parameters));

        return $this->curlRespObj;
    }

    // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
    public function removeProductToUser()
    {
        $removeProductUrl = $this->url . '/api/user/removeProductFromUsers' . $this->tokenUrl;
        $this->isHmProduct();
        if ((isset($this->curlRespObj->success) && $this->curlRespObj->success === false) || !$this->curlRespObj) {
            $this->buildError($this->curlRespObj->success,
                $this->curlRespObj->message);

            return false;
        }
        $this->hmProduct = $this->curlRespObj;

        $this->isHmUser();
        if (isset($this->curlRespObj->success) && $this->curlRespObj->success === false) {
            $this->buildError($this->curlRespObj->success,
                $this->curlRespObj->message);
            return false;
        }
        $this->hmUser = $this->curlRespObj;

        $data = [
            'productId' => $this->hmProduct->productId,
            'userIds' => [$this->hmUser->userId]
        ];
        $this->getCurlResponse($removeProductUrl, static::HTTP_METHOD_POST,
            json_encode($data));

        return isset($this->curlRespObj->success) ? $this->curlRespObj->success : false;
    }

    /* ANZGO-3687 Added by Maryjes Tanada 04/10/2018
     * Get teacher Product ID
     */
    public function getHmTeacherProduct()
    {
        $url = $this->url . "/api/product/teacherProduct/" . $this->hmId . $this->tokenUrl;
        $this->getCurlResponse($url);
        $this->hmProduct = $this->curlRespObj;

        return $this->hmProduct;
    }

}

class HotMathsModel
{
    private $db;

    public function __construct()
    {
        $this->db = Loader::db();

    }

    /**
     * Gets the access token to be used in HotMaths API.
     *
     * @param string $environment The current server environment. Can be
     *     'testing' or 'production'.
     * @return string
     */
    public function getAccessToken($environment)
    {
        $sql = "SELECT access_token FROM Hotmaths_API WHERE env = ?";
        $result = $this->db->GetRow($sql, array($environment));

        return $result['access_token'];
    }

    /**
     * Deletes any user HotMaths subscription
     *
     * @param string $accesscode
     * @return int
     */
    public function deleteUserSubscription($accesscode)
    {
        $sql = "DELETE FROM UserSubscription WHERE AccessCode = ? LIMIT 1";
        $this->db->Execute($sql, array($accesscode));

        return $this->db->Affected_Rows();
    }

    /**
     * Resets an accesscode.
     *
     * @param $accesscode
     * @return
     */
    public function rollbackAccesscode($accesscode)
    {
        $sql = "UPDATE AccessCodes SET UserID = NULL,DateActivated = NULL,IPAddress = NULL,UserAgent = NULL,
            Usable = 'Y',UsageCount = (UsageCount - 1) WHERE AccessCode = ? LIMIT 1";
        $this->db->Execute($sql, array($accesscode));

        return $this->db->Affected_Rows();
    }

    /**
     * @param $hmUser
     * @param $brandCode
     * @return
     */
    public function insertUserToTngHm($hmUser, $brandCode)
    {
        $sql = "INSERT INTO Hotmaths (UserID, authorizationToken, externalId, tokenExpiryDate, brandCodes,
              schoolYear, subscriberType, dateCreated) VALUES (?,?,?,?,?,?,?,?);";
        $paramArr = array(
            $hmUser->externalId,
            $hmUser->accessToken,
            $hmUser->userId,
            $hmUser->accessTokenExpiresIn,
            $brandCode,
            // We do not get the user's brandCode because it does not change despite adding new products.
            // So we use the product's brandCode. $hmUser->brandCode
            $hmUser->schoolYear,
            $hmUser->subscriberType,
            date('Y-m-d h:i:s')
        );
        $this->db->Execute($sql, $paramArr);

        return $this->db->Insert_ID('Hotmaths');
    }

    /**
     * @param $tngUserId
     * @param $brandCode
     * @param $schoolYear
     * @return mixed
     */
    public function getTngHotmathsUserCount($tngUserId, $brandCode, $schoolYear)
    {
        $sql = "SELECT COUNT(*) AS userCount FROM Hotmaths WHERE UserID = ? AND brandCodes = ? AND schoolYear = ?";
        $result = $this->db->GetRow($sql,
            array($tngUserId, $brandCode, $schoolYear));

        return $result['userCount'];
    }

    /**
     * @param $saId
     * @return array|bool
     */
    public function getTngHmProductBySAId($saId)
    {
        $sql = "SELECT `HmID` FROM `CupGoSubscriptionAvailability` WHERE `ID` = ?";

        return $this->db->GetRow($sql, array($saId));
    }

    /**
     * @param $batch_id
     * @return array|bool
     */
    public function is_accesscode_viable($batchId)
    {
        $sql = "SELECT EOL FROM AccessCodeBatch WHERE ID=?";

        return $this->db->GetRow($sql, array($batchId));
    }

    // ANZGO-2867

    /**
     * Check if there is a user type restriction on the tab.
     *
     * @param $tab_id
     * @return array|bool
     */
    public function check_user_type_restriction($tabId)
    {
        $sql = "SELECT UserTypeIDRestriction, ContentVisibility, ContentType
                    FROM CupGoTabs
                    WHERE ID = ?";

        return $this->db->GetRow($sql, array($tabId));
    }

    // ANZGO-2867

    /**
     * we can actually query the HotMath Directly if they just give 1 HM
     * access_token to all users - lets do that first then - lets change this
     * rule later when they complain
     *
     * @param $user_id
     * @return array|bool
     */
    public function check_hotmaths($userId)
    {
        $sql = "SELECT hm.*, ug.gID FROM Hotmaths hm
                JOIN Users u ON u.uID = hm.UserID
                JOIN UserGroups ug ON u.uID = ug.uID
                WHERE hm.UserID  = ?
                AND brandCodes = 'DYNAMICENGLISH'
                LIMIT 1";

        return $this->db->GetRow($sql, array($userId));
    }

    // ANZGO-2867

    /**
     * let's check on what tab we need to display the link
     *
     * @param $user_id
     * @param $tab_id
     * @return array|bool
     */
    public function check_tab_access($userId, $tabId)
    {
        $sql = "SELECT * FROM CupGoTabAccess WHERE UserID = ? AND TabID = ?";

        return $this->db->GetRow($sql, array($userId, $tabId));
    }

    // ANZGO-2902

    /**
     * @param $userId
     * @param $brandCode
     * @return array|bool
     */
    public function getTngHotmathsUser($userId, $brandCode)
    {
        // ANZGO-3521 modified by jbernardez 20170914
        // modified query to group by authorization token, this will make the latest
        // authorizationToken be first in row
        $sql = "SELECT * FROM Hotmaths WHERE brandCodes = ? AND UserID = ?
                GROUP BY authorizationToken ORDER BY ID DESC ";

        return $this->db->GetRow($sql, array($brandCode, $userId));
    }

    // ANZGO-3659 added by jbernardez 20180309
    // added function to get the most recent authorization token regardless of brandcode
    public function getRecentAuthorizationToken($userId)
    {
        $sql = "SELECT * FROM Hotmaths WHERE UserID = ?
                GROUP BY authorizationToken ORDER BY ID DESC ";

        return $this->db->GetRow($sql, array($userId));
    }

    /* ANZGO-3687 Added by Maryjes Tanada 04/10/2018
    * Get authorizationToken of specific user and user type
    */
    public function getRecentAuthorizationTokenByUserType($userId, $userType)
    {
        $sql = "SELECT * FROM Hotmaths WHERE UserID = ? AND subscriberType = ?
                GROUP BY authorizationToken ORDER BY ID DESC ";

        return $this->db->GetRow($sql, array($userId, $userType));
    }

    // ANZGO-2418 added by James Bernardez 2017/06/27

    /**
     * @param $userId
     * @param $saId
     * @return array|bool
     */
    public function getCupGoUserSubscriptionHMProvision($userId, $saId)
    {
        $sql = "SELECT * FROM CupGoUserSubscriptionHMProvision WHERE UserID = ? AND SA_ID = ?";

        return $this->db->GetRow($sql, array($userId, $saId));
    }

    public function getUnprocessedProvisionedHotMathsSubscription(
        $userId,
        $limit = '18446744073709551615'
    ) {
        $sql = "SELECT * FROM CupGoUserSubscriptionHMProvision WHERE UserID = ? AND HMActive IS NULL LIMIT $limit";

        return $this->db->GetAll($sql, array($userId));
    }

    // ANZGO-2418 added by James Bernardez 2017/06/27

    /**
     * @param $userId
     * @param $saId
     * @return
     */
    public function updateCupGoUserSubscriptionHMProvision($userId, $saId)
    {
        $sql = "UPDATE CupGoUserSubscriptionHMProvision SET HMActive = 1 WHERE UserID = ? AND SA_ID = ?";

        return $this->db->Execute($sql, array($userId, $saId));
    }

    // ANZGO-3521 added by jbernardez 20170919
    // ANZGO-3622 Modified by John Renzo Sunico 01/26/2018
    // ANZGO-3659 modified by jbernardez 20180307
    /**
     * This query queries Hotmaths and CupGoUserSubscription in order to find
     * the latest created authorizationToken, checks if the created
     * authorizationToken is expired, and returns false if expired Hotmaths has
     * a 1 year expiry on their authorizationTokens, so we just add 1 year to
     * the createdDate and verify it with today's date.
     *
     * modifying this check in validity, it should check validity regardless of
     * SA_ID or brancodes as every UserSubscription and brandcodes use the most
     * recent access token.
     *
     * modified the return value from true to row of result
     *
     * @param $userId
     * @param $brandCodes
     * @param $said
     * @return bool
     */
    public function checkHMTokenValidity($userId)
    {
        $sql = "SELECT HM.ID, HM.UserID, HM.authorizationToken, HM.brandCodes, HM.dateCreated, DATE_ADD(HM.dateCreated,
                INTERVAL 1 YEAR) AS dateCreatedPlusOneYear
                FROM Hotmaths HM
                WHERE HM.UserID = ?
                GROUP BY authorizationtoken
                ORDER BY HM.ID DESC
                LIMIT 1";
        $result = $this->db->GetRow($sql, array($userId, $brandCodes, $said));

        $dateNow = date('Y-m-d H:m:s');

        if (count($result) > 0) {
            if (strtotime($result['dateCreatedPlusOneYear']) < strtotime($dateNow)) {
                // if it enters here, it means it needs to be updated
                return false;
            }

            return $result;
        } else {
            return false;
        }
    }


    public function getUserBrandCodes($userID)
    {
        $sql = 'SELECT DISTINCT brandCodes FROM Hotmaths WHERE UserID = ? AND brandCodes <> \'\'';

        return array_column($this->db->GetAll($sql, $userID), 'brandCodes');
    }

    /** 
     * SB-154
     * @author machua
     * added 20190502
     * Update the TNG authorization token with the HM access token
     * @param int $hotmathsID
     * @param string $hmAccessToken
     * @return bool
     */
    public function updateRecentAuthorizationToken($hotmathsID, $hmAccessToken)
    {
        $sql = "UPDATE Hotmaths SET authorizationToken = ? WHERE ID = ?";
        return $this->db->Execute($sql, [$hmAccessToken, $hotmathsID]);
    }

}
