<?php
/**
 * Handles auxiliary functions needed for the Edit Go Content page.
 * Handles display of data, etc.
 *
 * @author paulbalila
 */
class EditGoContentHelper {
    private $v;
    private $wform;
    public function __construct() {
        $this->v = View::getInstance();
        $this->wform = Loader::helper('wform', 'cup_content');
    }
    
    public function displayParentFolders($data,$selectID='') {
        $html = "";
        if($data) {
            foreach ($data as $d) {
                $html .= "<tr>";
                if($d['ID'] == $selectID) {
                    $html .= "<td class='parentFolders-select selected' id='" . $d['ID'] . "' url='" . $this->v->url('/dashboard/cup_content/titles/getSubfolders') . "'><span>" . trim($d['FolderName']) . "</span>"
                            . "<a class='pull-right go-archiver' id='parentFolders-archive' style='display:none;' title='Archive' url='" . $this->v->url('/dashboard/cup_content/titles/archiveHeading/parentFolders') . "'>Archive</a></td>";
                } else {
                    $html .= "<td class='parentFolders-select' id='" . $d['ID'] . "' url='" . $this->v->url('/dashboard/cup_content/titles/getSubfolders') . "'><span>" . trim($d['FolderName']) . "</span>"
                            . "<a class='pull-right go-archiver' id='parentFolders-archive' style='display:none;' title='Archive' url='" . $this->v->url('/dashboard/cup_content/titles/archiveHeading/parentFolders') . "'>Archive</a></td>";
                }
                
                $html .= "</tr>";
            }
        } else {
            $html = $this->showEmptyTable();
        }
        return $html;
    }
    
    public function displaySubfolders($data,$selectID = '') {
        $html = "";
        if($data) {
            foreach ($data as $d) {
                $title = ($d['CMS_Name']) ? trim($d['ContentHeading']) . " [" . $d['CMS_Name'] . "]" : trim($d['ContentHeading']);

                $html .= "<tr>";
                if($d['ID'] == $selectID) {
                    $html .= "<td class='subFolders-select selected' id='" . $d['ID'] . "' url='" . $this->v->url('/dashboard/cup_content/titles/getContentInfo') . "'><span>" . $title . "</span>"
                            . "<a class='pull-right go-archiver' id='subFolders-archive' style='display:none;' title='Archive' url='" . $this->v->url('/dashboard/cup_content/titles/archiveHeading/subFolders') . "'>Archive</a></td>";
                } else {
                    $html .= "<td class='subFolders-select' id='" . $d['ID'] . "' url='" . $this->v->url('/dashboard/cup_content/titles/getContentInfo') . "'><span>" . $title . "</span>"
                            . "<a class='pull-right go-archiver' id='subFolders-archive' style='display:none;' title='Archive' url='" . $this->v->url('/dashboard/cup_content/titles/archiveHeading/subFolders') . "'>Archive</a></td>";
                }
                $html .= "</tr>";
            }
        } else {
            $html = $this->showEmptyTable();
        }
        return $html;
    }
    
    public function displayContentDetails($data,$tableID = '') {
        $html = '';
        if($data) {
            foreach ($data as $nt) {
                $html .= "<tr>";
                if($nt['ID'] == $tableID) {
                    $html .= "<td class='content-heading-select selected' id='" . $nt['ID'] . "' url='" . $this->v->url('/dashboard/cup_content/titles/getContentDetailInfo/') . "' type='" . $nt['TypeID'] . "'>" . $this->formatInactiveContentDetail($nt["Public_Name"], $nt["Active"])
                            . "<input type='hidden' name='sorting[" . $nt['ID'] . "]' value='" . $nt['SortOrder'] . "' class='content-detail-select-hidden' />"
                            . "<a class='pull-right go-archiver' id='contentDetails-archive' style='display:none;' title='Archive' url='" . $this->v->url('/dashboard/cup_content/titles/archiveHeading/contentDetails') . "'>Archive</a>"
                            . "</td>";
                } else {
                    $html .= "<td class='content-heading-select' id='" . $nt['ID'] . "' url='" . $this->v->url('/dashboard/cup_content/titles/getContentDetailInfo/') . "' type='" . $nt['TypeID'] . "'>" . $this->formatInactiveContentDetail($nt["Public_Name"], $nt["Active"])
                            . "<input type='hidden' name='sorting[" . $nt['ID'] . "]' value='" . $nt['SortOrder'] . "' class='content-detail-select-hidden' />"
                            . "<a class='pull-right go-archiver' id='contentDetails-archive' style='display:none;' title='Archive' url='" . $this->v->url('/dashboard/cup_content/titles/archiveHeading/contentDetails') . "'>Archive</a>"
                            . "</td>";
                }
                
                $html .= "</tr>";
            }
        } else {
            $html = $this->showEmptyTable();
        }
        return $html;
    }
    
