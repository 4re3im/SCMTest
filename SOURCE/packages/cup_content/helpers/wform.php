<?php 
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Helpful functions for working with forms. Includes HTML input tags and the like
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('title/model', 'cup_content');
class WFormHelper
{
    protected $form = false;

    public function __construct()
    {
        $this->form = Loader::helper('form');
    }

    public function multipleItems($fieldname, $valueOrArray = array(), $button = array())
    {
        if ($valueOrArray == null || $valueOrArray === '' || $valueOrArray === false) {
            $valueOrArray = array();
        }

        if (is_string($valueOrArray)) {
            if (strlen($valueOrArray) > 0) {
                $valueOrArray = array($valueOrArray);
            } else {
                $valueOrArray = array();
            }
        }
        
        $fieldText = '';
        $fieldText .= '<div class="multiple-items-group" ref="' . $fieldname . '">';
        
        $fieldnameItem = $fieldname . '[]';
        
        foreach ($valueOrArray as $eachValue) {
            $hiddenField = '<input type="hidden" name="' . $fieldnameItem . '" value="' . $eachValue . '"/>';
            $valueItem = "<span class=\"value_item\" style='cursor:pointer;'>{$eachValue}{$hiddenField}</span>";
            
            $fieldText .= $valueItem;
        }
        
        if (count($valueOrArray) < 1) {
            $fieldText .= '<i style="empty_value_message">empty value</i>';
        }
        
        $fieldText .= '</div>';
        
        if ($button !== false) {
            $buttonDefault = array(
                'href' => 'javascript: void(0);',
                'text' => 'Edit', 
            );
            $button = array_merge($buttonDefault, $button);
            $fieldText .= '<div class="h_5 w_5"></div>';
            $fieldText .= '<div style="margin-left:15px">';
            $fieldText .= $this->button(
                $button['text'],
                $button['href'],
                array(
                    'class' => 'wform-button-blue',
                    'ref'   => $fieldname
                ),
                true
            );
            $fieldText .= '</div>';
        }
        
        return $fieldText;
    }
    
    public function singleItem($fieldname, $valueOrArray = array(), $button = array())
    {
        if ($valueOrArray == null || $valueOrArray === '' || $valueOrArray === false) {
            $valueOrArray = array();
        }

        if (is_string($valueOrArray)) {
            if (strlen($valueOrArray) > 0) {
                $valueOrArray = array($valueOrArray);
            } else {
                $valueOrArray = array();
            }
        }
        
        $isAssoc = false;
        $isAssoc = array_keys($valueOrArray) !== range(0, count($valueOrArray) - 1);
        
        $fieldText = '';
        $fieldText .= '<div class="multiple-items-group" ref="' . $fieldname . '">';
        
        $fieldnameItem = $fieldname;
        
        foreach ($valueOrArray as $key => $eachValue) {
            $itemValue = $eachValue;
            $displayValue = $eachValue;

            if ($isAssoc) {
                $itemValue = $key;
            }
            
            $hiddenField = '<input type="hidden" name="' . $fieldnameItem . '" value="' . $itemValue . '"/>';
            $valueItem = "<span class=\"value_item\" style='cursor:pointer;'>{$displayValue}{$hiddenField}</span>";
            
            $fieldText .= $valueItem;
        }
        
        if (count($valueOrArray) < 1) {
            $fieldText .= '<i style="empty_value_message">empty value</i>';
        }
        
        $fieldText .= '</div>';
        
        if ($button !== false) {
            $buttonDefault = array(
                'href' => 'javascript: void(0);',
                'text' => 'Edit', 
            );
            $button = array_merge($buttonDefault, $button);
            $fieldText .= '<div class="h_5 w_5"></div>';
            $fieldText .= '<div style="margin-left:15px">';
            $fieldText .= $this->button(
                $button['text'],
                $button['href'],
                array(
                    'class' => 'wform-button-blue',
                    'ref'   => $fieldname
                ),
                true
            );
            $fieldText .= '</div>';
        }
        
        return $fieldText;
    }
    
