<?php

/**
 * Class User
 * Overrides core concrete 5 User Model
 * to implement GIGYA
 *
 * @author jsunico@cambridge.org
 */

Loader::library('gigya/GSSDK');
Loader::library('gigya/GigyaService');
Loader::library('gigya/GigyaSocialize');
Loader::library('gigya/GigyaAccount');

class User extends Concrete5_Model_User
{
    /**
     * @var GigyaAccountData
     */
    public $gigyaData;

    /**
     * @var User
     */
    public $localUser;

    /**
     * @var bool
     */
    public $uIsActive;

    /**
     * @var int
     */
    public $uLastLogin;

    /**
     * @var string
     */
    public $gigyaUID;

    /**
     * Login user by GigyaUID.
     * If verify is true it will not verify the signature and timestamp.
     *
     * @param $uid
     * @param $signature
     * @param $timestamp
     * @param bool $verify
     * @return bool
     */
    public static function loginByGigyaUID(
        $uid,
        $signature = null,
        $timestamp = null,
        $verify = true
    )
    {
        $gigyaService = new GigyaService();
        $user = static::getByGigyaUID($uid);

        $isValidUser = $verify
            ? $gigyaService->verifyUser($uid, $signature, $timestamp)
            : true;

        if (!$isValidUser && $user) {
            return false;
        }

        static::regenerateSession();
        $_SESSION['uID'] = $user->getUserID();
        $_SESSION['uName'] = $user->getUserName();
        $_SESSION['uBlockTypesSet'] = false;
        $_SESSION['uGroups'] = $user->_getUserGroups(true);
        $_SESSION['uLastOnline'] = $user->localUser->getLastOnline();
        $_SESSION['uTimezone'] = $user->getUserTimezone();
        $_SESSION['uDefaultLanguage'] = $user->getUserDefaultLanguage();
        $_SESSION['gigyaUID'] = $uid;

        $user->recordLogin();
        $user->setUserForeverCookie();

        return true;
    }

    public static function registerUser(GigyaAccountData $data)
    {
        $db = Loader::db();
        $query = <<<SQL
        INSERT INTO Users
            (
                uName,
                uEmail,
                uDateAdded,
                uDefaultLanguage,
                uTimezone,
                uIsActive,
                uIsValidated
            )
        VALUES (?,?,NOW(),?,?,1,1)
SQL;
        try {
            $db->Execute($query, [
                $data->getFullName(),
                $data->getEmail(),
                $data->getLocale(),
                $data->getTimezone()
            ]);

            $userId = (int)$db->Insert_ID();
            $user = static::getByUserID($userId);
            $userInfo = UserInfo::getByID($userId);

            $gigyaAccount = new GigyaAccount($data->getUID());
            $gigyaAccount->setSystemId($userId);

            $firstName = $data->getFirstName();
            if ($firstName) {
                $userInfo->setAttribute('uFirstName', $firstName);
            }

            $lastName = $data->getLastName();
            if ($lastName) {
                $userInfo->setAttribute('uLastName', $lastName);
            }

            // GCAP-490 remove as email is not needed for double saving in user attributes
            // $email = $data->getEmail();
            // if ($email) {
            //     $userInfo->setAttribute('uEmail', $email);
            // }

            $schoolName = $data->getSchoolName();
            if ($schoolName) {
                $userInfo->setAttribute('uSchoolName', $schoolName);
            }

            // GCAP-490 remove as postcode is not needed for double saving in user attributes
            // $postCode = $data->getPostCode();
            //  if ($schoolName) {
            //     $userInfo->setAttribute('uSchoolPostCode', $postCode);
            // }

            $role = $data->getRole();
            if ($role) {
                $userGroup = Group::getByName($role);
                $user->enterGroup($userGroup);
            }

            return $userId;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }

    }

