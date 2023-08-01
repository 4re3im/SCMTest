<?php  

Loader::model('title/model', 'cup_content');

class DashboardCupContentTitlesAddController extends Controller
{
    private $pkgHandle = 'cup_content';

    public function on_start()
    {
        $html = Loader::helper('html');
        $this->set('disableThirdLevelNav', true);

        // SB-456 added by jbernardez 20200213
        $cssPath = (string)$html->css('title-style.css', $this->pkgHandle)->file . "?v=1.01";
        $this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . $cssPath . '" />');
        $this->addHeaderItem($html->javascript('bootstrap-modal.js', 'cup_content'));
        $this->addHeaderItem($html->javascript('bootstrap-tab.js', 'cup_content'));
        $this->addHeaderItem($html->javascript('tiny_mce/tiny_mce.js'));
        // GCAP-625 added by mtanada 20200128
        $jsPath = (string)$html->javascript('titles-script.js', $this->pkgHandle)->file . "?v=1.7";
        $this->addHeaderItem('<script type="text/javascript" src="' . $jsPath . '"></script>');
        $this->addHeaderItem($html->css('bootstrap.min.css', 'cup_content'));
        
        $this->addFooterItem($html->javascript('malsup/jquery.form.min.js', 'cup_content'));
        $this->addFooterItem($html->javascript('go-content-scripts.js', 'cup_content'));
    }
    
    public function submit()
    {
        $post = CupContentTitle::convertPost($this->post());
        
        Loader::model('collection_types');
        $val = Loader::helper('validation/form');
        $vat = Loader::helper('validation/token');
        
        $val->setData($this->post());
        $val->addRequired('name', t('Name required.'));
        $val->test();
        
        $error = $val->getError();
    
        if (!$vat->validate('create_title')) {
            $error->add($vat->getErrorMessage());
        }
    
        if ($error->has()) {
            $_SESSION['alerts'] = array('error' => $error->getList());
            $this->set('entry', $post);
        } else {
            $post = CupContentTitle::convertPost($this->post());
            Loader::helper('tools', 'cup_content');
            
            $title = new CupContentTitle();

            $publishDate = !empty($post['publishDate']) ? $publishDate = $post['publishDate'] : date('Y-m-d H:i:s');
            $nzCircaPrice = !empty($post['nz_circa_price']) ? $post['nz_circa_price'] : 0;

            $title->id                  = $post['id'];
            $title->isbn13              = $post['isbn13'];
            $title->isbn10              = $post['isbn10'];
            $title->name                = $post['name'];
            $title->customName          = $post['customName'];
            $title->subtitle            = $post['subtitle'];
            $title->customSubtitle      = $post['customSubtitle'];
            // SB-425 added by jbernardez 20200205
            $title->myResourcesTitle    = $post['myResourcesTitle'];
            $title->edition             = $post['edition'];
            $title->shortDescription    = $post['shortDescription'];
            $title->longDescription     = $post['longDescription'];
            $title->feature             = $post['feature'];
            $title->content             = $post['content'];
            $title->yearLevels          = $post['yearLevels'];
            $title->publishDate         = $publishDate;
            $title->availability        = $post['availability'];
            $title->goUrl               = $post['goUrl'];
            $title->previewUrl          = $post['previewUrl'];
            // part of series, stand alone, study guide
            $title->type                = $post['type'];
            $title->series              = $post['series'];
            $title->descriptionOption   = $post['descriptionOption'];
            $title->divisions           = $post['divisions'];
            $title->regions             = $post['regions'];
            $title->tagline             = $post['tagline'];
            $title->reviews             = $post['reviews'];

            $title->aus_circa_price     = $post['aus_circa_price'];
            $title->nz_circa_price      = $nzCircaPrice;
            
            $title->search_priority     = $post['search_priority'];
            
            $title->coreProductID       = $post['coreProductID'];
            
            $title->isEnabled           = $post['isEnabled'];
            $title->hasInspectionCopy   = $post['hasInspectionCopy'];
            $title->hasAccessCode       = $post['hasAccessCode'];
            $title->hasDownloadableFile = $post['hasDownloadableFile'];
            $title->is_free_shipping    = $post['is_free_shipping'];
            
            $title->new_product_flag    = $post['new_product_flag'];
            $title->cart_message        = $post['cart_message'];
            $title->cart_popup_content  = $post['cart_popup_content'];

            // ANZGO-1708
            $title->isGoProduct         = $post['isGoProduct'];
                        
            // ANZUAT-90
            $title->showBuyNow          = $post['showBuyNow'];
            
            // GCAP-625
            $title->demo_id              = (int)$post['demo_id'];
	    
            $title->formats             = array();
            if (isset($post['formats'])) {
                $title->formats = $post['formats'];
            }

            // ANZGO-1738
            $title->tabs                = array();
            if (isset($post['tabs'])) {
                $title->tabs = $post['tabs'];
            }
            
            $title->subjects            = array();
            if (isset($post['subjects'])) {
                $title->subjects = $post['subjects'];
            }
            
            $title->authors             = array();
            if (isset($post['authors'])) {
                $title->authors = $post['authors'];
            }
            
            $title->relatedTitleIDs     = array();
            if (isset($post['relatedTitleIDs'])) {
                $title->relatedTitleIDs = $post['relatedTitleIDs'];
            }
            
            $title->supportingTitleIDs  = array();
            if (isset($post['supportingTitleIDs'])) {
                $title->supportingTitleIDs = $post['supportingTitleIDs'];
            }
            
            $title->setProductData($post);
            
            $entry = $title->getAssoc();
            
            if ($title->save()) {
                $_SESSION['alerts'] = array('success' => 'New Title has been added successfully');
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $filename = $_FILES['image']['tmp_name'];
                    $title->saveImage($filename);
                    $globalGoFilename = $title->saveGlobalGoImage($filename);
                    $title->saveThumbnailURL($globalGoFilename);
                }
                
                $this->redirect('/dashboard/cup_content/titles/show/' . $title->id);
            } else {
                $this->set('entry', $entry);
                $_SESSION['alerts'] = array('error' => $title->errors);
            }
        }
    }
}