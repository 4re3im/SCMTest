<?php

/**
 * ANZGO- Added by: John Renzo S. Sunico, 8/18/2017 8:08 AM
 * Analytics Subscription
 * Update: Aug. 24, 2017 Change function names and queries
 */

defined('C5_EXECUTE') || die(_('Access Denied.'));

class AnalyticsTitle
{

    /**
     * ANZGO-3526 Modified by Shane Camus 10/05/17
     * @return mixed
     */
    public static function getEPubAndITBTitles()
    {
        $db = Loader::db();
        $sql = "SELECT id, name as Title, isbn13 as ISBN ";
        $sql .= "FROM CupContentTitle ";
        $sql .= "WHERE id IN ( ";
        $sql .= "SELECT DISTINCT(TitleID) ";
        $sql .= "FROM CupGoTabs ";
        $sql .= "WHERE Private_TabText LIKE '%preview_content%' ";
        $sql .= "OR Private_TabText LIKE '%interactive_book%' ";
        $sql .= "GROUP BY TitleID ";
        $sql .= "ORDER BY TitleID)";
        return $db->GetAll($sql);
    }

    /**
     * ANZGO-3505 Added by John Renzo Sunico, October 5, 2017
     * Returns object if title is found
     * @param $keyword
     * @return int ID
     */
    public static function getTitleIDByPrivateTabKeyword($keyword)
    {
        $db = Loader::db();
        $sql = 'SELECT titleID FROM CupGoTabs WHERE Private_TabText LIKE ? LIMIT 1';
        $result = $db->GetRow($sql, array("%$keyword%"));

        if ($result) {
            return $result['titleID'];
        }

        return 0;
    }

    /**
     * ANZGO-3505 Added by John Renzo Sunico, October 5, 2017
     * Returns id, name, isbn13 in array format
     * @param int $id
     * @return array $description
     */
    public static function getShortAssocDescription($id)
    {
        $db = Loader::db();
        $sql = 'SELECT id, name, isbn13 FROM CupContentTitle WHERE id = ? LIMIT 1';
        $title = $db->GetRow($sql, array(intval($id)));
        $description = array();

        if ($title) {
            $description = array(
                'id' => $title['id'],
                'name' => $title['name'],
                'isbn13' => $title['isbn13']
            );
        }

        return $description;
    }

    /**
     * ANZGO-3511 Added by John Renzo Sunico 11/21/2017
     * Return titles that are related to ITB
     */
    public static function getITBTitles()
    {
        $db = Loader::db();
        $sql = <<<sql
                SELECT id, isbn13 as ISBN, name as Title
                FROM CupContentTitle
                WHERE id IN (
                    SELECT titleID FROM CupGoTabs WHERE Private_TabText LIKE '%/go/interactive_book/%'
                );
sql;
        return $db->GetAll($sql);
    }

    /**
     * ANZGO-3480 Added by Jeszy Tanada, November 20, 2017
     * Returns Enabled Titles ISBN, Name, Platform (GO/Edjin/Elevate)
     * @param $month $year in question
     * @return array
     */
    public static function getEnabledTitles()
    {
        $db = Loader::db();

        $sql = <<<sql
            SELECT id, isbn13, name  FROM CupContentTitle
            WHERE isEnabled = 1 ORDER BY id;
sql;
        return $db->GetAll($sql);
    }

    public static function getTabDetails($titleID)
    {
        $db = Loader::db();

        $sql = <<<sql
            SELECT cgt.TitleId, cgt.TabName, cgt.ElevateProduct, cgt.HMProduct
            FROM CupGoTabs cgt
            INNER JOIN CupContentTitle cct ON cgt.TitleID = cct.ID
            WHERE cgt.TitleID = ?;
sql;
        return $db->GetAll($sql, array($titleID));
    }
}