    /**
     * Search User from Gigya and returns user object.
     *
     * @param $uid
     * @param $isCreateIfNotExistInDb
     * @return null|User
     */
    public static function getByGigyaUID($uid, $isCreateIfNotExistInDb = true)
    {
        $gigyaAccount = new GigyaAccount($uid);
        $accountData = $gigyaAccount->getAccountInfo();

        $localUserId = $accountData->getSystemID();
        if (!$localUserId && $isCreateIfNotExistInDb) {
            $localUserId = static::registerUser($accountData);
        }

        $localUser = parent::getByUserID($localUserId);

        if (!$accountData->isValid()) {
            return null;
        }

        $user = new static();
        $user->uID = $localUserId;
        $user->uName = $accountData->getFullName();
        $user->uIsActive = $accountData->getIsActive();
        $user->uDefaultLanguage = $accountData->getLocale();
        $user->uLastLogin = $localUser->uLastLogin;
        $user->uTimezone = $accountData->getTimezone();
        $user->uGroups = $user->_getUserGroups(true);
        $user->superUser = ($user->getUserID() == USER_SUPER_ID);
        $user->gigyaData = $accountData;
        $user->localUser = $localUser;
        $user->gigyaUID = $uid;

        return $user;
    }

    /**
     * Overrides checkLogin
     *
     * @return bool
     */
    public function checkLogin()
    {
        if (!$this->isGigyaUser()) {
            return parent::checkLogin();
        }

        $aeu = Config::get('ACCESS_ENTITY_UPDATED');
        if ($aeu && $aeu > $_SESSION['accessEntitiesUpdated']) {
            static::refreshUserGroups();
        }

        if ($_SESSION['uID'] <= 0) {
            return null;
        } else {
            /*
            * GCAP-369 added by mtanada 20190404 remove UVH type:2
            * Migrated gigya user, to check user validation hash table
            * NOTE: To be removed after Gigya deployment
            */
            $hash = $_COOKIE['ccmUserHash'];
            $uIDHash = UserValidationHash::getUserID($hash, UVTYPE_LOGIN_FOREVER);
            if (!isset($hash) || !$uIDHash) {
                parent::logout();
                header('Location: /go/login');
                exit;
            }
        }

        $gigyaUID = $_SESSION['gigyaUID'];
        $user = static::getByGigyaUID($gigyaUID);

        if ((int)$user->getUserID() !== (int)$_SESSION['uID']) {
            return false;
        }

        if (!$user->gigyaData->getIsActive()) {
            return false;
        }

        $_SESSION['uOnlineCheck'] = time();
        $difference = $_SESSION['uOnlineCheck'] - $_SESSION['uLastOnline'];
        if ($difference > (ONLINE_NOW_TIMEOUT / 2)) {
            $db = Loader::db();
            $query = 'UPDATE Users SET uLastOnline = ? WHERE uID = ?';
            $db->query($query, [$_SESSION['uOnlineCheck'], $this->uID]);

            $_SESSION['uLastOnline'] = $_SESSION['uOnlineCheck'];
        }

        return true;
    }

    /**
     * Returns true if current logged in user is from Gigya.
     *
     * @return bool
     */
    public function isGigyaUser()
    {
        return isset($_SESSION['gigyaUID']);
    }

    /**
     * Returns forever cookie of user.
     *
     * @param $uID
     * @return bool|mixed
     */
    public static function getForeverCookie($uID)
    {
        $db = Loader::db();
        $sql = <<<SQL
          SELECT uHash FROM UserValidationHashes WHERE uID = ? AND type = ?
          ORDER BY uDateGenerated DESC
SQL;
        return $db->GetOne($sql, [$uID, UVTYPE_LOGIN_FOREVER]);
    }

    public static function getGUIDByUID($uid)
    {
        $db = Loader::db();
        $sql = 'SELECT gUID FROM Users WHERE uID = ?';

        return $db->GetOne($sql, [$uid]);
    }

    public function getEmail()
    {
        $db = Loader::db();
        return $db->GetOne('SELECT uEmail FROM Users WHERE uID = ?', [$this->uID]);
    }

    public function leaveGroups()
    {
        if (!$this->uID) {
            return false;
        }

        $db = Loader::db();
        return $db->Execute('DELETE FROM UserGroups WHERE uID = ?', [$this->uID]);
    }
}
