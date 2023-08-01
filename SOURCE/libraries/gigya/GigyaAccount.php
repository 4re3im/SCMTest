<?php

Loader::library('gigya/GSSDK');
Loader::library('gigya/BaseGigya');
Loader::library('gigya/GigyaAccountData');
Loader::library('gigya/GigyaUtils');

class GigyaAccount extends BaseGigya
{
    const METHOD_LOGIN = 'accounts.login';
    const METHOD_GET_ACCOUNT_INFO = 'accounts.getAccountInfo';
    const METHOD_SET_ACCOUNT_INFO = 'accounts.setAccountInfo';
    const METHOD_SEARCH = 'accounts.search';
    const METHOD_INIT_REGISTRATION = 'accounts.initRegistration';
    const METHOD_REGISTER = 'accounts.register';
    const METHOD_FINALIZE_REGISTRATION = 'accounts.finalizeRegistration';
    const METHOD_LOGOUT = 'accounts.logout';
    const METHOD_RESEND_VERIFICATION = 'accounts.resendVerificationCode';
    const DEFAULT_INCLUDE_DATA = 'profile,data,subscriptions,userInfo,preferences,isLockedOut,identities-active';
    const DEFAULT_PROFILE_FIELDS = 'locale,username,timezone';
    const DATA_REG_TOKEN = 'regToken';
    const DATA_EMAIL = 'email';
    const DATA_USERNAME = 'username';
    const DATA_PASSWORD = 'password';
    const DATA_PROFILE = 'profile';
    const DATA_DATA = 'data';

    const GIGYA_PROFILE_KEY = 'profile';
    const GIGYA_INSTITUTE_ROLE_KEY = 'data.eduelt.instituteRole';
    const GIGYA_SUBSCRIPTIONS_KEY = 'subscriptions';
    const GIGYA_IS_ACTIVE_KEY = 'isActive';
    const GIGYA_ADDLOGIN_EMAILS_KEY = 'addLoginEmails';
    const GIGYA_REMOVELOGIN_EMAILS_KEY = 'removeLoginEmails';

    private $uid;

    public function __construct($uid = false)
    {
        $this->uid = $uid;
    }

    public function setUID($uid)
    {
        $this->uid = $uid;
    }

    /**
     * Login user. Returns user data if credentials are valid.
     * @param $username
     * @param $password
     * @return GigyaAccountData
     */
    public function login($username, $password, $include = null)
    {
        // GCAP-784 modified by machua 30032020 to get profile.data in the response
        $request = $this->newRequest(static::METHOD_LOGIN);
        $request->setParam("loginID", $username);
        $request->setParam("password", $password);
        if (!is_null($include)) {
            $request->setParam("include", $include);
        }

        return new GigyaAccountData($request->send());
    }

    /**
     * Get user account info.
     * @param null $include
     * @param null $extraProfileFields
     * @return GigyaAccountData
     */
    public function getAccountInfo($include = null, $extraProfileFields = null)
    {
        $include = !is_null($include)
            ? $include
            : static::DEFAULT_INCLUDE_DATA;
        $extraProfileFields = !is_null($extraProfileFields)
            ? $extraProfileFields
            : static::DEFAULT_PROFILE_FIELDS;

        $request = $this->newRequest(static::METHOD_GET_ACCOUNT_INFO);
        $request->setParam('UID', $this->uid);
        $request->setParam('include', $include);
        $request->setParam('extraProfileFields', $extraProfileFields);

        $accountData = new GigyaAccountData($request->send());
        $accountData->setUID($this->uid);

        return $accountData;
    }

    /**
     * GCAP-238 Added by Eleirold Asuncion <easuncion@cambridge.org> 01-10-2019
     * Archive User. Disables the login of the user.
     * @return bool|GSResponse
     */
    public function activateOrArchiveAccount($value)
    {
        $request = $this->newRequest(static::METHOD_SET_ACCOUNT_INFO);
        $request->setParam('UID', $this->uid);
        $request->setParam('isActive', $value);

        return $this->handleResponse($request->send());
    }

