<?php

/**
 * ANZGO-3485 Call of Testhub API
 * Added by Shane Camus 8/25/2017
 */

class TesthubApi
{
    public static function getURI()
    {
        $protocol = 'https://';

        if(strpos($_SERVER['HTTP_HOST'], 'dev') !== false) {
            $host = 'testhub-dev.cambridge.edu.au';
        } else if(strpos($_SERVER['HTTP_HOST'], 'uat') !== false) {
            $host = 'testhub-uat.cambridge.edu.au';
        } else if(strpos($_SERVER['HTTP_HOST'], 'staging') !== false) {
            $host = 'testhub-testdeploy.cambridge.edu.au';
        } else {
            $host = 'testhub.cambridge.edu.au';
        }

        return $protocol . $host . '/analytics/';
    }

    public static function getCheckAnswersClick($month, $year)
    {
        $data = array (
            'privateKey' => TESTHUB_KEY,
            'month' => $month,
            'year' => $year
        );

        $uri = self::getURI() . 'CheckAnswerCount';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $uri);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = json_decode(curl_exec($curl));
        curl_close($curl);

        if (isset($result)) {
            return $result;
        } else {
            return FALSE;
        }
    }

}
