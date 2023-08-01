<?php

defined('C5_EXECUTE') || die("Access Denied.");

class FormAttributeHelper extends Concrete5_Helper_Form_Attribute
{
    protected $obj;

    public function reset()
    {
        unset($this->obj);
    }

    public function setAttributeObject($obj)
    {
        $this->obj = $obj;
    }

    /**
     * ANZGO-3461 Added by John Renzo Sunico, Aug 02, 2017
     * Added @helper_position parameter.
     */
    /**
     * @param $key
     * @param bool $required
     * @param bool $includeLabel
     * @param string $template
     * @param string $disabled
     * @param bool $placeholder
     * @param bool $editSpecial
     * @param string $helperPosition
     * @return mixed
     */
    public function display($key,
                            $required = false,
                            $includeLabel = true,
                            $template = 'composer',
                            $disabled = '',
                            $placeholder = false,
                            $editSpecial = false,
                            $helperPosition = 'before',
                            $loginOverride = false)
    {
        if (is_object($key)) {
            $obj = $key;
        } else {
            $oclass = get_class($this->obj);
            if ($oclass == 'UserInfo') {
                $class = 'UserAttributeKey';
            } else {
                $class = $oclass . 'AttributeKey';
            }
            $obj = call_user_func(array($class, 'getByHandle'), $key);
        }

        if (!is_object($obj)) {
            return false;
        }

        // Hack to activate "getAttributeValueObject" method to put User values in the attributes rendered
        $u = new User();
        if ($u) {
            $ui = UserInfo::getByID($u->getUserID());
            $this->setAttributeObject($ui);
            if (is_object($this->obj)) {
                $value = $this->obj->getAttributeValueObject($obj);
            }
        } else {
            $value = FALSE;
        }

        /**
         * ANZGO-3461 Added by John Renzo Sunico, Aug 02, 2017
         * Checks if helper text should be displayed before the input element;
         */

        if ($helperPosition == 'before') {
            $html = $this->getNotes($obj->akHandle, $placeholder);
        }

        // ANZGO-3672 added by jbernardez 20180327
        // $html .= '<div class="form-signup form-group row ">';

        // ANZGO-3671 Modified by Shane Camus 03/20/2018
        if ($includeLabel) {
            switch ($obj->akHandle) {
                // ANZGO-3672 added by jbernardez 20180322
                case 'uTermsConditions':
                case 'uCustomerCare' :
                case 'uPMByEmail':
                case 'uPMByRegularPost':
                    $html .= '<label class="col-lg-3 control-label">&nbsp;</label>';
                    break;
                default:
                    $html .= $obj->render('label', false, true);
                    break;
            }
        }

        if ($placeholder) {
            $html .= '<div class="input-field">';

        } else {
            switch ($key->getAttributeKeyHandle()) {
                case 'uSubjectsTaught':
                case 'uStateUS':
                case 'uStateNZ':
                case 'uStateCA':
                case 'uStateAU':
                case 'uState':
                    if ($editSpecial) {
                        $html .= '<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 form-col" 
                                       id="contact-state-selector">';
                    } else {
                        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col">';
                    }
                    break;
                case 'uProductsUsing':
                    $html .= '<div class="input-field">';
                    break;
                default:
                    $html .= '<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 form-col">';
                    break;
            }
        }

        // ANZGO-3671 Modified by Shane Camus 03/20/2018
        switch ($obj->akHandle) {
            case 'uSubjectsTaught':
            case 'uProductsUsing':
                // ANZGO-3920 modified by jbernardez 20181121
                $html .= "<div class='input-field' 
                               style='overflow:auto;padding-left:25px;margin-left:15px;width:96%;'>";
                $html .= $obj->render($template, $value, true);
                $html .= "</div>";
                break;
            case 'uName':
            case 'uSecurityAnswer':
                $html .= "<input type='text' class='form-control go-input' name='" . $obj->akHandle . "' />";
                break;
            case 'uEmail':
                // ANZGO-3920 modified by jbernardez 20181116
                if ($loginOverride) {
                    $html .= "<label for='email-field'>" . $obj->akName . "</label>";
                    if ($placeholder) {
                        $html .= "<input type='email' class='email-field' name='" . $obj->akHandle . "' 
                                id='email' placeholder='e.g john.smith@cambridge.org' required /> ";
                    } else {
                        $html .= "<input type='email' class='email-field' name='" . $obj->akHandle . "' 
                                id='email' required /> ";
                    }
                    $html .= '<span class="field-error"></span>';
                } else {
                    $html .= $obj->render($template, $value, true);
                }
                break;
            case 'uPassword':
                // ANZGO-3920 modified by jbernardez 20181116
                if ($loginOverride) {
                    $html .= "<label for='password-field'>" . $obj->akName . "</label>";
                    if ($placeholder) {
                        $html .= "<input id='password-field' type='password' class='password-field' 
                        name='" . $obj->akHandle . "' placeholder='' required />";
                    } else {
                        $html .= "<input id='password-field' type='password' class='password-field' 
                        name='" . $obj->akHandle . "' required />";
                    }
                    $html .= "<span toggle='#password-field' class='field-icon toggle-password'>Show</span>";
                    $html .= '<ul class="password-req">
                                <li class="length">Eight (8) characters minimum</li>
                                <li class="letter">One (1) letter</li>
                                <li class="number">One (1) number</li>
                            </ul>';
                    $html .= '<span class="field-error"></span>';
                } else {
                    $html .= "<input type='password' class='form-control go-input' name='" . $obj->akHandle . "' />";
                }
                break;
            case 'uPasswordConfirm':
                // ANZGO-3920 modified by jbernardez 20181116
                if ($loginOverride) {
                    $html .= "<label for='confirm-password-field'>" . $obj->akName . "</label>";
                    if ($placeholder) {
                        $html .= "<input id='confirm-password-field' type='password' class='confirm-password-field' 
                                name='" . $obj->akHandle . "' equalto='uPassword' placeholder='' required /> ";
                    } else {
                        $html .= "<input id='confirm-password-field' type='password' class='confirm-password-field' 
                                name='" . $obj->akHandle . "' equalto='uPassword' required /> ";
                    }
                    $html .= "<span toggle='#confirm-password-field' class='field-icon toggle-password'>Show</span>";
                    $html .= '<span class="field-error"></span>';
                } else {
                    $html .= "<input type='password' class='form-control go-input' name='" . $obj->akHandle . "' 
                                equalto='uPassword' />";
                }
                break;
            // ANZGO-3920 modified by jbernardez 20181119
            case 'uFirstName':
                if ($loginOverride) {
                    $html .= "<label for='input-field'>" . $obj->akName . "</label>";
                    if ($placeholder) {
                        $html .= "<input type='text' class='fname-field' name='akID[" . $obj->akID . "][value]' 
                                       id='akID[" . $obj->akID . "][value]' placeholder='e.g. John' /> ";
                    } else {
                        $html .= "<label for='input-field'>" . $obj->akName . "</label>
                                <input type='text' class='fname-field' name='akID[" . $obj->akID . "][value]' 
                                       id='akID[" . $obj->akID . "][value]' /> ";
                    }
                    $html .= '<span class="field-error"></span>';
                } else {
                    $html .= $obj->render($template, $value, true);
                }
                break;
            // ANZGO-3920 modified by jbernardez 20181119
            case 'uLastName':
                if ($loginOverride) {
                    $html .= "<label for='input-field'>" . $obj->akName . "</label>";
                    if ($placeholder) {
                        $html .= "<input type='text' class='fname-field' name='akID[" . $obj->akID . "][value]' 
                                   id='akID[" . $obj->akID . "][value]' placeholder='e.g. Smith' /> ";
                    } else {
                        $html .= "<label for='input-field'>" . $obj->akName . "</label>
                            <input type='text' class='fname-field' name='akID[" . $obj->akID . "][value]' 
                                   id='akID[" . $obj->akID . "][value]' /> ";
                    }
                    $html .= '<span class="field-error"></span>';
                } else {
                    $html .= $obj->render($template, $value, true);
                }
                break;
            // ANZGO-3920 modified by jbernardez 20181119
            case 'uSchoolName':
                if ($loginOverride) {
                    $html .= "<label for='input-field'>" . $obj->akName . "</label>";
                    if ($placeholder) {
                        $html .= "<input type='text' class='fname-field' name='akID[" . $obj->akID . "][value]' 
                                       id='akID[" . $obj->akID . "][value]' placeholder='' required /> ";
                    } else {
                        $html .= "<label for='input-field'>" . $obj->akName . "</label>
                                <input type='text' class='fname-field' name='akID[" . $obj->akID . "][value]' 
                                       id='akID[" . $obj->akID . "][value]' required /> ";
                    }
                    $html .= '<span class="field-error"></span>';
                } else {
                    $html .= $obj->render($template, $value, true);
                }
                break;
            // ANZGO-3920 modified by jbernardez 20181119
            case 'uSchoolPostCode':
                if ($loginOverride) {
                    $html .= "<label for='input-field'>" . $obj->akName . "</label>";
                    if ($placeholder) {
                        $html .= "<input type='text' class='fname-field' name='akID[" . $obj->akID . "][value]' 
                                       id='akID[" . $obj->akID . "][value]' placeholder='Postcode' required /> ";
                    } else {
                        $html .= "<label for='input-field'>" . $obj->akName . "</label>
                                <input type='text' class='fname-field' name='akID[" . $obj->akID . "][value]' 
                                       id='akID[" . $obj->akID . "][value]' required /> ";
                    }
                    $html .= '<span class="field-error"></span>';
                } else {
                    $html .= $obj->render($template, $value, true);
                }
                break;
            // ANZGO-3672 added by jbernardez 20180322
            case 'uTermsConditions':
            case 'uCustomerCare' :
            case 'uPMByEmail':
            case 'uPMByRegularPost':
                if ($value) {
                    if ($value->getValue()) {
                        $html .= "<input class='" . $obj->akHandle . "' type='checkbox' 
                                         name='akID[" . $obj->akID . "][value]' 
                                         id='akID[" . $obj->akID . "][value]' " . $disabled . " 
                                         value='1' checked/> " . $obj->akName;
                    } else {
                        $html .= "<input class='" . $obj->akHandle . "' type='checkbox' 
                                         name='akID[" . $obj->akID . "][value]' 
                                         id='akID[" . $obj->akID . "][value]' " . $disabled . " 
                                         value='1' /> " . $obj->akName;
                    }
                } else {
                    $html .= "<input class='" . $obj->akHandle . "' type='checkbox' 
                                     name='akID[" . $obj->akID . "][value]' 
                                     id='akID[" . $obj->akID . "][value]' " . $disabled . " 
                                     value='1' /> " . $obj->akName;
                }
                break;
            case 'uPositionType':
                $html .= str_replace('** None', $obj->akName, $obj->render($template, $value, true));
                break;
            case 'uStateUS':
            case 'uStateCA':
            case 'uStateAU':
            case 'uStateNZ':
                $temp = str_replace('** None', $obj->akName, $obj->render($template, $value, true));
                $html .= str_replace($obj->akName, "State", $temp);
                break;
            case 'uCountry':
                $v = View::getInstance();
                $temp = str_replace('** None', $obj->akName, $obj->render($template, $value, true));
                $temp = str_replace(
                    "ccm-input-select", "form-control go-input contact-country-select", $temp);
                $html .= $temp . "<input type='hidden' value='" . $v->url("/go/signup/getStatesProvinces") . "'>";
                break;
            default:
                // ANZGO-1738
                // ANZGO-3920 modified by jbernardez 20181119
                if ($innerLabel) {
                    $html .= "<label for='input-field'>" . $obj->akName . "</label>";
                }
                if ($placeholder) {
                    $html .= "<input type='text' class='fname-field' name='akID[" . $obj->akID . "][value]' 
                                   id='akID[" . $obj->akID . "][value]' placeholder='" . $obj->akName . "' /> ";
                } else {
                    $html .= $obj->render($template, $value, true);
                }
                break;
        }

        $html .= '<span id="helpBlock" class="help-block"></span>';

        $html .= '</div>'; // col-lg-9
        // $html .= '</div>'; // form-group row

        /**
         * ANZGO-3461 Added by John Renzo Sunico, Aug 02, 2017
         * Checks if helper text should be shown below / after the element.
         */

        if ($helperPosition == 'after') {
            // ANZGO-3920 modified by jbernardez 20181119
            // blocked this for the moment, looking for a better way to not show the notes
            // $html .= $this->getNotes($obj->akHandle, $placeholder);
        }

        $formatted = str_replace("span5 ccm-input-text", "form-control go-input", $html);
        $formatted = str_replace("ccm-input-select", "form-control go-input", $formatted);
        $formatted = str_replace(
                        "ccm-input-checkbox",
                        "ccm-input-checkbox form-control-tickable",
                        $formatted);

        if(!$editSpecial) {
            $formatted = str_replace('selected="selected"',"", $formatted);
        }

        if ($key == 'uSubjectsTaught') {
            $formatted = str_replace(
                            "control-label",
                            "col-lg-12 col-md-12 col-sm-12 col-xs-12 control-label",
                            $formatted);
        } else {
            $formatted = str_replace(
                            "control-label",
                            "col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label",
                            $formatted);
        }

        return $formatted;
    }

