<?php
/**
 * Handles all Edit Go series editor functions
 * this should be the same as product editor controller, just to give
 * distinction to series editor
 *
 * @author jbernardez
 */
class GoSeriesEditorController extends Controller
{
    protected $pkgHandle = 'go_product_editor';
    protected $pkgCupContent = 'cup_content';
    private $gpeDB;
    private $gseDB;

    public function __construct()
    {
        parent::__construct();
        Loader::model('go_product_editor_model', $this->pkgHandle);
        Loader::model('go_series_editor_model', $this->pkgHandle);
        $this->gpeDB = new GoProductEditorModel();
        $this->gseDB = new GoSeriesEditorModel();
        $this->uh = Loader::helper('concrete/urls');
    }

    public function on_start()
    {
        global $u;
        $validUsers = array('Administrators', 'Production (code creation)', 'Customer service', 'Marketing');
        $validFlag = false;
        // Check if user is logged in and a valid user.
        if ($u->isLoggedIn()) {
            // Check if valid user.
            $userGroups = $u->getUserGroups();
            foreach ($userGroups as $group) {
                if (in_array($group, $validUsers)) {
                    $validFlag = true;
                    break;
                }
            }
            if (!$validFlag) {
                $this->redirect('/login');
            }
        } else {
            $this->redirect('/login');
        }

        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_product_editor_theme"));
    }

    public function view($seriesID = 0)
    {
        // let's start here
        // 1. instead of directly loading the title ID, let us look first if
        // the series ID has a tag for dummy title ID. CupContentSeriesTitlesReference table
        // 2. then load that title ID from the series
        // 3. if not, create a dummy title id in the titles
        // 4. then load that created title id
        $titleID = $this->gseDB->getTitleIDBySeriesID($seriesID);

        // if titleID returned is false, create the title with Series information
        if (!$titleID) {
            // create title
            $titleID = $this->createTitleWithSeriesID($seriesID);

            // then insert to the reference table after creation of the title
            $this->gseDB->saveIsSeriesTitle($seriesID, $titleID);
        }

        if ($titleID > 0) {
            $url = DIR_REL . '/files/cup_content/images/titles/';

            $title = $this->gpeDB->getGoTitle($titleID);
            $foldersResult = $this->gpeDB->getFolders($titleID);
            $tabsResult = $this->gpeDB->getTabs($titleID);

            $this->set('title', $title);
            $this->set('folders', $foldersResult);
            $this->set('tabs', $tabsResult);
            $this->set('url', $url);
        }
        $this->set('title_id', $titleID);
    }

    private function createTitleWithSeriesID($seriesID)
    {
        Loader::model('model', $this->pkgCupContent);
        $series = new CupContentSeries($seriesID);
        $title = new CupContentTitle();

        $title->isbn13 = 'SERIES' . str_pad($seriesID, 7, '0', STR_PAD_LEFT);
        $title->name = $series->name;
        $title->displayName = $series->name;
        $title->prettyUrl = $series->prettyUrl;
        $title->shortDescription = $series->shortDescription;
        $title->longDescription = $series->longDescription;
        $title->divisions_save = $series->exisiting_result['divisions'];
        $title->regions_save = $series->exisiting_result['regions'];
        $title->yearLevels_save = $series->exisiting_result['yearLevels'];
        $title->type = 'stand alone';
        $title->tagline = $series->tagline;
        $title->saveNew();

        // SB-389 added by jbernardez 20191106
        if ($series->hasImage()) {
            $title->reSaveImageFromSeries($series->seriesID);
        }

        // SB-389 added by jbernardez 20191106
        // this has been added as there are files created with .png
        // and older files uploaded without the .png
        // this is to check on both, to copy image properly
        if ($series->hasImagePNG()) {
            $title->reSaveImageFromSeries($series->seriesID . '.png');
        }

        if ($title->id) {
            return $title->id;
        }

        return false;
    }

