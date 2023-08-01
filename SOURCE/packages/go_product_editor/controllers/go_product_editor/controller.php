<?php
/**
 * Handles all Edit Go product editor functions
 *
 * @author paulbalila
 */

// CGO-253 Added by mmascarinas 2023/02/09
Loader::library('hub-sdk/autoload');

use HubEntitlement\Models\Product;

class GoProductEditorController extends Controller {
    protected $pkg_handle = 'go_product_editor';
    private $gpeDb;
    // GCAP-845 Created by JSulit 2020/05/08
    private $groupTabList;
    // CGO-253 Created by mmascarinas 2023/02/09
    private $db;
    private $user;

    public function __construct() {
        parent::__construct();
        Loader::model('go_product_editor_model', $this->pkg_handle);
        $this->gpeDb = new GoProductEditorModel();
        $this->uh = Loader::helper('concrete/urls');
        $this->db = Loader::db();
        $this->user = new User();
    }

    public function on_start() {
        global $u;
        $valid_users = array('Administrators','Production (code creation)','Customer service', 'Marketing');
        $valid_flag = FALSE;
        // Check if user is logged in and a valid user.
        if($u->isLoggedIn()) {
            // Check if valid user.
            $user_groups = $u->getUserGroups();
            foreach ($user_groups as $group) {
                if(in_array($group, $valid_users)) {
                    $valid_flag = TRUE;
                    break;
                }
            }
            if(!$valid_flag) {
                $this->redirect('/login');
            }
        } else {
            $this->redirect('/login');
        }

        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_product_editor_theme"));
        // GCAP-845 Modified by JSulit 2020/05/08
        $this->groupTabList = $this->gpeDb->getGroupTabList();
    }

    public function view($title_id = 0) {
        if ($title_id > 0) {

            $url = DIR_REL . '/files/cup_content/images/titles/';

            $title = $this->gpeDb->getGoTitle($title_id);
            $folders_result = $this->gpeDb->getFolders($title_id);

            $tabs_result = $this->gpeDb->getTabs($title_id);

            $this->set('title', $title);

            $this->set('folders', $folders_result);
            $this->set('tabs', $tabs_result);
            $this->set('url', $url);
        }
        $this->set('title_id',$title_id);
    }

    // TAB FUNCTIONS
    public function get_tab_edit_form($tab_id,$output_buffer = FALSE) {
        $tab_result = $this->gpeDb->getTabDetails($tab_id);
        // ANZGO-2902
        $groups = $this->gpeDb->getGroups();
        $icons = $this->gpeDb->getIcons();
        $hm_products = $this->gpeDb->loadHotMathsAPITrialProducts('production');
        $args = array('tab_result' => $tab_result,
            'groups' => $groups,
            'tab_id' => $tab_id,
            'hm_products' => $hm_products,
            'icons' => $icons,
            'groupTabList' => $this->groupTabList,
        );
        if($output_buffer) {
            ob_start();
            Loader::packageElement('tabs/tab_edit_form',  $this->pkg_handle,$args);
            return ob_get_clean();
        } else {
            Loader::packageElement('tabs/tab_edit_form',  $this->pkg_handle,$args);
            die();
        }
    }
    // GCAP-845 Modified groubTabName by JSulit 2020/05/08
    public function get_tab_add_form() {
        $groups = $this->gpeDb->getGroups();
        $icons = $this->gpeDb->getIcons();
        $args = array(
            'groups' => $groups,
            'icons' => $icons,  
            'groupTabList' => $this->groupTabList
        );
        Loader::packageElement('tabs/tab_add_form', $this->pkg_handle, $args);
        die();
    }

    public function get_tab_contents($tab_id) {
        $tab_name = $this->post('tabName');
        $title_id = $this->post('titleId');
        $content_added_result = $this->gpeDb->getContentAdded($tab_id);
        $local_content_folders_result = $this->gpeDb->getFolders($title_id);
        $global_content_result = $this->gpeDb->getGlobalContents();

        $args = array(
            'tab_name'=>$tab_name,
            'tab_id' => $tab_id,
            'content_added'=>$content_added_result,
            'local_content_folders' => $local_content_folders_result,
            'global_content_folders' => $global_content_result
        );
        Loader::packageElement('tabs/tab_contents_display',$this->pkg_handle,$args);
        die();
    }