    public function multipleTitleItems($fieldname, $valueOrArray = array(), $button = array())
    {
        if ($valueOrArray == null || $valueOrArray === '' || $valueOrArray === false) {
            $valueOrArray = array();
        }

        if (is_string($valueOrArray)) {
            if (strlen($valueOrArray) > 0) {
                $valueOrArray = array($valueOrArray);
            } else {
                $valueOrArray = array();
            }
        }
        
        $fieldText = '';
        $fieldText .= '<div class="multiple-items-group" ref="' . $fieldname . '">';
        
        $fieldnameItem = $fieldname . '[]';
        
        $isEmpty = true;
        foreach ($valueOrArray as $eachValue) {
            $titleObj = CupContentTitle::fetchByID($eachValue);
            
            if ($titleObj) {
                $valueContent = $titleObj->generateProductName();
                if (strlen($titleObj->series) > 0) {
                    $valueContent .= ' (Series: ' . $titleObj->series . ')';
                }
                
                $hiddenField = '<input type="hidden" name="' . $fieldnameItem . '" value="' . $eachValue . '"/>';
                $valueItem = "<span class=\"value_item\" style='cursor:pointer;'>{$valueContent}{$hiddenField}</span>";
                
                $fieldText .= $valueItem;
                $isEmpty = false;
            }
        }
        
        if ($isEmpty) {
            $fieldText .= '<i style="empty_value_message">empty value</i>';
        }
        
        $fieldText .= '</div>';
        
        if ($button !== false) {
            $buttonDefault = array(
                'href' => 'javascript: void(0);',
                'text' => 'Edit', 
            );
            $button = array_merge($buttonDefault, $button);
            $fieldText .= '<div class="h_5 w_5"></div>';
            $fieldText .= '<div style="margin-left:15px">';
            $fieldText .= $this->button(
                $button['text'],
                $button['href'],
                array(
                    'class' => 'wform-button-blue',
                    'ref'   => $fieldname
                ),
                true
            );
            $fieldText .= '</div>';
        }
        
        return $fieldText;
    }
    
    public function button($text, $link = '#', $attributes = array(), $buttonColor = false)
    {
        // SB-354 modified by machua/gbalila 20190927 revert old codes to render correct attributes
        $defaultAttributes = array(
            'href' => $link,
            'class' => 'wform-button'
        );
        
        foreach ($attributes as $key => $value) {
            if (array_key_exists($key, $defaultAttributes)) {
                $defaultAttributes[$key] .= " $value";
            } else {
                $defaultAttributes[$key] = $value;
            }
        }
        
        if ($buttonColor === 'blue') {
            $defaultAttributes['class'] .= ' wform-button-blue';
        }
        
        $attrText = array();
        foreach ($defaultAttributes as $key => $value) {
            $attrText[] = $key . '="' . str_replace('"', '\"', $value) . '"';
        }
        
        if (count($attrText) > 0) {
            $attrText = ' ' . implode(' ', $attrText);
        } else {
            $attrText = '';
        }
        $fieldText = '<a' . $attrText . '><span>' . $text . '</span></a>';
        
        return $fieldText;
    }
    
    public function divionsSelection($selectedValues = array(), $fieldname = 'divisions')
    {
        $fieldname .= '[]';
        
        $selectionValue = array('Primary' => false, 'Secondary' => false);
        if (is_array($selectedValues) && count($selectedValues) > 0) {
            if (in_array('Primary', $selectedValues)) {
                $selectionValue['Primary'] = true;
            }
            
            if (in_array('Secondary', $selectedValues)) {
                $selectionValue['Secondary'] = true;
            }
        }
        
        $fieldHtml = '';
        foreach ($selectionValue as $key => $isSelected) {
            $fieldHtml .= "<span class=\"value_item\">" . $this->form->checkbox($fieldname, $key, $isSelected) . "{$key}</span>";
        }
        
        $fieldHtml = '<div class="wcheckbox-group">' . $fieldHtml . '</div>';
        return $fieldHtml;
    }
    
