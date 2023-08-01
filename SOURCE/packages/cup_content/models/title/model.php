<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('product/model', 'core_commerce');

define('TITLE_IMAGES_FOLDER', DIR_BASE . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'cup_content' . DIRECTORY_SEPARATOR .
        'images' . DIRECTORY_SEPARATOR . 'titles' . DIRECTORY_SEPARATOR);
define('SERIES_IMAGES_FOLDER', DIR_BASE . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'cup_content' . DIRECTORY_SEPARATOR .
        'images' . DIRECTORY_SEPARATOR . 'series' . DIRECTORY_SEPARATOR);

Loader::model('sales/tax/rate', 'core_commerce');

class CupContentTitle extends Object
{
    protected $id                       = false;
    protected $isbn13                   = false;
    protected $isbn10                   = false;
    protected $name                     = false;
    protected $customName               = false;
    protected $displayName              = false;
    protected $subtitle                 = false;
    protected $customSubtitle           = false;
    protected $displaySubtitle          = false;
    // SB-457 added  by jbernardez 20200206
    protected $myResourcesTitle         = false;
    protected $edition                  = false;
    protected $prettyUrl                = false;
    protected $shortDescription         = false;
    protected $longDescription          = false;
    protected $content                  = false;
    protected $feature                  = false;
    protected $yearLevels               = false;
    protected $publishDate              = false;
    protected $availability             = false;
    protected $goUrl                    = false;
    protected $previewUrl               = false;
    // part of series, stand alone, study guide
    protected $type                     = false;
    protected $series                   = false;
    protected $descriptionOption        = false;
    protected $divisions                = false;
    protected $regions                  = false;
    protected $tagline                  = false;
    protected $reviews                  = false;
    protected $aus_circa_price          = false;
    protected $nz_circa_price           = false;
    protected $auProductID              = false;
    protected $nzProductID              = false;
    protected $formats                  = array();
    // ANZGO-1738
    protected $tabs                     = array();
    // ANZGO-1812
    protected $contentFolders           = array();
    protected $subjects                 = array();
    protected $authors                  = array();
    protected $relatedTitleIDs          = array();
    protected $supportingTitleIDs       = array();
    protected $createdAt                = false;
    protected $modifiedAt               = false;
    protected $isEnabled                = false;
    protected $hasInspectionCopy        = false;
    protected $hasAccessCode            = false;
    protected $hasDownloadableFile      = false;
    protected $is_free_shipping         = false;
    protected $search_priority          = false;
    protected $new_product_flag         = false;
    protected $cart_message             = false;
    protected $cart_popup_content       = false;
    protected $divisions_save           = false;
    protected $regions_save             = false;
    protected $yearLevels_save          = false;
    protected $authors_save             = false;
    protected $sample_pages             = false;
    protected $image                    = false;
    protected $product_post             = array();
    protected $submit_data              = false;
    protected $system_errors            = array();
    protected $errors                   = array();
    protected $answercode_definition    = false;
    // ANZGO-1708
    protected $isGoProduct              = false;

    // ANZUAT-90
    protected $showBuyNow               = false;

    // ANZGO-3043
    public $hmTitle                     = false;

    // GCAP-625
    protected $demo_id                  = false;

    private $thumbnail_url = null;
    const GLOBAL_GO_IMG_SIZE = 260;

    function __construct($id = false) {
        if ($id) {
            $cacheObj = false;
            if ($cacheObj !== false) {
                $this->copyFromObject($cacheObj);
            } else {
                $db = Loader::db();
                $q = 'select * from CupContentTitle where id = ?';
                $result = $db->getRow($q, array($id));

                if ($result) {

                    $this->id                   = $result['id'];
                    $this->isbn13               = $result['isbn13'];
                    $this->isbn10               = $result['isbn10'];
                    $this->name                 = $result['name'];
                    $this->customName           = $result['customName'];
                    $this->displayName          = $result['displayName'];
                    $this->subtitle             = $result['subtitle'];
                    $this->customSubtitle       = $result['customSubtitle'];
                    $this->displaySubtitle      = $result['displaySubtitle'];
                    // SB-457 added by jbernardez 20200206
                    $this->myResourcesTitle     = $result['myResourcesTitle'];
                    $this->edition              = $result['edition'];
                    $this->prettyUrl            = $result['prettyUrl'];
                    $this->shortDescription     = $result['shortDescription'];
                    $this->longDescription      = $result['longDescription'];
                    $this->content              = $result['content'];
                    $this->feature              = $result['feature'];
                    $this->yearLevels           = $result['yearLevels'];
                    $this->publishDate          = $result['publishDate'];
                    $this->availability         = $result['availability'];
                    $this->goUrl                = $result['goUrl'];
                    $this->previewUrl           = $result['previewUrl'];
                    // part of series, stand alone, study guide
                    $this->type                 = $result['type'];
                    $this->series               = $result['series'];
                    $this->descriptionOption    = $result['descriptionOption'];
                    $this->divisions            = $result['divisions'];
                    $this->regions              = $result['regions'];
                    $this->tagline              = $result['tagline'];
                    $this->reviews              = $result['reviews'];

                    $this->aus_circa_price      = $result['aus_circa_price'];
                    $this->nz_circa_price       = $result['nz_circa_price'];

                    $this->auProductID          = $result['auProductID'];
                    $this->nzProductID          = $result['nzProductID'];

                    $this->createdAt            = $result['createdAt'];
                    $this->modifiedAt           = $result['modifiedAt'];

                    $this->isEnabled            = $result['isEnabled'];
                    $this->hasInspectionCopy    = $result['hasInspectionCopy'];
                    $this->hasAccessCode        = $result['hasAccessCode'];
                    $this->hasDownloadableFile  = $result['hasDownloadableFile'];
                    $this->is_free_shipping     = $result['is_free_shipping'];

                    $this->search_priority      = $result['search_priority'];
                    $this->new_product_flag     = $result['new_product_flag'];

                    $this->cart_message         = $result['cart_message'];
                    $this->cart_popup_content   = $result['cart_popup_content'];

                    // ANZGO-1708
                    $this->isGoProduct          = $result['isGoTitle'];

                    // ANZUAT-90
                    $this->showBuyNow           = $result['showBuyNow'];

                    // ANZGO-3043
                    $this->hmTitle              = $this->getHmTitle($id);

                    // GCAP-625
                    $this->demo_id              = (int)$result['demo_id'];

                    if (strlen($this->divisions) > 0) {
                        //[F][1][2][3]..[12]
                        $tmp = trim($this->divisions, '[]');
                        $this->divisions = explode('][', $tmp);
                    }

                    if (strlen($this->regions) > 0) {
                        //[Australia][New Zealand][Queensland][Victoria]
                        $tmp = trim($this->regions, '[]');
                        $this->regions = explode('][', $tmp);
                    }

                    if (strlen($this->yearLevels) > 0) {
                        //[F][1][2][3]..[12]
                        $tmp = trim($this->yearLevels, '[]');
                        $this->yearLevels = explode('][', $tmp);
                    }


                    $this->formats = array();
                    $tmp_query = "select * from CupContentTitleFormats where titleID = ?";
                    $tmp_results = $db->getAll($tmp_query, array($this->id));

                    if (is_array($tmp_results)) {
                        foreach ($tmp_results as $each_row) {
                            $this->formats[] = $each_row['format'];
                        }
                    }

                    // ANZGO-1738
                    $this->tabs = array();
                    $tmp_query = "SELECT * FROM CupGoTabs WHERE titleID = ? AND TabLevel = 1 AND TabType = 'TAB'";
                    $tmp_results = $db->getAll($tmp_query, array($this->id));

                    if (is_array($tmp_results)) {
                        foreach ($tmp_results as $each_row) {
                            $this->tabs[] = $each_row['TabName'];
                        }
                    }

                    // ANZGO-1812
                    $this->contentFolders = array();
                    $tmp_query = "SELECT * FROM CupGoContentFolders WHERE titleID = ?";
                    $tmp_results = $db->getAll($tmp_query, array($this->id));

                    if (is_array($tmp_results)) {
                        foreach ($tmp_results as $each_row) {
                            $this->contentFolders[] = $each_row['FolderName'];
                        }
                    }

                    $this->subjects = array();
                    $tmp_query = "select * from CupContentTitleSubjects where titleID = ?";
                    $tmp_results = $db->getAll($tmp_query, array($this->id));

                    if (is_array($tmp_results)) {
                        foreach ($tmp_results as $each_row) {
                            $this->subjects[] = $each_row['subject'];
                        }
                    }

                    $this->authors = array();
                    $tmp_query = "select * from CupContentTitleAuthors where titleID = ?";
                    $tmp_results = $db->getAll($tmp_query, array($this->id));

                    if (is_array($tmp_results)) {
                        foreach ($tmp_results as $each_row) {
                            $this->authors[] = $each_row['author'];
                        }
                    }

                    $this->relatedTitleIDs = array();
                    $tmp_query = "select * from CupContentTitleRelatedTitle where titleID = ?";
                    $tmp_results = $db->getAll($tmp_query, array($this->id));

                    if (is_array($tmp_results)) {
                        foreach ($tmp_results as $each_row) {
                            $this->relatedTitleIDs[] = $each_row['related_titleID'];
                        }
                    }

                    $this->supportingTitleIDs = array();
                    $tmp_query = "select * from CupContentTitleSupportingTitle where titleID = ?";
                    $tmp_results = $db->getAll($tmp_query, array($this->id));

                    if (is_array($tmp_results)) {
                        foreach ($tmp_results as $each_row) {
                            $this->supportingTitleIDs[] = $each_row['supporting_titleID'];
                        }
                    }

                    $this->thumbnail_url = $result['thumbnail_url'];
                }
                $this->setToCache();
            }
        }

        $this->loadAnswerCodes();
    }

    public static function getFromCache($id)
    {
        $hashKey = "CupContentTitle_" . $id;
        $obj = Cache::get($hashKey, false);

        return $obj;
    }

    public function copyFromObject($object)
    {
        $this->id                   = $object->id;
        $this->isbn13               = $object->isbn13;
        $this->isbn10               = $object->isbn10;
        $this->name                 = $object->name;
        $this->customName           = $object->customName;
        $this->displayName          = $object->displayName;
        $this->subtitle             = $object->subtitle;
        $this->customSubtitle       = $object->customSubtitle;
        $this->displaySubtitle      = $object->displaySubtitle;
        // SB-457 added by jbernardez 20200206
        $this->myResourcesTitle     = $object->myResourcesTitle;
        $this->edition              = $object->edition;
        $this->prettyUrl            = $object->prettyUrl;
        $this->shortDescription     = $object->shortDescription;
        $this->longDescription      = $object->longDescription;
        $this->content              = $object->content;
        $this->feature              = $object->feature;
        $this->yearLevels           = $object->yearLevels;
        $this->publishDate          = $object->publishDate;
        $this->availability         = $object->availability;
        $this->goUrl                = $object->goUrl;
        $this->previewUrl           = $object->previewUrl;
        // part of series, stand alone, study guide
        $this->type                 = $object->type;
        $this->series               = $object->series;
        $this->descriptionOption    = $object->descriptionOption;
        $this->divisions            = $object->divisions;
        $this->regions              = $object->regions;
        $this->tagline              = $object->tagline;
        $this->reviews              = $object->reviews;

        $this->aus_circa_price      = $object->aus_circa_price;
        $this->nz_circa_price       = $object->nz_circa_price;

        $this->auProductID          = $object->auProductID;
        $this->nzProductID          = $object->nzProductID;

        $this->createdAt            = $object->createdAt;
        $this->modifiedAt           = $object->modifiedAt;

        $this->isEnabled            = $object->isEnabled;
        $this->hasInspectionCopy    = $object->hasInspectionCopy;
        $this->hasAccessCode        = $object->hasAccessCode;
        $this->hasDownloadableFile  = $object->hasDownloadableFile;
        $this->is_free_shipping     = $object->is_free_shipping;

        $this->search_priority      = $object->search_priority;

        $this->new_product_flag     = $object->new_product_flag;

        $this->cart_message         = $object->cart_message;
        $this->cart_popup_content   = $object->cart_popup_content;

        $this->divisions            = $object->divisions;
        $this->regions              = $object->regions;
        $this->yearLevels           = $object->yearLevels;
        $this->formats              = $object->formats;
        // ANZGO-1738
        $this->tabs                 = $object->tabs;
        // ANZGO-1812
        $this->contentFolders       = $object->contentFolders;
        $this->subjects             = $object->subjects;

        $this->authors              = $object->authors;
        $this->relatedTitleIDs      = $object->relatedTitleIDs;
        $this->supportingTitleIDs   = $object->supportingTitleIDs;

        // ANZGO-1708
        $this->isGoProduct          = $object->isGoProduct;

        // ANZUAT-90
        $this->showBuyNow           = $object->showBuyNow;

        // GCAP-625
        $this->demo_id              = $object->demo_id;
    }

