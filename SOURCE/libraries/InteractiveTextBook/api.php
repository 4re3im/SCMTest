<?php

/**
 * ANZGO-3511 , Added by John Renzo S. Sunico, 11/21/2017
 * Handles connection to Interactive Book
 */

Loader::model('user/user_model');
Loader::model('analytics_title', 'go_analytics');

class InteractiveTextBookAPI
{
    const FIELD_MONTH = 'month';
    const FIELD_YEAR = 'year';
    const FIELD_USER_ID = 'uID';
    const FIELD_TITLE = 'Title';
    const FIELD_HOURS_SPENT = 'HoursSpent';

    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_GET = 'GET';

    public static function generateUrl($controller)
    {
        $protocol = 'http://';
        $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
        $baseRequest = '/go/interactive_book/analytics/';

        return $protocol . $host . $baseRequest . $controller;
    }

    public static function fetchResponse($uri, $method, $params=[], $headers=[])
    {
        if (!is_array($params)) {
            throw new InvalidArgumentException(
                'Function expecting parameters to be array. ' . gettype($params) . ' given.'
            );
        }

        if ($method === static::HTTP_METHOD_GET) {
            $uri .= '?' . http_build_query($params);
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $uri);

        if ($method === static::HTTP_METHOD_POST) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        }

        if ($headers && is_array($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        $result = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $result;
    }

    public static function getCredentials()
    {
        return base64_encode(md5(ITB_SECRET_KEY));
    }

    public static function getTimeSpentOnBookPerMonthYear($month, $year, $group)
    {
        $url = static::generateUrl('getTimeSpentInBookPerMonth');
        $params = [static::FIELD_MONTH => $month, static::FIELD_YEAR => $year];
        $authorizationHeader = 'Authorization: Bearer ' . static::getCredentials();
        $headers = array($authorizationHeader);

        $results = static::fetchResponse($url, static::HTTP_METHOD_POST, $params, $headers);

        $userIDs = array_unique(array_column($results, 'uID'));
        $validUsers = UserModel::filterUserIDsByGroup($userIDs, $group);
        $records = [];

        foreach ($results as $log) {

            if (!in_array($log[static::FIELD_USER_ID], $validUsers)) {
                continue;
            }

            $directory = $log[static::FIELD_TITLE];
            $titleID = AnalyticsTitle::getTitleIDByPrivateTabKeyword($directory);

            if (!$titleID) {
                continue;
            }

            if (!in_array($titleID, array_keys($records))) {
                $titleDescription = AnalyticsTitle::getShortAssocDescription($titleID);
                $records[$titleID] = array_merge(
                    [static::FIELD_HOURS_SPENT => $log[static::FIELD_HOURS_SPENT]],
                    $titleDescription);
            } else {
                $records[$titleID][static::FIELD_HOURS_SPENT] += $log[static::FIELD_HOURS_SPENT];
            }
        }

        sort($records);
        return $records;
    }
}