    public function regionsSelection($selectedValues = array(), $fieldname = 'regions')
    {
        $fieldname .= '[]';
        
        $selectionValue = array(
            'Australia' => array(
                'Australian Capital Territory'  => false,
                'New South Wales'               => false, 
                'Northern Territory'            => false, 
                'Queensland'                    => false, 
                'South Australia'               => false, 
                'Tasmania'                      => false, 
                'Victoria'                      => false, 
                'Western Australia'             => false,
            ),
            'New Zealand'                       => false,
            // SB-557 added by mabrigos 20200514
            'South Africa' => array(
                'CAPS'                          => false
            ),
            'Nigeria' => array(
                'NERDC'                         => false
            ),
            'Ghana' => array(
                'NaCCA'                         => false
            ),
            'Cameroon' => array(
                'General Secondary Education'   => false
            ),
            'Namibia' => array(
                'NamCol'   => false,
                'NIED'   => false
            ),
            'India' => array(
                'CBSE'   => false,
                'ICSE'   => false
            ),
            // SB-345 added by jbernardez 20190923
            // SB-398 modified by jbernardez 20191112
            // SB-421 modified by mabrigos 20191212
            'Other' => array(
                'AQA'                                           => false,
                'Cambridge Assessment International Education'  => false,
                'Edexcel'                                       => false,
                'International Baccalaureate'                   => false,
                'OCR'                                           => false,
                'WJEC/Eduqas'                                   => false,
                'UK'                                            => false,
                'Caribbean Examinations Council'                => false,
            ),
        );
                                
        if (is_array($selectedValues) && count($selectedValues) > 0) {
            if (in_array('New South Wales', $selectedValues)) {
                $selectionValue['Australia']['New South Wales'] = true;
            }
            
            if (in_array('Northern Territory', $selectedValues)) {
                $selectionValue['Australia']['Northern Territory'] = true;
            }
            
            if (in_array('Queensland', $selectedValues)) {
                $selectionValue['Australia']['Queensland'] = true;
            }
            
            if (in_array('South Australia', $selectedValues)) {
                $selectionValue['Australia']['South Australia'] = true;
            }
            
            if (in_array('Tasmania', $selectedValues)) {
                $selectionValue['Australia']['Tasmania'] = true;
            }
            
            if (in_array('Western Australia', $selectedValues)) {
                $selectionValue['Australia']['Western Australia'] = true;
            }
            
            if (in_array('Victoria', $selectedValues)) {
                $selectionValue['Australia']['Victoria'] = true;
            }
            
            if (in_array('Australian Capital Territory', $selectedValues)) {
                $selectionValue['Australia']['Australian Capital Territory'] = true;
            }
            
            if (in_array('New Zealand', $selectedValues)) {
                $selectionValue['New Zealand'] = true;
            }

            // SB-345 added by jbernardez 20190923
            // SB-398 modified by jbernardez 20191112
            // SB-421 modified by mabrigos 20191212
            if (in_array('AQA', $selectedValues)) {
                $selectionValue['Other']['AQA'] = true;
            }

            if (in_array('Cambridge Assessment International Education', $selectedValues)) {
                $selectionValue['Other']['Cambridge Assessment International Education'] = true;
            }

            if (in_array('Edexcel', $selectedValues)) {
                $selectionValue['Other']['Edexcel'] = true;
            }

            if (in_array('International Baccalaureate', $selectedValues)) {
                $selectionValue['Other']['International Baccalaureate'] = true;
            }

            if (in_array('OCR', $selectedValues)) {
                $selectionValue['Other']['OCR'] = true;
            }

            if (in_array('WJEC/Eduqas', $selectedValues)) {
                $selectionValue['Other']['WJEC/Eduqas'] = true;
            }

            if (in_array('UK', $selectedValues)) {
                $selectionValue['Other']['UK'] = true;
            }

            if (in_array('Caribbean Examinations Council', $selectedValues)) {
                $selectionValue['Other']['Caribbean Examinations Council'] = true;
            }

            // SB-557 added by mabrigos 20200514
            if (in_array('CAPS', $selectedValues)) {
                $selectionValue['South Africa']['CAPS'] = true;
            }

            if (in_array('NERDC', $selectedValues)) {
                $selectionValue['Nigeria']['NERDC'] = true;
            }

            if (in_array('NaCCA', $selectedValues)) {
                $selectionValue['Ghana']['NaCCA'] = true;
            }

            if (in_array('General Secondary Education', $selectedValues)) {
                $selectionValue['Cameroon']['General Secondary Education'] = true;
            }

            if (in_array('NamCol', $selectedValues)) {
                $selectionValue['Namibia']['NamCol'] = true;
            }

            if (in_array('NIED', $selectedValues)) {
                $selectionValue['Namibia']['NIED'] = true;
            }

            if (in_array('CBSE', $selectedValues)) {
                $selectionValue['India']['CBSE'] = true;
            }

            if (in_array('ICSE', $selectedValues)) {
                $selectionValue['India']['ICSE'] = true;
            }
        }
        
        $fieldHtml = '';
        
        foreach ($selectionValue as $country => $cvalue) {
            $fieldHtml .= '<div class="country-group">';
            
            if (is_array($cvalue)) {
                $fieldHtml .= $country . ':';
                
                $fieldHtml .= '<div class="state-group">';
                foreach ($cvalue as $key => $isSelected) {
                    $fieldHtml .= '<span class="value_item">';
                    $fieldHtml .= $this->form->checkbox($fieldname, $key, $isSelected) . $key;
                    $fieldHtml .= '</span>';
                }
                $fieldHtml .= '</div>';
            } else {
                $fieldHtml .= '<span class="value_item">';
                $fieldHtml .= $country.':'; 
                    $fieldHtml .= '<div class="state-group">';
                        $fieldHtml .= '<span class="value_item">';
                        $fieldHtml .= $this->form->checkbox($fieldname, $country, $cvalue).'<i>All Regions</i>';
                        $fieldHtml .= '</span>';
                    $fieldHtml .= '</div>';
                $fieldHtml .= '</span>';
            }
            $fieldHtml .= '</div>';
        }
        
        $fieldHtml = '<div class="wregion-group">' . $fieldHtml . '</div>';
        return $fieldHtml;
    }
    