    public function setToCache()
    {
        $hashKey = "CupContentTitle_" . $this->id;
        Cache::set($hashKey, false, $this, 300);
    }

    public static function fetchByID($id)
    {
        $cacheObj = false;
        if ($cacheObj !== false) {
            return $cacheObj;
        } else {
            $object = new CupContentTitle($id);
            if ($object->id === false) {
                return false;
            } else {
                return $object;
            }
        }
    }

    public static function fetchByISBN13($isbn13)
    {
        $db = Loader::db();
        $q = "select * from CupContentTitle where isbn13 = ?";
        $result = $db->getRow($q, array($isbn13));

        if ($result) {
            $object = new CupContentTitle($result['id']);
            if ($object->id === false) {
                return false;
            } else {
                return $object;
            }
        }

        return $object;
    }

    public static function fetchByISBN10($isbn10)
    {
        $db = Loader::db();
        $q = "select * from CupContentTitle where isbn10 = ?";
        $result = $db->getRow($q, array($isbn10));

        if ($result) {
            $object = new CupContentTitle($result['id']);
            if ($object->id === false) {
                return false;
            } else {
                return $object;
            }
        }

        return $object;
    }

    public static function fetchByPrettyUrl($prettyUrl)
    {
        $object = new CupContentTitle();
        $object->loadByPrettyUrl($prettyUrl);

        if ($object->id === false) {
            return false;
        } else {
            return $object;
        }
    }

    public static function fetchByProductId($product_id)
    {
        $obj = self::fetchByAuProductId($product_id);
        if ($obj === false) {
            $obj = self::fetchByNzProductId($product_id);
        }

        return $obj;
    }

    public static function fetchByAuProductId($product_id)
    {
        $db = Loader::db();
        $q = "select * from CupContentTitle where auProductID = ?";
        $result = $db->getRow($q, array($product_id));

        if ($result) {
            $object = new CupContentTitle();
            $object->loadByID($result['id']);

            if ($object->id === false) {
                return false;
            } else {
                return $object;
            }
        } else {
            return false;
        }
    }

    public static function fetchByNzProductId($product_id)
    {
        $db = Loader::db();
        $q = "select * from CupContentTitle where nzProductID = ?";
        $result = $db->getRow($q, array($product_id));

        if ($result) {
            $object = new CupContentTitle();
            $object->loadByID($result['id']);

            if ($object->id === false) {
                return false;
            } else {
                return $object;
            }
        } else {
            return false;
        }
    }

    public function loadByID($requestID)
    {
        $this->id                   = false;
        $this->isbn13               = false;
        $this->isbn10               = false;
        $this->name                 = false;
        $this->customName           = false;
        $this->displayName          = false;
        $this->subtitle             = false;
        $this->customSubtitle       = false;
        $this->displaySubtitle      = false;
        // SB-457 added by jbernardez 20200206
        $this->myResourcesTitle     = false;
        $this->edition              = false;
        $this->prettyUrl            = false;
        $this->shortDescription     = false;
        $this->longDescription      = false;
        $this->content              = false;
        $this->feature              = false;
        $this->yearLevels           = false;
        $this->publishDate          = false;
        $this->availability         = false;
        $this->goUrl                = false;
        $this->previewUrl           = false;
        // part of series, stand alone, study guide
        $this->type                 = false;
        $this->series               = false;
        $this->descriptionOption    = false;
        $this->divisions            = false;
        $this->regions              = false;
        $this->tagline              = false;
        $this->reviews              = false;

        $this->aus_circa_price      = false;
        $this->nz_circa_price       = false;

        $this->auProductID          = false;
        $this->nzProductID          = false;

        $this->formats              = array();
        // ANZGO-1738
        $this->tabs                 = array();
        // ANZGO-1812
        $this->contentFolders       = array();
        $this->subjects             = array();
        $this->authors              = array();

        $this->relatedTitleIDs      = array();
        $this->supportingTitleIDs   = array();

        $this->createdAt            = false;
        $this->modifiedAt           = false;

        $this->isEnabled            = false;
        $this->hasInspectionCopy    = false;
        $this->hasAccessCode        = false;
        $this->hasDownloadableFile  = false;
        $this->is_free_shipping     = false;

        $this->search_priority      = false;

        $this->new_product_flag     = false;

        $this->cart_message         = false;
        $this->cart_popup_content   = false;

        // ANZGO-1708
        $this->isGoProduct          = false;

        // ANZUAT-90
        $this->showBuyNow           = false;

        // GCAP-625
        $this->demo_id              = false;

        $db = Loader::db();
        $q = "select * from CupContentTitle where id = ?";
        $result = $db->getRow($q, array($requestID));

        if ($result) {

            $this->id                   = $result['id'];
            $this->isbn13               = $result['isbn13'];
            $this->isbn10               = $result['isbn10'];
            $this->name                 = $result['name'];
            $this->customName           = $result['customName'];
            $this->displayName          = $result['displayName'];
            $this->subtitle             = $result['subtitle'];
            $this->customSubtitle       = $result['customSubtitle'];
            $this->displaySubtitle      = $result['displaySubtitle'];
            // SB-457 added by jbernardez 20200206
            $this->myResourcesTitle     = $result['myResourcesTitle'];
            $this->edition              = $result['edition'];
            $this->prettyUrl            = $result['prettyUrl'];
            $this->shortDescription     = $result['shortDescription'];
            $this->longDescription      = $result['longDescription'];
            $this->content              = $result['content'];
            $this->feature              = $result['feature'];
            $this->yearLevels           = $result['yearLevels'];
            $this->publishDate          = $result['publishDate'];
            $this->getAvailability      = $result['availability'];
            $this->goUrl                = $result['goUrl'];
            $this->previewUrl           = $result['previewUrl'];
            // part of series, stand alone, study guide
            $this->type                 = $result['type'];
            $this->series               = $result['series'];
            $this->descriptionOption    = $result['descriptionOption'];
            $this->divisions            = $result['divisions'];
            $this->regions              = $result['regions'];
            $this->tagline              = $result['tagline'];
            $this->reviews              = $result['reviews'];

            $this->aus_circa_price      = $result['aus_circa_price'];
            $this->nz_circa_price       = $result['nz_circa_price'];

            $this->auProductID          = $result['auProductID'];
            $this->nzProductID          = $result['nzProductID'];

            $this->createdAt            = $result['createdAt'];
            $this->modifiedAt           = $result['modifiedAt'];

            $this->isEnabled            = $result['isEnabled'];
            $this->hasInspectionCopy    = $result['hasInspectionCopy'];
            $this->hasAccessCode        = $result['hasAccessCode'];
            $this->hasDownloadableFile  = $result['hasDownloadableFile'];
            $this->is_free_shipping     = $result['is_free_shipping'];

            $this->search_priority      = $result['search_priority'];

            $this->new_product_flag     = $result['new_product_flag'];

            $this->cart_message         = $result['cart_message'];
            $this->cart_popup_content   = $result['cart_popup_content'];

            // ANZGO-1708
            $this->isGoProduct          = $result['isGoTitle'];

            // ANZUAT-90
            $this->showBuyNow           = $result['showBuyNow'];

            // GCAP-625
            $this->demo_id              = (int)$result['demo_id'];

            if (strlen($this->divisions) > 0) {
                //[F][1][2][3]..[12]
                $tmp = trim($this->divisions, '[]');
                $this->divisions = explode('][', $tmp);
            }

            if (strlen($this->regions) > 0) {
                //[Australia][New Zealand][Queensland][Victoria]
                $tmp = trim($this->regions, '[]');
                $this->regions = explode('][', $tmp);
            }

            if (strlen($this->yearLevels) > 0) {
                //[F][1][2][3]..[12]
                $tmp = trim($this->yearLevels, '[]');
                $this->yearLevels = explode('][', $tmp);
            }

            $this->formats = array();
            $tmp_query = "select * from CupContentTitleFormats where titleID = ?";
            $tmp_results = $db->getAll($tmp_query, array($this->id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $this->formats[] = $each_row['format'];
                }
            }

            // ANZGO-1738
            $this->tabs = array();
            $tmp_query = "select * from CupGoTabs where titleID = ?";
            $tmp_results = $db->getAll($tmp_query, array($this->id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $this->tabs[] = $each_row['TabName'];
                }
            }


            // ANZGO-1812
            $this->contentFolders = array();
            $tmp_query = "select * from CupGoContentFolders where titleID = ?";
            $tmp_results = $db->getAll($tmp_query, array($this->id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $this->contentFolders[] = $each_row['FolderName'];
                }
            }

            $this->subjects = array();
            $tmp_query = "select * from CupContentTitleSubjects where titleID = ?";
            $tmp_results = $db->getAll($tmp_query, array($this->id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $this->subjects[] = $each_row['subject'];
                }
            }

            $this->authors = array();
            $tmp_query = "select * from CupContentTitleAuthors where titleID = ?";
            $tmp_results = $db->getAll($tmp_query, array($this->id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $this->authors[] = $each_row['author'];
                }
            }

            $this->relatedTitleIDs = array();
            $tmp_query = "select * from CupContentTitleRelatedTitle where titleID = ?";
            $tmp_results = $db->getAll($tmp_query, array($this->id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $this->relatedTitleIDs[] = $each_row['related_titleID'];
                }
            }

            $this->supportingTitleIDs = array();
            $tmp_query = "select * from CupContentTitleSupportingTitle where titleID = ?";
            $tmp_results = $db->getAll($tmp_query, array($this->id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $this->supportingTitleIDs[] = $each_row['supporting_titleID'];
                }
            }
        } else {
            return false;
        }
    }

    public function loadByPrettyUrl($prettyUrl)
    {
        $db = Loader::db();
        $q = "select * from CupContentTitle where prettyUrl = ?";
        $result = $db->getRow($q, array($prettyUrl));

        if ($result) {
            return $this->loadByID($result['id']);
        } else {
            return false;
        }
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }

    public function getUrl()
    {
        $ch = Loader::helper('cup_content_html', 'cup_content');
        return $ch->url('/education/titles/' . urlencode($this->prettyUrl));
    }

    public function getSamplePages($is_page_proof = false)
    {
        Loader::model('title_sample_page/model', 'cup_content');
        if (!$this->sample_pages) {
            $this->sample_pages = array();

            $db = Loader::db();
            $tmp_query = "select id from CupContentTitleSamplePages WHERE titleID = ?";
            $params = array($this->id);
            if ($is_page_proof) {
                $tmp_query = "select id from CupContentTitleSamplePages WHERE is_page_proof = 1 AND titleID = ?";
            }
            $tmp_results = $db->getAll($tmp_query, $params);

            foreach ($tmp_results as $each_result) {
                $tmp_object = new CupContentTitleSamplePage($each_result['id']);
                $this->sample_pages[] = $tmp_object;
            }
        }

        return $this->sample_pages;
    }

    public function getSelectedDescription($html2text = false)
    {
        $str = "";
        if (strcmp($this->descriptionOption, 'title short description') == 0) {
            $str = $this->shortDescription;
        } elseif (strcmp($this->descriptionOption, 'title long description') == 0) {
            $str = $this->longDescription;
        } elseif (strcmp($this->descriptionOption, 'series short description') == 0) {
            $str = $this->getSeriesObject()->shortDescription;
        } elseif (strcmp($this->descriptionOption, 'series long description') == 0) {
            $str = $this->getSeriesObject()->longDescription;
        }


        if ($html2text) {
            require_once(DIR_PACKAGES . '/cup_content/libraries/html2text.php');
            $h2t = & new html2text($str);
            $str = $h2t->get_text();
        }

        return $str;
    }

