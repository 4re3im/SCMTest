<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 26/01/2021
 * Time: 11:02 AM
 */

class BaseDS
{
    const DS_URL = 'https://ds.eu1.gigya.com/';
    const SEARCH = 'ds.search';
    const STORE = 'ds.store';

    public $cURLParams = [];
    public $response = [];

    private $endpoint;

    public function __construct()
    {
        $this->cURLParams = [
            'ApiKey' => GIGYA_API_KEY,
            'secret' => GIGYA_SECRET_KEY,
            'userKey' => GIGYA_USER_KEY,
        ];

        $this->response = [
            'status' => false,
            'data' => null,
            'error' => ''
        ];
    }

    protected function getCURLObject()
    {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "",
                CURLOPT_HTTPHEADER => array(
                    "Postman-Token: b78a9981-3270-4f9b-8c03-eae3da23a04b",
                    "cache-control: no-cache"
                ),
            ));
        } catch (Exception $e) {
            return false;
        }

        return $curl;
    }

    protected function executeCURL($save = false)
    {
        $curl = $this->getCURLObject();
        if (!$curl) {
            $this->response['error'] = 'There was an error connecting to Gigya';
            return;
        }

        $url = $this->formatURL();
        
        curl_setopt($curl, CURLOPT_URL, $url);
        $response = curl_exec($curl);

        if (curl_error($curl)) {
            $this->response['error'] = 'There was an error querying DS in Gigya';
            return;
        }

        $this->response['status'] = true;
        $this->response['data'] = json_decode($response, true);

        curl_close($curl);
    }

    public function formatURL()
    {
        if (!$this->endpoint) {
            $this->response['error'] = 'There is no Gigya endpoint defined.';
            return $this->response;
        }

        $formattedURL = static::DS_URL . $this->endpoint . '?';
        $formattedURL .= http_build_query(
            $this->cURLParams,
            '',
            '&',
            PHP_QUERY_RFC3986
        );
        return $formattedURL;
    }

    public function query($queryString)
    {
        $this->endpoint = static::SEARCH;
        $this->cURLParams['query'] = $queryString;
        $this->executeCURL();
        return $this->response;
    }

    public function save($data, $oid = 'auto')
    {
        $this->endpoint = static::STORE;
        $this->cURLParams['data'] = $data;
        $this->cURLParams['type'] = 'sr_institution';
        $this->cURLParams['oid'] = $oid;
        $this->executeCURL();
        return $this->response;
    }
}