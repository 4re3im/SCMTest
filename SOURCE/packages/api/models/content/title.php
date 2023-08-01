<?php

/**
 * ANZGO-3649 Added by John Renzo S. Sunico, 03/06/2018
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../base_model.php';

class ContentTitle extends BaseModel
{
    const RESOURCE_TYPE_ITB = 'ITB';
    const RESOURCE_TYPE_EPUB = 'EPUB';

    // SB-612 modified by jbernardez 20200707
    const INTERACTIVE = 'Interactive Textbook';
    const PREVIEW_INTERACTIVE = 'Preview Interactive Textbook';

    public $_table = 'CupContentTitle';

    public function __construct()
    {
        parent::__construct();
        Loader::model('tab', $this->pkgHandle);
    }

    public function isSample($bookName, $resourceType)
    {
        $searchKey = '';
        $bookName = filter_var($bookName, FILTER_SANITIZE_STRING);

        switch ($resourceType) {
            CASE static::RESOURCE_TYPE_ITB:
                $searchKey = '%/go/interactive_book%?bn=' . $bookName . '"%';
                break;
            CASE static::RESOURCE_TYPE_EPUB:
                $searchKey = '%/go/epub/preview_content.php%?directory=' . $bookName . '"%';
                break;
            DEFAULT:
                break;
        }

        $tab = new Tab();
        return $tab->findByPublicTabText($searchKey) > 0;
    }

    // SB-538 added byb jbernardez 20200415
    public function previewLinkByISBN($isbn)
    {
        // SB-612 modified by jbernardez 20200707
        $query = 'SELECT CupContentTitle.isbn13, CupGoTabs.TitleID, CupGoTabs.TabName, 
                    CupGoTabs.Public_TabText, CupGoTabs.HMProduct, CupGoTabs.ElevateProduct
                    FROM CupContentTitle 
                    INNER JOIN CupGoTabs ON CupContentTitle.id = CupGoTabs.TitleID 
                    WHERE CupContentTitle.isbn13 = ?
                    AND CupGoTabs.TabName LIKE "%Interactive Textbook%" ';

        $db = Loader::db();
        $publicTabTexts = $db->getAll($query, $isbn);

        $publicTabText = array();
        $isHMEProduct = false;
        if (is_array($publicTabTexts)) {
            foreach ($publicTabTexts as $row) {
                if ($row['HMProduct'] == 'Y' || $row['ElevateProduct'] == 'Y') {
                    $isHMEProduct = true;
                }
                $publicTabText[$row['TabName']] = $row['Public_TabText'];
            }
        } else {
            return false;
        }

        if ($isHMEProduct) {
            return $publicTabText[static::PREVIEW_INTERACTIVE];
        } else {
            return $publicTabText[static::INTERACTIVE];
        }
    }
}