    // TAB FUNCTIONS
    public function get_tab_edit_form($tabID, $outputBuffer = false)
    {
        $tabResult = $this->gpeDB->getTabDetails($tabID);
        $icons = $this->gpeDB->getIcons();
        $groups = $this->gpeDB->getGroups();
        
        $hmProducts = $this->gpeDB->loadHotMathsAPITrialProducts('production');
        $args = array(
            'tab_result'    => $tabResult,
            'groups'        => $groups,
            'tab_id'        => $tabID,
            'hm_products'   => $hmProducts,
            'icons'         => $icons
        );

        if ($outputBuffer) {
            ob_start();
            Loader::packageElement('tabs/tab_edit_form',  $this->pkgHandle, $args);
            return ob_get_clean();
        } else {
            Loader::packageElement('tabs/tab_edit_form',  $this->pkgHandle, $args);
            die();
        }
    }

    public function get_tab_add_form()
    {
        $groups = $this->gpeDB->getGroups();
        $icons = $this->gpeDB->getIcons();
        $args = array('groups' => $groups, 'icons' => $icons);
        Loader::packageElement('tabs/tab_add_form', $this->pkgHandle, $args);
        die();
    }

    public function get_tab_contents($tabID)
    {
        $tabName = $this->post('tabName');
        $titleID = $this->post('titleId');
        $contentAddedResult = $this->gpeDB->getContentAdded($tabID);
        $localContentFoldersResult = $this->gpeDB->getFolders($titleID);
        $globalContentResult = $this->gpeDB->getGlobalContents();

        $args = array(
            'tab_name'                  => $tabName,
            'tab_id'                    => $tabID,
            'content_added'             => $contentAddedResult,
            'local_content_folders'     => $localContentFoldersResult,
            'global_content_folders'    => $globalContentResult
        );
        Loader::packageElement('tabs/tab_contents_display',$this->pkgHandle,$args);
        die();
    }

    private function add_tab()
    {
        $data = $this->post('add_tab');
        $titleID = $this->post('title_id');
        $newTabID = $this->gpeDB->saveNewTab($data, $titleID);
        $html = array(
            'tab_form'  => $this->get_tab_edit_form($newTabID, true),
            'tabs_list' => $this->get_tabs_list($titleID)
        );
        echo json_encode($html);
        die();
    }

    private function edit_tab()
    {
        $data = $this->post('edit_tab');
        $titleID = $this->post('title_id');

        $updateFlag = $this->gpeDB->updateTab($data);
        $html = array(
            'tab_form'  => $this->get_tab_edit_form($data['ID'], true),
            'tabs_list' => $this->get_tabs_list($titleID)
        );
        echo json_encode($html);
        die();
    }


    private function delete_tab_action($tabID = '')
    {
        $deleteResult = $this->gpeDB->deleteTab($tabID);
        $titleID = $this->post('title_id');
        $html = array(
            'tabs_list' => $this->get_tabs_list($titleID)
        );
        echo json_encode($html);
        die();
    }

    private function get_tabs_list($titleID)
    {
        $tabsResult = $this->gpeDB->getTabs($titleID);
        ob_start();
        Loader::packageElement('tabs/tabs_list', $this->pkgHandle, array('tabs' => $tabsResult));
        return ob_get_clean();
    }

    public function add_content($tabID, $contentID)
    {
        $heading = $this->get('heading');
        $contentID = $this->get('id');
        $contents = array(
            0 => array(
                'ContentHeading'    => $heading,
                'ColumnNumber'      => 1,
                'Active'            => 'Y',
                'Visibility'        => 'Public',
                'DemoOnly'          => 'N',
                'ID'                => 'temp-' . $this->get_rand_id(),
                'ContentID'         => $contentID
            )
        );
        $args = array(
            'content'       => $contents,
            'tab_id'        => $tabID,
            'content_id'    => $contentID,
            'to_append'     => true
        );
        ob_start();
        Loader::packageElement('tabs/content_added', $this->pkgHandle, $args);
        echo ob_get_clean();
        die();
    }