    private function add_tab() {
        $data = $this->post('add_tab');
        $title_id = $this->post('title_id');
        $new_tab_id = $this->gpeDb->saveNewTab($data, $title_id);
        $html = array(
            'tab_form' => $this->get_tab_edit_form($new_tab_id, TRUE),
            'tabs_list' => $this->get_tabs_list($title_id)
        );
        echo json_encode($html);
        die();
    }

    private function edit_tab() {
        $data = $this->post('edit_tab');
        $title_id = $this->post('title_id');
        $update_flag = $this->gpeDb->updateTab($data);
        $html = array(
            'tab_form' => $this->get_tab_edit_form($data['ID'],TRUE),
            'tabs_list' => $this->get_tabs_list($title_id)
        );
        echo json_encode($html);
        die();
    }

    // CGO-283 Added by mmascarinas 2023/02/15
    private function logTabAction($logType = 'Tabs', $logText = '', $isLogInternal = true)
    {
        $currentUser = $this->user;

        $values = array(
            $logType,
            $logText,
            $isLogInternal,
            $currentUser->uID,
            date('Y/m/d H:i:s')
        );

        $this->db->Execute(
            'INSERT INTO Logs (
                logType, logText, logIsInternal, logUserID, timestamp
            ) VALUES (?, ?, ?, ?, ?)',
            $values
        );
    }

    private function delete_tab_action($tab_id = '') {
        $this->gpeDb->deleteTab($tab_id);
        $currentUser = $this->user;

        // CGO-253 Added by mmascarinas 2023/02/09
        $logText = "TabID $tab_id has been deleted by user $currentUser->uID";

        // CGO-283 Added by mmascarinas 2023/02/15
        $products = Product::where([
            'keyword' => "\"id\": $tab_id"
        ]);

        if (!empty($products)) {
            $producIds = array_map(function ($product) {
                return $product->attributes['id'];
            }, $products);

            $productString = implode(",\n ", $producIds);
            $logText .= "\nAllocated to the following PEAS Product(s) [\n $productString \n]";
        }

        $this->logTabAction('Tabs',  $logText, true);

        $title_id = $this->post('title_id');
        $html = array(
            'tabs_list' => $this->get_tabs_list($title_id)
        );

        echo json_encode($html);
        die();
    }

    private function get_tabs_list($title_id){
        $tabs_result = $this->gpeDb->getTabs($title_id);
        ob_start();
        Loader::packageElement('tabs/tabs_list',$this->pkg_handle,array('tabs' => $tabs_result));
        return ob_get_clean();
    }

    public function add_content($tab_id, $content_id) {
        $heading = $this->get('heading');
        $content_id = $this->get('id');
        $contents = array(
            0 => array(
                'ContentHeading' => $heading,
                'ColumnNumber' => 1,
                'Active' => 'Y',
                'Visibility' => 'Public',
                'DemoOnly' => 'N',
                'ID' => 'temp-' . $this->get_rand_id(),
                'ContentID' => $content_id
            )
        );
        $args = array('content'=>$contents, 'tab_id' => $tab_id, 'content_id' => $content_id ,'to_append' => TRUE);
        ob_start();
        Loader::packageElement('tabs/content_added',$this->pkg_handle,$args);
        echo ob_get_clean();
        die();
    }

    public function delete_content_added($tab_id,$id) {
        $del_flag = $this->gpeDb->deleteAddedContent($id);
        echo $this->get_content_added($tab_id);
        die();
    }

    private function edit_content_added() {
        $data = $this->post('edit_content_added');
        $tab_id = $data['tab_id'];
        $this->gpeDb->updateTabContents($data);
        $html = array(
            'content_added' => $this->get_content_added($tab_id)
        );
        echo json_encode($html);
        die();
    }


    private function get_content_added($tab_id) {
        $contents_result = $this->gpeDb->getContentAdded($tab_id);
        ob_start();
        Loader::packageElement('tabs/content_added',$this->pkg_handle,array('content'=>$contents_result, 'tab_id' => $tab_id));
        return ob_get_clean();
    }

    // CONTENT FUNCTIONS
    // FOLDER
    public function get_folder($folder_id) {
        $folder_name = $this->post('folderName');
        echo $this->get_folder_edit_form($folder_id, $folder_name);
        die();
    }

    public function get_add_folder_form($title_id) {
        ob_start();
        Loader::packageElement('contents/folder_add_form',$this->pkg_handle,array('title_id'=>$title_id));
        echo ob_get_clean();
        die();
    }

    private function add_folder() {
        $data = $this->post('add_folder');
        $title_id = $this->post('title_id');
        $ins_flag = $this->gpeDb->saveNewFolder($title_id,$data);
        if($ins_flag) {
            $folders_result = $this->gpeDb->getFolders($title_id);
            $html = array(
                'folders_list' => $this->get_folders_list($folders_result, $title_id, $ins_flag, FALSE, TRUE),
                'edit_form' => $this->get_folder_edit_form($ins_flag, $data['FolderName'], TRUE)
            );
            echo json_encode($html);
            die();
        }
    }

    private function edit_folder() {
        $data = $this->post('edit_folder');
        $title_id = $this->post('title_id');
        $update_flag = $this->gpeDb->updateFolder($data);
        $folders_result = $this->gpeDb->getFolders($title_id);
        $html = array(
            'folders_list' => $this->get_folders_list($folders_result, $title_id, $data['ID'], FALSE,TRUE),
            'edit_form' => $this->get_folder_edit_form($data['ID'], $data['FolderName'], TRUE)
        );
        echo json_encode($html);
        die();
    }

    private function get_folder_edit_form($id,$folder_name = "",$output_buffer = FALSE) {
        $args = array('folder_id'=>$id,'folder_name'=>$folder_name);
        if($output_buffer) {
            ob_start();
            Loader::packageElement('contents/folder_edit_form',$this->pkg_handle,$args);
            return ob_get_clean();
        } else {
            Loader::packageElement('contents/folder_edit_form',$this->pkg_handle,$args);
            die();
        }
    }

    private function get_folders_list($folders,$title_id,$new_folder_id = FALSE, $subfolder_id = FALSE, $output_buffer = FALSE) {
        $args = array('folders'=>$folders,'title_id'=>$title_id,'new_folder_id'=>$new_folder_id, 'subfolder_id' => $subfolder_id);
        if($output_buffer) {
            ob_start();
            Loader::packageElement('contents/folders_list',$this->pkg_handle,$args);
            return ob_get_clean();
        } else {
            Loader::packageElement('contents/folders_list',$this->pkg_handle,$args);
            die();
        }
    }

    public function delete_folder_action($id) {
        $del_flag = $this->gpeDb->deleteFolder($id);
        $title_id = $this->post('title_id');
        $folders_result = $this->gpeDb->getFolders($title_id);
        $html = array(
            'folders_list' => $this->get_folders_list($folders_result, $title_id, FALSE, FALSE,TRUE),
        );
        echo json_encode($html);
        die();
    }

    // SUBFOLDER
    public function get_subfolder_details($subfolder_id, $folder_id, $output_buffer = FALSE) {
        $details_result = $this->gpeDb->getSubFolderDetails($subfolder_id);
        $args = array('details'=>$details_result, 'subfolder_id' => $subfolder_id, 'folder_id' => $folder_id);
        if($output_buffer) {
            ob_start();
            Loader::packageElement('contents/subfolder_edit_form',  $this->pkg_handle,$args);
            return ob_get_clean();
        } else {
            Loader::packageElement('contents/subfolder_edit_form',  $this->pkg_handle,$args);
            die();
        }
    }

    public function get_add_subfolder_form($folder_id,$folder_name) {
        $args = array(
            'folder_id' => $folder_id,
            'folder_name' => $folder_name
        );
        ob_start();
        Loader::packageElement('contents/subfolder_add_form',  $this->pkg_handle, $args);
        echo ob_get_clean();
        die();
    }

    private function edit_subfolder() {
        $data = $this->post('edit_subfolder');
        $title_id = $this->post('title_id');
        $folder_id = $data['FolderID'];
        $response = [];

        // ANZGO-2904
        // Store FilesMeta in temporary storage
        // use temporary when there are files to be uploaded, then
        // UNSET FILESMETA
        $filesMetaTmp = $data['FilesMeta'];
        unset($data['FilesMeta']);

        $update_flag = $this->gpeDb->updateSubFolder($data);
        $updated_name = $this->gpeDb->getSubFolderHeading($data['ID']);
        $heading = ($updated_name['ContentHeading']) ? $updated_name['ContentHeading'] : "(" . $updated_name['CMS_Name'] . ")";

        // ANZGO-2904
        // add if there are files uploaded
        $files = $_FILES['edit_subfolder'];
        if (!empty($files)) {

            $filesMeta = array();
            foreach ($filesMetaTmp as $key => $value) {
                $filesMeta[] = $key;
            }

            $contentCount = count($files['name']['SubcontentFiles']) - 1;

            for ($i = 0; $i <= $contentCount; $i++) {
                $name = $files['name']['SubcontentFiles'][$i];
                $type = $files['type']['SubcontentFiles'][$i];
                $tmp_name = $files['tmp_name']['SubcontentFiles'][$i];
                $error = $files['error']['SubcontentFiles'][$i];
                $size = $files['size']['SubcontentFiles'][$i];

                $date = strtotime(date('Y-m-d H:i:s'));

                // Build dataOverride
                $dataOverride = array (
                    'ContentID' => $data['ID'],
                    'folder_id' => $folder_id,
                    'Visibility' => 'Public',
                    'Active' => 'Y',
                    'CMS_Notes' => '',
                    'TypeID' => '1005',
                    'Public_Name' => $name,
                    'FileInfo' => '',
                    'Public_Description' => '',
                    'WindowBehaviour' => 'New',
                    'URL' => '',
                    'WindowSize' => '320x240',
                    'HTML_Content' => '',
                    'FileName' => $name,
                    'FilePath' => '',
                    'FileSize' => $size,
                    'FileUploadDate' => $date
                );

                $file = array (
                    'name' => $name,
                    'type' => $type,
                    'tmp_name' => $tmp_name,
                    'error' => $error,
                    'size' => $size,
                );

                // filter if file is included in FilesMeta
                // if it is, then proceed to saving the file, if not, just skip
                $comparisonName = str_replace(" ", "|", $name);
                if (in_array($comparisonName, $filesMeta)) {
                    $response = $this->add_content_detail($dataOverride, $file);
                    if (array_key_exists('error', $response)) {
                        break;
                    }
                }
            }

        }

        $folders_result = $this->gpeDb->getFolders($title_id);
        $html = array(
            'edit_form' => $this->get_subfolder_details($data['ID'], $folder_id,TRUE),
            'folders_list' => $this->get_folders_list($folders_result, $title_id, $folder_id, $data['ID'], TRUE)
        );

        if (array_key_exists('error', $response)) {
            $html['error'] = $response['error'];
        }

        echo json_encode($html);
        die();
    }

    private function add_subfolder() {

        $data = $this->post('add_subfolder');
        $title_id = $this->post('title_id');
        $response = [];

        // ANZGO-2904
        // Store FilesMeta in temporary storage
        // use temporary when there are files to be uploaded, then
        // UNSET FILESMETA
        $filesMetaTmp = $data['FilesMeta'];
        unset($data['FilesMeta']);

        $ins_id = $this->gpeDb->addSubFolder($data, $title_id);

        // ANZGO-2904
        // add if there are files uploaded
        $folder_id = $data['FolderID'];
        $files = $_FILES['add_subfolder'];
        if (!empty($files)) {

            $filesMeta = array();
            foreach ($filesMetaTmp as $key => $value) {
                $filesMeta[] = $key;
            }

            $contentCount = count($files['name']['SubcontentFiles']) - 1;

            for ($i = 0; $i <= $contentCount; $i++) {
                $name = $files['name']['SubcontentFiles'][$i];
                $type = $files['type']['SubcontentFiles'][$i];
                $tmp_name = $files['tmp_name']['SubcontentFiles'][$i];
                $error = $files['error']['SubcontentFiles'][$i];
                $size = $files['size']['SubcontentFiles'][$i];

                $date = strtotime(date('Y-m-d H:i:s'));

                // Build dataOverride
                $dataOverride = array (
                    'ContentID' => $ins_id,
                    'folder_id' => $folder_id,
                    'Visibility' => 'Public',
                    'Active' => 'Y',
                    'CMS_Notes' => '',
                    'TypeID' => '1005',
                    'Public_Name' => $name,
                    'FileInfo' => '',
                    'Public_Description' => '',
                    'WindowBehaviour' => 'New',
                    'URL' => '',
                    'WindowSize' => '320x240',
                    'HTML_Content' => '',
                    'FileName' => $name,
                    'FilePath' => '',
                    'FileSize' => $size,
                    'FileUploadDate' => $date
                );

                $file = array (
                    'name' => $name,
                    'type' => $type,
                    'tmp_name' => $tmp_name,
                    'error' => $error,
                    'size' => $size,
                );

                // filter if file is included in FilesMeta
                // if it is, then proceed to saving the file, if not, just skip
                $comparisonName = str_replace(" ", "|", $name);
                if (in_array($comparisonName, $filesMeta)) {
                    $response = $this->add_content_detail($dataOverride, $file);
                    if (array_key_exists('error', $response)) {
                        break;
                    }
                }
            }

        }

        $folders_result = $this->gpeDb->getFolders($title_id);
        $html = array(
            'edit_form' => $this->get_subfolder_details($ins_id, $data['FolderID'],TRUE),
            'folders_list' => $this->get_folders_list($folders_result, $title_id, $data['FolderID'], $ins_id, TRUE)
        );

        if (array_key_exists('error', $response)) {
            $html['error'] = $response['error'];
        }

        echo json_encode($html);
        die();
    }

    public function delete_subfolder_action($subfolder_id, $folder_id) {
        $title_id = $this->post('title_id');
        $del_flag = $this->gpeDb->deleteSubFolder($subfolder_id, $folder_id);

        $folders_result = $this->gpeDb->getFolders($title_id);

        $html = array(
            'folders_list' => $this->get_folders_list($folders_result, $title_id, $folder_id, FALSE, TRUE)
        );

        echo json_encode($html);
        die();
    }

    // SUBFOLDER CONTENT
    public function get_subfolder_content_details($subfolder_content_id, $subfolder_id = FALSE, $folder_id = FALSE,$output_buffer = FALSE) {
        $subfolder_content_result = $this->gpeDb->getSubFolderContents($subfolder_content_id);
        $args = array('details'=>$subfolder_content_result, 'subfolder_id' => $subfolder_id, 'folder_id' => $folder_id);

        if($output_buffer) {
            ob_start();
            Loader::packageElement('contents/subfolder_content_edit_form',$this->pkg_handle,$args);
            return ob_get_clean();
        } else {
            Loader::packageElement('contents/subfolder_content_edit_form',$this->pkg_handle,$args);
            die();
        }
    }

    public function get_add_content_detail_form($subfolder_id, $folder_id) {
        ob_start();
        Loader::packageElement('contents/subfolder_content_add_form',$this->pkg_handle,array('subfolder_id'=>$subfolder_id,'folder_id'=>$folder_id));
        echo ob_get_clean();
        die();
    }

    // ANZGO-2904
    // UPDATE
    private function add_content_detail($dataOverride = null, $fileOverride = null) {

        // ANZGO-2904
        if (!is_null($dataOverride)) {
            $data = $dataOverride;
            $file = $fileOverride;
            $folder_id = $data['folder_id'];
            $title_id = $this->post('title_id');
        } else {
            $data = $this->post('add_content_detail');
            $file = $_FILES['ContentDetailFile'];
            $folder_id = $data['folder_id'];
            $title_id = $this->post('title_id');
        }

        if(!empty($file)) {
            $response = $this->handle_single_file_upload($file);
            if (array_key_exists('error', $response)) {
                return $response;
            } else {
                $data['FilePath'] = $response['success'];
            }
        } else if (empty($data['FileName'])) {
            echo 'Could not save content. No file to upload.';
            die();
        }
        
        $ins_id = $this->gpeDb->saveNewContentDetail($data);

        // ANZGO-2904
        if (is_null($dataOverride)) {
            $folders_result = $this->gpeDb->getFolders($title_id);
            $html = array(
                'edit_form' => $this->get_subfolder_content_details($ins_id, $data['ContentID'], $folder_id, TRUE),
                'folders_list' => $this->get_folders_list($folders_result, $title_id, $folder_id, $data['ContentID'], TRUE),
		//Modified by JSulit SB-976
                'content_detail_id' => $ins_id
            );

            echo json_encode($html);
            die();
        }

        return array('success' => 'Block of content added successfully');
    }

    private function edit_content_detail() {
        $data = $this->post('edit_content_detail');
        $file = $_FILES['ContentDetailFile'];
        $response = null;
        if(!empty($file)) {
            $response = $this->handle_single_file_upload($file, TRUE);
            if (array_key_exists('error', $response)) {
                break;
            }
            $data['FilePath'] = $response['success'];
        }
        $title_id = $this->post('title_id');
        $subfolder_id = $data['subfolder_id'];
        $folder_id = $data['folder_id'];
        $update_flag = $this->gpeDb->updateContentDetail($data);
        $folders_result = $this->gpeDb->getFolders($title_id);
        $html = array(
            'edit_form' => $this->get_subfolder_content_details($data['ID'],$subfolder_id,$folder_id,TRUE),
            'folders_list' => $this->get_folders_list($folders_result, $title_id, $folder_id, $subfolder_id,TRUE),
            'content_detail_id' => $data['ID']
        );

        if (!is_null($response) && array_key_exists('error', $response)) {
            $html['error'] = $response['error'];
        }

        echo json_encode($html);
        die();
    }

    public function delete_content_detail_action($content_detail_id, $subfolder_id, $folder_id) {
        $title_id = $this->post('title_id');
        $del_flag = $this->gpeDb->deleteContentDetail($content_detail_id);
        $folders_result = $this->gpeDb->getFolders($title_id);
        $html = array(
            'folders_list' => $this->get_folders_list($folders_result, $title_id, $folder_id, $subfolder_id,TRUE)
        );
        echo json_encode($html);
        die();
    }


    /**
     * This is where all forms go to and further triaged.
     */
    public function general_form_landing() {
        // Get all post data.
        $data = $this->post();

        // Get form input array key and call the equivalent function.
        $keys = array_keys($data);
        foreach ($keys as $key) {
            if($key != 'title_id') {
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
    public function general_delete($method,$arg1 = '') {
        $this->$method($arg1);
    }

    private function get_rand_id() {
        $tmp = "";
        $source = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890";
        while (strlen($tmp) <= 4) {
            $tmp .= substr($source, rand(0, strlen($source)), 1);
        }
        return $tmp;
    }

    // ANZGO-2899
    public function updateSorting($type) {
        $data = $_POST['sorting'];

        switch ($type) {
            case 'tabs':
                echo $this->gpeDb->updateTabSorting($data);
                break;
            case 'content_details':
                echo $this->gpeDb->updateContentDetailSortable($data);
                break;
            case 'tab_content':
                echo $this->gpeDb->updateTabContentSorting($data);
                break;
            default:
                break;
        }
        exit;
    }

    private function handle_single_file_upload($files, $single_file = FALSE) {
        Loader::library('file/importer');
        $importer = new FileImporter();

        $importDetails = $importer->import($files['tmp_name'],$files['name']);

        if(is_numeric($importDetails)) {
            // SB-1129 modified by machua 08262022
            $errorMessage = $importer->getErrorMessage($importDetails);
            $fileName = $files['name'];

            return array('error' => $errorMessage. ' (' . $fileName . ')');
        }

        return array('success' => $importDetails->getRelativePath());
    }
    // <!-- Added by JSulit SB-976 -->
    public function initialize_upload() {
          $temp = sys_get_temp_dir();
          $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
          $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
          
          $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $_FILES["file"]["name"];
          $filePath = $temp . DIRECTORY_SEPARATOR . $fileName;
          $date = strtotime(date('Y-m-d H:i:s'));
          // Open temp file
          $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
          if ($out) {
            // Read binary input stream and append it to temp file
            $in = fopen($_FILES['file']['tmp_name'], "rb");
          
            if ($in) {
              while ($buff = fread($in, 4096))
                fwrite($out, $buff);
            } else {
              die('{"OK": 0, "info": "error reading the file."}');
            }
            fclose($in);
            fclose($out);
          
            unlink($_FILES['file']['tmp_name']);
          } else {
            die('{"OK": 0, "info": "error chunking"}');
          }
          // Check if file has been uploaded
          if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off
            rename("{$filePath}.part", $filePath);
            $response = $this->handle_single_file_upload(['tmp_name' => $filePath, 'name' => $fileName]);
            if (array_key_exists('error', $response)) {
                echo $response['error'];
                exit;
            }
            $fileFinalPath = str_replace($fileName, '', $response['success']);

            $content_detail_id = $this->post('content_detail_id');
            $file_size = $this->post('file_size');
            $public_name = $this->post('public_name');
            $public_description = $this->post('public_description');
            $file_info = $this->post('file_info');
            $con_notes = $this->post('con_notes');
            $this->gpeDb->updateContentDetail([ 
                'ID' => $content_detail_id,
                'WindowWidth' => 240,
                'WindowHeight' => 320,
                'Visibility' => 'Public',
                'Active' => 'Y',
                'CMS_Notes' => $con_notes,
                'TypeID' => '1005',
                'Public_Name' => $public_name,
                'FileInfo' => $file_info,
                'Public_Description' => $public_description,
                'WindowBehaviour' => 'New',
                'URL' => '',
                'WindowSize' => '320x240',
                'HTML_Content' => '',
                'FileName' => $fileName,
                'FileSize' => $file_size,
                'FilePath' =>$fileFinalPath,
                'FileUploadDate' => $date

            ]);
          }
    }
}