    /**
     * Display function for labels, for review to check which modules are using this
     * @param $key
     * @param bool $value
     * @return bool|mixed
     */
    public function display_label($key, $value = false)
    {
        if (is_object($key)) {
            $obj = $key;
        } else {
            $oclass = get_class($this->obj);
            if ($oclass == 'UserInfo') {
                $class = 'UserAttributeKey';
            } else {
                $class = $oclass . 'AttributeKey';
            }
            $obj = call_user_func(array($class, 'getByHandle'), $key);
        }

        if (!is_object($obj)) {
            return false;
        }

        if (is_object($this->obj)) {
            $value = $this->obj->getAttributeValueObject($obj);
        }

        $html = '<div class="form-group row">';
        $html .= $obj->render('label', false, true);
        $html .= '<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">';
        $html .= '<p class="form-control-static">';
        $html .= $value;
        $html .= '</p>';
        $html .= '</div>'; // col-lg-9
        $html .= '</div>'; // form-group row
        return str_replace("control-label", "col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label", $html);
    }

    // ANZGO-3735 Modified by Shane Camus 05/30/18

    /**
     * methid for getting footnotes on a specific attribute
     * @param $akHandle
     * @param bool $placeholder
     * @return bool|string
     */
    private function getNotes($akHandle, $placeholder = false)
    {
        switch ($akHandle) {
            case 'uSchoolPhoneNumber':
                $notes = $this->wrapper("Phone number may be used to verify if you are a teacher.", $placeholder);
                break;
            case 'uEmailAddress':
                $notes = $this->wrapper(
                    "<strong>Important:</strong> You will need to activate your account using this email address 
                    and it will be your login name.",
                    $placeholder);
                break;
            case 'uSecurityQuestion':
                $notes = $this->wrapper(
                    "To help keep your account safe, please choose a question and provide an answer that 
                    only you know. We will use this to verify any future account detail changes.",
                    $placeholder);
                break;
            /**
             * ANZGO-3461 Added by John Renzo Sunico, Aug 02, 2017
             * Added new case for uEmail and updated uPassword Notes;
             */
            case 'uEmail':
                $notes = $this->wrapper(
                    "This will be your login name. You will need a valid email address to activate your account.",
                    $placeholder);
                break;
            case 'uPassword':
                $notes = $this->wrapper(
                    "Your password should be 8 characters in length. It must contain a mix of letters and numbers.",
                    $placeholder);
                break;
            case 'uPositionTitle':
                $notes = $this->wrapper("Position title example: <i>English Coordinator</i>", $placeholder);
                break;
            default:
                $notes = FALSE;
                break;
        }

        return $notes;
    }

    /**
     * Wrapper method, for review if it can be removed or modified
     * @param $content
     * @param bool $placeholder
     * @return string
     */
    function wrapper($content, $placeholder = false)
    {
        $notes = "<div class='form-group row'>";

        if ($placeholder) {
            $notes .= "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col'>";
        } else {
            $notes .= "<label class='col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label'>&nbsp;</label>";
            $notes .= "<div class='col-lg-9 col-md-12 col-sm-12 col-xs-12 form-col'>";
        }

        $notes .= "<p class='form-control-static' style='padding-top: 0px;'>";
        $notes .= $content;
        $notes .= "</p>";
        $notes .= "</div>";
        $notes .= "</div>";
        return $notes;
    }

    function cmp($a, $b)
    {
        return strcmp($a->value, $b->value);
    }
}