    // GCAP-530 Added by Shane Camus 11/06/19
    public function verifyAccount()
    {
        $request = $this->newRequest(static::METHOD_SET_ACCOUNT_INFO);
        $request->setParam('UID', $this->uid);
        $request->setParam('isVerified', 'true');
        $response = $this->handleResponse($request->send());

        return $response->GetErrorCode() === 0;
    }

    /** 
    * GCAP-285 Added by Carl Lewi Godoy <cgodoy@cambridge.org> 02-15-2019
    * Check user if verified before Login submission.
    * @return bool|undefined
    */
    public function checkUserVerification($loginID)
    {
        $result = $this->searchUserByEmail($loginID)->getArray('results')->getArray('0');
        if (empty($result)) {
            return undefined;
        }

        return $result->getBool('isVerified');
    }

    // GCAP-839 modified by mtanada 20200420, mtanada 20210120
    public function searchUserByEmail($email)
    {
        // Emails will be search with no case-sensitivity
        $query = "SELECT * FROM accounts WHERE identities.email contains '$email'";
        return $this->search($query);
    }

    /* SB-571 Added by mtanada 20200518
     * Used emailAccounts for checking of IdP instead of accounts
     * to cover the Lite and Lite-Full accounts
     */
    public function searchUserIdpByEmail($email)
    {
        $loginID = strtolower($email);
        $query = "SELECT data.systemIDs.idType FROM emailAccounts WHERE email = '$loginID' OR email = '$email'";

        return $this->search($query);
    }

    /* SB-571 Added by mtanada 20200519
     * Filter the System IDs of the user
     * Provider data is not populated if the account is Lite
     */
    public function getUserIdpByEmail($email)
    {
        $result = $this->searchUserIdpByEmail($email);
        $isNull = is_null($result->getArray('results')->getArray('0'));
        if ($isNull) {
            return false;
        }
        $data = $result->getArray('results')->getArray('0')->getObject('data');

        if (!empty($data) && !empty((array) $data)) {
            return $result->getArray('results')->getArray('0')->getObject('data')->getObject('systemIDs');
        }
        return false;
    }

    // SB-520 modified by mabrigos 20200323
    public function getProfileByEmail($email)
    {
        $result = $this->searchUserByEmail($email);
        $isEmpty = empty($result->getArray('results')->getArray('0'));
        if (!$isEmpty) {
            return [
                'UID' => $result->getArray('results')->getArray('0')->getString('UID'),
                'profile' => $result->getArray('results')->getArray('0')->getObject('profile')->toJsonString()
            ];
        }
        return false;
    }

    // GCAP-1240 Added by mabrigos 20210223
    public function searchUserByEmailAndRole($email, $role)
    {
        $query = "SELECT * FROM accounts WHERE identities.email CONTAINS '$email' ";
        $query .= "AND data.eduelt.instituteRole.role CONTAINS '$role'";
        
        $result = $this->search($query);
        $isEmpty = empty($result->getArray('results')->getArray('0'));

        if (!$isEmpty) {
            return [
                'UID' => $result->getArray('results')->getArray('0')->getString('UID'),
                'profile' => $result->getArray('results')->getArray('0')->getObject('profile')->toJsonString(),
                'instituteRole' => $result->getArray('results')->getArray('0')->getObject('data')->getObject('eduelt')->getArray('instituteRole')->toJsonString()
            ];
        }
        return false;
    }

    // GCAP-504 Campion modified by mtanada 20190930
    public function searchUserWithoutGUID($userId)
    {
        $query = 'SELECT * FROM accounts WHERE (data.systemIDs.idType = "GO" OR data.systemIDs.idType = "go") AND data.systemIDs.idValue = ' .
            (int)$userId;
        $result = $this->search($query);
        return $result->getArray('results')->getArray('0')->getArray('identities')
            ->getArray('0')->getString('providerUID');
    }