    public function displayTabs($data, $tabID = '') {
        $html = "";
        if($data) {
            foreach ($data as $d) {
                $html .= "<tr>";
                if($d['ID'] == $tabID) {
                    $html .= "<td class='tab-select selected' id='" . $d['ID'] . "' url='" . $this->v->url('/dashboard/cup_content/titles/getTabDetails') . "'><span>" . trim($d["TabName"]) . "</span>"
                            . "<input type='hidden' name='sorting[" . $d['ID'] . "]' value='" . $d['SortOrder'] . "' class='tab-select-hidden' />"
                            . "<a class='pull-right go-archiver' id='tabs-archive' style='display:none;' title='Archive' url='" . $this->v->url('/dashboard/cup_content/titles/archiveHeading/tabs') . "'>Archive</a>"
                            . "</td>";
                } else {
                    $html .= "<td class='tab-select' id='" . $d['ID'] . "' url='" . $this->v->url('/dashboard/cup_content/titles/getTabDetails') . "'><span>" . trim($d["TabName"]) . "</span>"
                            . "<input type='hidden' name='sorting[" . $d['ID'] . "]' value='" . $d['SortOrder'] . "' class='tab-select-hidden' />"
                            . "<a class='pull-right go-archiver' id='tabs-archive' style='display:none;' title='Archive' url='" . $this->v->url('/dashboard/cup_content/titles/archiveHeading/tabs') . "'>Archive</a>"
                            . "</td>";
                }
                $html .= "</tr>";
            }
        } else {
            $html = $this->showEmptyTable();
        }
        return $html;
    }
    
    public function getLinkContent($data) {
        $elementsArr = array(
            'Public_Name' => array('label' => 'Public Name','type' => 'text', 'value' => $data['Public_Name']),
            'URL' => array('label' => 'URL','type' => 'text', 'value' => $data['URL']),
            'WindowBehaviour' => array('label' => 'Window Behaviour (opens in)', 'type' => 'select', 'value' => $data['WindowBehaviour'],'options' => array('New' => 'New','Current' => 'Current')),
            'Public_Description' => array('label' => 'Public Description','type' => 'textarea', value => $data['Public Description'],'prop' => array('class' => 'form-control'))
        );
        return $this->buildForm($elementsArr); 
    }
    
    public function getHTMLInfo($data) {
        $elementsArr = array(
            'Public_Name' => array('label' => 'Public Name','type' => 'text', 'value' => $data['Public_Name']),
            'Public_Description' => array('label' => 'Public Description','type' => 'textarea', 'value' => $data['Public_Description'],'prop' => array('class' => 'form-control')),
            'WindowWidth' => array('label' => 'Window Width','type' => 'text', 'value' => $data['WindowWidth']),
            'WindowHeight' => array('label' => 'Window Height','type' => 'text', 'value' => $data['WindowHeight']),
            'HTML_Content' => array('label' => 'HTML Content','type' => 'textarea', value => $data['HTML_Content'],'prop' => array('class' => 'ccm-advanced-editor'))
        );
        return $this->buildForm($elementsArr);
    }
    
    public function getFileInfo($data) {
        $elementsArr = array(
            'Public_Name' => array('label' => 'Public Name','type' => 'text', 'value' => $data['Public_Name']),
            'FileInfo' => array('label' => 'File Info','type' => 'text', 'value' => $data['FileInfo']),
            'Public_Description' => array('label' => 'Public Description','type' => 'textarea', 'value' => $data['Public_Description'],'prop' => array('class' => 'form-control')),
            'CatalogueFile' => array('label' => 'Catalogue','type' => 'select', 'value' => $data['CatalogueFile'], 'options' => array('Y' => 'Yes', 'N' => 'No')),
        );
        return $this->buildForm($elementsArr);
    }
    