    public function getAssoc()
    {
        $temp = array(
            'id'                    => $this->id,
            'isbn13'                => $this->isbn13,
            'isbn10'                => $this->isbn10,
            'name'                  => $this->name,
            'customName'            => $this->customName,
            'displayName'           => $this->displayName,
            'subtitle'              => $this->subtitle,
            'customSubtitle'        => $this->customSubtitle,
            'displaySubtitle'       => $this->displaySubtitle,
            // SB-457 added by jbernardez 20200206
            'myResourcesTitle'      => $this->myResourcesTitle,
            'edition'               => $this->edition,
            'prettyUrl'             => $this->prettyUrl,
            'shortDescription'      => $this->shortDescription,
            'longDescription'       => $this->longDescription,
            'content'               => $this->content,
            'feature'               => $this->feature,
            'yearLevels'            => $this->yearLevels,
            'publishDate'           => $this->publishDate,
            'availability'          => $this->availability,
            'goUrl'                 => $this->goUrl,
            'previewUrl'            => $this->previewUrl,
            // part of series, stand alone, study guide
            'type'                  => $this->type,
            'series'                => $this->series,
            'descriptionOption'     => $this->descriptionOption,
            'divisions'             => $this->divisions,
            'regions'               => $this->regions,
            'tagline'               => $this->tagline,
            'reviews'               => $this->reviews,
            'aus_circa_price'       => $this->aus_circa_price,
            'nz_circa_price'        => $this->nz_circa_price,
            'auProductID'           => $this->auProductID,
            'nzProductID'           => $this->nzProductID,
            'formats'               => $this->formats,
            // ANZGO-1738
            'tabs'                  => $this->tabs,
            // ANZGO-1812
            'contentFolders'        => $this->contentFolders,
            'subjects'              => $this->subjects,
            'authors'               => $this->authors,
            'createdAt'             => $this->createdAt,
            'modifiedAt'            => $this->modifiedAt,
            'isEnabled'             => $this->isEnabled,
            'hasInspectionCopy'     => $this->hasInspectionCopy,
            'hasAccessCode'         => $this->hasAccessCode,
            'hasDownloadableFile'   => $this->hasDownloadableFile,
            'is_free_shipping'      => $this->is_free_shipping,
            'search_priority'       => $this->search_priority,
            'new_product_flag'      => $this->new_product_flag,
            'cart_message'          => $this->cart_message,
            'cart_popup_content'    => $this->cart_popup_content,
            'relatedTitleIDs'       => $this->relatedTitleIDs,
            'supportingTitleIDs'    => $this->supportingTitleIDs,
            // ANZGO-1708
            'isGoTitle'             => $this->isGoProduct,
            // ANZUAT-90
            'showBuyNow'            => $this->showBuyNow,
            'hmTitle'               => $this->hmTitle,
            // GCAP-625
            'demo_id'               => (int)$this->demo_id
        );

        if ($temp['id'] === false) {
            $temp['id'] = '';
        }

        return $temp;
    }

    public function setSubmitData($post)
    {
        $this->submit_data = $post;
    }

    public function setProductData($post)
    {
        $product_post = array();
        foreach ($post as $key => $value) {
            if (strpos($key, 'pr') === 0) {
                $product_post[$key] = $value;
            } elseif (strpos($key, 'ak') === 0) {
                $product_post[$key] = $value;
            }
        }
        $product_post['parentCID'] = 0;
        if (isset($post['parentCID'])) {
            $product_post['parentCID'] = $post['parentCID'];
        }

        $prName = "";
        $prDescription = "";

        if (isset($post['customName']) && strlen($post['customName']) > 0) {
            $prName = $post['customName'];
        } elseif (isset($post['name']) && strlen($post['name']) > 0) {
            $prName = $post['name'];
        }

        if (isset($post['customSubtitle']) && strlen($post['customSubtitle']) > 0) {
            $prName .= " - " . $post['customSubtitle'];
        } elseif (isset($post['subtitle']) && strlen($post['subtitle']) > 0) {
            $prName .= " - " . $post['subtitle'];
        }

        if (isset($post['edition']) && strlen($post['edition']) > 0) {
            $prName .= " Edition:" . $post['edition'];
        }


        if (isset($post['isbn13']) && strlen($post['isbn13']) > 0) {
            $prDescription .= "ISBN 13: {$post['isbn13']}\n";
        }

        if (isset($post['type']) && $post['type'] == 'part of series' && isset($post['series'])) {
            $prDescription .= "SERIES: {$post['series']}\n";
        } elseif (isset($post['type']) && $post['type'] == 'stand alone') {
            $prDescription .= "Stand Alone\n";
        } elseif (isset($post['type']) && $post['type'] == 'study guide') {
            $prDescription .= "Study Guide\n";
        }

        if (isset($post['descriptionOption']) && $post['descriptionOption'] == 'title short description' && isset($post['shortDescription'])) {
            $prDescription .= "Description: {$post['shortDescription']}\n";
        } elseif (isset($post['descriptionOption']) && $post['descriptionOption'] == 'title long description' && isset($post['longDescription'])) {
            $prDescription .= "Description: {$post['longDescription']}\n";
        } elseif (isset($post['descriptionOption']) && $post['descriptionOption'] == 'series short description' && isset($post['series'])) {
            Loader::model('series/model', 'cup_content');
            $series = new CupContentSeries();
            $prDescription .= 'Description: ';
            if ($series->loadByName($post['series'])) {
                $prDescription .= "{$series->shortDescription}\n";
            }
        } elseif (isset($post['descriptionOption']) && $post['descriptionOption'] == 'series long description' && isset($post['series'])) {
            Loader::model('series/model', 'cup_content');
            $series = new CupContentSeries();
            $prDescription .= 'Description: ';
            if ($series->loadByName($post['series'])) {
                $prDescription .= "{$series->longDescription}\n";
            }
        }

        $product_post['prName'] = trim($prName);
        $product_post['prDescription'] = trim($prDescription);
        $this->product_post = $product_post;
    }

    public function getDisplayName()
    {
        $prName = "";
        if (isset($this->customName) && strlen($this->customName) > 0) {
            $prName = $this->customName;
        } elseif (isset($this->name) && strlen($this->name) > 0) {
            $prName = $this->name;
        }

        return $prName;
    }

    public function getDisplaySubtitle()
    {
        $tmp = "";
        if (isset($this->customSubtitle) && strlen($this->customSubtitle) > 0) {
            $tmp = $this->customSubtitle;
        } elseif (isset($this->subtitle) && strlen($this->subtitle) > 0) {
            $tmp = $this->subtitle;
        }

        return $tmp;
    }

    public function generateProductName()
    {
        $prName = "";
        if (isset($this->customName) && strlen($this->customName) > 0) {
            $prName = $this->customName;
        } elseif (isset($this->name) && strlen($this->name) > 0) {
            $prName = $this->name;
        }

        if (isset($this->customSubtitle) && strlen($this->customSubtitle) > 0) {
            $prName .= " - " . $this->customSubtitle;
        } elseif (isset($this->subtitle) && strlen($this->subtitle) > 0) {
            $prName .= " - " . $this->subtitle;
        }

        if (isset($this->edition) && strlen($this->edition) > 0) {
            $prName .= " Edition:" . $this->edition;
        }

        return $prName;
    }

    protected function loadAnswerCodes()
    {
        $csv_answercode_filepath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'answercodes.csv';

        $answer_codes = array();

        if (($handle = fopen($csv_answercode_filepath, "r")) !== false) {
            while (($data = fgetcsv($handle, 10000, ",")) !== false) {
                $line = array();
                $line['code'] = trim($data[0]);
                $line['name'] = trim($data[1]);
                $line['note'] = trim($data[2]);
                if (isset($data[3])) {
                    $line['custom_name'] = trim($data[3]);
                }
                $answer_codes[] = $line;
            }
            fclose($handle);

            $this->answercode_definition = $answer_codes;
        } else {
            $this->answercode_definition = false;
        }
    }

    public function getAvailability($key = 'raw')
    {
        if (strcmp($key, 'raw') == 0) {
            return $this->availability;
        } elseif (strcmp($key, 'name') == 0) {
            if ($this->answercode_definition == false) {
                $this->loadAnswerCodes();
            }
            if (isset($this->answercode_definition[$this->availability])) {
                if (isset($this->answercode_definition[$this->availability]['custom_name'])) {
                    $key = 'custom_name';
                }
                return $this->answercode_definition[$this->availability][$key];
            } else {
                return false;
            }
        }
    }

    public function generateProductDescription()
    {
        $prDescription = "";
        if (isset($this->descriptionOption) && $this->descriptionOption == 'title short description' && isset($this->shortDescription)) {
            $prDescription .= "Description: {$this->shortDescription}\n";
        } elseif (isset($this->descriptionOption) && $this->descriptionOption == 'title long description' && isset($this->longDescription)) {
            $prDescription .= "Description: {$this->longDescription}\n";
        } elseif (isset($this->descriptionOption) && $this->descriptionOption == 'series short description' && isset($this->series)) {
            Loader::model('series/model', 'cup_content');
            $series = new CupContentSeries();
            $prDescription .= 'Description: ';
            if ($series->loadByName($post['series'])) {
                $prDescription .= "{$series->shortDescription}\n";
            }
        } elseif (isset($this->descriptionOption) && $this->descriptionOption == 'series long description' && isset($this->series)) {
            Loader::model('series/model', 'cup_content');
            $series = new CupContentSeries();
            $prDescription .= 'Description: ';
            if ($series->loadByName($this->series)) {
                $prDescription .= "{$series->longDescription}\n";
            }
        }

        if (isset($this->isbn13) && strlen($this->isbn13) > 0) {
            $prDescription .= "ISBN 13: {$this->isbn13}\n";
        }

        if (isset($this->type) && $this->type == 'part of series' && isset($this->series)) {
            $prDescription .= "SERIES: {$this->series}\n";
        } elseif (isset($this->type) && $this->type == 'stand alone') {
            $prDescription .= "Stand Alone\n";
        } elseif (isset($this->type) && $this->type == 'study guide') {
            $prDescription .= "Study Guide\n";
        }

        if (isset($this->descriptionOption) && $this->descriptionOption == 'title short description' && isset($this->shortDescription)) {
            $prDescription .= "Description: {$this->shortDescription}\n";
        } elseif (isset($this->descriptionOption) && $this->descriptionOption == 'title long description' && isset($this->longDescription)) {
            $prDescription .= "Description: {$this->longDescription}\n";
        } elseif (isset($this->descriptionOption) && $this->descriptionOption == 'series short description' && isset($this->series)) {
            Loader::model('series/model', 'cup_content');
            $series = new CupContentSeries();
            $prDescription .= 'Description: ';
            if ($series->loadByName($post['series'])) {
                $prDescription .= "{$series->shortDescription}\n";
            }
        } elseif (isset($this->descriptionOption) && $this->descriptionOption == 'series long description' && isset($this->series)) {
            Loader::model('series/model', 'cup_content');
            $series = new CupContentSeries();
            $prDescription .= 'Description: ';
            if ($series->loadByName($this->series)) {
                $prDescription .= "{$series->longDescription}\n";
            }
        }

        return $prDescription;
    }