    // GCAP-839 modified by mtanada 20200420 to include CLS teachers
    public function searchAllGoTeachers($limit = 15, $page = 1)
    {
        $start = 0;
        $page = (int)$page;

        if($page > 1) {
            $start = ($page * $limit) - $limit;
        }

        $query = "select UID, profile, created, data.eduelt, preferences from accounts ";
        $query .= "where isActive = 'true' and isVerified = 'true' ";
        $query .= "and data.eduelt.instituteRole.role contains 'teacher' and (data.systemIDs.idType contains 'GO' ";
        $query .= "OR data.systemIDs.idType contains 'go' OR preferences.terms.Go.isConsentGranted = 'true' ";
        $query .= "OR preferences.terms.go.isConsentGranted = 'true') ";
        $query .= "order by created desc limit $limit start $start";

        return $this->search($query);
    }

    public function getUsersByOIDAndRole($oid, $isPaginated = false, $options, $role = 'student')
    {
         
        $query = "SELECT UID, profile.firstName, profile.lastName, profile.email, data.eduelt.instituteRole.role, data.eduelt.instituteRole.key_s FROM accounts ";
        $query .= "WHERE data.eduelt.instituteRole.key_s CONTAINS '$oid'";
        $query .= "AND data.eduelt.instituteRole.role CONTAINS '$role' ";

        if (!$isPaginated) {
            return $this->search($query);
        }

        $start = 0;
        $page = (int)$options['page'];
        $limit = (int)$options['limit'];

        if ($page > 1) {
            $start = ($page * $limit) - $limit;
        }

        $query .= "LIMIT $limit START $start";

        return $this->search($query);
    }

    // GCAP-530 Modified by Shane Camus 10/25/19
    public function searchUsersByGoID($users)
    {
        $ids = array_column($users, 'uID');

        $query = "SELECT UID, profile, data FROM accounts ";
        $query .= "WHERE data.systemIDs.idType = 'go' AND data.systemIDs.idValue IN ('" . implode("','", $ids) . "')";
        return $this->search($query)->GetData()->ToString();
    }

    public function searchUsersByGigyaUID($users)
    {
        $ids = array_column($users, 'uID');

        $query = "SELECT UID, profile, data FROM accounts ";
        $query .= "WHERE (data.systemIDs.idType = 'GO' OR data.systemIDs.idType = 'go') ";
        $query .= "AND UID IN ('" . implode("','", $ids) . "')";

        $result = $this->search($query);

        if (!$result) {
            return false;
        }
        // GCAP-1046 modified by mabrigos 20201123
        return $result->GetData()->ToString();
    }

    // GCAP-844 Added by machua 20200511 
    public function searchUserByGOUID($goUID)
    {
        $query = 'SELECT * FROM accounts';
        $query .= ' WHERE (data.systemIDs.idType = "GO" OR data.systemIDs.idType = "go") AND data.systemIDs.idValue = ' .
            (int)$goUID;
        $result = $this->search($query);
        $responseText = $result->GetResponseText();
        $responseJSON = json_decode($responseText);
        if ($responseJSON->objectsCount > 0) {
            return $responseJSON->results[0];
        } else {
            return null;
        }
    }

    // GCAP-541 Campion added by machua/mtanada 20191016
    public function searchLiteUsersBySystemId($users)
    {
        $ids = array_column($users, 'uID');

        $query = "SELECT data.systemIDs, profile FROM emailAccounts
            WHERE (data.systemIDs.idType = 'GO' OR data.systemIDs.idType = 'go')
            AND data.systemIDs.idValue IN ('" . implode("','", $ids) . "');";

        return $this->search($query)->GetData()->ToString();
    }

    public function searchLiteUsersByGigyaUID($users)
    {
        $ids = array_column($users, 'uID');

        $query = "SELECT data.systemIDs, profile FROM emailAccounts
            WHERE (data.systemIDs.idType = 'GO' OR data.systemIDs.idType = 'go')
            AND UID IN ('" . implode("','", $ids) . "');";

        return $this->search($query)->GetData()->ToString();
    }

