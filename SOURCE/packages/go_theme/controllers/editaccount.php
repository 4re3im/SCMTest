<?php

/**
 * Description of activate
 *
 * @author paulbalila
 */
class EditAccountController extends Controller {
    /**
     * Set needed attributes to display. We are using handles of the created attributes
     * @var array
     */
    private $required_attr = array('uPassword','uPasswordConfirm','uFirstName','uLastName','uSchoolName','uSchoolPhoneNumber','uSchoolAddress','uSuburb','uState','uPostcode','uCountry');
    
    public function on_start() {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_plain_theme"));
        
    }
    
    /**
     * 
     * @param String $mode Either 'password' (to edit/change password) or 'contact' (to edit/chenge contact details)
     * @param String $user 'Student' or null - then it is a teacher
     */
    public function view($mode,$user = FALSE) {
        Loader::model('gouser','go_theme');
        $userModel = new GoUser();

        /* Get User object and get group type */
        $u = new User();
        
        /* Get UserInfo object */
        $ui = UserInfo::getByID($u->getUserID());
        
        /* Get needed attribute sets. */
        // Required for student and teacher: uContactDetails, uLoginDetails, go_user
        $login_attribs = AttributeSet::getByHandle('uLoginDetails');
        $contact_attribs = AttributeSet::getByHandle('uContactDetails');
        $general_checkboxes = AttributeSet::getByHandle('uCheckBoxes');
        
        // Required for teacher: uTeacherContactDetails
        if($user != 'Student') {
            $teacher_attribs = AttributeSet::getByHandle('uTeacherContactDetails');
            $teacher_checkboxes = AttributeSet::getByHandle('uTeacherCheckboxes');
            $this->set('teacher_attribs',$teacher_attribs->getAttributeKeys());
            $this->set('teacher_checkboxes',$teacher_checkboxes->getAttributeKeys());
        }

        // Hack for the Manual Activation details
        $manualActivationDetails = $userModel->getUserManualActivation($u->uID);
        $this->set('mad',$manualActivationDetails);
        
        $this->set('general_checkboxes',$general_checkboxes->getAttributeKeys());
        $this->set('login_attribs',$login_attribs->getAttributeKeys());
        $this->set('contact_attribs',$contact_attribs->getAttributeKeys());
        $this->set('user_info',$ui);
        $this->set('required',$this->required_attr);
        $this->set('type',$user);
        $this->set('mode',$mode);
    }
}
