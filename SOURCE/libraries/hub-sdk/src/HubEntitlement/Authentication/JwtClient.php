<?php

/**
 * JWT Client
 * Authentication handler for Hub
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Authentication;

use HubEntitlement\Configuration\ConfigurationManager as Config;
use HubEntitlement\Session\SessionManager as Session;

use BernardoSilva\JWTAPIClient\APIClient;
use BernardoSilva\JWTAPIClient\AccessTokenCredentials;
use Psr\Http\Message\ResponseInterface;

class JwtClient
{
    /**
     * Base URL of GlobalHub
     * Value should have trailing /
     *
     * @var string
     */
    private $baseUrl;

    /**
     * Origin url of the client
     * Serve as username
     *
     * @var string
     */
    private $originUrl;

    /**
     * Secret key of the client
     * Serve as password
     *
     * @var string
     */
    private $secretKey;

    /**
     * Request handler
     * @var APIClient
     */
    private $client;

    const GENERATE_URL = '/v2/token/';
    const REFRESH_URL = '/v2/token/refresh/';

    /**
     * JwtClient
     *
     * Get config from .env file;
     * Setup credentials for future requests
     */
    public function __construct()
    {
        $this->baseUrl = Config::get('PEAS_BASE_URL');
        $this->originUrl = Config::get('HUB_APP_ORIGIN_URL');
        $this->secretKey = Config::get('HUB_APP_SECRET_KEY');
    }

    /**
     * Generate JWT token using
     *  - origin_url
     *  - secret_key
     *
     * @return array
     */
    public function generateCredentials()
    {
        $options = [
            'verify' => false,
            'json' => [
                'origin_url' => $this->originUrl,
                'secret_key' => $this->secretKey
            ]
        ];

        $response = $this->client->post(static::GENERATE_URL, $options);
        return $this->parseResponse($response);
    }

    /**
     * Generate new JWT Token using
     *   - refresh token
     *
     * @param string $refreshToken
     * @return array
     */
    public function refreshCredentials($refreshToken)
    {
        $options = [
            'verify' => false,
            'json' => [
                'refresh_token' => $refreshToken
            ]
        ];

        $response = $this->client->post(static::REFRESH_URL, $options);
        return $this->parseResponse($response);
    }

    /**
     * Sets client credential using instance of
     * BernardoSilva\JWTAPIClient\AccessTokenCredentials
     *
     * @param $token string
     */
    private function setClientToken($token)
    {
        $credentials = new AccessTokenCredentials($token);
        $this->client->setCredentials($credentials);
    }

    /**
     * Generate or Refresh client credentials
     */
    private function setClientCredentials()
    {
        $token = Session::get(Session::SESSION_JWT_KEY);
        $refreshToken = Session::get(Session::SESSION_JWT_REFRESH_TOKEN);

        $jwtToken = null;
        if (!$token) {
            $tokenInfo = $this->generateCredentials();
            $jwtToken = $tokenInfo['data']['token'];
            $refreshToken = $tokenInfo['data']['refreshToken'];
            Session::setJwt($jwtToken, $refreshToken);
        } elseif ($token[Session::KEY_JWT_EXPIRY] < time()) {
            $tokenInfo = $this->refreshCredentials($refreshToken);
            $jwtToken = $tokenInfo['data']['token'];
            Session::setJwt($jwtToken);
        } else {
            $jwtToken = $token[Session::KEY_JWT_TOKEN];
        }

        $this->setNewClient();
        $this->setClientToken($jwtToken);
    }

    /**
     * Generate new instance of client
     */
    private function setNewClient()
    {
        $this->client = new APIClient($this->baseUrl);
    }

    /** Returns client handler */
    public function getService()
    {
        if (!$this->client) {
            $this->setNewClient();
            $this->setClientCredentials();
        }

        return $this->client;
    }

    /**
     * Parse the content
     * @param ResponseInterface $response
     * @return mixed
     */
    public function parseResponse(ResponseInterface $response)
    {
        return json_decode(
            $response->getBody()->__toString(),
            true
        );
    }

    /**
     * Sets baseUrl of client.
     *
     * @param $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
}
