<?php

/**
 * ANZGO-3466
 * Added by Shane Camus 08/11/2017
 */

defined('C5_EXECUTE') || die(_('Access Denied.'));
define('AUTHENTICATION_INDEX', 1);

class EPubActivityHelper
{

    public static function ePubDetails()
    {
        Loader::model('analytics_epub', 'go_analytics');

        $data = AnalyticsEPub::getEPubTitles();

        $titles = array();

        foreach ($data as $_data) {

            $titleID = $_data['TitleID'];
            $titleCode = explode('=', (explode('"', $_data['Private_TabText'])[1]))[1];

            $result = AnalyticsEPub::getEPubTitleDetails($titleID);
            $titleISBN = $result['isbn13'];
            $titleName = $result['name'];

            $titles[] = array (
                'id' => $titleID,
                'isbn' => $titleISBN,
                'name' => $titleName,
                'code' => $titleCode
            );
        }

        return $titles;
    }
}