    public function yearlevelSelection($selectedValues = array(), $fieldname = 'yearLevels', $miscFields = array())
    {
        $fieldname .= '[]';
        
        $select_options = array(
            '11-12' => '11-12',
            '9-10'  => '9-10',
            '7-10'  => '7-10',
            '7-8'   => '7-8',
            '5-6'   => '5-6',
            '3-4'   => '3-4',
            '1-6'   => '1-6',
            'F-2'   => 'F-2',
            '12'    => '12',
            '11'    => '11',
            '10'    => '10',
            '9'     => '9',
            '8'     => '8',
            '7'     => '7',
            '6'     => '6',
            '5'     => '5',
            '4'     => '4',
            '3'     => '3',
            '2'     => '2',
            '1'     => '1',
            'F'     => 'F',
            // SB-398 modified by jbernardez 20191112
            // SB-421 modified by mabrigos 20191212
            'A Level'                                   => 'A Level',
            'Checkpoint'                                => 'Checkpoint',
            'International AS and A Level'              => 'International AS and A Level',
            'Primary'                                   => 'Primary',
            'GCSE'                                      => 'GCSE',
            'IB Diploma'                                => 'IB Diploma',
            'IGCSE'                                     => 'IGCSE',
            'Lower Secondary'                           => 'Lower Secondary',
            'O Level'                                   => 'O Level',
            'Pre&ndash;U'                               => 'Pre&ndash;U',
            'Primary Checkpoint'                        => 'Primary Checkpoint',
            'CAPE'                                      => 'CAPE',
            // SB-557 added by mabrigos 20200514
            'Primary 1-6'                               => 'Primary 1-6',
            'Junior Secondary 1-3'                      => 'Junior Secondary 1-3',
            'Senior Secondary 1-3'                      => 'Senior Secondary 1-3',
            'Primary 1'                                 => 'Primary 1',
            'Primary 2'                                 => 'Primary 2',
            'Primary 3'                                 => 'Primary 3',
            'Primary 4'                                 => 'Primary 4',
            'Primary 5'                                 => 'Primary 5',
            'Primary 6'                                 => 'Primary 6',
            'Junior Secondary 1'                        => 'Junior Secondary 1',
            'Junior Secondary 2'                        => 'Junior Secondary 2',
            'Junior Secondary 3'                        => 'Junior Secondary 3',
            'Senior Secondary 1'                        => 'Senior Secondary 1',
            'Senior Secondary 2'                        => 'Senior Secondary 2',
            'Senior Secondary 3'                        => 'Senior Secondary 3',
            'B1'                                        => 'B1',
            'B2'                                        => 'B2',
            'B3'                                        => 'B3',
            'B4'                                        => 'B4',
            'B5'                                        => 'B5',
            'B6'                                        => 'B6',
            '6ème'                                      => '6ème',
            '5ème'                                      => '5ème',
            '4ème'                                      => '4ème',
            '3ème'                                      => '3ème',
            '2nde'                                      => '2nde',
            '1ère'                                      => '1ère',
            'Terminale'                                 => 'Terminale',
            // SB-904 Added by Shane Camus 08/17/2021
            'Cambridge Nationals'                       => 'Cambridge Nationals'
        );
        
        $values = array(
            '11-12',
            '9-10',
            '7-10',
            '7-8',
            '5-6',
            '3-4',
            'F-6',
            'F-2',
            '12',
            '11',
            '10',
            '9',
            '8',
            '7',
            '6',
            '5',
            '4',
            '3',
            '2',
            '1',
            'F',
            // SB-398 modified by jbernardez 20191112
            'A Level',
            'Checkpoint',
            'International AS and A Level',
            'Primary',
            'GCSE',
            'Diploma',
            'IGCSE',
            'Lower Secondary',
            'O Level',
            'Pre&ndash;U',
            'Primary Checkpoint',
            'CAPE',
            // SB-557 added by mabrigos 20200514
            'Primary 1-6',
            'Junior Secondary 1-3',
            'Senior Secondary 1-3',
            'Primary 1',
            'Primary 2',
            'Primary 3',
            'Primary 4',
            'Primary 5',
            'Primary 6',
            'Junior Secondary 1',
            'Junior Secondary 2',
            'Junior Secondary 3',
            'Senior Secondary 1',
            'Senior Secondary 2',
            'Senior Secondary 3',
            'B1',
            'B2',
            'B3',
            'B4',
            'B5',
            'B6',
            '6ème',
            '5ème',
            '4ème',
            '3ème',
            '2nde',
            '1ère',
            'Terminale',
            'Cambridge Nationals'
        );
        
        $fieldHtml = '';
        $fieldHtml .= '<div class="wyearlevel-group">';
        foreach ($values as $key => $value) {
            // SB-398 modified by jbernardez 20191112
            if (in_array($key, array(8, 15, 21, 25, 31))) {
                $fieldHtml .= '</div><div class="wyearlevel-group">';
            }

            $fieldHtml .= '<span class="value_item">';
            $fieldHtml .= $this->form->checkbox($fieldname, $value, @in_array($value, $selectedValues)) . $value;
            $fieldHtml .= '</span>';
        }
        $fieldHtml .= '</div>';
        return $fieldHtml;
    }
    