    public function delete_content_added($tabID, $id)
    {
        $delFlag = $this->gpeDB->deleteAddedContent($id);
        echo $this->get_content_added($tabID);
        die();
    }

    private function edit_content_added()
    {
        $data = $this->post('edit_content_added');
        $tabID = $data['tab_id'];
        $this->gpeDB->updateTabContents($data);
        $html = array(
            'content_added' => $this->get_content_added($tabID)
        );
        echo json_encode($html);
        die();
    }


    private function get_content_added($tabID)
    {
        $contents_result = $this->gpeDB->getContentAdded($tabID);
        ob_start();
        Loader::packageElement('tabs/content_added', $this->pkgHandle, array('content'=>$contents_result, 'tab_id' => $tabID));
        return ob_get_clean();
    }

    // CONTENT FUNCTIONS
    // FOLDER
    public function get_folder($folderID)
    {
        $folderName = $this->post('folderName');
        echo $this->get_folder_edit_form($folderID, $folderName);
        die();
    }

    public function get_add_folder_form($titleID)
    {
        ob_start();
        Loader::packageElement('contents/folder_add_form', $this->pkgHandle, array('title_id' => $titleID));
        echo ob_get_clean();
        die();
    }

    private function add_folder()
    {
        $data = $this->post('add_folder');
        $titleID = $this->post('title_id');
        $insFlag = $this->gpeDB->saveNewFolder($titleID, $data);
        if ($insFlag) {
            $foldersResult = $this->gpeDB->getFolders($titleID);
            $html = array(
                'folders_list'  => $this->get_folders_list($foldersResult, $titleID, $insFlag, false, true),
                'edit_form'     => $this->get_folder_edit_form($insFlag, $data['FolderName'], true)
            );
            echo json_encode($html);
            die();
        }
    }

    private function edit_folder()
    {
        $data = $this->post('edit_folder');
        $titleID = $this->post('title_id');
        $updateFlag = $this->gpeDB->updateFolder($data);
        $foldersResult = $this->gpeDB->getFolders($titleID);
        $html = array(
            'folders_list' => $this->get_folders_list($foldersResult, $titleID, $data['ID'], false, true),
            'edit_form' => $this->get_folder_edit_form($data['ID'], $data['FolderName'], true)
        );
        echo json_encode($html);
        die();
    }

    private function get_folder_edit_form($id, $folderName = '', $outputBuffer = false)
    {
        $args = array(
            'folder_id'     => $id,
            'folder_name'   => $folderName
        );
        if ($outputBuffer) {
            ob_start();
            Loader::packageElement('contents/folder_edit_form', $this->pkgHandle, $args);
            return ob_get_clean();
        } else {
            Loader::packageElement('contents/folder_edit_form', $this->pkgHandle, $args);
            die();
        }
    }

    private function get_folders_list($folders, $titleID, $newFolderID = false, $subfolderID = false, $outputBuffer = false)
    {
        $args = array(
            'folders'       => $folders,
            'title_id'      => $titleID,
            'new_folder_id' => $newFolderID,
            'subfolder_id'  => $subfolderID
        );

        if ($outputBuffer) {
            ob_start();
            Loader::packageElement('contents/folders_list',$this->pkgHandle,$args);
            return ob_get_clean();
        } else {
            Loader::packageElement('contents/folders_list',$this->pkgHandle,$args);
            die();
        }
    }

    public function delete_folder_action($id)
    {
        $delFlag = $this->gpeDB->deleteFolder($id);
        $titleID = $this->post('title_id');
        $foldersResult = $this->gpeDB->getFolders($titleID);
        $html = array(
            'folders_list' => $this->get_folders_list($foldersResult, $titleID, false, false, true),
        );
        echo json_encode($html);
        die();
    }

