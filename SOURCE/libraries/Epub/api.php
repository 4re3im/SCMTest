<?php

/**
 * ANZGO-3466 Call of EPUB API
 * Added by Shane Camus 8/14/2017
 */

/** Load all class dependencies */

Loader::model('user/user_model');
Loader::model('title/cup_content_title');
Loader::model('analytics_title', 'go_analytics');

class EpubApi
{
    const TABLE_BOOKMARKS = 'bookmarks';
    const TABLE_ANNOTATOR = 'annotator';

    const FIELD_DATA = 'data';
    const FIELD_HOURS_SPENT = 'HoursSpent';
    const FIELD_UID = 'uID';
    const FIELD_TITLE = 'Title';
    const FIELD_MONTH = 'month';
    const FIELD_YEAR = 'year';
    const FIELD_PRIVATE_KEY = 'privateKey';

    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_GET = 'GET';

    public static function getURI()
    {
        $protocol = 'http://';
        $host = filter_input(INPUT_SERVER, 'HTTP_HOST');

        return $protocol . $host . '/go/epub/analytics/';
    }

    /**
     * Handles API request and return response
     * ANZGO-3481 Added by John Renzo S. Sunico, October 10, 2017
     * @param $uri
     * @param $method
     * @param array $params
     * @throws InvalidArgumentException if parameters is not array
     * @return mixed
     */
    public static function fetchResponse($uri, $method, $params = [])
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

        $result = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $result;
    }

    /**
     * ANZGO-3505 Added by John Renzo S. Sunico, October 10, 2017
     * Returns list of Epub assets per Month and Year
     * @param int $month
     * @param int $year
     * @return bool | array
     */
    public static function getRichMediaLogCountPerMonthYear($month = 0, $year = 0)
    {
        $params = array(
            static::FIELD_PRIVATE_KEY => EPUB_KEY,
            static::FIELD_MONTH => $month,
            static::FIELD_YEAR => $year
        );

        $uri = static::getURI() . 'getRichMediaLogCountPerMonthYear/';
        $result = static::fetchResponse($uri, static::HTTP_METHOD_POST, $params);

        if (isset($result)) {
            return $result;
        }

        return false;
    }

    // ANZGO-3485 Added by Shane Camus 08/25/2017
    // ANZGO-3655 Modified by Shane Camus 03/12/2018
    public static function getTestHubAssetIDPerSeriesID($seriesID)
    {
        $params = array(
            static::FIELD_PRIVATE_KEY => EPUB_KEY,
            'seriesID' => $seriesID
        );

        $uri = static::getURI() . 'getTestHubAssetIDs';
        $result = static::fetchResponse($uri, static::HTTP_METHOD_POST, $params);

        if (isset($result)) {
            return $result;
        }

        return false;
    }

    /**
     * ANZGO-3481 Added by John Renzo S. Sunico, October 10, 2017
     * Returns bookmarks and annotations per month and year.
     * Was planning to separate them but the code is exactly
     * the same so I decided to just do them in one.
     * @param $month
     * @param $year
     * @param $table
     * @param null $group
     * @return array|bool
     */
    public static function getEpubBookmarksOrAnnotationCountPerMonthYear($month, $year, $table, $group = null)
    {
        $params = array(
            static::FIELD_PRIVATE_KEY => EPUB_KEY,
            static::FIELD_MONTH => $month,
            static::FIELD_YEAR => $year,
            'table' => $table
        );

        $uri = static::getURI() . 'getBookmarksOrAnnotationsPerMonthYear/';
        $result = static::fetchResponse($uri, static::HTTP_METHOD_POST, $params);

        if (!isset($result['data']) || !$result['data']) {
            return null;
        }

        $records = $result['data'];
        $userIDs = array_unique(array_column($records, 'uID'));
        $validUsers = UserModel::filterUserIDsByGroup($userIDs, $group);

        $formattedRecords = array();
        foreach ($records as $record) {
            $dirName = $record['Dir'];

            if (!in_array($record['uID'], $validUsers)) {
                continue;
            }

            if (!isset($formattedRecords[$dirName])) {
                $title = new CupContentTitleModel();
                $title->getTitleByEpubDirectory($dirName);

                if ($title->getID()) {
                    $formattedRecords[$dirName] = array_merge(
                        $title->getShortAssocDescription(),
                        array($table => 1)
                    );
                }

                continue;
            }

            $formattedRecords[$dirName][$table] += 1;
        }

        sort($formattedRecords);

        return $formattedRecords;
    }

    // ANZGO-3509 Added by Shane Camus 10/16/17
    // Use this function for getting count of different functionality
    public static function getFunctionalityCountPerMonthYear(
        $month = 0,
        $year = 0,
        $titleCode = '',
        $functionality = '')
    {
        $params = array(
            static::FIELD_PRIVATE_KEY => EPUB_KEY,
            static::FIELD_MONTH => $month,
            static::FIELD_YEAR => $year,
            'titleCode' => $titleCode,
            'functionality' => $functionality
        );

        $uri = static::getURI() . 'getFunctionCountPerMonthYear';
        $result = static::fetchResponse($uri, static::HTTP_METHOD_POST, $params);

        if (isset($result)) {
            return $result;
        }

        return false;
    }

    // ANZGO-3536 Added by Shane Camus 11/8/17
    // Use this function for getting count of different assets
    public static function getAssetLogCountPerMonthYear(
        $month = 0,
        $year = 0,
        $titleCode = '',
        $assetType = '',
        $detail = null
    )
    {
        $params = array(
            static::FIELD_PRIVATE_KEY => EPUB_KEY,
            static::FIELD_MONTH => $month,
            static::FIELD_YEAR => $year,
            'titleCode' => $titleCode,
            'assetType' => $assetType
        );

        if ($detail != null) {
            $params['detail'] = $detail;
        }

        $uri = static::getURI() . 'getAssetLogCountPerMonthYear';
        $result = static::fetchResponse($uri, static::HTTP_METHOD_POST, $params);

        if (isset($result)) {
            return $result;
        }

        return false;
    }

    /**
     * ANZGO-3511 Added by John Renzo Sunico 11/22/2017
     * Returns time spent on Reader per Month Year and Group
     * @param $month
     * @param $year
     * @param $group
     * @return array|mixed
     */
    public static function getTimeSpentInBookPerMonth($month, $year, $group)
    {
        $params = array(
            static::FIELD_PRIVATE_KEY => EPUB_KEY,
            static::FIELD_MONTH => $month,
            static::FIELD_YEAR => $year
        );

        $url = static::getURI() . 'getTimeSpentPerMonthYear';
        $result = static::fetchResponse($url, static::HTTP_METHOD_POST, $params);

        if (!$result[static::FIELD_DATA]) {
            return $result[static::FIELD_DATA];
        }

        $userIDs = array_unique(array_column($result['data'], 'uID'));
        $validUsers = UserModel::filterUserIDsByGroup($userIDs, $group);
        $records = [];

        foreach ($result[static::FIELD_DATA] as $log) {

            if (!in_array($log[static::FIELD_UID], $validUsers)) {
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