    public function titleTypeSelection($selected_value, $fieldname = 'type')
    {
        $select_options = array(
            'part of series' => 'part of series',
            'stand alone' => 'stand alone',
            'study guide' => 'study guide'
        );
                    
        $id = $fieldname;
        if ((strpos($id, '[]') + 2) == strlen($id)) {
            $id = substr($key, 0, strpos($id, '[]'));
        }
        
        $str = '<select name="' . $fieldname . '" id="' . $id . '" >';

        foreach ($select_options as $k => $value) {
            $selected = '';
            
            if (strcmp($selected_value, $k) == 0) {
                $selected = 'selected="selected"';
            }
            $str .= '<option value="' . $k . '" ' . $selected . '>' . $value . '</option>';
        }
        $str .= '</select>';
        return $str;
    }
    
    public function titleDescriptionOptionSelection($selected_value, $fieldname = 'descriptionOption')
    {
        $select_options = array(
            'title short description' => 'title short description',
            'title long description' => 'title long description',
            'series short description' => 'series short description',
            'series long description' => 'series long description'
        );
        
        $id = $fieldname;
        if ((strpos($id, '[]') + 2) == strlen($id)) {
            $id = substr($key, 0, strpos($id, '[]'));
        }
        
        $str = '<select name="' . $fieldname . '" id="' . $id . '" >';

        foreach($select_options as $k => $value) {
            $selected = '';
            
            if (strcmp($selected_value, $k) == 0) {
                $selected = 'selected="selected"';
            }
            $str .= '<option value="' . $k . '" ' . $selected . '>' . $value . '</option>';
        }
        $str .= '</select>';
        return $str;
    }
    
