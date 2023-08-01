<?php defined('C5_EXECUTE') || die(_('Access Denied.'));

/* ANZGO-3596 Added by Maryjes Tanada 02/21/2018
 * Bookseller Report Data per Month Year
 */

class AnalyticsBookSellerReport
{

    public static function countBookSellerProvisionedCmsSubs($month, $year, $userGroup)
    {
        $db = Loader::db();
        $sql = <<<sql
              SELECT cgus.CreatedBy as BooksellerID, u.uEmail as Bookseller, cgs.ISBN_13 as ISBN, cgs.CMS_Name as Title,
              count(case when ug.gID = ? AND cgus.PurchaseType = 'PROVISION' AND cgus.Active = 'Y' then ug.gID end)
              as 'ProvisionedCount',
              count(case when ug.gID = ? AND cgus.PurchaseType = 'CMS' AND cgus.Active = 'Y' then ug.gID end) as
              'CMSCount',
              count(case when ug.gID = ? AND cgus.Active = 'N' AND cgus.DateDeactivated IS NOT NULL then ug.gID end)
              as 'DeactivatedCount'
              FROM CupGoUserSubscription cgus
              INNER JOIN CupGoSubscription cgs ON cgus.S_ID = cgs.ID
              INNER JOIN UserGroups ug ON cgus.UserID = ug.uID
              INNER JOIN Users u ON cgus.CreatedBy = u.uID
              WHERE cgus.CreatedBy IN (select ug.uID from UserGroups ug where ug.gID = 9)
              AND MONTH(cgus.CreationDate) = ? AND YEAR(cgus.CreationDate) = ?
              GROUP BY BooksellerID, ISBN;
sql;
        return $db->GetAll($sql, array($userGroup, $userGroup, $userGroup, $month, $year));
    }
}
