<?php

/**
 * HOTMATHS API v3.0
 * ANZGO-3914 Added by Shane Camus 11/12/18
 */

define('TEST_API', "https://api-test.edjin.com");
define('DS_TEST_POINTER', "https://testscience.hotmaths.com.au/cambridgeLogin");
define('DE_TEST_POINTER', "https://testenglish.hotmaths.com.au/cambridgeLogin");

define('PROD_API', "https://api.edjin.com");
define('DS_PROD_POINTER', "https://dynamicscience.cambridge.edu.au/cambridgeLogin");
define('DE_PROD_POINTER', "https://dynamicenglish.cambridge.edu.au/cambridgeLogin");


class NewerHotMathsAPI
{
    const HTTP_METHOD_POST = 'post';
    const MESSAGE = 'message';
    const SUCCESS = 'success';
    const ACTION = 'action';
    const ACCESS_CODE = 'accessCode';
    const HM_PROD_ID = 'hmProductID';
    const USER_ID = 'userID';

    private $error;
    private $response;
    private $curlResponseObject;
    private $model;
    protected $curlLink = TEST_API;
    protected $deAccessLink = DE_TEST_POINTER;
    protected $dsAccessLink = DS_TEST_POINTER;
    protected $tokenURL;
    protected $userID;
    protected $hmID;
    protected $saID;
    protected $accessCode;
    protected $responseType = 'STRING';

    /**
     * NewHotMathsAPI constructor.
     * @param $params
     */
    public function __construct()
    {
        Loader::library('HotMaths/model');
        Loader::library('FileLog/FileLogger');

        $this->setObjectParameters();
    }


    /**
     * @param $params
     */
    private function setObjectParameters()
    {
        $this->model = new NewHotMathsModel();
        $token = $this->getToken($this->getEnvironment());
        $this->setURLs($token, $this->getEnvironment());
    }

    /**
     * @return string
     */
    private function getEnvironment()
    {
        return defined('PRODUCTION_MODE') && PRODUCTION_MODE ? 'production' : 'testing';
    }

    /**
     * @return mixed
     */
    private function getToken($environment)
    {
        return $this->model->getAccessToken($environment);
    }

    /**
     * @param $token
     */
    private function setURLs($token, $environment)
    {
        if ($environment === 'production') {
            $this->curlLink = PROD_API;
            $this->dsAccessLink = DS_PROD_POINTER;
            $this->deAccessLink = DE_PROD_POINTER;
        }

        $this->tokenURL = "?access_token=$token";
    }

    /**
     * @param $message
     * @param int $statusCode
     * @param string $severity
     */
    protected function setError($message, $statusCode = 400, $severity = 'error')
    {
        $this->error = array(
            static::SUCCESS => false,
            static::MESSAGE => $message,
            'status' => $statusCode,
            'severity' => $severity
        );

        $this->log($this->error);

        if (strtoupper($this->responseType) == 'JSON') {
            $this->error = json_encode($this->error);
        }

    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param $status
     * @param $message
     * @param $action
     */
    protected function setCustomResponse($status, $message, $action)
    {
        $this->response = array(
            static::SUCCESS => $status,
            static::MESSAGE => $message,
            static::ACTION => $action
        );

        $this->log($this->response);

        if (strtoupper($this->responseType) === 'JSON') {
            $this->response = json_encode($this->response);
        }
    }

    /**
     * @return mixed
     */
    public function getCustomResponse()
    {
        return $this->response;
    }

    /**
     * @param $url
     * @param string $action
     * @param bool $data
     * @return mixed
     */
    protected function requestHMAPI($url, $action = '', $data = false)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        if (strtolower($action) === static::HTTP_METHOD_POST) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data)
                )
            );
        }

        $curlResponse = curl_exec($curl);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($curlResponse, 0, $headerSize);
        $body = substr($curlResponse, $headerSize);
        $this->curlResponseObject = json_decode($body);
        curl_close($curl);

        $this->log(
            array(
                'url' => $url,
                'method' => (strtolower($action) == static::HTTP_METHOD_POST) ? 'POST' : 'GET',
                'status' => explode(' ', $header)[1],
                'requestData' => json_decode($data),
                'response' => $this->curlResponseObject
            )
        );

        return $this->curlResponseObject;
    }

    /**
     * @return mixed
     */
    protected function getHMAPIResponse()
    {
        return $this->curlResponseObject;
    }

    /**
     * @param $meta
     */
    private function log($meta)
    {
        $u = new User();

        FileLogger::log(
            array(
                'timestamp' => date('r'),
                static::USER_ID => $u->getUserID(),
                'info' => 'HM API Function: ' . debug_backtrace()[1]['function'],
                'meta' => $meta
            )
        );
    }
}
