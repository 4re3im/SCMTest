<?php
/**
 * Created by PhpStorm.
 * User: jsunico
 * Date: 10/12/2018
 * Time: 1:41 PM
 */
require_once DIR_BASE . '/vendor/autoload.php';
Loader::library('gigya/GSSDK');
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class BaseGigya
{
    private $apiKey = GIGYA_API_KEY;
    private $secretKey = GIGYA_SECRET_KEY;
    private $userKey = GIGYA_USER_KEY;

    public function __construct()
    {
        $this->log = new Logger('Provisioning');
        $this->log->pushHandler(
            new StreamHandler('logs/provisioning.' . date("Y-m-d", time()) .'.log', Logger::INFO)
        );
    }

    /**
     * Handles Gigya Response
     *
     * @param $response GSResponse
     * @return bool|GSResponse
     */
    public function handleResponse($response)
    {
        if ($response->getErrorCode() === 0) {
            return $response;
        }
        return false;
    }

    /**
     * Returns new request with credentials pre-populated.
     *
     * @param $method
     * @param null $params
     * @param bool $useHttps
     * @return GSRequest
     */
    public function newRequest($method, $params = null, $useHttps = true)
    {
        $request = new GSRequest(
            $this->apiKey,
            $this->secretKey,
            $method,
            $params,
            $useHttps,
            $this->userKey
        );
        $request->setAPIDomain(GIGYA_DATA_CENTER_DOMAIN);

        return $request;
    }

    public function setAPIKey($key)
    {
        $this->apiKey = $key;
    }

    public function setSecretKey($key)
    {
        $this->secretKey = $key;
    }

    public function setUserKey($key)
    {
        $this->userKey = $key;
    }
}