    public function save()
    {
        if ($this->type == 'part of series') {

            Loader::model('series/model', 'cup_content');
            $series = new CupContentSeries();

            if (!$series->loadByName($this->series)) {
                $this->errors[] = "Invalid Series";
            }

            $this->regions = $series->regions;
            $this->divisions = $series->divisions;
            $this->subjects = $series->subjects;
            $this->review = $series->review;

            if (count($this->yearLevels) < 1) {
                $this->yearLevels = $series->yearLevels;
            }
        } elseif ($this->type == 'stand alone') {
            if (in_array('New South Wales', $this->regions) ||
                in_array('New South Wales', $this->regions) ||
                in_array('Northern Territory', $this->regions) ||
                in_array('Queensland', $this->regions) ||
                in_array('South Australia', $this->regions) ||
                in_array('Tasmania', $this->regions) ||
                in_array('Victoria', $this->regions) ||
                in_array('Western Australia', $this->regions)
            ) {
                $post['regions'][] = 'Australia';
            }

            $this->series = '';
        }

        if ($this->validation()) {
            $this->name = trim($this->name);
            $this->customName = trim($this->customName);
            $this->subtitle = trim($this->subtitle);
            $this->customSubtitle = trim($this->customSubtitle);
            $this->displayName = $this->getDisplayName();
            $this->displaySubtitle = $this->getDisplaySubtitle();

            // SB-425 added by jbernardez 20200205
            $this->myResourcesTitle = trim($this->myResourcesTitle);

            Loader::helper('tools', 'cup_content');
            $this->prettyUrl = $this->generatePrettyUrl();

            $this->divisions_save = "";
            if (is_array($this->divisions) && count($this->divisions) > 0) {
                $tmp_string = implode('][', $this->divisions);
                $this->divisions_save = '[' . $tmp_string . ']';
            }

            $this->regions_save = "";
            if (is_array($this->regions) && count($this->regions) > 0) {
                $tmp_string = implode('][', $this->regions);
                $this->regions_save = '[' . $tmp_string . ']';
            }

            $this->yearLevels_save = "";
            if (is_array($this->yearLevels) && count($this->yearLevels) > 0) {
                $tmp_string = implode('][', $this->yearLevels);
                $this->yearLevels_save = '[' . $tmp_string . ']';
            }

            $this->authors_save = "";
            if (is_array($this->authors_save) && count($this->authors_save) > 0) {
                $tmp_string = implode('][', $this->authors_save);
                $this->authors_save = '[' . $tmp_string . ']';
            }

            // update
            if ($this->id > 0) {
                $this->modifiedAt = date('Y-m-d H:i:s');

                $db = Loader::db();

                // SB-425 modified by jbernardez 20200205
                $q = "update CupContentTitle set isbn13 = ?, isbn10 = ?, name = ?, customName = ?,
                            displayName = ?, subtitle = ?, customSubtitle = ?,
                            displaySubtitle = ?, myResourcesTitle = ?, edition = ?, prettyUrl = ?,
                            shortDescription = ?, longDescription = ?, content = ?, feature = ?,
                            yearLevels = ?, publishDate = ?, availability = ?,
                            goUrl = ?, previewUrl = ?, type = ?, series = ?,
                            descriptionOption = ?, divisions = ?, regions = ?,
                            tagline = ?, " . //auProductID = ?, nzProductID = ?,
                        "   reviews = ?,
                            aus_circa_price = ?, nz_circa_price = ?,
                            isEnabled = ?, hasInspectionCopy = ?,
                            hasAccessCode = ?, hasDownloadableFile = ?, is_free_shipping = ?,
                            search_priority = ?, new_product_flag = ?,
                            cart_message = ?, cart_popup_content = ?,
                            createdAt = ?, modifiedAt = ?, isGoTitle = ?, showBuyNow = ?, demo_id = ?
                        WHERE id = ?"; // ANZGO-1708 isGoProduct
                // SB-425 modified by jbernardez 20200205
                $v = array($this->isbn13, $this->isbn10, $this->name, $this->customName,
                    $this->displayName, $this->subtitle, $this->customSubtitle,
                    $this->displaySubtitle, $this->myResourcesTitle, $this->edition, $this->prettyUrl,
                    $this->shortDescription, $this->longDescription, $this->content, $this->feature,
                    $this->yearLevels_save, $this->publishDate, $this->availability,
                    $this->goUrl, $this->previewUrl, $this->type, $this->series,
                    $this->descriptionOption, $this->divisions_save, $this->regions_save,
                    $this->tagline, //$this->auProductID, $this->nzProductID,
                    $this->reviews,
                    $this->aus_circa_price, $this->nz_circa_price,
                    $this->isEnabled, $this->hasInspectionCopy,
                    $this->hasAccessCode, $this->hasDownloadableFile, $this->is_free_shipping,
                    $this->search_priority, $this->new_product_flag,
                    $this->cart_message, $this->cart_popup_content,
                    $this->createdAt, $this->modifiedAt, $this->isGoProduct, $this->showBuyNow, $this->demo_id,
                    $this->id);
                $r = $db->prepare($q);
                $res = $db->Execute($r, $v);

                // ANZGO-3043
                if (($this->hmTitle != '') OR ($this->hmTitle != false)) {
                    $this->processHmTitle($this->hmTitle, $this->id);
                }

                if ($res) {
                    if (strlen($this->auProductID) > 0 && $this->auProductID > 0) {
                        $this->updateProduct('AU');
                    }

                    if (strlen($this->nzProductID) > 0 && $this->nzProductID > 0) {
                        $this->updateProduct('NZ');
                    }

                    // ANZGO-1911
                    if ($this->saveFormats() && $this->saveSubjects() &&
                            $this->saveAuthors() && $this->saveRelatedTitleIDs() && $this->saveSupportingTitleIDs()) {
                        $this->setToCache();
                        $this->loadByID($this->id);
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            // insert
            } else {
                return $this->saveNew();
            }
        } else {
            return false;
        }
    }

    // ANZGO-3043
    private function getHmTitle($titleID)
    {
        $db = Loader::db();

        // Check if brandCode is already there, if it is, run a update, if not, run a insert
        $query = "SELECT * FROM CupGoBrandCodeTitles WHERE titleID = ? ";
        $value = array($titleID);
        $result = $db->GetRow($query, $value);

        return ($result) ? $result['brandCode'] : false;
    }

    // ANZGO-3043
    private function processHmTitle($brandCode, $titleID)
    {
        $db = Loader::db();

        // Check if brandCode is already there, if it is, run a update, if not, run a insert
        $query = "SELECT * FROM CupGoBrandCodeTitles WHERE brandCode = ? ";
        $value = array($brandCode);
        $ready = $db->prepare($query);
        $result = $db->Execute($ready, $value);

        if ($result->fields == false) {
            // insert
            $runQuery = "INSERT INTO CupGoBrandCodeTitles (titleID, brandCode) VALUES (?, ?) ";
        } else {
            // update
            $runQuery = "UPDATE CupGoBrandCodeTitles SET titleID = ? WHERE brandCode = ? ";
        }

        $runValue = array($titleID, $brandCode);
        $runReady = $db->prepare($runQuery);
        $result = $db->Execute($runReady, $runValue);
    }

    public function updateProduct($region = 'AU', $price = false, $qty = false, $weight = false)
    {
        $product = false;
        if ($region == 'AU' && $this->auProductID && $this->auProductID > 0) {
            $product = $this->getAuProduct();
        } elseif ($region == 'NZ' && $this->nzProductID && $this->nzProductID > 0) {
            $product = $this->getNzProduct();
        }

        $prStatus = 1;
        $prPhysicalGood = 1;
        $prRequiresShipping = 1;
        $prQuantityUnlimited = 0;
        $prQuantityAllowNegative = 0;
        $prMinimumPurchaseQuantity = 1;
        $prQuantity = 0;
        $prWeight = 0;
        $prWeightUnits = 'kg';
        $prPrice = 99999;
        $prRequiresTax = 1;
        $prDimL = "0.0000";
        $prDimW = "0.0000";
        $prDimH = "0.0000";
        $prDimUnits = "cm";
        $prUseTieredPricing = 0;
        $prRequiresLoginToPurchase = 0;
        $prShippingModifier = 0;

        if ($product && is_numeric($product->getProductID()) && $product->getProductID() > 0) {
            $prStatus = $product->prStatus;
            $prRequiresShipping = $product->prRequiresShipping;
            $prQuantityAllowNegative = $product->prQuantityAllowNegative;
            $prQuantity = $product->prQuantity;
            $prMinimumPurchaseQuantity = $product->prMinimumPurchaseQuantity;
            $prWeight = $product->prWeight;
            $prWeightUnits = $product->prWeightUnits;
            $prPrice = $product->prPrice;
            $prRequiresTax = $product->prRequiresTax;
            $prDimL = $product->prDimL;
            $prDimW = $product->prDimW;
            $prDimH = $product->prDimH;
            $prDimUnits = $product->prDimUnits;
            $prUseTieredPricing = $product->prUseTieredPricing;
            $prRequiresLoginToPurchase = $product->prRequiresLoginToPurchase;
            $prShippingModifier = $product->prShippingModifier;
        }

        if ($price !== false) {
            $prPrice = $price;
        }

        if ($qty !== false) {
            $prQuantity = $qty;
        }

        if ($weight) {
            $prWeight = $weight;
        }

        if ($this->hasAccessCode || $this->hasDownloadableFile) {
            $prPhysicalGood = 0;
            $prRequiresShipping = 0;
            $prQuantityUnlimited = 1;
            $prQuantity = 0;
        }

        if ($region == 'NZ') {
            $prQuantityAllowNegative = 1;
        }

        $temp = Array(
            'prStatus' => $prStatus,
            'prQuantity' => $prQuantity,
            'prQuantityAllowNegative' => $prQuantityAllowNegative,
            'prQuantityUnlimited' => $prQuantityUnlimited,
            'prMinimumPurchaseQuantity' => $prMinimumPurchaseQuantity,
            'prPhysicalGood' => $prPhysicalGood,
            'prRequiresLoginToPurchase' => $prRequiresLoginToPurchase,
            'prPrice' => $prPrice,
            'prRequiresTax' => $prRequiresTax,
            'prRequiresShipping' => $prRequiresShipping,
            'prWeight' => $prWeight,
            'prWeightUnits' => $prWeightUnits,
            'prShippingModifier' => $prShippingModifier
        );

        if ($region == 'AU') {
            return $this->saveAuProduct($temp);
        } elseif ($region == 'NZ') {
            return $this->saveNzProduct($temp);
        }

        return false;
    }

    public function saveAuProduct($post_data)
    {
        $post_data['prName'] = $this->generateProductName();
        $post_data['prDescription'] = $this->generateProductDescription();
        $post_data['prDescription'] = "Shop Region: Australia\n" . $post_data['prDescription'];
        $post_data['prLanguage'] = 'en_AU';

        $product_id = $this->auProductID;
        if ($this->auProductID > 0) {
            $product_id = $this->saveCoreCommerceProduct($this->auProductID, $post_data);
        } else {
            $product_id = $this->saveCoreCommerceProduct(false, $post_data);
        }

        if ($product_id) {
            if ($this->auProductID > 0) {
                return true;
            } else {
                $db = Loader::db();
                $q = "update CupContentTitle set auProductID = ? WHERE id = ?";
                $v = array($product_id, $this->id);
                $r = $db->prepare($q);
                $res = $db->Execute($r, $v);
                if ($res) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    public function saveNzProduct($post_data)
    {
        $post_data['prName'] = $this->generateProductName();
        $post_data['prDescription'] = $this->generateProductDescription();
        $post_data['prDescription'] = "Shop Region: New Zealand\n" . $post_data['prDescription'];
        $post_data['prLanguage'] = 'en_NZ';

        $product_id = $this->saveCoreCommerceProduct($this->nzProductID, $post_data);

        if ($product_id) {
            if ($this->nzProductID > 0) {
                return true;
            } else {
                $db = Loader::db();
                $q = "update CupContentTitle set nzProductID = ? WHERE id = ?";
                $v = array($product_id, $this->id);
                $r = $db->prepare($q);
                $res = $db->Execute($r, $v);
                if ($res) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    protected function saveCoreCommerceProduct($product_id = false, $post_data = false)
    {
        Loader::model("product/model", 'core_commerce');

        $product = new CoreCommerceProduct();
        if (strlen($product_id) > 0 && $product->load($product_id)) {

            $product->update($post_data);

            $product->setProductSets($post_data['prsID']);
            Loader::model("attribute/categories/core_commerce_product", 'core_commerce');
            $aks = CoreCommerceProductAttributeKey::getList();
            foreach ($aks as $uak) {
                $uak->saveAttributeForm($product);
            }

            return $product->getProductID();
        } elseif (strlen($product_id) > 0 && $product_id > 0) {
            $this->errors[] = 'Invalid Product ID';

            return false;
        } else {
            $product = CoreCommerceProduct::add($post_data);

            $product->setPurchaseGroups($post_data['gID']);
            $product->setProductSets($post_data['prsID']);

            Loader::model("attribute/categories/core_commerce_product", 'core_commerce');
            $aks = CoreCommerceProductAttributeKey::getList();
            foreach ($aks as $uak) {
                $uak->saveAttributeForm($product);
            }

            return $product->getProductID();
        }
    }

    public function getAuProduct()
    {
        $product = new CoreCommerceProduct();
        if ($this->auProductID && $this->auProductID > 0) {
            $product->load($this->auProductID);
        }

        return $product;
    }

    public function getNzProduct()
    {
        $product = new CoreCommerceProduct();
        if ($this->nzProductID && $this->nzProductID > 0) {
            $product->load($this->nzProductID);
        }

        return $product;
    }

    public function getCurrentLocateProduct()
    {
        if (!isset($_SESSION['DEFAULT_LOCALE'])) {
            $_SESSION['DEFAULT_LOCALE'] = 'en_AU';
        }

        if (strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0) {
            if ($this->shouldAUProduct()) {
                return $this->getAuProduct();
            }
        } elseif (strcmp($_SESSION['DEFAULT_LOCALE'], 'en_NZ') == 0) {
            if ($this->shouldNZProduct()) {
                return $this->getNzProduct();
            }
        }

        return false;
    }

    public function getCurrentLocateCircaPrice()
    {
        if (!isset($_SESSION['DEFAULT_LOCALE'])) {
            $_SESSION['DEFAULT_LOCALE'] = 'en_AU';
        }

        $val = false;
        if (strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0) {
            $tmp = floatval($this->aus_circa_price);
            if ($tmp - 0.0001 > 0) {
                $val = number_format($tmp, 2, '.', '');
            }
        } elseif (strcmp($_SESSION['DEFAULT_LOCALE'], 'en_NZ') == 0) {
            $tmp = floatval($this->nz_circa_price);
            if ($tmp - 0.0001 > 0) {
                $val = number_format($tmp, 2, '.', '');
            }
        }

        return $val;
    }

    public function getAuthorObjects()
    {
        Loader::model('author/model', 'cup_content');

        $end_note = array(
            '(ed.)',
            '(introduction)',
            '(Editor)',
            '(illustrator)',
            '(Series editor)',
            '(Consulting Editor)',
        );

        $authors = array();
        foreach ($this->authors as $each_author) {
            $name = $each_author;

            // replace multiple spaces with single space
            $name = preg_replace('!\s+!', ' ', $name);
            foreach ($end_note as $suf) {
                if (strrpos($name, $suf) > 1) {
                    $name = substr($name, 0, strrpos($name, $suf));
                }
            }

            $name = trim($name);
            $authors[$each_author] = CupContentAuthor::fetchByName($name);
        }

        return $authors;
    }

    public function getSeriesObject()
    {
        if (strcmp($this->type, 'part of series') == 0) {
            Loader::model('series/model', 'cup_content');
            return CupContentSeries::fetchByName($this->series);
        }

        return false;
    }

    public function getRelatedObjects()
    {
        $objs = array();
        foreach ($this->relatedTitleIDs as $each_id) {
            if ($each_id != $this->id) {
                $tmp_obj = self::fetchByID($each_id);
                if ($tmp_obj && $tmp_obj->isEnabled) {
                    $objs[] = $tmp_obj;
                }
            }
        }

        return $objs;
    }

    public function getSupportingObjects()
    {
        $objs = array();
        foreach ($this->supportingTitleIDs as $each_id) {
            if ($each_id != $this->id) {
                $tmp_obj = self::fetchByID($each_id);
                if ($tmp_obj && $tmp_obj->isEnabled) {
                    $objs[] = $tmp_obj;
                }
            }
        }

        return $objs;
    }

    public function hasSeries()
    {
        if (strlen(trim($this->series)) > 0) {
            return true;
        } else {
            return false;
        }
    }

    protected function saveFormats($title_id = false)
    {
        $is_saved = true;

        if ($title_id === false) {
            $title_id = $this->id;
        }

        if (is_array($this->formats)) {
            $db = Loader::db();
            $exisiting_formats = array();
            $tmp_query = "select * from CupContentTitleFormats where titleID = ?";
            $tmp_results = $db->getAll($tmp_query, array($title_id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $exisiting_formats[] = $each_row['format'];
                }
            }

            foreach ($this->formats as $each_format) {
                if (!in_array($each_format, $exisiting_formats)) {
                    $tmp_query = "INSERT INTO CupContentTitleFormats (titleID, format) VALUES (?, ?)";
                    $tmp_result = $db->Execute($tmp_query, array($title_id, $each_format));
                    if (!$tmp_result) {
                        $this->errors[] = "Format [{$each_format}] could not be added.";
                        $is_saved = false;
                    }
                }
            }

            foreach ($exisiting_formats as $each_format) {
                if (!in_array($each_format, $this->formats)) {
                    $tmp_query = "DELETE FROM CupContentTitleFormats WHERE titleID = ? AND format = ?";
                    $tmp_result = $db->Execute($tmp_query, array($title_id, $each_format));
                    if (!$tmp_result) {
                        $this->errors[] = "Format [{$each_format}] could not be deleted.";
                        $is_saved = false;
                    }
                }
            }
        } else {
            $is_saved = false;
            $this->errors[] = "Format data error could not be saved.";
        }

        return $is_saved;
    }

    // ANZGO-1738
    protected function saveTabs($title_id = false)
    {
        $is_saved = true;

        if ($title_id === false) {
            $title_id = $this->id;
        }

        if (is_array($this->tabs)) {
            $db = Loader::db();
            $exisiting_tabs = array();
            $tmp_query = "select * from CupGoTabs where titleID = ?";
            $tmp_results = $db->getAll($tmp_query, array($title_id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $exisiting_tabs[] = $each_row['TabName'];
                }
            }

            foreach ($this->tabs as $each_tab) {
                if (!in_array($each_tab, $exisiting_tabs)) {
                    $tmp_query = "INSERT INTO CupGoTabs (titleID, TabName, TabLevel) VALUES (?, ?, ?)";
                    $tmp_result = $db->Execute($tmp_query, array($title_id, $each_tab, 1));
                    if (!$tmp_result) {
                        $this->errors[] = "Tab [{$each_tab}] could not be added.";
                    }
                }
            }

            foreach ($exisiting_tabs as $each_tab) {
                if (!in_array($each_tab, $this->tabs)) {
                    $tmp_query = "DELETE FROM CupGoTabs WHERE titleID = ? AND TabName = ?";
                    $tmp_result = $db->Execute($tmp_query, array($title_id, $each_tab));
                    if (!$tmp_result) {
                        $this->errors[] = "Tab [{$each_tab}] could not be deleted.";
                    }
                }
            }
        } else {
            $is_saved = false;
            $this->errors[] = "Tab data error could not be saved.";
        }

        return $is_saved;
    }

    // ANZGO-1944
    public function saveNewTab($data)
    {
        $db = Loader::db();

        $groups = $this->getGroups();
        $studentKey = array_search("Student", $groups);

        $newSortSql = "SELECT MAX(SortOrder) + 1 AS newSortNum FROM CupGoTabs WHERE titleID = ?";
        $result = $db->GetRow($newSortSql, array($data['ID']));
        $sortNum = ($result['newSortNum']) ? $result['newSortNum'] : 1;

        $sql = "INSERT INTO CupGoTabs (titleID, TabName, TabLevel, SortOrder, UserTypeIDRestriction,ContentVisibility) VALUES (?, ?, ?, ?, ?, ?)";
        $db->Execute($sql,array($data['ID'],$data['TabName'],1,$sortNum,$studentKey,"closed"));
        return $db->Insert_ID('CupGoTabs');
    }

    // ANZGO-1812
    protected function saveContentFolders($title_id = false)
    {
        $is_saved = true;

        if ($title_id === false) {
            $title_id = $this->id;
        }

        if (is_array($this->contentFolders)) {
            $db = Loader::db();
            $exisiting_contentFolders = array();
            $tmp_query = "select * from CupGoContentFolders where titleID = ?";
            $tmp_results = $db->getAll($tmp_query, array($title_id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $exisiting_contentFolders[] = $each_row['FolderName'];
                }
            }

            foreach ($this->contentFolders as $each_contentFolder) {
                if (!in_array($each_contentFolder, $exisiting_contentFolders)) {
                    $tmp_query = "INSERT INTO CupGoContentFolders (titleID, FolderName) VALUES (?, ?)";
                    $tmp_result = $db->Execute($tmp_query, array($title_id, $each_contentFolder));
                    if (!$tmp_result) {
                        $this->errors[] = "Folder [{$each_contentFolder}] could not be added.";
                    }
                }
            }

            foreach ($exisiting_contentFolders as $each_contentFolder) {
                if (!in_array($each_contentFolder, $this->contentFolders)) {
                    $tmp_query = "DELETE FROM CupGoContentFolders WHERE titleID = ? AND FolderName = ?";
                    $tmp_result = $db->Execute($tmp_query, array($title_id, $each_contentFolder));
                    if (!$tmp_result) {
                        $this->errors[] = "Folder [{$each_contentFolder}] could not be deleted.";
                    }
                }
            }

        } else {
            $is_saved = false;
            $this->errors[] = "Tab data error could not be saved.";
        }

        return $is_saved;
    }

    protected function saveSubjects($title_id = false)
    {
        $is_saved = true;

        if ($title_id === false) {
            $title_id = $this->id;
        }

        if (is_array($this->subjects)) {
            $db = Loader::db();
            $exisiting_subjects = array();
            $tmp_query = "select * from CupContentTitleSubjects where titleID = ?";
            $tmp_results = $db->getAll($tmp_query, array($title_id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $exisiting_subjects[] = $each_row['subject'];
                }
            }

            foreach ($this->subjects as $each_subject) {
                if (!in_array($each_subject, $exisiting_subjects)) {
                    $tmp_query = "INSERT INTO CupContentTitleSubjects (titleID, subject) VALUES (?, ?)";
                    $tmp_result = $db->Execute($tmp_query, array($title_id, $each_subject));
                    if (!$tmp_result) {
                        $this->errors[] = "Subject [{$each_subject}] could not be added.";
                        $is_saved = false;
                    }
                }
            }

            foreach ($exisiting_subjects as $each_subject) {
                if (!in_array($each_subject, $this->subjects)) {
                    $tmp_query = "DELETE FROM CupContentTitleSubjects WHERE titleID = ? AND subject = ?";
                    $tmp_result = $db->Execute($tmp_query, array($title_id, $each_subject));
                    if (!$tmp_result) {
                        $this->errors[] = "Subject [{$each_subject}] could not be deleted.";
                        $is_saved = false;
                    }
                }
            }
        } else {
            $is_saved = false;
            $this->errors[] = "Subject data error could not be saved.";
        }

        return $is_saved;
    }

    protected function saveAuthors($title_id = false)
    {
        $is_saved = true;

        if ($title_id === false) {
            $title_id = $this->id;
        }

        if (is_array($this->authors)) {
            $db = Loader::db();
            $exisiting_authors = array();
            $tmp_query = "select * from CupContentTitleAuthors where titleID = ?";
            $tmp_results = $db->getAll($tmp_query, array($title_id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $exisiting_authors[] = $each_row['author'];
                }
            }

            foreach ($this->authors as $each_author) {
                if (!in_array($each_author, $exisiting_authors)) {
                    $tmp_query = "INSERT INTO CupContentTitleAuthors (titleID, author) VALUES (?, ?)";
                    $tmp_result = $db->Execute($tmp_query, array($title_id, $each_author));
                    if (!$tmp_result) {
                        $this->errors[] = "Author [{$each_author}] could not be added.";
                        $is_saved = false;
                    }
                }
            }

            foreach ($exisiting_authors as $each_author) {
                if (!in_array($each_author, $this->authors)) {
                    $tmp_query = "DELETE FROM CupContentTitleAuthors WHERE titleID = ? AND author = ?";
                    $tmp_result = $db->Execute($tmp_query, array($title_id, $each_author));
                    if (!$tmp_result) {
                        $this->errors[] = "Author [{$each_author}] could not be deleted.";
                        $is_saved = false;
                    }
                }
            }
        } else {
            $is_saved = false;
            $this->errors[] = "Author data error could not be saved.";
        }

        return $is_saved;
    }

    protected function saveRelatedTitleIDs($title_id = false)
    {
        $is_saved = true;

        if ($title_id === false) {
            $title_id = $this->id;
        }

        if (is_array($this->relatedTitleIDs)) {
            $db = Loader::db();
            $exisiting_title_ids = array();
            $tmp_query = "select * from CupContentTitleRelatedTitle where titleID = ?";
            $tmp_results = $db->getAll($tmp_query, array($title_id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $exisiting_title_ids[] = $each_row['related_titleID'];
                }
            }

            foreach ($this->relatedTitleIDs as $each_title_id) {
                if (!in_array($each_title_id, $exisiting_title_ids)) {
                    $tmp_query = "INSERT INTO CupContentTitleRelatedTitle (titleID, related_titleID) VALUES (?, ?)";
                    $tmp_result = $db->Execute($tmp_query, array($title_id, $each_title_id));
                    if (!$tmp_result) {
                        $this->errors[] = "Related Title [ID: {$each_title_id}] could not be added.";
                        $is_saved = false;
                    }
                }
            }

            foreach ($exisiting_title_ids as $each_title_id) {
                if (!in_array($each_title_id, $this->relatedTitleIDs)) {
                    $tmp_query = "DELETE FROM CupContentTitleRelatedTitle WHERE titleID = ? AND related_titleID = ?";
                    $tmp_result = $db->Execute($tmp_query, array($title_id, $each_title_id));
                    if (!$tmp_result) {
                        $this->errors[] = "Related Title [ID: {$each_title_id}] could not be deleted.";
                        $is_saved = false;
                    }
                }
            }
        } else {
            $is_saved = false;
            $this->errors[] = "Related Titles could not be saved.";
        }

        return $is_saved;
    }

    protected function saveSupportingTitleIDs($title_id = false)
    {
        $is_saved = true;

        if ($title_id === false) {
            $title_id = $this->id;
        }

        if (is_array($this->supportingTitleIDs)) {
            $db = Loader::db();
            $exisiting_title_ids = array();
            $tmp_query = "select * from CupContentTitleSupportingTitle where titleID = ?";
            $tmp_results = $db->getAll($tmp_query, array($title_id));

            if (is_array($tmp_results)) {
                foreach ($tmp_results as $each_row) {
                    $exisiting_title_ids[] = $each_row['supporting_titleID'];
                }
            }

            foreach ($this->supportingTitleIDs as $each_title_id) {
                if (!in_array($each_title_id, $exisiting_title_ids)) {
                    $tmp_query = "INSERT INTO CupContentTitleSupportingTitle (titleID, supporting_titleID) VALUES (?, ?)";
                    $tmp_result = $db->Execute($tmp_query, array($title_id, $each_title_id));
                    if (!$tmp_result) {
                        $this->errors[] = "Supporting Title [ID: {$each_title_id}] could not be added.";
                        $is_saved = false;
                    }
                }
            }

            foreach ($exisiting_title_ids as $each_title_id) {
                if (!in_array($each_title_id, $this->supportingTitleIDs)) {
                    $tmp_query = "DELETE FROM CupContentTitleSupportingTitle WHERE titleID = ? AND supporting_titleID = ?";
                    $tmp_result = $db->Execute($tmp_query, array($title_id, $each_title_id));
                    if (!$tmp_result) {
                        $this->errors[] = "Supporting Title [ID: {$each_title_id}] could not be deleted.";
                        $is_saved = false;
                    }
                }
            }
        } else {
            $is_saved = false;
            $this->errors[] = "Related Titles could not be saved.";
        }

        return $is_saved;
    }

    public function saveNew()
    {
        $this->createdAt = date('Y-m-d H:i:s');
        $this->modifiedAt = $this->createdAt;

        $this->coreProductID = $product_id;

        $db = Loader::db();
        // SB-425 modified by jbernardez 20200205
        $q = "INSERT INTO CupContentTitle (isbn13, isbn10, name, customName, displayName,
                                    subtitle, customSubtitle, displaySubtitle, myResourcesTitle, edition,
                                    prettyUrl, shortDescription, longDescription, content,
                                    feature, yearLevels, publishDate,
                                    availability, goUrl, previewUrl, type,
                                    series, descriptionOption,
                                    divisions, regions, tagline,
                                    auProductID, nzProductID,
                                    reviews, aus_circa_price, nz_circa_price,
                                    isEnabled, hasInspectionCopy,
                                    hasAccessCode, hasDownloadableFile, is_free_shipping,
                                    search_priority, new_product_flag,
                                    cart_message, cart_popup_content,
                                    createdAt, modifiedAt, isGoTitle, showBuyNow, demo_id)
                    VALUES (?, ?, ?, ?, ?,
                            ?, ?, ?, ?, ?,
                            ?, ?, ?, ?,
                            ?, ?, ?,
                            ?, ?, ?, ?,
                            ?, ?,
                            ?, ?, ?,
                            ?, ?,
                            ?, ?, ?,
                            ?, ?,
                            ?, ?, ?,
                            ?, ?,
                            ?, ?,
                            ?, ?, ?, ?, ?)"; // ANZGO-1708

        // SB-425 modified by jbernardez 20200205
        $v = array($this->isbn13, $this->isbn10, $this->name, $this->customName, $this->displayName,
            $this->subtitle, $this->customSubtitle, $this->displaySubtitle, $this->myResourcesTitle, $this->edition,
            $this->prettyUrl, $this->shortDescription, $this->longDescription, $this->content,
            $this->feature, $this->yearLevels_save, $this->publishDate,
            $this->availability, $this->goUrl, $this->previewUrl, $this->type,
            $this->series, $this->descriptionOption,
            $this->divisions_save, $this->regions_save, $this->tagline,
            $this->auProductID, $this->nzProductID,
            $this->reviews, $this->aus_circa_price, $this->nz_circa_price,
            $this->isEnabled, $this->hasInspectionCopy,
            $this->hasAccessCode, $this->hasDownloadableFile, $this->is_free_shipping,
            $this->search_priority, $this->new_product_flag,
            $this->cart_message, $this->cart_popup_content,
            $this->createdAt, $this->modifiedAt, $this->isGoProduct, $this->showBuyNow, $this->demo_id); // ANZGO-1708
        $r = $db->prepare($q);
        $res = $db->Execute($r, $v);

        if ($res) {
            $new_id = $db->Insert_ID();

            // ANZGO-3043
            if (($this->hmTitle != '') OR ($this->hmTitle != false)) {
                $this->processHmTitle($this->hmTitle, $new_id);
            }

            // ANZGO-1911
            if ($this->saveFormats($new_id) && $this->saveSubjects($new_id) && $this->saveAuthors($new_id) && $this->saveRelatedTitleIDs($new_id) && $this->saveSupportingTitleIDs($new_id)) {
                $this->loadByID($new_id);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete()
    {
        if ($this->id > 0) {
            $this->deleteImage();

            Loader::model('title_sample_page/model', 'cup_content');
            $sample_file_dir = TITLE_SAMPLE_PAGE_DIR . DIRECTORY_SEPARATOR . $this->id;
            if (is_dir($sample_file_dir)) {
                foreach (scandir($sample_file_dir) as $item) {
                    if ($item == '.' || $item == '..')
                        continue;
                    unlink($sample_file_dir . DIRECTORY_SEPARATOR . $item);
                }
                rmdir($sample_file_dir);
            }

            $db = Loader::db();
            $tmp_query = "DELETE FROM CupContentTitleSamplePages WHERE titleID = ?";
            $tmp_result = $db->Execute($tmp_query, array($this->id));
            if (!$tmp_result) {
                $this->errors[] = "Sample Pages could not be deleted.";
            }

            $tmp_query = "DELETE FROM CupContentTitleFormats WHERE titleID = ?";
            $tmp_result = $db->Execute($tmp_query, array($this->id));
            if (!$tmp_result) {
                $this->errors[] = "Formats could not be deleted.";
            }

            // ANZGO-1738
            $tmp_query = "DELETE FROM CupGoTabs WHERE titleID = ?";
            $tmp_result = $db->Execute($tmp_query, array($this->id));
            if (!$tmp_result) {
                $this->errors[] = "Tabs could not be deleted.";
            }

            $tmp_query = "DELETE FROM CupContentTitleSubjects WHERE titleID = ?";
            $tmp_result = $db->Execute($tmp_query, array($this->id));
            if (!$tmp_result) {
                $this->errors[] = "Subjects could not be deleted.";
            }

            $tmp_query = "DELETE FROM CupContentTitleAuthors WHERE titleID = ?";
            $tmp_result = $db->Execute($tmp_query, array($this->id));
            if (!$tmp_result) {
                $this->errors[] = "Authors could not be deleted.";
            }

            $tmp_query = "DELETE FROM CupContentTitleRelatedTitle WHERE titleID = ?";
            $tmp_result = $db->Execute($tmp_query, array($this->id));
            if (!$tmp_result) {
                $this->errors[] = "Related titles could not be deleted.";
            }

            if ($this->auProductID > 0) {
                $this->getAuProduct()->delete();
            }

            if ($this->nzProductID > 0) {
                $this->getNzProduct()->delete();
            }

            if (count($this->errors) > 0) {
                return false;
            }

            $q = "DELETE FROM CupContentTitle WHERE id = ?";

            $result = $db->Execute($q, array($this->id));
            if ($result) {
                return true;
            } else {
                $this->errors[] = "Error occurs when deleting this Title";
                return false;
            }
        } else {
            $this->errors[] = "id is missing";
            return false;
        }
    }

    public function validation()
    {
        $this->name = trim($this->name);
        $this->previewUrl = trim($this->previewUrl);
        $this->goUrl = trim($this->goUrl);

        $this->errors = array();

        if (strlen($this->isbn13) < 1) {
            $this->errors[] = "ISBN13 is required";
        } else if (strlen($this->isbn13) != 13) {
            $this->errors[] = "ISBN13 required exact 13 digits";
        }

        if (strlen($this->name) < 1) {
            $this->errors[] = "Name is required";
        } else {
            $db = Loader::db();
            $params = array($this->name, $this->customSubtitle);

            $q = "select count(id) as count from CupContentTitle WHERE name LIKE ? AND subtitle LIKE ?";
            if ($this->id > 0) {
                $q .= ' AND id <> ?';
                $params[] = $this->id;
            }
            $db_result = $db->getRow($q, $params);

            if ($db_result['count'] > 0) {
                $this->errors[] = "Name & Subtitle has been used";
            }
        }

        if (count($this->regions) < 1) {
            $this->errors[] = "Region is required";
        }

        if (count($this->divisions) < 1) {
            $this->errors[] = "Division is required";
        }

        if (count($this->errors) > 0) {
            return false;
        }

        return true;
    }

    public function isISBN13Valid($n)
    {
        if (strlen($n) != 13) {
            return false;
        }
        $check = 0;
        for ($i = 0; $i < 13; $i+=2)
            $check += substr($n, $i, 1);
        for ($i = 1; $i < 12; $i+=2)
            $check += 3 * substr($n, $i, 1);
        return $check % 10 == 0;
    }

    public function generatePrettyUrl()
    {
        Loader::helper('tools', 'cup_content');
        $tmp_title = trim($this->name);
        if (strlen(trim($this->customName)) > 0) {
            $tmp_title = trim($this->customName);
        }

        $tmp_subtitle = trim($this->subtitle);
        if (strlen(trim($this->customSubtitle)) > 0) {
            $tmp_subtitle = trim($this->customSubtitle);
        }

        $tmp_edition = trim($this->edition);
        if (strlen(trim($this->edition)) > 0) {
            $tmp_edition = trim($this->edition);
        }
        $tmp_title = CupContentToolsHelper::string2prettyURL($tmp_title);
        $tmp_edition = CupContentToolsHelper::string2prettyURL($tmp_edition);
        $tmp_subtitle = CupContentToolsHelper::string2prettyURL($tmp_subtitle);

        if (strlen($tmp_subtitle) > 0) {
            $tmp_title .= ':' . $tmp_subtitle;
        }

        if (strlen($tmp_edition) > 0) {
            $tmp_title .= ':edition-' . $tmp_edition;
        }

        return $tmp_title;
    }

    public function hasAccessCode()
    {
        if ($this->hasAccessCode) {
            return true;
        } else {
            return false;
        }
    }

    public function saveImage($filename)
    {
        $dest_filename_original = $this->isbn13;

        $dest_filename_180 = $this->isbn13 . '_180.jpg';
        $dest_filename_90 = $this->isbn13 . '_90.jpg';
        $dest_filename_60 = $this->isbn13 . '_60.jpg';

        if (!is_dir(TITLE_IMAGES_FOLDER)) {
            mkdir(TITLE_IMAGES_FOLDER, 0777, true);
        }

        $dest_filename_original = TITLE_IMAGES_FOLDER . $dest_filename_original;
        $res = copy($filename, $dest_filename_original);
        chmod($dest_filename_original, 0777);

        $dest_filename_180 = TITLE_IMAGES_FOLDER . $dest_filename_180;
        $dest_filename_90 = TITLE_IMAGES_FOLDER . $dest_filename_90;
        $dest_filename_60 = TITLE_IMAGES_FOLDER . $dest_filename_60;

        $imgHelper = Loader::helper('image', 'cup_content');
        $imgHelper::resize2width($filename, 180, $dest_filename_180);
        $imgHelper::resize2width($filename, 90, $dest_filename_90);
        $imgHelper::resize2width($filename, 60, $dest_filename_60);
        chmod($dest_filename_180, 0777);
        chmod($dest_filename_90, 0777);
        chmod($dest_filename_60, 0777);
    }

    public function saveGlobalGoImage($filename)
    {
        $imgHelper = Loader::helper('image', 'cup_content');
        $formattedDateTime = date('YmdHis');
        $randomString = openssl_random_pseudo_bytes(10);
        $hex = bin2hex($randomString);
        $safeFileName = $formattedDateTime . '-' . $hex . '-' . $this->isbn13 . '.jpg';
        $path = TITLE_IMAGES_FOLDER . $safeFileName;

        $imgHelper::resize2width($filename, static::GLOBAL_GO_IMG_SIZE, $path);
        chmod($path, 0777);

        return $safeFileName;
    }

    public function saveThumbnailURL($filename = null)
    {
        $db = Loader::db();

        $path = null;

        if ($filename !== null) {
            $sourcePath = TITLE_IMAGES_FOLDER . $filename;
            $path = 'files/cup_content/images/titles/' . $filename;

            if (!file_exists($sourcePath)) {
                $path = null;
            }
        }

        $query = "UPDATE CupContentTitle SET thumbnail_url = ? WHERE id = ?";
        $db->Execute($query, [$path, $this->id]);
    }

    // SB-389 added by jbernardez 20191106
    // should run this if Series Image was located
    public function reSaveImageFromSeries($filename)
    {
        $dest_filename_original = $this->isbn13;

        $dest_filename_180 = $this->isbn13 . '_180.jpg';
        $dest_filename_90 = $this->isbn13 . '_90.jpg';
        $dest_filename_60 = $this->isbn13 . '_60.jpg';

        if (!is_dir(TITLE_IMAGES_FOLDER)) {
            mkdir(TITLE_IMAGES_FOLDER, 0777, true);
        }

        $dest_filename_original = TITLE_IMAGES_FOLDER . $dest_filename_original;
        $res = copy(SERIES_IMAGES_FOLDER . $filename, $dest_filename_original);
        chmod($dest_filename_original, 0777);

        $dest_filename_180 = TITLE_IMAGES_FOLDER . $dest_filename_180;
        $dest_filename_90 = TITLE_IMAGES_FOLDER . $dest_filename_90;
        $dest_filename_60 = TITLE_IMAGES_FOLDER . $dest_filename_60;

        $imgHelper = Loader::helper('image', 'cup_content');
        $imgHelper::resize2width(SERIES_IMAGES_FOLDER . $filename, 180, $dest_filename_180);
        $imgHelper::resize2width(SERIES_IMAGES_FOLDER . $filename, 90, $dest_filename_90);
        $imgHelper::resize2width(SERIES_IMAGES_FOLDER . $filename, 60, $dest_filename_60);
        chmod($dest_filename_180, 0777);
        chmod($dest_filename_90, 0777);
        chmod($dest_filename_60, 0777);
    }

    public function getImageURL($size = false)
    {
        $url = DIR_REL . '/packages/cup_content/images/';
        $images_url = DIR_REL . '/files/cup_content/images/titles/';
        $filename = "imagena";
        if ($size) {
            $filename = $this->isbn13 . '_' . $size . '.jpg';
            if (!file_exists(TITLE_IMAGES_FOLDER . $filename)) {
                $filename = "title_na_" . $size . '.jpg';
            } else {
                $url = $images_url;
            }
        } else {
            $filename = $this->isbn13;
            if (!file_exists(TITLE_IMAGES_FOLDER . $filename)) {
                $filename = "title_na.jpg";
            } else {
                $url = $images_url;
            }
        }

        $url .= $filename;

        // Added by Paul Balila for ticket ANZGO-3032, 2016-10-28
        // Add a unique number as a query after the filename.
        $url .= '?=' . time();
        return $url;
    }

    public function deleteImage()
    {
        /* Remove all images */
        $files = array();
        $files[] = TITLE_IMAGES_FOLDER . $this->isbn13;
        $files[] = TITLE_IMAGES_FOLDER . $this->isbn13 . '_180.jpg';
        $files[] = TITLE_IMAGES_FOLDER . $this->isbn13 . '_90.jpg';
        $files[] = TITLE_IMAGES_FOLDER . $this->isbn13 . '_60.jpg';

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    public function hasImage()
    {
        $filename = $this->isbn13;
        return file_exists(TITLE_IMAGES_FOLDER . $filename);
    }

    public static function convertPost($post)
    {
        $default_values = array(
            'id' => "",
            'isbn13' => "",
            'isbn10' => "",
            'name' => "",
            'customName' => "",
            'customName' => "",
            'subtitle' => "",
            'customSubtitle' => "",
            // SB-425 added by jbernardez 20200206
            'myResourcesTitle' => "",
            'edition' => "",
            'prettyUrl' => "",
            'shortDescription' => "",
            'longDescription' => "",
            'feature' => "",
            'yearLevels' => "",
            'publishDate' => "",
            'availability' => "",
            'goUrl' => "",
            // part of series, stand alone, study guide
            'type' => "",
            'series' => "",
            'descriptionOption' => "",
            'divisions' => array(),
            'regions' => array(),
            'tagline' => "",
            'reviews' => "",
            "auz_circa_price" => "",
            "nz_circa_price" => "",
            'coreProductID' => "",
            'formats' => array(),
            // ANZGO-1738
            'tabs' => array(),
            'subjects' => array(),
            'authors' => array(),
            'createdAt' => "",
            'modifiedAt' => "",
            'isEnabled' => 0,
            'hasInspectionCopy' => 0,
            'hasAccessCode' => 0,
            // ANZGO-1708
            'isGoProduct' => 0,
            // GCAP-625
            'demo_id' => ""
        );

        $post = array_merge($default_values, $post);

        if ($post['yearLevels'] == "") {
            $post['yearLevels'] = array();
        } elseif (is_string($post['yearLevels'])) {
            $year_value = $post['yearLevels'];
            $tmp_yearLevel = array();
            if (strlen($year_value) > 1) {
                if (!in_array($year_value, $tmp_yearLevel)) {
                    $tmp_yearLevel[] = $year_value;
                }

                $tmp = explode('-', $year_value);
                $min = 1;
                $max = $tmp[1];
                if ($tmp[0] == 'F') {
                    if (!in_array('F', $tmp_yearLevel)) {
                        $tmp_yearLevel[] = 'F';
                    }
                    $min = 1;
                } else {
                    $min = $tmp[0];
                }

                for ($i = $min; $i <= $max; $i++) {
                    if (!in_array($i, $tmp_yearLevel)) {
                        $tmp_yearLevel[] = $i;
                    }
                }
            } else {
                if (!in_array($year_value, $tmp_yearLevel)) {
                    $tmp_yearLevel[] = $year_value;
                }
            }
            $post['yearLevels'] = $tmp_yearLevel;
        } elseif (is_array($post['yearLevels'])) {
            $tmp_yearLevel = array();
            foreach ($post['yearLevels'] as $year_value) {
                if (strlen($year_value) > 1) {
                    if (!in_array($year_value, $tmp_yearLevel)) {
                        $tmp_yearLevel[] = $year_value;
                    }

                    $tmp = explode('-', $year_value);
                    $min = 1;
                    $max = $tmp[1];
                    if ($tmp[0] == 'F') {
                        if (!in_array('F', $tmp_yearLevel)) {
                            $tmp_yearLevel[] = 'F';
                        }
                        $min = 1;
                    } else {
                        $min = $tmp[0];
                    }

                    for ($i = $min; $i <= $max; $i++) {
                        if (!in_array($i, $tmp_yearLevel)) {
                            $tmp_yearLevel[] = $i;
                        }
                    }
                } else {
                    if (!in_array($year_value, $tmp_yearLevel)) {
                        $tmp_yearLevel[] = $year_value;
                    }
                }
            }
            $post['yearLevels'] = $tmp_yearLevel;
        }

        if (in_array('New South Wales', $post['regions']) || in_array('New South Wales', $post['regions']) || in_array('Northern Territory', $post['regions']) || in_array('Queensland', $post['regions']) || in_array('South Australia', $post['regions']) || in_array('Tasmania', $post['regions']) || in_array('Victoria', $post['regions']) || in_array('Western Australia', $post['regions'])) {

            $post['regions'][] = 'Australia';
        }

        if (in_array('Australia', $post['regions']) && in_array('New Zealand', $post['regions'])) {
            $post['regions'][] = 'Australia & New Zealand';
        }

        return $post;
    }

    public function shouldAUProduct()
    {
        $check_array = array('Australia', 'New South Wales', 'Northern Territory', 'Queensland',
            'South Australia', 'Tasmania', 'Victoria', 'Western Australia', 'Australia & New Zealand');

        if (strcmp($this->type, 'part of series') == 0) {
            $check_array = array_intersect($this->getSeriesObject()->regions, $check_array);
        } else {
            $check_array = array_intersect($this->regions, $check_array);
        }

        if (count($check_array) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function shouldNZProduct()
    {
        $check_array = array('New Zealand', 'Australia & New Zealand');

        if (strcmp($this->type, 'part of series') == 0) {
            $check_array = array_intersect($this->getSeriesObject()->regions, $check_array);
        } else {
            $check_array = array_intersect($this->regions, $check_array);
        }

        if (count($check_array) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getDownloadableFile()
    {
        Loader::model('title_downloadable_file/model', 'cup_content');
        return CupContentTitleDownloadableFile::fetchAllByTitleID($this->id);
    }

    // ANZGO-1738
    // Modified for ANZGO-1944
    /**
     * This function gets all contents from a tab under a title.
     *
     * @param int $title_id
     * @param string $tabname
     * @return mixed Array on success, boolean on failure
     */
    public function getTabByIdName($title_id, $tabname)
    {
        if ($title_id) {
            $cacheObj = self::getFromCache($title_id);

            $db = Loader::db();
            $q = "select * from CupGoTabs AS cgt where cgt.`ID` = ? AND cgt.`ID` NOT IN(SELECT RecordID FROM CupGoArchive WHERE TableName IN ('CupGoTabs','CMS_Tabs'))";
            $result = $db->getRow($q, array($title_id, $tabname));

            return ($result) ? $result : false;

        } else {
            return false;
        }
    }

    public function getTabByID($tabID)
    {
        $db = Loader::db();
        $q = "select * from CupGoTabs AS cgt where (cgt.`ID` = ?) AND cgt.`ID` NOT IN(SELECT RecordID FROM CupGoArchive WHERE TableName IN ('CupGoTabs','CMS_Tabs'))";
        $result = $db->getRow($q, array($tabID));

        return ($result) ? $result : false;

    }

    // ANZGO-1738
    public function tabSaveNew($data)
    {
        $this->createdAt = date('Y-m-d H:i:s');
        $this->modifiedAt = $this->createdAt;

        $db = Loader::db();

        $q = "INSERT INTO CupGoTabs (
                        titleID,
                        TabName,
                        Columns,
                        TabColour,
                        WatermarkImage,
                        DefaultTab,
                        AllowSearch,
                        MyResourcesLink,
                        Active,
                        Visibility,
                        ContentVisibility,
                        UserTypeIDRestriction,
                        ContentType,
                        CustomAccessMessage,
                        AlwaysUsePublicText,
                        Public_TabText,
                        Private_TabText,
                        TabLevel
                    )
                    VALUES (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?
                )";

        $v = array(
            $data['titleID'],
            $data['TabName'],
            $data['Columns'],
            $data['TabColour'],
            $data['WatermarkImage'],
            $data['DefaultTab'],
            $data['AllowSearch'],
            $data['MyResourcesLink'],
            $data['Active'],
            $data['Visibility'],
            $data['ContentVisibility'],
            $data['UserTypeIDRestriction'],
            $data['ContentType'],
            $data['CustomAccessMessage'],
            $data['AlwaysUsePublicText'],
            $data['Public_TabText'],
            $data['Private_TabText'],
            1
        );

        $r = $db->prepare($q);
        $res = $db->Execute($r, $v);
    }

    // ANZGO-1738
    /**
     * Updates the genearal details of a tab
     *
     * @author
     * @editedBy Paul Balila
     *
     * @param array $data
     * @return int
     */
    public function tabSave($data)
    {
        $db = Loader::db();

        $sqlArr = array();
        $params = array();

        $id = $data['ID'];
        unset($data['ID'],$data['TitleID']);

        foreach ($data as $dKey => $dVal) {
            if($dKey == 'FreeAccess' && $dVal == 'null') {
                $dVal = NULL;
            }

            $sqlArr[] = $dKey . ' = ?';
            $params[] = ($dVal);
        }

        $sql = "UPDATE CupGoTabs SET " . implode(",", $sqlArr) . " WHERE ID = ?";
        $params[] = $id;

        $db->Execute($sql,$params);
        return ($db->Affected_Rows() > 0);
    }

    // ANZGO-1738
    public function getGroups()
    {
        $db = Loader::db();
        $groups = array();
        $tmp_query = "select * from Groups";
        $tmp_results = $db->getAll($tmp_query);

        if (is_array($tmp_results)) {
            foreach ($tmp_results as $each_row) {
                $groups[$each_row['gID']] = $each_row['gName'];
            }
        }

        return $groups;
    }

    // ANZGO-1809
    public function getGlobalContentsTabs()
    {
        $db = Loader::db();
        $query = "select ID,CMS_Name,ContentHeading from CupGoContent where Global ='Y' AND ID NOT IN(SELECT RecordID FROM `CupGoArchive` WHERE TableName IN('CMS_Content','CupGoContent'))"; // fix query, temporary only
        $result = $db->Execute($query);
        return ($result->RowCount() > 0) ? $result->GetAll() : false;
    }

    // ANZGO-1812
    /**
     * Gets the content of the folders related to a title.
     *
     * @author james Bernardez
     * @editedBy Paul Balila
     * @param type $title_id
     * @param type $foldername
     * @return type
     */
    public function getFolders($folder_id)
    {
        $db = Loader::db();

        // To deal with the Content folders, we apply 2 queries.
        // First, we check for headings with ContentTypeID = 1004.
        $q = "SELECT c.ID, c.titleID, c.ContentData, c.ContentTypeID, c.ContentHeading, c.ContentDescription, c.CMS_Notes, c.CMS_Name, c.Global "
                . "FROM CupGoContent c "
                . "JOIN CupGoFolderContent fc ON c.ID = fc.ContentID WHERE (fc.FolderID = ? "
                . "AND c.`ContentTypeID` NOT IN (SELECT ID FROM CupGoContentDetailType WHERE ID != 1006)) "
                . "AND c.`ID` NOT IN (SELECT `RecordID` FROM `CupGoArchive` WHERE `TableName` IN('CupGoContent','CMS_Content'))";

        $q .= " UNION ";

        $q .= "SELECT c.ID, c.titleID, c.ContentData, c.ContentTypeID, c.ContentHeading, c.ContentDescription, c.CMS_Notes, c.CMS_Name, c.Global "
                . "FROM CupGoContent c "
                . "JOIN CupGoFolderContent fc ON c.ID = fc.ContentID WHERE (fc.FolderID = ? "
                . "AND c.`ContentTypeID` IS NULL) "
                . "AND c.`ID` NOT IN (SELECT `RecordID` FROM CupGoArchive WHERE `TableName` IN('CupGoContent','CMS_Content'))";
        $selResult = $db->getAll($q, array($folder_id,$folder_id));

        return ($selResult) ? $selResult : false;
    }

    // ANZGO-1944
    /**
     * Gets all parent folders under a title.
     *
     * @author Paul Balila
     * @param int $titleID
     * @return array
     */
    public function getParentFolders($titleID)
    {
        $db = Loader::db();
        $sql = "SELECT * FROM `CupGoContentFolders` WHERE `titleID` = ? AND `ID` NOT IN(SELECT `RecordID` FROM CupGoArchive WHERE TableName IN('CupGoContentFolders','CMS_ContentFolders'))";
        return $db->GetAll($sql, array($titleID));
    }

    // ANZGO-1812
    /**
     * Gets all content details.
     *
     * @author James Bernardez
     * @editedBy Paul Balila
     * @param int $id
     * @return array
     */
    public function getContentByID($id)
    {
        $db = Loader::db();
        $q = "SELECT * FROM CupGoContent WHERE ID = ?";
        $result = $db->getRow($q, array($id));

        return array('details'=>$result,'content_details'=>$this->getContentDetails($id));
    }

    // ANZGO-1812
    // Modified for ANZGO-1944
    /**
     * Saves subfolder details.
     *
     * @author James Bernardez
     * @editedBy Paul Balila
     *
     * @param array $data POSTed data
     * @return array
     */
    public function contentSave($data)
    {
        $db = Loader::db();
        $sqlArr = array();
        $params = array();

        // Assign these details to a variable and...
        $id = $data['contentID-hidden'];
        $folderid = $data['FolderID'];
        $title_id = $data['titleID'];

        // ...remove them from the array.
        unset($data['contentID-hidden'],$data['titleID'],$data['FolderID']);

        // Build the query from the array as well as the parameters needed.
        foreach ($data as $dKey => $dVal) {
            $sqlArr[] = $dKey . ' = ?';
            $params[] = $dVal;
        }

        // Create the query.
        $sql = "UPDATE CupGoContent SET " . implode(",", $sqlArr) . " WHERE ID = ?";

        // Put the ID at the tail of the array.
        $params[] = $id;

        // Of course, execute the built query.
        $db->Execute($sql, $params);

        return $this->getFolders($folderid);
    }

    // Added by Paul Balila, 2016-04-28
    // For ticket ANZUAT-149
    /**
     * @author Paul Balila
     */
    public function getContentIDByFolder($folderID)
    {
        $db = Loader::db();
        $query = "SELECT ID FROM CupGoContent WHERE ID IN(SELECT `ContentID` FROM `CupGoFolderContent` WHERE `FolderID` = ?)";
        $result = $db->GetAll($query,array($folderID));
        return $result;
    }

    // ANZGO-1812
    /**
     * Handles saving of new folders and returns all subfolders.
     *
     * @author James Bernardez
     * @editedBy Paul Balila
     * @param array $data POST data
     * @return array
     */
    public function contentSaveNew($data)
    {
        $db = Loader::db();

        // Insert new subfolder in CupGoContent
        $insQuery = "INSERT INTO CupGoContent(CreationDate,ContentHeading,titleID,Global,ContentTypeID) VALUES (NOW(),?,?,?,?)";
        $db->Execute($insQuery,array($data["ContentHeading"],$data["titleID"],"N",1004));
        $contentID = $db->Insert_ID("CupGoContent");

        // Insert new record in CupGoFolderContent.
        $recordQuery = "INSERT INTO CupGoFolderContent(FolderID,ContentID) VALUES(?,?)";
        $db->Execute($recordQuery, array($data['folderID'],$contentID));

        // Get all subfolders
        return array('result' => $this->getFolders($data['folderID']),'newID'=>$contentID);
    }

    // ANZGO-1812
    // Modified for ANZGO-1944
    /**
     * Retrieves all content details under an ID.
     *
     * @author James Bernardez
     * @editedBy Paul Balila
     *
     * @param int $id
     * @return array
     */
    public function getContentDetails($id)
    {
        $db = Loader::db();

        if (is_array($id)) {
            $q = "SELECT * FROM `CupGoContentDetail` WHERE `ContentID` IN(" . implode(",", $id) . ") AND `ID` NOT IN(SELECT `RecordID` FROM CupGoArchive WHERE TableName IN('CupGoContentDetails','CMS_ContentDetails')) ORDER BY SortOrder ASC ";
            $result = $db->getAll($q);
        } else {
            $q = "SELECT * FROM `CupGoContentDetail` WHERE `ContentID` = ? AND `ID` NOT IN(SELECT `RecordID` FROM CupGoArchive WHERE TableName IN('CupGoContentDetails','CMS_ContentDetails')) ORDER BY SortOrder ASC ";
            $result = $db->getAll($q, array($id));
        }

        return ($result) ? $result : false;
    }

    // ANZGO-1812
    // Modified for ANZGO-1944
    /**
     * Gets all content details
     *
     * @param int $id
     * @return array
     */
    public function getContentDetailByID($id)
    {
        $db = Loader::db();
        $q = "SELECT * FROM CupGoContentDetail WHERE ID = ?";
        $result = $db->getRow($q, array($id));

        return ($result) ? $result : false;
    }

    // ANZGO-1812
    /**
     * Handles content folder detail updates
     *
     * @author James Bernardez
     * @editedBy Paul Balila
     *
     * @param array $data Array of POSTed data
     * @return string
     */
    public function detailSave($data)
    {
        $db = Loader::db();

        $id = $data['ID'];
        $content_id = $data['ContentID'];
        unset($data['ID'],$data['ContentID']);
        $setArray = array();
        foreach ($data as $sKey => $sValue) {
            // Check if string is URL. If it is, convert its characters to SQL safe characters
            if(filter_var($sValue,FILTER_VALIDATE_URL) || $this->checkStringForURL($sValue)) {
                $setArray[] = $sKey . " = '" . urlencode($sValue) . "'";
            } else {
                $setArray[] = $sKey . " = '" . addslashes($sValue) . "'";
            }

        }

        $updateSql = "UPDATE CupGoContentDetail SET " . implode(",", $setArray) . " WHERE ID = ?";
        $db->Execute($updateSql,array($id));

        return array('result'=>$this->getContentDetails($content_id),'id'=>$id);
    }

    // ANZGO-1812
    /**
     * Saves a single content detail.
     *
     * @author James Bernardez
     * @editedBy Paul Balila
     *
     * @param type $data
     * @return type
     */
    public function detailSaveNew($data)
    {
        $db = Loader::db();

        // Added by Paul Balila
        // Gets max sort order number under that content which is then incremented, and finally added in the insert details.
        $newSortSql = "SELECT MAX(SortOrder) + 1 AS newSortNum FROM CupGoContentDetail WHERE ContentID = ?";
        $result = $db->GetRow($newSortSql, array($data['ContentID']));
        $sortNum = ($result['newSortNum']) ? $result['newSortNum'] : 1;

        $q = "INSERT INTO CupGoContentDetail(Public_Name,ContentID,TypeID,SortOrder) VALUES (?,?,?,?)";

        $v = array($data['PublicName'], $data['ContentID'], $data['TypeID'], $sortNum);
        $db->Execute($q, $v);

        $last_ins_id = $db->Insert_ID('CupGoContentDetail');
        return array('result'=>$this->getContentDetails($data['ContentID']),'ins_id'=>$last_ins_id);
    }

    // ANZGO-1842
    /**
     * Simulates the above function, detailSaveNew(), but handles multiple insert.
     * Especially used in multi file uploads.
     * @author Paul balila
     * @param array $params
     */
    public function detailSaveNewMultiple($contentID, $fileDetails)
    {
        $db = Loader::db();
        $insIDs = array();

        // Gets max sort order number under that content which is then incremented, and finally added in the insert details.
        $newSortSql = "SELECT MAX(SortOrder) + 1 AS newSortNum FROM CupGoContentDetail WHERE ContentID = ?";
        foreach ($fileDetails as $f) {
            $result = $db->GetRow($newSortSql, array($contentID));
            $sortNum = ($result['newSortNum']) ? $result['newSortNum'] : 1;
            $sql = "INSERT INTO CupGoContentDetail(CreationDate,ContentID,TypeID,SortOrder,FileName,FilePath,FileSize,Public_Name,FileUploadDate) VALUES "
                    . "(NOW()," . $contentID . ",1005," . $sortNum . ",'" . $f['FileName'] . "','" . $f['FilePath'] . "'," . $f['FileSize'] . ",'" . $f['FileName'] . "',NOW())";
            $db->Execute($sql);
            $insIDs[] = $db->Insert_ID();
        }

        return $this->getContentDetails($contentID);
    }

    // ANZGO-1812
    /**
     * Handles single file saving and returns all content details under a certain ContentID;
     *
     * @author James Bernardez
     * @editedBy Paul Balila
     * @param array $data Contains all the file details to be saved.
     */
    public static function uploadDetailSave($tableID,$contentID,$fileDetails)
    {
        $db = Loader::db();

        $updateSql = "UPDATE CupGoContentDetail SET FileName = ?,FilePath = ?,FileSize = ?,FileUploadDate = NOW(),TypeID = 1005 WHERE ID = " . $tableID;
        foreach ($fileDetails as $f) {
            $updateResult = $db->Execute($updateSql, array($f['FileName'], $f['FilePath'], $f['FileSize']));
        }

        $getSql = "SELECT * FROM CupGoContentDetail WHERE ContentID = ? ORDER BY SortOrder";
        return $db->GetAll($getSql,array($contentID));
    }

    // ANZGO-1812
    // Modified for ANZGO-1944
    /**
     * Saves new folder heading under a title and returns all folder headings under that title.
     *
     * @author James Bernardez
     * @editedBy Paul Balila
     * @param array $data
     * @return array
     */
    public function contentFolderSaveNew($data)
    {
        $db = Loader::db();

        $q = "INSERT INTO CupGoContentFolders (FolderName,titleID) VALUES (?,?)";
        $db->Execute($q,array($data['FolderName'],$data['titleID']));
        $ins_id = $db->Insert_ID('CupGoContentFolders');

        return array('result'=>$this->getParentFolders($data['titleID']),'newID'=>$ins_id);
    }

    // ANZGO-1944
    public function editFolderHeading($data)
    {
        $db = Loader::db();
        $sql = "UPDATE CupGoContentFolders SET `FolderName` = ? WHERE `ID` = ?";
        $db->Execute($sql,array($data['FolderName'],$data['ID']));
        return ($db->Affected_Rows() > 0);
    }

    // ANZGO-1812
    // Modified for ANZGO-1944
    /**
     * Sorts the SortOrder of the Content Details
     *
     * @author James Bernardez
     * @editedBy Paul Balila
     * @param array $data
     * @return boolean
     */
    public function updateContentDetailSortable($data)
    {
        $db = Loader::db();
        $keys = array();
        $sql = "UPDATE CupGoContentDetail SET `SortOrder` = CASE `ID` ";
        foreach ($data as $dKey => $dValue) {
            $sql .= " WHEN " . $dKey . " THEN " . $dValue;
            $keys[] = $dKey;
        }
        $sql .= " END WHERE `ID` IN (" . implode(",", $keys) . ")";
        $db->Execute($sql);
        return ($db->Affected_Rows() > 0);
    }

    //ANZGO-1944
    public function archiveHeading($id,$tableName,$userID)
    {
        $db = Loader::db();
        $sql = "INSERT INTO `CupGoArchive`(RecordID,TableName,ArchivedDate,ArchivedByUserID) VALUES (?,?,NOW(),?)";
        $db->Execute($sql,array($id,$tableName,$userID));
        return $db->Insert_ID('CupGoArchive');
    }

    private function checkStringForURL($string)
    {
        $regex = "((http|ftp|https)\:\/\/)|(\?[a-z]\=)";

        return preg_match("/$regex/", $string);
    }

    // GCAP-704 Added by mtanada 20200228
    public static function fetchBydemoId($demoId)
    {
        $db = Loader::db();
        $q = "select * from CupContentTitle where demo_id = ?";
        $result = $db->getRow($q, array($demoId));

        return $result;
    }
}
