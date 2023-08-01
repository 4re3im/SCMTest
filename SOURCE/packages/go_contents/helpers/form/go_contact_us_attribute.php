<?php
/**
 * Display helper of Contact Us form
 *
 * @author paulbalila
 */
class FormGoContactUsAttributeHelper extends Concrete5_Helper_Form_Attribute {
        
    protected $obj;

    public function reset() {
        unset($this->obj);
    }

    public function setAttributeObject($obj) {
        $this->obj = $obj;
    }

    public function display($key, $required = false, $includeLabel = true, $template = 'composer', $disabled = '', $placeholder = FALSE) {
        if (is_object($key)) {
            $obj = $key;
        } else {
            $oclass = get_class($this->obj);
            switch ($oclass) {
                case 'UserInfo':
                    $class = 'UserAttributeKey';
                    break;
                default:
                    $class = $oclass . 'AttributeKey';
                    break;
            }
            $obj = call_user_func(array($class, 'getByHandle'), $key);
        }

        if (!is_object($obj)) {
            return false;
        }

        // Hack to activate "getAttributeValueObject" method to put User values in the attributes rendered
        $u = new User();
        if($u) {
            $ui = UserInfo::getByID($u->getUserID());
            $this->setAttributeObject($ui);
            if (is_object($this->obj)) {
                $value = $this->obj->getAttributeValueObject($obj);
            }
        } else {
            $value = FALSE;
        }


        $html = '';

        $html .= '<div class="form-signup form-group row form-group-contact">';

        if ($includeLabel) {
            switch ($obj->akHandle) {
                case 'uPMByEmail':
                case 'uPMByRegularPost':
                    $html .= '<label class="col-lg-3 control-label">&nbsp;</label>';
                    break;
                default:
                    $html .= $obj->render('label', false, true);
                    break;
            }
        }

        if($placeholder) {
            $html .= '<div class="col-lg-12 form-col">';
        } else {
            if ($key->getAttributeKeyHandle() == 'uSubjectsTaught') {
                $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col">';
            } else {
                $html .= '<div class="col-lg-9 col-md-9 col-sm-9 col-xs-8 form-col">';
            }
        }


        switch ($obj->akHandle) {
            case 'uFirstName':
                $html .= "<input type='text' class='form-control go-input' name='first_name' id='" . $obj->akHandle . "' placeholder='" . $obj->akName . " *' /> ";
                break;
            case 'uLastName':
                $html .= "<input type='text' class='form-control go-input' name='last_name' id='" . $obj->akHandle . "' placeholder='" . $obj->akName . " *' /> ";
                break;
            case 'uSchoolAddress':
                $html .= "<input type='text' class='form-control go-input' name='company' id='" . $obj->akHandle . "' placeholder='School *' /> ";
                break;
            case 'billing_phone':
                $html .= "<input type='text' class='form-control go-input' name='phone' id='" . $obj->akHandle . "' placeholder='" . $obj->akName . " *' /> ";
                break;
            case 'uPositionTitle':
                $html .= "<input type='text' class='form-control go-input' name='title' id='" . $obj->akHandle . "' placeholder='" . $obj->akName . " *' /> ";
                break;
            case 'uSubjectsTaught':
                $html .= "<div class='row'>";
                $html .= "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12 container-bg-1' style='height:40%;overflow:auto;padding-left:25px;margin-left:15px;width:96%;'>";
                $html .= $obj->render($template, $value, true);
                $html .= "</div>"; // col-lg-6
                $html .= "</div>"; // row
                break;
            case 'uEmail':
                if($placeholder) {
                    $html .= "<input type='email' class='form-control go-input' name='email' id='email' placeholder='" . $obj->akName . " *' /> ";
                } else {
                    $html .= "<input type='email' class='form-control go-input' name='" . $obj->akHandle . "' id='email' /> ";
                }
                break;
            case 'uPMByEmail':
            case 'uPMByRegularPost':
                if ($value) {
                    if ($value->getValue()) {
                        $html .= "<input type='checkbox' name='akID[" . $obj->akID . "][value]' id='akID[" . $obj->akID . "][value]' " . $disabled . " checked/> " . $obj->akName;
                    } else {
                        $html .= "<input type='checkbox' name='akID[" . $obj->akID . "][value]' id='akID[" . $obj->akID . "][value]' " . $disabled . " /> " . $obj->akName;
                    }
                } else {
                    $html .= "<input type='checkbox' name='akID[" . $obj->akID . "][value]' id='akID[" . $obj->akID . "][value]' " . $disabled . " /> " . $obj->akName;
                }
                break;
            default:
                $html .= $obj->render($template, $value, true);
                break;
        }

        $html .= '</div>'; // col-lg-9
        $html .= '</div>'; // form-group row

        $formatted = str_replace("span5 ccm-input-text", "form-control go-input", $html);
        $formatted = str_replace("ccm-input-text", "form-control go-input", $formatted);
        $formatted = str_replace("ccm-input-select", "form-control go-input", $formatted);
        $formatted = str_replace("ccm-input-checkbox", "ccm-input-checkbox form-control-tickable", $formatted);
        if ($key == 'uSubjectsTaught') {
            $formatted = str_replace("control-label", "col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label", $formatted);
        } else {
            $formatted = str_replace("control-label", "col-lg-12 col-md-12 col-sm-12 col-xs-12 control-label", $formatted);
        }

        return $formatted;
    }

    public function display_label($key, $value = false) {
        if (is_object($key)) {
            $obj = $key;
        } else {
            $oclass = get_class($this->obj);
            switch ($oclass) {
                case 'UserInfo':
                    $class = 'UserAttributeKey';
                    break;
                default:
                    $class = $oclass . 'AttributeKey';
                    break;
            }
            $obj = call_user_func(array($class, 'getByHandle'), $key);
        }

        if (!is_object($obj)) {
            return false;
        }

        if (is_object($this->obj)) {
            $value = $this->obj->getAttributeValueObject($obj);
        }
        $html .= '<div class="form-group row">';
        $html .= $obj->render('label', false, true);
        $html .= '<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">';
        $html .= '<p class="form-control-static">';
        $html .= $value;
        $html .= '</p>';
        $html .= '</div>'; // col-lg-9
        $html .= '</div>'; // form-group row
        $formatted = str_replace("control-label", "col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label", $html);
        return $formatted;
    }

    // Custom functions
    function wrapper($content, $placeholder = FALSE) {
        $notes = "<div class='form-group row'>";
        if ($placeholder) {
            $notes .= "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col'>";
        } else {
            $notes .= "<label class='col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label'>&nbsp;</label>";
            $notes .= "<div class='col-lg-9 col-md-9 col-sm-9 col-xs-8 form-col'>";
        }

        $notes .= "<p class='form-control-static'>";
        $notes .= $content;
        $notes .= "</p>";
        $notes .= "</div>"; // col-lg-9
        $notes .= "</div>"; // form-group row
        return $notes;
    }

    function cmp($a, $b) {
        return strcmp($a->value, $b->value);
    }
}