    public function getTabInfo($data,$titleID = FALSE) {
        if ($data) {
            $infoArray = array(
                'TabName' => array('label' => 'Tab Name', 'type' => 'text', 'value' => $data['TabName']),
                'ID' => array('label' => 'Tab Name', 'type' => 'hidden', 'value' => $data['ID']),
                'TitleID' => array('label' => 'Title ID', 'type' => 'hidden', 'value' => $titleID),
                'Columns' => array('label' => 'Columns', 'type' => 'select', 'value' => $data['Columns'], 'options' => array('1' => '1', '2' => '2')),
            );

            $optionsArray = array(
                'MyResourcesLink' => array('label' => 'My Resources Link', 'type' => 'radio', 'value' => $data['MyResourcesLink'], 'options' => array('Y' => 'Yes', 'N' => 'No')),
                'ResourceURL' => array('label' => 'Resource URL', 'type' => 'text', 'value' => $data['ResourceURL'])
            );

            $accessArray = array(
                'Active' => array('label' => 'Active', 'type' => 'radio', 'value' => $data['Active'], 'options' => array('Y' => 'Yes', 'N' => 'No')),
                'Visibility' => array('label' => 'Visibility', 'type' => 'radio', 'value' => $data['Visibility'], 'options' => array('Public' => 'Public', 'Private' => 'Private')),
                'ContentVisibility' => array('label' => 'Content Access', 'type' => 'radio', 'value' => $data['ContentVisibility'], 'options' => array('open' => 'Open', 'closed' => 'Closed')),
                'UserTypeIDRestriction' => array('label' => 'Access to tab', 'type' => 'wform', 'values' => $data['UserTypeIDRestriction']),
                // 'ContentType' => array('label' => 'Content Type', 'type' => 'wform', 'values' => $data['ContentType']),
                'FreeAccess' => array('label' => 'Free Access', 'type' => 'radio', 'value' => $data['FreeAccess'], 'options' => array('student' => 'Student', 'teacher' => 'Teacher', 'null' => 'None')),
                'CustomAccessMessage' => array('label' => 'Custom Message', 'type' => 'textarea', 'value' => $data['CustomAccessMessage']),
                
            );

            $textArray = array(
                'AlwaysUsePublicText' => array('label' => 'Always use public text', 'type' => 'radio', 'value' => $data['AlwaysUsePublicText'], 'options' => array('Y' => 'Yes', 'N' => 'No')),
                'Public_TabText' => array('label' => 'Public tab text', 'type' => 'textarea', 'value' => $data['Public_TabText'], 'prop' => array('class' => 'ccm-advanced-editor')),
                'Private_TabText' => array('label' => 'Private tab text', 'type' => 'textarea', 'value' => $data['Private_TabText'], 'prop' => array('class' => 'ccm-advanced-editor'))
            );

            $contents = array(
                'Information' => $this->wrapContents('Information', $this->buildForm($infoArray)),
                'Options' => $this->wrapContents('Options', $this->buildForm($optionsArray)),
                'AccessRights' => $this->wrapContents('Access Rights', $this->buildForm($accessArray)),
                'Text' => $this->wrapContents('Text', $this->buildForm($textArray))
            );

            return $contents;
        } else {
            return FALSE;
        }
    }
    
    
    
