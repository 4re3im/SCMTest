<?php

/**
 *
 */
class RegistrationHelper
{
    const STATUS_PROCESSING = 'Processing';
    const STATUS_VALIDATING = 'Validating';

    // SB-696 added by mtanada 20201007
    const COLUMN_START_CLASS = 9;
    const COLUMN_END_CLASS = 11;

    private $pkgHandle = 'go_provisioning_hotmaths';
    private $stringVal;
    private $concreteVal;
    private $pModel;
    private $hotMathsUsers = [];

    public function __construct()
    {
        Loader::model('provisioning', $this->pkgHandle);
        Loader::library('HotMaths/api');

        $this->stringVal = Loader::helper('validation/strings');
        $this->concreteVal = Loader::helper('concrete/validation');
        $this->pModel = new ProvisioningHotmathsModel();
    }

    public function validateEmail($email, $fileId)
    {
        if (!$this->stringVal->email($email)) {
            $this->pModel->updateUserStatusByEmail(
                $email,
                static::STATUS_PROCESSING,
                $fileId,
                static::STATUS_VALIDATING,
                'Invalid user email'
            );

            return false;
        } else {
            if (!$this->concreteVal->isUniqueEmail($email)) {
                $ui = UserInfo::getByEmail($email);
                $opts = array('uID' => $ui->getUserID());
                $this->pModel->updateUserStatusByEmail(
                    $email,
                    static::STATUS_PROCESSING,
                    $fileId,
                    'Existing',
                    'User already registered',
                    $opts
                );

                return false;
            }
        }

        return true;
    }

    public function validatePassword($email, $password, $fileId)
    {
        if (strlen($password) <= 0) {
            $this->pModel->updateUserStatusByEmail(
                $email,
                static::STATUS_PROCESSING,
                $fileId,
                static::STATUS_VALIDATING,
                'Password required.'
            );

            return false;
        } else {
            if (!$this->concreteVal->password($password)) {
                $this->pModel->updateUserStatusByEmail(
                    $email,
                    static::STATUS_PROCESSING,
                    $fileId,
                    static::STATUS_VALIDATING,
                    'Invalid password. Remove spaces, ", \', >, or <'
                );
                return false;
            }
        }

        return true;
    }

    public function validateGroup($email, $group, $fileId)
    {
        $group = strtolower($group);
        $group = trim($group);
        if (strcmp($group, 'student') === 0 || strcmp($group, 'teacher') === 0) {
            return true;
        } else {
            $this->pModel->updateUserStatusByEmail(
                $email,
                static::STATUS_PROCESSING,
                $fileId,
                static::STATUS_VALIDATING,
                'Invalid group - student or teacher ONLY.'
            );

            return false;
        }

    }

    public function registerUser($data, $fileId)
    {
        $temp['uName'] = $data[0];
        $temp['uEmail'] = $data[0];
        $temp['uPassword'] = $data[1];
        $temp['uPasswordConfirm'] = $data[1];
        $temp['uGroup'] = ucfirst($data[7]);

        // ANZGO-3597 added by jbernardez 20180131
        // make sure new attribute uCreatedByID is added first via C5 dashboard
        $u = new User();

        $user = UserInfo::register($temp);
        if (is_object($user)) {
            $userGroup = ucfirst($data[7]);
            if (!is_object(Group::getByName($userGroup))) {
                $g = Group::add($userGroup, 'Group for ' . $userGroup . ' users.');
            } else {
                $g = Group::getByName($userGroup);
            }

            $user->setAttribute('uFirstName', $data[2]);
            $user->setAttribute('uLastName', $data[3]);
            $user->setAttribute('FirstName', $data[2]);
            $user->setAttribute('LastName', $data[3]);
            $user->setAttribute('uSchoolName', $data[4]);
            // ANZGO-3597 added by jbernardez 20180131
            $user->setAttribute('uCreatedByID', $u->uID);

            if (strcmp('teacher', strtolower($data[7])) === 0) {
                $user->setAttribute('uCountry', 'Australia');
                $user->setAttribute('uStateAU', $data[5]);
                $user->setAttribute('uPostcode', $data[6]);
            }
            $user->markValidated();

            $u = User::getByUserID($user->getUserID());
            if (!$u->inGroup($g)) {
                $u->enterGroup($g);
            }
            $opts = array('uID' => $u->uID);
            $this->pModel->updateUserStatusByEmail(
                $data[0],
                static::STATUS_PROCESSING,
                $fileId,
                'Registered',
                'Register OK',
                $opts
            );

            return true;
        } else {
            return false;
        }
    }

    /**
     * ANZGO-3445 Modified by John Renzo S. Sunico, November 06, 2017
     *
     * Cleans array of data by
     * trimming whitespaces of each element value
     *
     * @param $data array
     * @return array|string
     */
    public function cleanData($data)
    {
        $cleanedData = array();

        foreach ($data as $key => $value) {
            // SB-696 modified by mtanada 20201007 Retain spaces for class names
            if ($key >= static::COLUMN_START_CLASS && $key <= static::COLUMN_END_CLASS) {
                $cleanedData[$key] = $value;
            } else {
                // SB-465 modified by mabrigos 20200513
                $cleanedData[$key] = str_replace(' ', '', trim($value));
            }
        }
        return $cleanedData;
    }

    /**
     * ANZGO-3611 Added by John Renzo Sunico, 01/29/2018
     * @param $emails
     * @return array
     */
    public function searchHMUsersByEmail($emails)
    {
        $api = new HotMathsApi([
            'userId' => 0,
            'accessCode' => '',
            'response' => 'JSON'
        ]);

        $emails = ['emails' => $emails];
        $this->hotMathsUsers = $api->searchUser($emails);

        return $this->hotMathsUsers;
    }

    /**
     * ANZGO-3611 Added by John Renzo Sunico, 01/29/2018
     * ANZGO-3642 Modified by John Renzo Sunico, 02/22/2018
     * @param $email
     * @param $group
     * @param $fileId
     * @return bool
     */
    public function isInHM($email, $fileId)
    {
        $hmUser = $this->getHMUserFromList($email);
        $hmUser = array_pop($hmUser);

        if (!$hmUser) {
            return true;
        }

        $this->pModel->updateUserStatusByEmail(
            $email,
            static::STATUS_PROCESSING,
            $fileId,
            static::STATUS_VALIDATING,
            'User exists in HM. Check account details.'
        );

        return false;
    }

    /**
     * ANZGO-3611 Added by John Renzo Sunico, 01/29/2018
     * @param $email
     * @return array
     */
    public function getHMUserFromList($email)
    {
        if (!$email || !$this->hotMathsUsers) {
            return [];
        }

        return array_filter($this->hotMathsUsers, function ($e) use (&$email) {
            return (strtolower($e->email) === strtolower($email));
        });
    }

}
