<?php
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Description of manage
 *
 * @author paulbalila
 */

Loader::model('global_content_model','global_content');

class DashboardGlobalTabManagementController extends Controller {
    private $gcm;
    
    public function on_start() {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->javascript('scripts.js', 'global_content'));
        $this->addHeaderItem($html->css('styles.css', 'global_content'));
        $this->addHeaderItem($html->javascript('tiny_mce/tiny_mce.js'));
        
        // Configure tinyMCE
        $this->addHeaderItem(""
                . "<script type='text/javascript'>"
                . "tinymce.init({"
                . "selector : 'textarea',"
                . "width : 650,"
                . "height : 300,"
                . "theme : 'advanced',"
                . "theme_advanced_buttons1 : 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect',"
                . "theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor',"
                . "theme_advanced_buttons3 : 'hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,',"
                . "theme_advanced_toolbar_location : 'top',"
                . "theme_advanced_toolbar_align : 'left'"
                . "});"
                . "</script>");
        $this->gcm = new GlobalContentModel();
        
    }
    
    public function view() {
        $gc_tabs = $this->gcm->getGlobalContentsTabs();
        $this->set('tabs',$gc_tabs);
    }
    
    public function getContentDetails() {
        $cID = $_POST['id'];
        $gc_content = $this->gcm->getContentDetails($cID);
        $htmlContent = $this->formatContentDisplay($gc_content);
        echo $htmlContent;
        exit;
    }
    
    public function saveGlobalContent($id) {
        $content = $_POST['content'];
        $flag = $this->gcm->updateGlobalContent($id,$content);
        echo json_encode($flag);
        exit;
    }
    
    public function formatContentDisplay($content) {
        $v = View::getInstance();
        $html = '<form class="form-stacked inline-form-fix global-content-form" action="' . $v->url('/dashboard/global_tab_management/saveGlobalContent/' . $content['ID']) . '" method="POST">';
        $html .= '<br />';
        $html .= '<br />';
        $html .= '<label>Content Data</label>';
        $html .= '<textarea class="ccm-advanced-editor" id="global-content-tetxtarea" rows="7" resize="false" name="content[ContentData]">';
        $html .= $content['ContentData'];
        $html .= '</textarea>';
        $html .= '<br />';
        $html .= '<br />';
        $html .= '<button class="btn btn-success pull-right" id="submit-global-content">Submit</button>';
        $html .= '</form>';
        return $html;
    }
}
