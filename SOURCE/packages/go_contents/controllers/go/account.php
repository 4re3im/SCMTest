<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of activate
 *
 * @author paulbalila
 */
class GoAccountController extends Controller
{
    const ACCOUNT = 'Account';
    const VALUE = 'value';

    private $error;
    private $required;
    private $email;

    public function __construct()
    {
        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());
        $this->email = $ui->uEmail;
    }

    public function on_start()
    {
        // SB-292 added by machua 20190806 to redirect user to login
        global $u;
        if (!$u->isLoggedIn()) {
            $this->redirect('/go/login');
        }
        
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_theme"));
        $this->error = Loader::helper('validation/error');
        $this->required = array(
            'uPassword',
            'uFirstName',
            'uLastName',
            'uSchoolName',
            'uSchoolPhoneNumber',
            'uSchoolAddress',
            'uSuburb',
            'uState',
            'uPostcode',
            'uCountry'
        );

        CupGoLogs::trackUser(static::ACCOUNT, "View my details");
    }

    public function view()
    {
        $this->set('email', $this->email);
    }

    // ANZGO-3735 Modified by Shane Camus 05/30/18
    public function do_edit($mode)
    {
        $valc = Loader::helper('concrete/validation');

        Loader::model('gouser', 'go_theme');
        $userModel = new GoUser();
        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());

        if ($mode == 'password') {
            $prev_pass = $this->post('oldPassword');
            $password = $this->post('uPassword');
            $passwordConfirm = $this->post('uPasswordConfirm');

            $u1 = new User($u->uName, $prev_pass);

            if ($u1->isError()) {
                try {
                    switch ($u1->getError()) {
                        case USER_NON_VALIDATED:
                            throw new Exception(t('This account has not yet been validated. 
                            Please check the email associated with this account and follow the link it contains.'));
                            break;
                        case USER_INVALID:
                            if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
                                throw new Exception(t('Invalid email address or password.'));
                            } else {
                                throw new Exception(t('Invalid password.'));
                            }
                            break;
                        case USER_INACTIVE:
                            throw new Exception(t('This user is inactive. Please contact us regarding this account.'));
                            break;
                        default:
                            break;
                    }
                } catch (Exception $e) {
                    $this->error->add($e);
                }
            } else {

                if (strlen($password) < USER_PASSWORD_MINIMUM) {
                    $this->error->add(t('Password must be %s characters or longer.', USER_PASSWORD_MINIMUM));
                }

                if (!preg_match("/\d/", $password)) {
                    $this->error->add(t('Password must contain at least 1 number.'));
                }

                if (!preg_match("/[a-zA-Z]/", $password)) {
                    $this->error->add(t('Password must contain at least 1 letter.'));
                }

                if ($password != $passwordConfirm) {
                    $this->error->add(t('The two passwords provided do not match.'));
                }
            }

            if (!$this->error->has()) {
                $update = $ui->update($this->post());
                if ($update) {
                    $this->set('success', 'Your details have been updated.');
                    CupGoLogs::trackUser(static::ACCOUNT, "Change password");
                }
            } else {
                $this->set('errors', $this->error->getList());
            }

        } else {
            $contact_attribs = AttributeSet::getByHandle('uContactDetails');
            foreach ($contact_attribs->getAttributeKeys() as $ca) {
                if (in_array($ca->getAttributeKeyHandle(), $this->required)) {
                    $temp = $this->post('akID');
                    $ui->setAttribute($ca, $temp[$ca->getAttributeKeyID()][static::VALUE]);
                }
            }

            // ANZGO-3671 Modified by Shane Camus 03/20/2018
            if ($_POST['type'] == 'Teacher') {
                $teacher_attribs = AttributeSet::getByHandle('uTeacherContactDetails');
                foreach ($teacher_attribs->getAttributeKeys() as $ta) {
                    $temp2 = $this->post('akID');
                    $ui->setAttribute($ta, $temp2[$ta->getAttributeKeyID()][static::VALUE]);
                }

                // ANZGO-3809 Deleted by Shane Camus 07/25/2018

            } else {
                $general_checkboxes = AttributeSet::getByHandle('uCheckBoxes');
                foreach ($general_checkboxes->getAttributeKeys() as $gc) {
                    $temp1 = $this->post('akID');
                    $ui->setAttribute($gc, $temp1[$gc->getAttributeKeyID()][static::VALUE]);
                }
            }

            // Hack for the manual activation details
            $updateManAct = $userModel->updateManualActivation($u->uID, $this->post('manAct'));

            CupGoLogs::trackUser(static::ACCOUNT, "Edit Contact Details");
            $this->set('contact_success', 'Your details have been updated.');
            $this->set('email', $this->email);
        }
    }

    // GCAP-236 added by jdchavez 01/18/2019
    public function gigyaEditDetails()
    {
        // GCAP-531 - gxbalila
        // Plain C5 POST, GET not working?! (:shookt:) so used file_get_contents
        // SB-560 / SB-541 modified by jbernardez 20200519
        $input = $this->post();

        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());

        $ui->setAttribute('uFirstName', $input['firstName']);
        $ui->setAttribute('uLastName', $input['lastName']);
        $ui->setAttribute('uSchoolName', $input['school']);

        CupGoLogs::trackUser(static::ACCOUNT, "Edit Contact Details through Gigya");
        exit;
    }

    //GCAP-367 added by jdchavez 04/03/2019
    public function getHomeSchoolData()
    {
        Loader::library('gigya/datastore/GigyaInstitution');
        $gigyaInstitution = new GigyaInstitution();
        $result = $gigyaInstitution->getHomeSchoolData();
        echo json_encode($result);
        exit;
    }

    public function getUserGigyaPreferences()
    {
        $u = new User();
        $g = Group::getByName('Administrators');
        $response = ['isAccepted' => false, 'isLoggedIn' => false];

        if($u->isLoggedIn() && (!$u->inGroup($g) && !$u->isSuperUser())) {
            $gigyaAccount = new GigyaAccount();
            $isAccepted = $gigyaAccount->getUserPreferences($u->getEmail());
            $response['isAccepted'] = ($isAccepted === 1);
            $response['isLoggedIn'] = $u->isLoggedIn();
        }

        echo json_encode($response);
        die;
    }
}