    // SUBFOLDER
    public function get_subfolder_details($subfolderID, $folderID, $outputBuffer = false)
    {
        $detailsResult = $this->gpeDB->getSubFolderDetails($subfolderID);
        $args = array(
            'details'       => $detailsResult,
            'subfolder_id'  => $subfolderID,
            'folder_id'     => $folderID
        );
        if ($outputBuffer) {
            ob_start();
            Loader::packageElement('contents/subfolder_edit_form',  $this->pkgHandle,$args);
            return ob_get_clean();
        } else {
            Loader::packageElement('contents/subfolder_edit_form',  $this->pkgHandle,$args);
            die();
        }
    }

    public function get_add_subfolder_form($folderID, $folderName)
    {
        $args = array(
            'folder_id'     => $folderID,
            'folder_name'   => $folderName
        );
        ob_start();
        Loader::packageElement('contents/subfolder_add_form',  $this->pkgHandle, $args);
        echo ob_get_clean();
        die();
    }

    private function edit_subfolder()
    {
        $data = $this->post('edit_subfolder');
        $titleID = $this->post('title_id');
        $folderID = $data['FolderID'];

        // Store FilesMeta in temporary storage
        // use temporary when there are files to be uploaded, then
        // UNSET FILESMETA
        $filesMetaTmp = $data['FilesMeta'];
        unset($data['FilesMeta']);

        $updateFlag = $this->gpeDB->updateSubFolder($data);
        $updatedName = $this->gpeDB->getSubFolderHeading($data['ID']);
        $heading = ($updatedName['ContentHeading']) ? $updatedName['ContentHeading'] : '(' . $updatedName['CMS_Name'] . ')';

        // add if there are files uploaded
        $files = $_FILES['edit_subfolder'];
        if (!empty($files)) {

            $filesMeta = array();
            foreach ($filesMetaTmp as $key => $value) {
                $filesMeta[] = $key;
            }

            $contentCount = count($files['name']['SubcontentFiles']) - 1;

            for ($i = 0; $i <= $contentCount; $i++) {
                $name       = $files['name']['SubcontentFiles'][$i];
                $type       = $files['type']['SubcontentFiles'][$i];
                $tmpName    = $files['tmp_name']['SubcontentFiles'][$i];
                $error      = $files['error']['SubcontentFiles'][$i];
                $size       = $files['size']['SubcontentFiles'][$i];
                $date       = strtotime(date('Y-m-d H:i:s'));

                // Build dataOverride
                $dataOverride = array (
                    'ContentID'             => $data['ID'],
                    'folder_id'             => $folderID,
                    'Visibility'            => 'Public',
                    'Active'                => 'Y',
                    'CMS_Notes'             => '',
                    'TypeID'                => '1005',
                    'Public_Name'           => $name,
                    'FileInfo'              => '',
                    'Public_Description'    => '',
                    'WindowBehaviour'       => 'New',
                    'URL'                   => '',
                    'WindowSize'            => '320x240',
                    'HTML_Content'          => '',
                    'FileName'              => $name,
                    'FilePath'              => '',
                    'FileSize'              => $size,
                    'FileUploadDate'        => $date
                );

                $file = array (
                    'name'      => $name,
                    'type'      => $type,
                    'tmp_name'  => $tmpName,
                    'error'     => $error,
                    'size'      => $size,
                );

                // filter if file is included in FilesMeta
                // if it is, then proceed to saving the file, if not, just skip
                $comparisonName = str_replace(' ', '|', $name);
                if (in_array($comparisonName, $filesMeta)) {
                    $this->add_content_detail($dataOverride, $file);
                }
            }

        }

        $foldersResult = $this->gpeDB->getFolders($titleID);
        $html = array(
            'edit_form'     => $this->get_subfolder_details($data['ID'], $folderID, true),
            'folders_list'  => $this->get_folders_list($foldersResult, $titleID, $folderID, $data['ID'], true)
        );

        echo json_encode($html);
        die();
    }

