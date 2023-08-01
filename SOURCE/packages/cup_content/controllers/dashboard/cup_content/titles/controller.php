<?php
Loader::model('title/list', 'cup_content');
Loader::model('title/model', 'cup_content');

class DashboardCupContentTitlesController extends Controller {

    private $pkgHandle = 'cup_content';

    public function on_start()
    {
        $html = Loader::helper('html');

        // SB-456 added by jbernardez 20200213
        $cssPath = (string)$html->css('title-style.css', $this->pkgHandle)->file . "?v=1.01";
        $this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . $cssPath . '" />');
        $this->addHeaderItem($html->javascript('bootstrap-modal.js', 'cup_content'));
        $this->addHeaderItem($html->javascript('bootstrap-tab.js', 'cup_content'));
        $this->addHeaderItem($html->javascript('tiny_mce/tiny_mce.js'));
        // GCAP-625 added by mtanada 20200128
        $jsPath = (string)$html->javascript('titles-script.js', $this->pkgHandle)->file . "?v=1.9";
        $this->addHeaderItem('<script type="text/javascript" src="' . $jsPath . '"></script>');
        $this->addHeaderItem($html->css('bootstrap.min.css', 'cup_content'));
        
        $this->addFooterItem($html->javascript('malsup/jquery.form.min.js', 'cup_content'));
        $this->addFooterItem($html->javascript('go-content-scripts.js', 'cup_content'));
    }

    public function view()
    {
        $this->redirect('/dashboard/cup_content/titles/search');
    }

    public function test()
    {
        $order_id = 5;
        loader::model('order/model', 'core_commerce');
        $order = new CoreCommerceOrder();
        $order->load($order_id);

        foreach ($order->getProducts() as $product) {
            $quantity = $product->quantity;
            for ($i = 1; $i <= $quantity; $i++) {
                echo $product->getProductID() . '|' . $i . "\n";
            }
        }

        exit();
    }

    public function show($title_id = false)
    {
        $title = CupContentTitle::fetchByID($title_id);

        if ($title === false) {
            $_SESSION['alerts']['failure'][] = 'Invalid Title ID';
            $this->redirect("/dashboard/cup_content/titles");
        }

        if ($title->hasDownloadableFile && $title->getDownloadableFile() === false) {
            $_SESSION['alerts']['info'][] = "Downloadable File is missing!";
        }

        $this->set('title', $title);
        $this->render('/dashboard/cup_content/titles/view');
    }

    public function deleteImage($title_id = false)
    {
        $title = CupContentTitle::fetchByID($title_id);
        if ($title === false) {
            $_SESSION['alerts'] = array('failure' => 'Invalid Title ID');
            $this->redirect("/dashboard/cup_content/titles");
        } else {
            $title->deleteImage();
            $this->redirect("/dashboard/cup_content/titles/show", $title_id);
        }
    }

    public function edit($title_id = false)
    {
        $data = $this->post();

        $title = CupContentTitle::fetchByID($title_id);
        if ($title === false) {
            $_SESSION['alerts'] = array('failure' => 'Invalid Title ID');
            $this->redirect("/dashboard/cup_content/titles");
        }

        // save
        if (count($this->post()) > 0) {
            Loader::model('collection_types');
            $val = Loader::helper('validation/form');
            $vat = Loader::helper('validation/token');

            $val->setData($this->post());
            $val->addRequired("name", t("name required."));
            $val->test();

            $error = $val->getError();

            if (!$vat->validate('edit_title')) {
                $error->add($vat->getErrorMessage());
            }

            if ($error->has()) {
                $_SESSION['alerts'] = array('error' => $error->getList());
            } else {
                $post = CupContentTitle::convertPost($this->post());

                $publication_date           = $post['publishDate'];
                $publication_date           = str_replace('/', '-', $publication_date);
                $publication_date           = date('Y-m-d H:i:s', strtotime($publication_date));
                $nzCircaPrice = !empty($post['nz_circa_price']) ? $post['nz_circa_price'] : 0;

                $title->isbn13              = $post['isbn13'];
                // SB-345 blocked by jbernardez 20190923
                // $title->isbn10 = $post['isbn10'];
                $title->name                = $post['name'];
                $title->customName          = $post['customName'];
                $title->subtitle            = $post['subtitle'];
                $title->customSubtitle      = $post['customSubtitle'];
                // SB-425 added by jbernardez 20200205
                $title->myResourcesTitle    = $post['myResourcesTitle'];
                $title->edition             = $post['edition'];
                $title->shortDescription    = $post['shortDescription'];
                $title->longDescription     = $post['longDescription'];
                $title->content             = $post['content'];
                $title->feature             = $post['feature'];
                $title->yearLevels          = $post['yearLevels'];
                $title->publishDate         = $publication_date;
                $title->availability        = $post['availability'];
                $title->goUrl               = $post['goUrl'];
                $title->previewUrl          = $post['previewUrl'];
                // part of series, stand alone, study guide
                $title->type                = $post['type'];
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

                // ANZGO-3043
                $title->hmTitle             = $post['hmTitle'];
		
		// GCAP-625
                $title->demo_id             = (int)$post['demo_id'];

                $title->formats = array();
                if (isset($post['formats'])) {
                    $title->formats = $post['formats'];
                }

                // ANZGO-1738
                $title->tabs = array();
                if (isset($post['tabs'])) {
                    $title->tabs = $post['tabs'];
                }

                // ANZGO-1812
                $title->contentFolders = array();
                if (isset($post['contentFolders'])) {
                    $title->contentFolders = $post['contentFolders'];
                }

                $title->subjects = array();
                if (isset($post['subjects'])) {
                    $title->subjects = $post['subjects'];
                }

                $title->authors = array();
                if (isset($post['authors'])) {
                    $title->authors = $post['authors'];
                }

                $title->series = array();
                if (isset($post['series'])) {
                    $title->series = $post['series'];
                }

                $title->relatedTitleIDs = array();
                if (isset($post['relatedTitleIDs'])) {
                    $title->relatedTitleIDs = $post['relatedTitleIDs'];
                }

                $title->supportingTitleIDs = array();
                if (isset($post['supportingTitleIDs'])) {
                    $title->supportingTitleIDs = $post['supportingTitleIDs'];
                }


                $title->setProductData($post);

                if ($title->save()) {
                    $_SESSION['alerts'] = array('success' => 'Title has been saved successfully');

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
        $this->set('entry', $title->getAssoc());
        $this->render('/dashboard/cup_content/titles/edit');
    }

    // Added by Paul Balila, 2016-04-12
    // For ticket ANZUAT-16
    public function delete($id)
    {
        $title = new CupContentTitle($id);
        $result = ($title->delete()) ? 'success' : 'fail';
        echo json_encode(array('result'=>$result,'error'=>$title->errors));
        exit;
    }

    public function editProduct($title_id, $region)
    {
        if (!in_array($region, array('au', 'nz'))) {
            $this->redirect("/dashboard/cup_content/titles");
        }


        $title = CupContentTitle::fetchByID($title_id);
        $product_id = false;
        if ($region == 'au') {
            $product_id = $title->auProductID;
        } elseif ($region == 'nz') {
            $product_id = $title->nzProductID;
        }

        if ($title === FALSE) {
            $_SESSION['alerts'] = array('failure' => 'Invalid Title ID');
            $this->redirect('/dashboard/cup_content/titles');
        }

        // save
        if (count($this->post()) > 0) {
            $valt_to_match = 'edit_title_product_' . $region;

            Loader::model('collection_types');
            $val = Loader::helper('validation/form');
            $vat = Loader::helper('validation/token');

            $val->setData($this->post());
            $val->test();

            $error = $val->getError();

            if (!$vat->validate($valt_to_match)) {
                $error->add($vat->getErrorMessage());
            }

            if ($error->has()) {
                $_SESSION['alerts'] = array('error' => $error->getList());
            } else {
                $post_data = $this->post();

                $save_result = false;
                if ($region == 'au') {
                    $save_result = $title->saveAuProduct($post_data);
                } elseif ($region == 'nz') {
                    $save_result = $title->saveNzProduct($post_data);
                }
                if ($save_result) {
                    $_SESSION['alerts'] = array('success' => 'Title Product (' . $region . ') has been saved successfully');
                    $this->redirect("/dashboard/cup_content/titles/show", $title_id);
                } else {
                    $this->set('entry', $entry);
                    $_SESSION['alerts'] = array('error' => $subject->errors);
                }
            }
        }


        $this->set('region', $region);
        $this->set('title', $title);
        $this->set('product_id', $product_id);
        $this->render('/dashboard/cup_content/titles/editProduct');
    }

    public function sample_page_list($title_id)
    {
        Loader::model('title_sample_page/list', 'cup_content');
        $list = new CupContentTitleSamplePageList();
        $list->filterByTitleID($title_id);

        $list->setItemsPerPage(40);


        $this->set('title_id', $title_id);
        $this->set('samplePageList', $list);
        $this->set('samplePages', $list->getPage());
        $this->set('pagination', $list->getPagination());

        $this->render('/dashboard/cup_content/titles/sample_page_list');
    }

    public function new_sample_page($title_id)
    {
        Loader::model('title_sample_page/model', 'cup_content');
        $sample_page = new CupContentTitleSamplePage();

        if (count($this->post()) > 0) {
            $post = $this->post();

            $sample_page->titleID = $title_id;
            $sample_page->description = $post['description'];
            $sample_page->is_page_proof = $post['is_page_proof'];

            if ($sample_page->save()) {
                $_SESSION['alerts'] = array('success' => 'New Title has been added successfully');
                $this->redirect("/dashboard/cup_content/titles/sample_page_list/" . $title_id);
            } else {

                $_SESSION['alerts'] = array('error' => $sample_page->errors);
            }
        }

        $entry = $sample_page->getAssoc();
        $this->set('entry', $entry);

        $this->render('/dashboard/cup_content/titles/new_sample_page');
    }

    public function edit_sample_page($sample_id)
    {
        Loader::model('title_sample_page/model', 'cup_content');
        $sample_page = new CupContentTitleSamplePage($sample_id);

        if (count($this->post()) > 0) {
            $post = $this->post();

            $sample_page->description = $post['description'];
            $sample_page->is_page_proof = $post['is_page_proof'];

            if ($sample_page->save()) {
                $_SESSION['alerts'] = array('success' => 'New Title Sample Page has been added successfully');
                $this->redirect('/dashboard/cup_content/titles/sample_page_list/' . $sample_page->titleID);
            } else {

                $_SESSION['alerts'] = array('error' => $sample_page->errors);
            }
        }

        $entry = $sample_page->getAssoc();
        $this->set('entry', $entry);

        $this->render('/dashboard/cup_content/titles/edit_sample_page');
    }

    public function delete_sample_page($sample_id)
    {
        Loader::model('title_sample_page/model', 'cup_content');

        $result = array('result' => 'failure', 'error' => 'unknown error');

        $sample_page = CupContentTitleSamplePage::fetchByID($sample_id);
        if ($sample_page->delete() === TRUE) {
            $result = array('result' => 'success', 'error' => 'unknown error');
        } else {
            $result = array('result' => 'failure', 'error' => array_shift($sample_page->errors));
        }

        echo json_encode($result);
        exit();
    }

    public function downloadSamplePageFile($sample_id)
    {
        Loader::model('title_sample_page/model', 'cup_content');
        $sample_page = new CupContentTitleSamplePage($sample_id);

        $filePath = $sample_page->getFilePath();


        if (!$filePath) {
            echo 'File not available';
        } else {
            $fileName = $sample_page->filename;
            $fileMeta = $sample_page->filemeta;
            header('Content-type: ' . $fileMeta);
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            readfile($filePath);
        }
        exit();
    }

    public function downloadable_file($title_id)
    {
        Loader::model('title_downloadable_file/model', 'cup_content');

        $fileObjects = CupContentTitleDownloadableFile::fetchAllByTitleID($title_id);
        $file = new CupContentTitleDownloadableFile();

        if ($fileObjects && count($fileObjects) > 0) {
            $file = $fileObjects[0];
        }

        if (count($this->post()) > 0) {

            $post = $this->post();

            $file->titleID = $title_id;
            $file->description = $post['description'];
            $file->expiry_type = $post['expiry_type'];

            if ($file->save()) {
                $_SESSION['alerts'] = array('success' => 'Title Downloadable File has been added successfully');
                $this->redirect("/dashboard/cup_content/titles/downloadable_file/", $title_id);
            } else {

                $_SESSION['alerts'] = array('error' => $sample_page->errors);
            }
        }


        $entry = $file->getAssoc();
        $this->set('titleID', $title_id);
        $this->set('entry', $entry);

        $this->render('/dashboard/cup_content/titles/downloadable_file');
    }

    public function downloadable_file_download($fid)
    {
        Loader::model('title_downloadable_file/model', 'cup_content');

        $file = CupContentTitleDownloadableFile::fetchByID($fid);

        $filePath = $file->getFilePath();

        if (!$filePath) {
            echo 'File not available';
        } else {
            $fileName = $file->filename;
            $fileMeta = $file->filemeta;
            header('Content-type: ' . $fileMeta);
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            readfile($filePath);
        }
        exit();
    }

    public function testTitle($pretty_url)
    {
        echo "$pretty_url\n";
        $title = CupContentTitle::fetchByPrettyUrl($pretty_url);
        print_r($title);

        exit();
    }

    public function importStandAlone()
    {
        if (isset($_FILES['file'])) {
            $worksheet_name = "TITLES in series (2)";
            if (isset($_POST['worksheet_name'])) {
                $worksheet_name = $_POST['worksheet_name'];
            }

            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

            Loader::model('title/import', 'cup_content');
            $ti = new CupContentTitleImport($_FILES['file']['tmp_name']);
            if (strcmp($ext, 'xls') == 0) {
                $ti->process('excel', $worksheet_name, 'Excel5');
            } elseif (strcmp($ext, 'xml') == 0) {
                $ti->process('excel', $worksheet_name, 'Excel2003XML');
            } elseif (strcmp($ext, 'xlsx') == 0) {
                $ti->process('excel', $worksheet_name, 'Excel2007');
            } else {
                $ti->process();
            }


            echo "\n<br/>\n Import Finished \n<br/>\n";
        }

        $this->render('/dashboard/cup_content/titles/import_stand_alone', 'cup_content');
    }

    public function fixRelatedTitle()
    {
        $db = Loader::db();
        $q = "UPDATE CupContentTitleRelatedTitle t1
                JOIN CupContentTitle t2 ON t2.isbn13 = t1.isbn13
                SET t1.related_titleID = t2.id
                WHERE t1.related_titleID IS NULL";
        $results = $db->execute($q, array());


        $db = Loader::db();
        $q = "UPDATE CupContentTitleSupportingTitle t1
                JOIN CupContentTitle t2 ON t2.isbn13 = t1.isbn13
                SET t1.supporting_titleID = t2.id
                WHERE t1.supporting_titleID IS NULL";
        $results = $db->execute($q, array());

        echo 'finished';
        exit();
    }

    public function clearTitles()
    {
        $db = Loader::db();
        $q = "TRUNCATE TABLE `CupContentTitleSubjects`;
                TRUNCATE TABLE CupContentTitleSamplePages;
                TRUNCATE TABLE CupContentTitleRelatedTitle;
                TRUNCATE TABLE CupContentTitleFormats;
                TRUNCATE TABLE CupContentTitleAuthors;
                TRUNCATE TABLE CupContentTitle;";
        foreach (explode("\n", $q) as $tq) {
            $results = $db->execute($tq, array());
        }

        echo 'finished';
        exit();
    }

    public function fixTitleImages()
    {
        $folder = DIR_BASE . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'cup_content' . DIRECTORY_SEPARATOR .
                'images' . DIRECTORY_SEPARATOR . 'titles' . DIRECTORY_SEPARATOR . 'import';

        if ($handle = opendir($folder)) {
            /* This is the correct way to loop over the directory. */
            while (false !== ($entry = readdir($handle))) {
                if (!in_array($entry, array('.', '..'))) {
                    $ext = pathinfo($entry, PATHINFO_EXTENSION);
                    if (in_array(strtolower($ext), array("gif", "jpg", "jpeg", "png"))) {
                        $isbn13 = basename($entry, '.' . $ext);
                        echo $isbn13 . "\n";
                        $title = CupContentTitle::fetchByISBN13($isbn13);
                        if ($title) {
                            $title->saveImage($folder . DIRECTORY_SEPARATOR . $entry);
                        }
                    }
                }
            }

            closedir($handle);
        }

        echo "\nfinished";
        exit();
    }

    public function sync()
    {
        Loader::model('title/batch_sync', 'cup_content');

        $worksheet_name = "Sheet1";

        $filename = CUP_CONTENT_SYNC_FOLDER . 'titles.xlsx';
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        Loader::model('title/import', 'cup_content');
        $ti = new CupContentTitleBatchSync($filename);
        if (strcmp($ext, 'xls') == 0) {
            $ti->process('excel', $worksheet_name, 'Excel5');
        } elseif (strcmp($ext, 'xml') == 0) {
            $ti->process('excel', $worksheet_name, 'Excel2003XML');
        } elseif (strcmp($ext, 'xlsx') == 0) {
            $ti->process('excel', $worksheet_name, 'Excel2007');
        } else {
            $ti->process();
        }


        echo "\n<br/>\n Import Finished \n<br/>\n";


        exit();
    }

    public function testSave()
    {
        Loader::model('title/model', 'cup_content');
        $titleObj = CupContentTitle::fetchByID(1);
        $titleObj->updateProduct('AU');
        exit();
    }

    public function test2()
    {
        Loader::model('title/model', 'cup_content');
        $titleObj = CupContentTitle::fetchByID(1);
        $titleObj->save();
        print_r($titleObj->getAuProduct());
        exit();
    }

    public function reTitles()
    {
        Loader::model('title/model', 'cup_content');
        $db = Loader::db();
        $q = 'SELECT ti.* from CupContentTitle ti';
        $results = $db->getAll($q, array());
        foreach ($results as $each) {
            $titleObj = CupContentTitle::fetchByID($each['id']);
            if ($titleObj) {
                $titleObj->save();
                echo "{$each['isbn13']} \t done\n";
            }
        }

        exit("finished");
    }

    // ANZGO-1738
    // Modified for ANZGO-1944
    /**
     * Saves general tab details
     * @author
     * @editedBy Paul Balila
     */
    public function saveTab()
    {
        $data = $_POST;

        Loader::model('title/model', 'cup_content');
        Loader::model('tabs/model', 'cup_content');

        $vHelper = Loader::helper('edit_go_content', 'cup_content');
        $tabModel = new CupContentTabs();

        $result = CupContentTitle::tabSave($data);
        $tabsResult = $tabModel->getTitleTabs($data['TitleID']);


        $tabs = $vHelper->displayTabs($tabsResult,$data['ID']);
        echo $tabs;
        exit;
    }

    // -- ANZGO-1809 -- //
    public function getGlobalContentTabs()
    {
        $tabID = $_POST['tabID'];
        $titleID = $_POST['titleID'];

        Loader::model('title/model', 'cup_content');
        Loader::model('tabs/model', 'cup_content');
        $formatter = Loader::helper('title_tabs', 'cup_content');

        $tabModel = new CupContentTabs();
        $contentModel = new CupContentTitle();

        $data = $_POST;

        Loader::model('title/model', 'cup_content');

        // Get all linked global contents in that tab under that title
        $linkedGlobalContents = $tabModel->getTabGlobalContents($tabID);

        // Get all global contents
        $globalContents = $contentModel->getGlobalContentsTabs();

        // Format tables and send as json array
        $html = array();
        $html['global_contents'] = $formatter->formatGlobalContent($globalContents, $linkedGlobalContents);
        $html['linked_global_content'] = $formatter->formatLinkedGlobalContents($linkedGlobalContents,'global');
        echo json_encode($html);
        exit;
    }

    public function getLocalContentTabs($isInternal = false)
    {
        $tabID = $_POST['tabID'];
        $titleID = $_POST['titleID'];

        Loader::model('title/model', 'cup_content');
        Loader::model('tabs/model', 'cup_content');
        $formatter = Loader::helper('title_tabs', 'cup_content');
        $html = array();

        $tabModel = new CupContentTabs();
        $contentModel = new CupContentTitle();

        // Get all linked global contents in that tab under that title
        $linkedGlobalContents = $tabModel->getTabGlobalContents($tabID);

        //Get linked folders
        $folders = $tabModel->getTabFolders($titleID);

        //Format folder display
        $html['folders'] = $formatter->formatTabFolders($folders);
        $html['linked_global_content'] = $formatter->formatLinkedGlobalContents($linkedGlobalContents,'local');


        if($isInternal) {
            return $html;
        } else {
            echo json_encode($html);
            exit;
        }
    }

    public function showAddedContent()
    {
        $title = $_POST['title'];
        $contentID = $_POST['contentID'];
        $tabID = $_POST['tabID'];
        $titleID = $_POST['titleID'];
        $class = $_POST['class'];

        Loader::model('tabs/model', 'cup_content');
        $contentModel = new CupContentTabs();

        // store to database
        // but first, get ID of tab...
        // $tabID = $contentModel->getTabID($tabName, $titleID);

        // then save to CupGoTabContent
        $tabs = $contentModel->insertTabContent($tabID, $contentID);

        // output to interface
        $formatter = Loader::helper('title_tabs', 'cup_content');
        echo $formatter->formatAddedContent($title, $tabs, $class);
        exit;
    }

    public function updateTabContent()
    {
        $columnName = $_POST['columnName'];
        $columnValue = $_POST['columnValue'];
        $tabContentID = $_POST['tabContentID'];

        Loader::model('tabs/model', 'cup_content');
        $contentModel = new CupContentTabs();

        $flag = $contentModel->updateTabContent($columnName, $columnValue, $tabContentID);
        echo ($flag) ? 1 : 0;
        exit;
    }

    public function deleteTabContent()
    {
        $tabContentID = $_POST['tabContentID'];

        Loader::model('tabs/model', 'cup_content');
        $contentModel = new CupContentTabs();

        $flag = $contentModel->deleteTabContent($tabContentID);
        echo ($flag) ? 1 : 0;
        exit;
    }

    public function getTabFolderContents()
    {
        $folderID = $_POST['folderID'];
        $tabID = $_POST['tabID'];

        Loader::model('tabs/model', 'cup_content');
        $tabModel = new CupContentTabs();
        $formatter = Loader::helper('title_tabs', 'cup_content');

        $titleID = $tabModel->getTitleIDFromFolderID($folderID);

        //Get folder contents
        $folderContents = $tabModel->getTabFolderContents($folderID);

        // Get linked folder contents.
        $contentIDs = $tabModel->getLinkedFolderContents($folderID, $tabID);

        $html = $formatter->formatFolderContent($folderContents, $contentIDs);
        echo $html;
        exit;
    }

    // -- END FOR ANZGO-1809 -- //

    //ANZGO-1944
    /**
     * Gets all details related to tab. Gets the general details as well as its
     * Global and Local content.
     *
     */
    public function getTabDetails()
    {
        $titleModel = new CupContentTitle();
        $vHelper = Loader::helper('edit_go_content','cup_content');
        $titleID = $_POST['titleID'];
        $tabID = $_POST['tabID'];

        $tabResult = $titleModel->getTabByID($tabID);
        $tabContent = $vHelper->getTabInfo($tabResult,$titleID);

        echo json_encode(array('GenDetails'=>$tabContent,'TabContents'=>$this->getLocalContentTabs(TRUE)));
        exit;
    }

    // ANZGO-1812
    // Modified for ANZGO-1944
    /**
     * Gets all the details under a content ID.
     *
     * @author James Bernardez
     * @editedBy Paul Balila
     */
    public function getContentInfo()
    {
        Loader::model('title/model', 'cup_content');
        $vHelper = Loader::helper('edit_go_content', 'cup_content');
        $cModel = new CupContentTitle();

        $data = $_POST;

        $result = $cModel->getContentByID($data['contentID']);
        $contentDetails = $vHelper->displayContentDetails($result['content_details']);
        echo json_encode(array('details'=>$result['details'],'content_details'=>$contentDetails));
        exit;
    }

    // ANZGO-1812
    /**
     * Handles subfolder content detail updates. Shows updated details.
     *
     * @author James Bernardez
     * @editedBy Paul Balila
     *
     */
    public function saveContent()
    {
        $data = $_POST;

        Loader::model('title/model', 'cup_content');
        $vHelper = Loader::helper('edit_go_content','cup_content');
        $cctModel = new CupContentTitle();

        $contents = $cctModel->contentSave($data);

        echo $vHelper->displaySubfolders($contents,$data['contentID-hidden']);
        exit;
    }

    // ANZGO-1812
    /**
     * Saves new subfolder and prints HTML content in string form.
     *
     * @author James Bernardez
     * @editedBy Paul Balila
     */
    public function saveFolder()
    {
        Loader::model('title/model', 'cup_content');
        $fModel = new CupContentTitle();
        $vHelper = Loader::helper('edit_go_content', 'cup_content');
        $data = $_POST;

        $result = $fModel->contentSaveNew($data);

        echo json_encode(array('body'=>$vHelper->displaySubfolders($result['result'],$result['newID'])));
        exit;
    }

    // ANZGO-1812
    /**
     * Retrieves the content details
     * @author James Bernardez
     * @editedBy Paul Balila
     */
    public function getContentDetailInfo()
    {
        $id = $_POST['id'];

        Loader::model('title/model', 'cup_content');
        $cModel = new CupContentTitle();
        $content = $cModel->getContentDetailByID($id);

        $content['URL'] = urldecode($content['URL']);

        echo json_encode($content);
        exit;
    }

    //ANZGO-1847
    /**
     * Gets the content type form for a certain content detail heading
     * @author Paul Balila
     */
    public function getContentTypeForm()
    {
        $vHelper = Loader::helper('edit_go_content','cup_content');
        Loader::model('title/model', 'cup_content');
        $headerArray = array(1001 => 'Link Content',1006 => 'HTML Info', 1005 => 'File Info');

        $typeId = $_POST['TypeID'];
        $contentId = $_POST['ID'];

        $cModel = new CupContentTitle();
        $content = $cModel->getContentDetailByID($contentId);
        switch ($typeId) {
            case 1001:
                $body = $vHelper->getLinkContent($content);
                break;
            case 1006:
                $body = $vHelper->getHTMLInfo($content);
                break;
            case 1005:
                $body = $vHelper->getFileInfo($content);
                $formUpload = $vHelper->buildFileUploadForm($content);
                break;
            default:
                break;
        }
        $retArray = array('header' => $headerArray[$typeId],'body'=>$body);
        if($typeId == 1005) {
            $retArray['uploadForm'] = $formUpload;
        }
        echo json_encode($retArray);
        exit;
    }

    // ANZGO-1812
    /**
     * Handles data saving of a subcontent folder
     * @author James Bernardez
     * @editedBy Paul Balila
     *
     */
    public function saveDetail()
    {
        $data = $_POST;

        Loader::model('title/model', 'cup_content');
        $cdHelper = Loader::helper('edit_go_content','cup_content');
        $cctModel = new CupContentTitle();

        if(!$data['ID']) {
            $result = $cctModel->getContentDetails($data['ContentID']);
            echo $cdHelper->displayContentDetails($result['result']);
        } else {
            $result = $cctModel->detailSave($data);
            echo $cdHelper->displayContentDetails($result['result'],$result['id']);
        }
        exit;
    }

    // ANZGO-1812
    /**
     * Saves new content detail heading and print out lsit of content detail heading
     * @author James Bernardez
     * @editedBy paul Balila
     */
    public function saveFolderDetail()
    {
        Loader::model('title/model', 'cup_content');
        $vHelper = Loader::helper('edit_go_content','cup_content');
        $cModel = new CupContentTitle();

        $data = $_POST;
        $contentDetail = $cModel->detailSaveNew($data);
        echo json_encode(array('body'=>$vHelper->displayContentDetails($contentDetail['result'],$contentDetail['ins_id'])));
        exit;
    }

    /**
     * Handles all file uploading functions for content folders
     * @author Paul Balila
     *
     * @param boolean $isSingleFile Checks if the uploaded files are multiple or not
     */
    public function handleFileUploads($isSingleFile = false)
    {
        Loader::library('file/importer');
        $importer = new FileImporter();

        $contentID = $_POST['ContentID'];
        $upDetails = array();

        for ($index = 0; $index < count($_FILES['files']['name']); $index++) {

            $importDetails = $importer->import($_FILES['files']['tmp_name'][$index],$_FILES['files']['name'][$index]);

            if(is_numeric($importDetails)) {
                echo $importer->getErrorMessage($importDetails);
                exit;
            }
            $upDetails[] = array(
                'FileName' => $importDetails->fvFilename,
                'FilePath' => $this->formatPath($importDetails->getPath(), $importDetails->fvFilename),
                'FileSize' => $_FILES['files']['size'][$index]
                );
        }
        if ($isSingleFile) {
            $tableID = $_POST['ID']; // ID field in CupGoContentDetail table
            $this->saveUploadDetail($tableID,$contentID, $upDetails);
        } else {
            $this->saveFileUploadDetails($contentID, $upDetails);
        }
    }

    /**
     * Saves new content detail and prints out the HTML for that detail.
     * @author Paul Balila
     * @param int $contentID
     * @param array $fileDetails
     */
    public function saveFileUploadDetails($contentID,$fileDetails)
    {
        Loader::model('title/model', 'cup_content');
        $cModel = new CupContentTitle();
        $vHelper = Loader::helper('edit_go_content','cup_content');
        $result = $cModel->detailSaveNewMultiple($contentID, $fileDetails);
        echo $vHelper->displayContentDetails($result);
        exit;
    }

    // ANZGO-1812
    /**
     * Saves update in file content of a content detail and builds the HTML form to "reset" the form.
     *
     * @author James Bernardez
     * @editedBy Paul Balila
     *
     * @param int $tableID
     * @param int $contentID
     * @param array $fileDetails
     */
    public function saveUploadDetail($tableID,$contentID,$fileDetails)
    {
        Loader::model('title/model', 'cup_content');
        $vHelper = Loader::helper('edit_go_content','cup_content');
        $titles = CupContentTitle::uploadDetailSave($tableID,$contentID,$fileDetails);
        echo $vHelper->displayContentDetails($titles,$tableID);
        exit;
    }

    // ANZGO-1812
    // Modified for ANZGO-1944
    /**
     * Saves new folder heading and returns array of folders under a title.
     *
     * @author James Bernardez
     * @editedBy Paul Balila
     */
    public function saveContentFolder()
    {
        Loader::model('title/model', 'cup_content');
        $vHelper = Loader::helper('edit_go_content', 'cup_content');
        $tModel = new CupContentTitle();
        $data = $_POST;
        $result = $tModel->contentFolderSaveNew($data);

        echo json_encode(array('body'=>$vHelper->displayParentFolders($result['result'],$result['newID'])));
        exit;
    }

    // ANZGO-1812
    // unused
    public function saveContentDetailSortable()
    {
        $data = $_POST['data'];

        Loader::model('title/model', 'cup_content');
        $saveUploadDetail = CupContentTitle::updateContentDetailSortable($data);
        exit;
    }

    public function updateSorting($type)
    {
        Loader::model('tabs/model', 'cup_content');
        Loader::model('title/model', 'cup_content');
        $tabModel = new CupContentTabs();
        $titleModel = new CupContentTitle();

        $data = $_POST['sorting'];

        switch ($type) {
            case 'tabs':
                echo $tabModel->updateTabSorting($data);
                break;
            case 'content_details':
                echo $titleModel->updateContentDetailSortable($data);
                break;
            case 'tab_content':
                echo $tabModel->updateTabContentSorting($data);
                break;
            default:
                break;
        }
        exit;
    }

    /**
     * Formats the path of the file to be saved
     * @author Paul Balila
     *
     * @param string $strPath
     * @param string $fileName
     * @return string The formatted path
     */
    private function formatPath($strPath,$fileName)
    {
        $strArr = explode("/", $strPath);
        $fArr = array();

        // Indicator if we need to store elements in $fArr
        $storeFlag = false;

        foreach ($strArr as $str) {
            // We initially chech if we have permission to store.
            if(!$storeFlag) {
                // If we reach the string 'files', set permission to store in $fArr to TRUE.
                $storeFlag = ($str == 'files');
            }

            if($str == $fileName) {
                break;
            }

            if($storeFlag) {
                $fArr[] = $str;
            }


        }
        return implode("/", $fArr) . "/";
    }

    //ANZGO-1944
    /**
     * Renders editing of Contents and tabs under a title.
     * @author Paul Balila
     * @param int $titleID
     */
    public function editGoContents($titleID)
    {
        Loader::model('tabs/model', 'cup_content');
        $titleModel = new CupContentTitle($titleID);
        $tabModel = new CupContentTabs();

        $vHelper = Loader::helper('edit_go_content', 'cup_content');

        // Get the folders under a title and format it for display.
        $parentFoldersResult = $titleModel->getParentFolders($titleID);
        $parentFolders = $vHelper->displayParentFolders($parentFoldersResult);

        // Get the tabs under a title and format it for display
        $tabsResult = $tabModel->getTitleTabs($titleID);
        $tabs = $vHelper->displayTabs($tabsResult);

        $this->set('titleID',$titleID);
        $this->set('titleName',$titleModel->getDisplayName());
        $this->set('parentFolders',$parentFolders);
        $this->set('tabs',$tabs);
        $this->render('/dashboard/cup_content/titles/editGoContents');
    }

    //ANZGO-1944
    /**
     * Gets all subfolder headings under a folder. Then format and prints them.
     *
     * @author Paul Balila
     */
    public function getSubfolders($folder_id = false)
    {
        Loader::model('tabs/model', 'cup_content');
        $titleModel = new CupContentTitle();
        $tabModel = new CupContentTabs();
        $vHelper = Loader::helper('edit_go_content', 'cup_content');

        $data = $_POST;

        $id = ($folder_id) ? $folder_id : $data['folderID'];
        $folders = $titleModel->getFolders($id);
        $subFolders = $vHelper->displaySubfolders($folders);
        echo $subFolders;
        exit;
    }

    // ANZGO-1944
    public function addNewTab()
    {
        Loader::model('tabs/model', 'cup_content');
        $vHelper = Loader::helper('edit_go_content', 'cup_content');

        $titleModel = new CupContentTitle();
        $tabModel = new CupContentTabs();

        $data = $_POST;

        $ins_id = $titleModel->saveNewTab($data);
        $tabsResult = $tabModel->getTitleTabs($data['ID']);
        $tabs = $vHelper->displayTabs($tabsResult,$ins_id);
        echo json_encode(array('body'=>$tabs));
        exit;
    }

    // ANZGO-1944
    public function editHeading($type)
    {
        Loader::model('title/model', 'cup_content');
        $titleModel = new CupContentTitle();
        $data = $_POST;

        switch ($type) {
            case 'folder':
                $result = $titleModel->editFolderHeading($data);
                break;
            default:
                break;
        }
        echo $result;
        exit;
    }

    public function archiveHeading($type)
    {
        Loader::model('title/model', 'cup_content');
        Loader::model('tabs/model', 'cup_content');

        $titleModel = new CupContentTitle();
        $tabModel = new CupContentTabs();

        $vHelper = Loader::helper('edit_go_content', 'cup_content');
        $u = new User();

        $data = $_POST;

        switch ($type) {
            case 'parentFolders':
                $titleModel->archiveHeading($data['ID'],'CupGoContentFolders',$u->uID);

                // Remove all content related to that folder
                $tabModel->deleteTabContentAfterFolderArchive($data['ID']);

                // Archive all contents related to that folder
                $content_ids = $titleModel->getContentIDByFolder($data['ID']);
                $ids_arr = array();
                if($content_ids) {
                    foreach ($content_ids as $id) {
                        $titleModel->archiveHeading($id['ID'],'CupGoContent',$u->uID);
                        $ids_arr[] = $id['ID'];
                    }
                }

                // Archive all content details under the archived contents
                $content_details = $titleModel->getContentDetails($ids_arr);
                if($content_details) {
                    foreach ($content_details as $cd) {
                        // Archive all contents under that folder
                        $titleModel->archiveHeading($cd['ID'],'CupGoContentDetails',$u->uID);
                    }
                }

                $body = $vHelper->displayParentFolders($titleModel->getParentFolders($data['titleID']));
                break;
            case 'subFolders':
                // Modified by Paul Balila, 2014-04-13
                // For ticket ANZUAT-63
                $flag = $titleModel->archiveHeading($data['ID'],'CupGoContent',$u->uID);
                if($flag) {
                    $content_details = $titleModel->getContentDetails($data['ID']);
                    if($content_details) {
                        foreach ($content_details as $cd) {
                            // Archive all contents under that folder
                            $titleModel->archiveHeading($cd['ID'],'CupGoContentDetails',$u->uID);
                        }
                    }
                }
                $body = $vHelper->displaySubfolders($titleModel->getFolders($data['folderID']));
                break;
            case 'contentDetails':
                $titleModel->archiveHeading($data['ID'],'CupGoContentDetails',$u->uID);
                $body = $vHelper->displayContentDetails($titleModel->getContentDetails($data['ContentID']));
                break;
            case 'tabs':
                $titleModel->archiveHeading($data['ID'],'CupGoTabs',$u->uID);
                $body = $vHelper->displayTabs($tabModel->getTitleTabs($data['titleID']));
                break;
            default:
                break;
        }
        echo json_encode(array('body'=>$body));
        exit;
    }

    public function getTabFolders()
    {
        $titleID = $this->post('titleID');
        Loader::model('tabs/model', 'cup_content');
        $tabModel = new CupContentTabs();
        $folders = $tabModel->getTabFolders($titleID);
        $formatter = Loader::helper('title_tabs', 'cup_content');
        echo $formatter->formatTabFolders($folders);
        exit;
    }

    // GCAP 704 Added by mtanada 20200228
    public function getDemoId()
    {
        $demoId = $_POST['demoId'];
        $title = CupContentTitle::fetchBydemoId($demoId);
        if (isset($title) && is_array($title) && count($title) > 0) {
            echo json_encode(array('titleName'=> $title['name']));
            exit;
        } else {
            echo json_encode(array('titleName'=>'available'));
            exit;
        }
    }
}