    // GCAP-1042 added by mtanada 20210629
    public function searchLiteUsersByEmail($users)
    {
        $emails = array_column($users, 'Email');
        // SB-912 modified by mabrigos
        $query = "SELECT UID, data.systemIDs, profile FROM emailAccounts
            WHERE (data.systemIDs.idType = 'GO' OR data.systemIDs.idType = 'go')
            AND profile.email IN ('" . implode("','", $emails) . "');";

        return $this->search($query)->GetData()->ToString();
    }

    public function getUserPreferences($email)
    {
        $info = $this->searchUserByEmail($email);

        return $info->getArray('results')
            ->getObject('0')
            ->getObject('preferences')
            ->getArray('terms')
            ->getArray('hub')
            ->getInt('isConsentGranted');
    }

    public function search($query)
    {
        $request = $this->newRequest(static::METHOD_SEARCH);
        $request->setParam('query', $query);

        return $this->handleResponse($request->send());
    }

    /**
     * Sets Gigya user info.
     *
     * @param array $data
     * @return bool
     */
    public function editUserInfo($gigyaID, array $data)
    {
        $gUtils = new GigyaUtils();
        $reducedData = $gUtils->reduceRequestData($data);
        $parsedData = $gUtils->parseRequestData($reducedData);

        $request = $this->newRequest(static::METHOD_SET_ACCOUNT_INFO);
        $request->setParam("UID", $gigyaID);

        if (!empty($parsedData[static::GIGYA_PROFILE_KEY])) {
            $request->setParam(
                static::GIGYA_PROFILE_KEY,
                json_encode($parsedData[static::GIGYA_PROFILE_KEY])
            );
        }

        if (!empty($parsedData[static::GIGYA_INSTITUTE_ROLE_KEY])) {
            $eduData = ['eduelt' => ['instituteRole' => $parsedData[static::GIGYA_INSTITUTE_ROLE_KEY]]];
            $request->setParam('data', json_encode($eduData));
        }

        if (!empty($parsedData[static::GIGYA_IS_ACTIVE_KEY])) {
            $request->setParam(static::GIGYA_IS_ACTIVE_KEY, $parsedData[static::GIGYA_IS_ACTIVE_KEY]);
        }

        if (!empty($parsedData[static::GIGYA_ADDLOGIN_EMAILS_KEY])) {
            $request->setParam(static::GIGYA_ADDLOGIN_EMAILS_KEY, $parsedData[static::GIGYA_ADDLOGIN_EMAILS_KEY]);
        }

        if (!empty($parsedData[static::GIGYA_REMOVELOGIN_EMAILS_KEY])) {
            $request->setParam(static::GIGYA_REMOVELOGIN_EMAILS_KEY, $parsedData[static::GIGYA_REMOVELOGIN_EMAILS_KEY]);
        }

        return $this->handleResponse($request->send());
    }

    public function initRegistration()
    {
        $request = $this->newRequest(static::METHOD_INIT_REGISTRATION);

        $response = $request->send();
        if ($response->getErrorCode() !== 0) {
            return false;
        }

        return $response->getString(static::DATA_REG_TOKEN);
    }

    public function register($data)
    {
        $regToken = $this->initRegistration();

        if (!$regToken) {
            return false;
        }

        $data[static::DATA_REG_TOKEN] = $regToken;

        $parameters = new GSObject(json_encode($data));

        $request = $this->newRequest(static::METHOD_REGISTER, $parameters);
        $response = $request->send();

        if ($response->getErrorCode() === 0) {
            return $this->finalizeRegistration($regToken);
        }

        return false;
    }

    public function finalizeRegistration($regToken)
    {
        $request = $this->newRequest(static::METHOD_FINALIZE_REGISTRATION);
        $request->setParam(static::DATA_REG_TOKEN, $regToken);

        $response = $request->send();
        if ($response->getErrorCode() === 0) {
            return true;
        }

        return false;
    }

    public function setSystemId($systemId)
    {
        $request = $this->newRequest(static::METHOD_SET_ACCOUNT_INFO);
        $request->setParam('UID', $this->uid);
        $request->setParam('data', json_encode([
            'systemIDs' => [
                [
                    'idType' => 'GO',
                    'idValue' => $systemId
                ]
            ]
        ]));

        return $this->handleResponse($request->send());
    }