    private function add_subfolder()
    {
        $data = $this->post('add_subfolder');
        $titleID = $this->post('title_id');

        // Store FilesMeta in temporary storage
        // use temporary when there are files to be uploaded, then
        // UNSET FILESMETA
        $filesMetaTmp = $data['FilesMeta'];
        unset($data['FilesMeta']);

        $insID = $this->gpeDB->addSubFolder($data, $titleID);

        // add if there are files uploaded
        $folderID = $data['FolderID'];
        $files = $_FILES['add_subfolder'];
        if (!empty($files)) {

            $filesMeta = array();
            foreach ($filesMetaTmp as $key => $value) {
                $filesMeta[] = $key;
            }

            $contentCount = count($files['name']['SubcontentFiles']) - 1;

            for ($i = 0; $i <= $contentCount; $i++) {
                $name       = $files['name']['SubcontentFiles'][$i];
                $type       = $files['type']['SubcontentFiles'][$i];
                $tmpName    = $files['tmp_name']['SubcontentFiles'][$i];
                $error      = $files['error']['SubcontentFiles'][$i];
                $size       = $files['size']['SubcontentFiles'][$i];
                $date       = strtotime(date('Y-m-d H:i:s'));

                // Build dataOverride
                $dataOverride = array (
                    'ContentID'             => $insID,
                    'folder_id'             => $folderID,
                    'Visibility'            => 'Public',
                    'Active'                => 'Y',
                    'CMS_Notes'             => '',
                    'TypeID'                => '1005',
                    'Public_Name'           => $name,
                    'FileInfo'              => '',
                    'Public_Description'    => '',
                    'WindowBehaviour'       => 'New',
                    'URL'                   => '',
                    'WindowSize'            => '320x240',
                    'HTML_Content'          => '',
                    'FileName'              => $name,
                    'FilePath'              => '',
                    'FileSize'              => $size,
                    'FileUploadDate'        => $date
                );

                $file = array (
                    'name'      => $name,
                    'type'      => $type,
                    'tmp_name'  => $tmpName,
                    'error'     => $error,
                    'size'      => $size,
                );

                // filter if file is included in FilesMeta
                // if it is, then proceed to saving the file, if not, just skip
                $comparisonName = str_replace(' ', '|', $name);
                if (in_array($comparisonName, $filesMeta)) {
                    $this->add_content_detail($dataOverride, $file);
                }
            }

        }

        $foldersResult = $this->gpeDB->getFolders($titleID);
        $html = array(
            'edit_form' => $this->get_subfolder_details($insID, $data['FolderID'], true),
            'folders_list' => $this->get_folders_list($foldersResult, $titleID, $data['FolderID'], $insID, true)
        );

        echo json_encode($html);
        die();
    }

    public function delete_subfolder_action($subfolderID, $folderID)
    {
        $titleID = $this->post('title_id');
        $delFlag = $this->gpeDB->deleteSubFolder($subfolderID, $folderID);
        $foldersResult = $this->gpeDB->getFolders($titleID);

        $html = array(
            'folders_list' => $this->get_folders_list($foldersResult, $titleID, $folderID, false, true)
        );

        echo json_encode($html);
        die();
    }

    // SUBFOLDER CONTENT
    public function get_subfolder_content_details($subfolderContentID, $subfolderID = false, $folderID = false, $outputBuffer = false)
    {
        $subfolderContentResult = $this->gpeDB->getSubFolderContents($subfolderContentID);
        $args = array(
            'details'       => $subfolderContentResult,
            'subfolder_id'  => $subfolderID,
            'folder_id'     => $folderID
        );

        if ($outputBuffer) {
            ob_start();
            Loader::packageElement('contents/subfolder_content_edit_form', $this->pkgHandle, $args);
            return ob_get_clean();
        } else {
            Loader::packageElement('contents/subfolder_content_edit_form', $this->pkgHandle, $args);
            die();
        }
    }

    public function get_add_content_detail_form($subfolderID, $folderID)
    {
        ob_start();
        Loader::packageElement('contents/subfolder_content_add_form', $this->pkgHandle,
            array(
                'subfolder_id' => $subfolderID,
                'folder_id' => $folderID
            )
        );
        echo ob_get_clean();
        die();
    }

