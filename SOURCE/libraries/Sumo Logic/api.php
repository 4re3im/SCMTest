<?php

/**
 * ANZGO-3654 Sumo Logic API
 * Added by Shane Camus 4/18/2018
 */

class SumoLogicApi
{
    private $params;

    public function __construct()
    {
        $this->params = array (
            'host' => $_SERVER['HTTP_HOST'],
            'url' => BASE_URL . $_SERVER[REQUEST_URI],
            'user agent' => $_SERVER['HTTP_USER_AGENT'],
            'sessionID' => session_id()
        );
    }

    public function log($data=[])
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException(
                'Function expecting parameters to be array. ' . gettype($data) . ' given.'
            );
        }

        $this->params = array_merge($this->params, $data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, SUMOLOGIC_HTTP_ENDPOINT);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $result = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $result;
    }

}