    public function buildFileUploadForm($data) {
        $form = Loader::helper('form');
        $v = View::getInstance();
        
        $uploadElementArr = array(
            'files' => array('label' => 'Files','type' => 'file', 'value' => ''),
            'ContentID' => array('label' => 'Content ID','type' => 'hidden', 'value' => $data['ContentID']),
            'ID' => array('label' => 'Content Detail ID','type' => 'hidden', 'value' => $data['ID']),
            'Upload' => array('label' => 'Upload','type' => 'submit', 'value' => 'Upload')
        );
        
        // Compute file size to human readable form.
        $sizeToKB = $data['FileSize'] / 1024;
        $sizeToMB = $data['FileSize'] / 1048576;
        $fileDetailsArr = array(
            'File Name' => $data['FileName'],
            'File Path' => $data['FilePath'],
            'File Size' => ($sizeToMB < 1) ? round($sizeToKB,2,PHP_ROUND_HALF_UP) . ' KB' : round($sizeToMB,2,PHP_ROUND_HALF_UP) . ' MB',
            'File Upload Date' => $data['FileUploadDate']
        );
        
        $html = "<form class='fileuploadmulti form-inline' id='single-file-upload' action='" . $v->url("/dashboard/cup_content/titles/handleFileUploads/1") . "' method='POST' enctype='multipart/form-data'>";
        $html .= "<div class='form-group'>";
        $html .= $this->buildForm($uploadElementArr);
        $html .= "</div>";
        $html .= "</form>";
        $html .= "</fieldset>";
        
        $html .= "<br /><table class='table table-bordered'>";
        $html .= "<tbody>";
        foreach ($fileDetailsArr as $fKey => $fValue) {
            $html .= "<tr>";
            $html .= "<th>" . $fKey . "</th>";
            $html .= "<td>" . $fValue . "</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        
        return $html;
    }
    
    
    
    private function buildForm($elements,$isHidden = FALSE) {
        $form = Loader::helper('form');
        foreach ($elements as $eKey => $eValue) {
            switch ($eValue['type']) {
                case 'select':
                    $html .= "<div class='form-group'>";
                    $html .= $form->label($eKey,$eValue['label']);
                    $html .= $form->select($eKey,$eValue['options'],$eValue['value'],array('class' => 'form-control'));
                    $html .= "</div>";
                    break;
                case 'textarea':
                    $value = urldecode($eValue['value']);
                    $html .= "<div class='form-group'>";
                    $html .= $form->label($eKey,$eValue['label']);
                    $html .= $form->textarea($eKey,$value,$eValue['prop']);
                    $html .= "</div>";
                    break;
                case 'file':
                    $temp = $form->file($eKey . '[]');
                    $html .= str_replace("ccm-input-file", "form-control", $temp);
                    break;
                case 'hidden':
                    $html .= $form->hidden($eKey,$eValue['value']);
                    break;
                case 'submit':
                    $temp = $form->submit($eKey,$eValue['value']);
                    $html .= "&nbsp;&nbsp;" . str_replace("ccm-input-submit", "btn-primary", $temp);
                    break;
                case 'button':
                    $temp = $form->button($eKey,$eValue['value']);
                    $html .= "&nbsp;&nbsp;" . str_replace("ccm-input-button", "btn-primary", $temp);
                    break;
                case 'radio':
                    $html .= "<div class='form-group'>";
                    $html .= $form->label($eKey,$eValue['label']);

                    if($eKey == 'FreeAccess' && is_null($eValue['value'])) {
                        $eValue['value'] = 'null';
                    }

                    foreach ($eValue['options'] as $oKey => $oVal) {
                        $html .= '<span class="ccm-radio-container">';
                            $html .= $form->radio($eKey,$oKey,$eValue['value']) . ' ' . $oVal;
                            $html .= '</span>';
                    }
                    $html .= "</div>";
                    break;
                case 'wform':
                    $html .= "<div class='form-group'>";
                    $html .= $form->label($eKey,$eValue['label']);
                    if($eKey == 'UserTypeIDRestriction') {
                        $html .= $this->wform->userTypeRestrictionGroups($eValue['values'],'UserTypeIDRestriction',TRUE);
                    } else {
                        $html .= $this->wform->contentType($eValue['values'],'ContentType',TRUE);
                    }
                    $html .= "</div>";
                    break;
                default:
                    $html .= "<div class='form-group'>";
                    $html .= $form->label($eKey,$eValue['label']);
                    
                    if($eKey == "URL") {
                        $eValue['value'] = urldecode($eValue['value']);
                    }
                    
                    if(isset($eValue['prop'])) {
                        $html .= $form->text($eKey,$eValue['value'],$eValue['prop']);
                    } else {
                        $html .= $form->text($eKey,$eValue['value'],array('class' => 'form-control'));
                    }
                    
                    $html .= "</div>";
                    break;
            }
        }
        return $html;
    }
    
    private function wrapContents($panelTitle,$contents) {
        $html = "<div class='panel panel-default'>";
        $html .= "<div class='panel-heading'><h3 class='panel-title'>" . $panelTitle . "</h3></div>";
        $html .= "<div class='panel-body'>" . $contents . "</div>";
        $html .= "</div>";
        return $html;
    }

    private function showEmptyTable() {
        $html = "<tr>";
        $html .= "<td id='empty-table'>Nothing found...</td>";
        $html .= "</tr>";
        return $html;
    }
    
    private function formatInactiveContentDetail($text,$status) {
        $newText = str_replace("_", " ", $text);
        if($status == "N") {
            $html = "<span style='color:red'>" . $newText . "</span>";
        } else {
            $html = "<span>" . $newText . "</span>";
        }
        return $html;
    }
}
