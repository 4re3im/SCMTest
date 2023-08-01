<?php

// ANZGO-3466 Added by Shane Camus 8/11/17

defined('C5_EXECUTE') || die(_('Access Denied.'));

class AnalyticsEPub
{
    public static function getEPubTitles()
    {
        $db = Loader::db();
        $sql = "SELECT DISTINCT(TitleID), Private_TabText ";
        $sql .= "FROM CupGoTabs WHERE Private_TabText LIKE '%preview_content.php%' ";
        $sql .= "GROUP BY TitleID ORDER BY TitleID";
        return $db->GetAll($sql);
    }

    public static function getEPubTitleDetails($titleID)
    {
        $db = Loader::db();
        $sql = "SELECT isbn13, name FROM CupContentTitle WHERE id = $titleID";
        return $db->getRow($sql);
    }

}