    public function dateSelector($fieldname, $fieldvalue = '')
    {
        $id = $fieldname;
        if ((strpos($id, '[]') + 2) == strlen($id)) {
            $id = substr($key, 0, strpos($id, '[]'));
        }
        
        $timestamp = strtotime($fieldvalue);
        if ($timestamp === false) {
            $timestamp = strtotime('-1 month');
        }

        $dateString = date('d/m/Y', $timestamp);
        $hiddenField = "<input type=\"text\" class=\"wform-input-date-field\" name=\"{$fieldname}\" id=\"{$id}\" value=\"{$dateString}\"/>";
        
        $displayId = "{$id}-date-display";
        $htmlCode = '<span class="wform-date-selector" id="' . $displayId . '">' . $dateString . '</span>' . $hiddenField;
        
        $script = <<<EOF
        <script>
            jQuery('input[name="{$fieldname}"]').css({width:'0px',border:'0px'});
            jQuery('input[name="{$fieldname}"]').datepicker({ dateFormat: "dd/mm/yy",
                                                    changeMonth: true,
                                                    changeYear: true,
                                                    onSelect: function(dateText, inst){
                                                        jQuery('#{$displayId}').html(dateText);
                                                        /* jQuery('.from-date').css('color', 'red'); */
                                                    }
                                                });
            jQuery('.wform-date-selector#{$displayId}').click(function(){
                                                        jQuery('input[name="{$fieldname}"]').datepicker("show");
                                                    });
        </script>
EOF;
        $htmlCode = $htmlCode . "\n {$script}";
        
        return $htmlCode;
    }

    // ANZGO-1738
    public function userTypeRestrictionGroups($selected_value, $fieldname = 'UserTypeIDRestriction',$bootstrap = false)
    {
        $groups = CupContentTitle::getGroups();

        $html = '';
        foreach ($groups as $gKey => $gVal) {
            if (strtolower($gVal) === 'student' || strtolower($gVal) === 'teacher') {
                $html .= '<span class="ccm-radio-container">';
                $html .= $this->form->radio($fieldname, $gKey, $selected_value) . ' ' . $gVal;
                $html .= '</span>';
            }
        }

        return $html;
    }

    // ANZGO-1738
    public function contentType($selected_value, $fieldname = 'ContentType',$bootstrap = false)
    {
        $select_options = array(
            'Data - Code' => 'Data - Code',
            'Data - Code - No Message' => 'Data - Code - No Message',
            'Data - Code - Teacher' => 'Data - Code - Teacher',
            'Data - Free' => 'Data - Free',
            'Data - Free - Teacher' => 'Data - Free - Teacher',
            'Electronic Version' => 'Electronic Version',
            'EMAC' => 'EMAC',
            'Essential Maths' => 'Essential Maths',
            'Interactive Textbook' => 'Interactive Textbook',
            'PDF Textbook' => 'PDF Textbook',
            'Print and Digital' => 'Print and Digital',
            'Recipe Calculator' => 'Recipe Calculator'
        );
        
        $id = $fieldname;
        if ((strpos($id, '[]') + 2) == strlen($id)) {
            $id = substr($key, 0, strpos($id, '[]'));
        }
        
        // Added by Paul Balila
        if($bootstrap) {
            $str = '<select name="' . $fieldname . '" id="' . $id . '" class="form-control">';
        } else {
            $str = '<select name="' . $fieldname . '" id="' . $id . '" class="span6">'; 
        }

        foreach($select_options as $k => $value) {
            $selected = '';
            
            if (strcmp($selected_value, $k) == 0) {
                $selected = 'selected="selected"';
            }
            $str .= '<option value="' . $k . '" ' . $selected . '>' . $value . '</option>';
        }
        $str .= '</select>';
        return $str;
    }
    
}