    /**
     * GCAP-CAMPION added by mtanada 20190924
     * Logout user in Gigya.
     * @param $sytemID
     */
    public function gigyaLogoutBySystemID($sytemID = false)
    {
        $UID = $this->searchUserWithoutGUID($sytemID);
        $request = $this->newRequest(static::METHOD_LOGOUT);
        $request->setParam('UID', $UID);
        return $this->handleResponse($request->send());
    }

    public function resendVerificationEmail($uid)
    {
        $request = $this->newRequest(static::METHOD_RESEND_VERIFICATION);
        $request->setParam('UID', $uid);
        return $this->handleResponse($request->send());
    }

    /**
     * SB-611 Added by mtanada 20200717
     * Fetch Gigya user data using email/s
     * @param array
     */
    public function searchUsersByEmails($emails)
    {
        $users = [];
        $query = "SELECT UID, profile, data.eduelt, data.systemIDs FROM emailAccounts WHERE ";
        // SB-753 modified by mabrigos 20210119
        foreach($emails as $email) {
            $query .= "profile.email contains '$email' OR ";
        }

        $result = $this->search(substr($query, 0, -3));
        if (!$result) {
            return false;
        }

        $result = $result->GetData()->ToString();
        $data = json_decode($result);

        if ($data->totalCount > 0) {
            foreach ($data->results as $gigyaUser) {
                $systemID = null;
                $systemIDArr = [];
                if ($gigyaUser->data->systemIDs) {
                    $systemIDArr = array_filter($gigyaUser->data->systemIDs, function ($id) {
                        return $id->idType === 'GO' || $id->idType === 'go';
                    });
                }

                if (count($systemIDArr) > 0) {
                    $systemID = $systemIDArr[0]->idValue;
                }
                $params = [
                    'UID'       => $gigyaUser->UID,
                    'email'     => $gigyaUser->profile->email,
                    'firstName' => $gigyaUser->profile->firstName,
                    'lastName'  => $gigyaUser->profile->lastName,
                    'role'      => $gigyaUser->data->eduelt->instituteRole[0]->role,
                    'systemID'  => $systemID
                ];
                $users[] = $params;
            }
        }

        return $users;
    }

    // GCAP-1286 Added by mabrigos 20210428
    public function fetchUsersWithCountryByUid($userIds)
    {
        $query = "SELECT UID, profile.email, profile.firstName, profile.lastName, ";
        $query .= "profile.country, data.eduelt FROM accounts ";
        $query .= "WHERE UID IN ('" . implode("','", $userIds) . "') ";
        $query .= "LIMIT 5000";
        $result = $this->search($query);

        if (!$result) {
            return false;
        }

        $data = json_decode($result->GetData()->ToString());
        $users = array();

        if ($data->totalCount > 0) {
            foreach ($data->results as $gigyaUser) {
                $params = [
                    'UID'           => $gigyaUser->UID,
                    'email'         => $gigyaUser->profile->email,
                    'firstName'     => $gigyaUser->profile->firstName,
                    'lastName'      => $gigyaUser->profile->lastName,
                    'country'       => $gigyaUser->profile->country,
                    'institution'   => $gigyaUser->data->eduelt->instituteRole
                ];
                $users[] = $params;
            }
        }
        return $users;
    }

    public function getUsersByOID($oids)
    {
        $query = "SELECT UID, profile.firstName, profile.lastName, ";
        $query .= "profile.email, data.eduelt.instituteRole ";
        $query .= "FROM accounts ";
        $query .= "WHERE data.eduelt.instituteRole.key_s IN ";
        $query .= "(\"" . implode('","', $oids)  . "\")";
        $query .= "AND (preferences.terms.go.isConsentGranted = true OR ";
        $query .= "preferences.terms.Go.isConsentGranted = true OR ";
        $query .= "data.systemIDs.idType = \"go\" OR ";
        $query .= "data.systemIDs.idType = \"Go\")";

        $result = $this->search($query);
        return $result->GetData()->toJsonString();
    }
}
