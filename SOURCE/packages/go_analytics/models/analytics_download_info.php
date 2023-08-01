<?php  defined('C5_EXECUTE') or die(_('Access Denied.'));
/**
 * ANZGO-3529 Added by: Jeszy Tanada, 10/09/2017
 * Get Analytics Download info
 */

class AnalyticsDownloadInfo
{
    /**
     * ANZGO-3529 Jeszy Tanada, 10/13/2017
     * Count of all logs from Title Page logs and MyResources page respectively
     */
    public static function countPdfPerTitle($month, $year)
    {
        $db = Loader::db();

        $sql = "SELECT cct.id AS ID, cct.isbn13 as ISBN, cct.name as Title, MONTH(cglu.CreatedDate) AS 'Month', ";
        $sql .= "YEAR(cglu.CreatedDate) AS 'Year', COUNT(cglu.UserID) AS 'Total' ";
        $sql .= "FROM CupContentTitle cct ";
        $sql .= "INNER JOIN CupGoContent cgc ON cct.ID = cgc.titleID ";
        $sql .= "INNER JOIN CupGoLogUser cglu ON cgc.ID = cglu.Info ";
        $sql .= "WHERE cglu.Action='PDF Textbook' ";
        $sql .= "AND MONTH(cglu.CreatedDate) = ? AND YEAR(cglu.CreatedDate) = ? ";
        $sql .= "GROUP BY ID;";
        return $db->GetAll($sql, array($month, $year));
    }

    /**
     * ANZGO-3529 Jeszy Tanada, 10/13/2017
     * Sum of all logs from Title Page logs and MyResources page respectively
     */
    public static function countWordActivityPerTitle($month, $year)
    {
        $db = Loader::db();

        $sql = "SELECT act.ID, act.ISBN, act.Title, act.Month, act.Year, ";
        $sql .= "SUM(Activity_Download_Count) AS 'Total' FROM (";
        //Pretty Url input to Info from Title page
        $sql .= "(SELECT cct.id AS ID, cct.isbn13 as ISBN, cct.name as Title, MONTH(cglu.CreatedDate) AS 'Month', ";
        $sql .= "YEAR(cglu.CreatedDate) AS 'Year', COUNT(cglu.UserID) AS 'Activity_Download_Count' ";
        $sql .= "FROM CupContentTitle cct ";
        $sql .= "INNER JOIN CupGoLogUser cglu ON cct.prettyUrl = cglu.Info ";
        $sql .= "WHERE (cglu.Action='Word activities' OR cglu.Action='Word activities Chapters') ";
        $sql .= "AND (MONTH(cglu.CreatedDate) = ? AND YEAR(cglu.CreatedDate) = ?) ";
        $sql .= "AND cglu.Info = cct.prettyUrl GROUP BY ID) ";
        $sql .= "UNION ALL ";
        //Content ID input to Info from My resources page
        $sql .= "(SELECT cct.id AS ID, cct.isbn13 as ISBN, cct.name as Title, MONTH(cglu.CreatedDate) AS 'Month', ";
        $sql .= "YEAR(cglu.CreatedDate) AS 'Year', COUNT(cglu.UserID) AS 'Activity_Download_Count' ";
        $sql .= "FROM CupContentTitle cct ";
        $sql .= "INNER JOIN CupGoContent cgc ON cct.ID = cgc.titleID ";
        $sql .= "INNER JOIN CupGoLogUser cglu ON cgc.ID = cglu.Info ";
        $sql .= "WHERE (cglu.Action='Word activities' OR cglu.Action='Word activities Chapters') ";
        $sql .= "AND (MONTH(cglu.CreatedDate) = ? AND YEAR(cglu.CreatedDate) = ?) ";
        $sql .= "GROUP BY ID)";
        $sql .= ") act GROUP BY act.ID;";
        return  $db->GetAll($sql, array($month, $year, $month, $year));
    }

    /**
     * ANZGO-3529 Jeszy Tanada, 10/13/2017
     * Sum of all logs from Title Page logs and MyResources page respectively
     */
    public static function countWeblinkClickPerTitle($month, $year)
    {
        $db = Loader::db();

        $sql = "SELECT weblink.ID, weblink.ISBN, weblink.Title, weblink.Month, weblink.Year, ";
        $sql .= "SUM(WebLink_Click_Count) AS 'Total' FROM (";
        //Content ID input to Info from MyResources page
        $sql .= "(SELECT cct.id AS ID, cct.isbn13 as ISBN, cct.name as Title, MONTH(cglu.CreatedDate) AS 'Month', ";
        $sql .= "YEAR(cglu.CreatedDate) AS 'Year', COUNT(cglu.UserID) AS 'WebLink_Click_Count' ";
        $sql .= "FROM CupContentTitle cct ";
        $sql .= "INNER JOIN CupGoTabs cgt ON cct.ID = cgt.titleID ";
        $sql .= "INNER JOIN CupGoTabContent cgtc ON cgt.ID = cgtc.TabID ";
        $sql .= "INNER JOIN CupGoLogUser cglu ON cgtc.ContentID = cglu.Info ";
        $sql .= "WHERE (cglu.Action='Weblinks Chapters' AND cglu.Info = cgtc.ContentID) ";
        $sql .= "AND (MONTH(cglu.CreatedDate) = ? AND YEAR(cglu.CreatedDate) = ?) ";
        $sql .= "GROUP BY ID) ";
        $sql .= "UNION ALL";
        //Pretty URL input to Info from Title page
        $sql .= "(SELECT cct.id AS ID, cct.isbn13 as ISBN, cct.name as Title, MONTH(cglu.CreatedDate) AS 'Month', ";
        $sql .= "YEAR(cglu.CreatedDate) AS 'Year', COUNT(cglu.UserID) AS 'WebLink_Click_Count' ";
        $sql .= "FROM CupContentTitle cct ";
        $sql .= "INNER JOIN CupGoLogUser cglu ON cct.prettyUrl = cglu.Info ";
        $sql .= "WHERE cglu.Action='Weblinks' AND cglu.Info = cct.prettyUrl ";
        $sql .= "AND (MONTH(cglu.CreatedDate) = ? AND YEAR(cglu.CreatedDate) = ?) ";
        $sql .= "GROUP BY ID) ";
        $sql .= ") weblink GROUP BY weblink.ID;";
        return $db->GetAll($sql, array($month, $year, $month, $year));

    }

    /**
     * ANZGO-3529 Jeszy Tanada, 10/13/2017
     * Logs from download function (log zip files only)
     */
    public static function countTeacherPackagePerTitle($month, $year)
    {
        $db = Loader::db();

        $sql = "SELECT cct.id AS ID, cct.isbn13 as ISBN, cct.name as Title, MONTH(cglu.CreatedDate) AS 'Month', ";
        $sql .= "YEAR(cglu.CreatedDate) AS 'Year', COUNT(cglu.UserID) AS 'Total' ";
        $sql .= "FROM CupContentTitle cct ";
        $sql .= "INNER JOIN CupGoContent cgc ON cct.ID = cgc.titleID ";
        $sql .= "INNER JOIN CupGoLogUser cglu ON cgc.ID = cglu.Info ";
        $sql .= "WHERE cglu.Action='Teacher Resource Package' ";
        $sql .= "AND (MONTH(cglu.CreatedDate) = ? AND YEAR(cglu.CreatedDate) = ?) ";
        $sql .= "GROUP BY ID;";
        return $db->GetAll($sql, array($month, $year));
    }
}
