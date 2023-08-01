 <?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of title_tabs_helper
 *
 * @author paulbalila
 */
class CupContentTitleTabsHelper {

    private function formatHeading($data) {
        $title = $data['ContentHeading'];
        if($data['CMS_Name']) {
            $title .= ' (' . $data['CMS_Name'] . ')';
        }
        return trim($title);
    }
    
    /**
     * 
     * Modified by Paul Balila, 2016-04-06
     * 
     */
    public function formatGlobalContent($globalContents,$linkedGlobalContents) {
        $v = View::getInstance();
        $temp = array();
        foreach ($linkedGlobalContents as $l) {
            $temp[] = $l['ContentID'];
        }

        $html = '<table class="table table-bordered">';
        $html .= '<tbody style="cursor:pointer;">';
        foreach($globalContents as $r) {
            $title = $this->formatHeading($r);
            $prog_title = str_replace(",", "", str_replace(" ","_",$title));
            
            $ftitle = str_replace(",", "", str_replace(" ", "_", $ftitle));
            $ftitle = str_replace("(", "", str_replace(")", "", $ftitle));
            $html .= '<tr>';

            if(in_array($r['ID'], $temp)) {
                $html .= '<td><span>' . $title . '</span>'
                        . '<span href="' . $v->url('/dashboard/cup_content/titles','showAddedContent') . '" class="glyphicon glyphicon-plus pull-right add-content" id="' . $ftitle . '" style="display:none;" title="Add content"></span>'
                        . '<input type="hidden" value="' . $r['ID'] . '" />'
                        . '</td>';
            } else {
                $html .= '<td><span>' . $title . '</span>'
                        . '<span href="' . $v->url('/dashboard/cup_content/titles','showAddedContent') . '" class="glyphicon glyphicon-plus pull-right add-content" id="' . $ftitle . '" title="Add content"></span>'
                        . '<input type="hidden" value="' . $r['ID'] . '" />'
                        . '</td>';
            }
                        
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        return $html;
    }
    
    public function formatAddedContent($title,$tabs, $class) {
        $v = View::getInstance();
        $html = '<tr>';
        $html .= '<td>Nothing found...</td>';
        $html .= '</tr>';

        if($tabs) {
            foreach ($tabs as $tab) {
                $html = '<tr id="' . $title . '" content-id="' . $tab['ID'] . '">';
         
                $html .= '<input type="hidden" class="tab-content-details" id="tabContent-' . $ftitle . '" value="' . $tab['ID'] . '" />';
                $html .= '<input type="hidden" class="tab-content-details" id="update-url" value="' . $v->url('/dashboard/cup_content/titles','updateTabContent') . '" />';
                $html .= '<td>' . $title . '';
                
                if($class == "global") {
                    $html .= "<input type='hidden' name='sorting[" . $tab['ID'] . "]' value='" . $tab['SortOrder'] . "' class='tab-content-hidden-g' />";
                } else {
                    $html .= "<input type='hidden' name='sorting[" . $tab['ID'] . "]' value='" . $tab['SortOrder'] . "' class='tab-content-hidden-l' />";
                }
                
                $html .= '</td>';
                $html .= '<td>'
                        . '<select class="tab-content-updater" column="ColumnNumber" style="z-index:100;">'
                        . '<option>---</option>'
                        . '<option value="1">1</option>'
                        . '<option value="2">2</option>'
                        . '</select>'
                        . '</td>';
                $html .= '<td>'
                        . '<select class="tab-content-updater" column="Active" style="z-index:100;">'
                        . '<option>---</option>'
                        . '<option value="Y" selected>Yes</option>'
                        . '<option value="N">No</option>'
                        . '</select>'
                        . '</td>';
                $html .= '<td>'
                        . '<select class="tab-content-updater" column="Visibility" style="z-index:100;">'
                        . '<option>---</option>'
                        . '<option value="Public" selected>Public</option>'
                        . '<option value="Private">Private</option>'
                        . '</select>'
                        . '</td>';
                $html .= '<td>'
                        . '<select class="tab-content-updater" column="DemoOnly" style="z-index:100;">'
                        . '<option>---</option>'
                        . '<option value="Y">Yes</option>'
                        . '<option value="N" selected>No</option>'
                        . '</select>'
                        . '</td>';
                $html .= '<td><span href="' . $v->url('/dashboard/cup_content/titles','deleteTabContent') . '" class="glyphicon glyphicon-trash remove-content" title-content="' . $tab['ContentID'] . '" style="cursor:pointer;"></span></td>';
                $html .= '</tr>';
            }
        }        
        return $html;
    }
    
    public function formatLinkedGlobalContents($result,$type) {
        $v = View::getInstance();
        $html = '';
        if($result) {
            foreach ($result as $r) {
                $title = $this->formatHeading($r);
                
                $html .= '<tr id="' . $title . '" content-id="' . $r['ID'] . '">';
                $html .= '<input type="hidden" class="tab-content-details" id="tabContent-' . $title . '" value="' . $r['ID'] . '" />';
                $html .= '<input type="hidden" class="tab-content-details" id="update-url" value="' . $v->url('/dashboard/cup_content/titles', 'updateTabContent') . '" />';
                $html .= '<td>' . $title . '';
                
                if($type == 'global') {
                    $html .= "<input type='hidden' name='sorting[" . $r['ID'] . "]' value='" . $r['SortOrder'] . "' class='tab-content-hidden-g' />";
                } else {
                    $html .= "<input type='hidden' name='sorting[" . $r['ID'] . "]' value='" . $r['SortOrder'] . "' class='tab-content-hidden-l' />";
                }
                        
                $html .= '</td>';
                
                // Select value for Column Number
                $html .= '<td>'
                . '<select class="tab-content-updater" column="ColumnNumber">'
                . '<option>---</option>';
                if($r['ColumnNumber'] == 1) {
                    $html .= '<option value="1" selected>1</option>';
                } else {
                    $html .= '<option value="1">1</option>';
                }
                
                if($r['ColumnNumber'] == 2) {
                    $html .= '<option value="2" selected>2</option>';
                } else {
                    $html .= '<option value="2">2</option>';
                }
                $html .= '</select>'
                . '</td>';

                // Select value for Active column
                $html .= '<td>'
                        . '<select class="tab-content-updater" column="Active">';
                $html .= '<option>---</option>';
                if ($r['Active'] == 'Y') {
                    $html .= '<option value="Y" selected>Yes</option>';
                } else {
                    $html .= '<option value="Y">Yes</option>';
                }
                if ($r['Active'] == 'N') {
                    $html .= '<option value="N" selected>No</option>';
                } else {
                    $html .= '<option value="N">No</option>';
                }
                $html .= '</select>'
                        . '</td>';

                // Select value for Visibility column
                $html .= '<td>'
                        . '<select class="tab-content-updater" column="Visibility">'
                        . '<option>---</option>';
                if ($r['Visibility'] == 'Public') {
                    $html .= '<option value="Public" selected>Public</option>';
                } else {
                    $html .= '<option value="Public">Public</option>';
                }
                if ($r['Visibility'] == 'Private') {
                    $html .= '<option value="Private" selected>Private</option>';
                } else {
                    $html .= '<option value="Private">Private</option>';
                }
                $html .= '</select>'
                        . '</td>';

                // Select value for DemoOnly column
                $html .= '<td>'
                        . '<select class="tab-content-updater" column="DemoOnly">'
                        . '<option>---</option>';
                if ($r['DemoOnly'] == 'Y') {
                    $html .= '<option value="Y" selected>Yes</option>';
                } else {
                    $html .= '<option value="Y">Yes</option>';
                }
                if ($r['DemoOnly'] == "N") {
                    $html .= '<option value="N" selected>No</option>';
                } else {
                    $html .= '<option value="N">No</option>';
                }
                $html .= '</select>'
                        . '</td>';
                $html .= '<td><span href="' . $v->url('/dashboard/cup_content/titles', 'deleteTabContent') . '" class="glyphicon glyphicon-trash remove-content" title-content="' . $r['ContentID'] . '" style="cursor:pointer;"></span></td>';

                $html .= '</tr>';
            }
        } else {
            $html = "<tr>";
            $html .= "<td id='empty-table'>Nothing found...</td>";
            $html .= "</tr>";
        }
        return $html;
    }
    
    public function formatTabFolders($result) {
        $v = View::getInstance();
        $html = '';
        foreach ($result as $r) {
            $html .= '<a class="btn btn-default tab-folders" href="' . $v->url('/dashboard/cup_content/titles', 'getTabFolderContents') . '" folder-id="' . $r['ID'] . '">' . ucwords($r['FolderName']) . '</a>&nbsp;';
        }
        return $html;
    }
    
    public function formatFolderContent($result,$currentContentIDs) {
        $v = View::getInstance();
        $html = '<table class="table table-bordered">';
        $html .= '<tbody style="cursor:pointer;">';
        if($result) {
            foreach ($result as $r) {
                $title = $this->formatHeading($r);
                $html .= '<tr>';
                if (in_array($r['ID'], $currentContentIDs)) {
                    $html .= '<td><span>' . $title . '</span>'
                            . '<span href="' . $v->url('/dashboard/cup_content/titles', 'showAddedContent') . '" class="glyphicon glyphicon-plus pull-right add-local-content" id="' . $r['ID'] . '" style="display:none;"></span>'
                            . '<input type="hidden" value="' . $r['ID'] . '" />'
                            . '</td>';
                } else {
                    $html .= '<td><span>' . $title . '</span>'
                            . '<span href="' . $v->url('/dashboard/cup_content/titles', 'showAddedContent') . '" class="glyphicon glyphicon-plus pull-right add-local-content" id="' . $r['ID'] . '"></span>'
                            . '<input type="hidden" value="' . $r['ID'] . '" />'
                            . '</td>';
                }
                $html .= '</tr>';
            }
        } else {
            $html .= "<tr>";
            $html .= "<td colspan='2'>Nothing found...</td>";
            $html .= "</tr>";
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        return $html;
    }
}