    // UPDATE
    private function add_content_detail($dataOverride = null, $fileOverride = null)
    {
        if (!is_null($dataOverride)) {
            $data       = $dataOverride;
            $file       = $fileOverride;
            $folderID   = $data['folder_id'];
            $titleID    = $this->post('title_id');
        } else {
            $data       = $this->post('add_content_detail');
            $file       = $_FILES['ContentDetailFile'];
            $folderID   = $data['folder_id'];
            $titleID    = $this->post('title_id');
        }

        if (!empty($file)) {
            $file_path = $this->handle_single_file_upload($file);
            $data['FilePath'] = $file_path;
        }
        $insID = $this->gpeDB->saveNewContentDetail($data);

        // ANZGO-2904
        if (is_null($dataOverride)) {
            $foldersResult = $this->gpeDB->getFolders($titleID);
            $html = array(
                'edit_form'     => $this->get_subfolder_content_details($insID, $data['ContentID'], $folderID, true),
                'folders_list'  => $this->get_folders_list($foldersResult, $titleID, $folderID, $data['ContentID'], true)
            );

            echo json_encode($html);
            die();
        }
    }

    private function edit_content_detail()
    {
        $data = $this->post('edit_content_detail');
        $file = $_FILES['ContentDetailFile'];

        if (!empty($file)) {
            $filePath = $this->handle_single_file_upload($file, true);
            $data['FilePath'] = $filePath;
        }

        $titleID        = $this->post('title_id');
        $subfolderID    = $data['subfolder_id'];
        $folderID       = $data['folder_id'];
        $updateFlag     = $this->gpeDB->updateContentDetail($data);
        $foldersResult  = $this->gpeDB->getFolders($titleID);
        $html = array(
            'edit_form'     => $this->get_subfolder_content_details($data['ID'], $subfolderID, $folderID, true),
            'folders_list'  => $this->get_folders_list($foldersResult, $titleID, $folderID, $subfolderID, true)
        );
        echo json_encode($html);
        die();
    }

    public function delete_content_detail_action($contentDetailID, $subfolderID, $folderID)
    {
        $titleID = $this->post('title_id');
        $delFlag = $this->gpeDB->deleteContentDetail($contentDetailID);
        $foldersResult = $this->gpeDB->getFolders($titleID);
        $html = array(
            'folders_list' => $this->get_folders_list($foldersResult, $titleID, $folderID, $subfolderID, true)
        );
        echo json_encode($html);
        die();
    }

    /**
     * This is where all forms go to and further triaged.
     */
    public function general_form_landing()
    {
        // Get all post data.
        $data = $this->post();

        // Get form input array key and call the equivalent function.
        $keys = array_keys($data);
        foreach ($keys as $key) {
            if ($key != 'title_id') {
                $method = $key;
                break;
            }
        }

        // Call the method.
        $this->$method();
    }

    /**
     * This is where all deleting functions go to and triaged further.
     *
     * @param string $method
     * @param string $arg1
     */
    public function general_delete($method, $arg1 = '')
    {
        $this->$method($arg1);
    }

    private function get_rand_id()
    {
        $tmp = '';
        $source = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890';
        while (strlen($tmp) <= 4) {
            $tmp .= substr($source, rand(0, strlen($source)), 1);
        }
        return $tmp;
    }

    public function updateSorting($type)
    {
        $data = $_POST['sorting'];

        switch ($type) {
            case 'tabs':
                echo $this->gpeDB->updateTabSorting($data);
                break;
            case 'content_details':
                echo $this->gpeDB->updateContentDetailSortable($data);
                break;
            case 'tab_content':
                echo $this->gpeDB->updateTabContentSorting($data);
                break;
            default:
                break;
        }
        exit;
    }

    private function handle_single_file_upload($files, $single_file = false)
    {
        Loader::library('file/importer');
        $importer = new FileImporter();

        $importDetails = $importer->import($files['tmp_name'],$files['name']);

        if (is_numeric($importDetails)) {
            echo $importer->getErrorMessage($importDetails);
            exit;
        }

        return $importDetails->getRelativePath();
    }
}
