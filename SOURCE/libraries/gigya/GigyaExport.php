<?php

class GigyaExport
{
    const FIELD_ID = 'uID';
    const FIELD_EMAIL = 'email';
    const FIELD_PASSWORD = 'password';
    const FIELD_IS_VALIDATED = 'isValidated';
    const FIELD_IS_ACTIVE = 'isActive';
    const FIELD_FIRST_NAME = 'firstName';
    const FIELD_LAST_NAME = 'lastName';
    const FIELD_SCHOOL_NAME = 'schoolName';
    const FIELD_ROLE = 'role';
    const FIELD_STATE = 'state';
    const FIELD_POST_CODE = 'postCode';

    const PROVISION = 'PROVISION';
    const LITE_PROVISION = 'LITE_PROVISION';
    const RESET_PASSWORD = 'RESET_PASSWORD';
    const BULK_DELETE = 'BULK_DELETE';
    const ATTRIBUTE_INSTITUTION = 'ATTRIBUTE_INSTITUTION';
    const EDIT_INSTITUTION = 'EDIT_INSTITUTION';

    /**
     * List of users
     * @var
     */
    public $list = [];

    /**
     * Current used filename
     * @var
     */
    private $currentFileName;

    private $mode;

    private $s3Bucket;

    /**
     * default system id value
     * @var idValue
     */
    private $idValue;

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function setBucket($bucket)
    {
        $this->s3Bucket = $bucket;
    }

    public function setSytemIdValue($value)
    {
        switch ($value) {
            case 'oidc-boxofbooks':
                $this->idValue = 'generic-user@boxofbooks.com.au';
                break;
            case 'oidc-lilydalebooks':
                $this->idValue = 'generic-user@lilydalebooks.com.au';
                break;
            case 'saml-Campion-Education':
                $this->idValue = 'generic-user@campion.com.au';
                break;
        }
    }

    /**
     * Adds users to queue for export.
     * GCAP-541 Campion modified by machua/mtanada 20191004
     * @param array $users
     * @param null|int $providerId
     * @param null|bool $isGlobalGoUser
     */
    public function addUsers(array $users, $providerId = null, $isGlobalGoUser = false)
    {
        if ($this->mode === static::RESET_PASSWORD || $this->mode === static::BULK_DELETE) {
            foreach ($users as $user) {
                $this->list[] = implode(',', $user);
            }
        } else {
            if ($providerId) {
                foreach ($users as $user) {
                    $this->addLiteUser($user, $providerId, $isGlobalGoUser);
                }
            } else {
                foreach ($users as $user) {
                    $this->addUser($user, $isGlobalGoUser);
                }
            }
        }

    }

    public function addUser($user, $isGlobalGoUser = false)
    {
        $uID = $user[static::FIELD_ID];
        $firstName = $user[static::FIELD_FIRST_NAME];
        $lastName = $user[static::FIELD_LAST_NAME];
        $state = $user[static::FIELD_STATE];
        $postCode = $user[static::FIELD_POST_CODE];
        $email = $user[static::FIELD_EMAIL];
        $password = $user[static::FIELD_PASSWORD];
        $isActive = true;
        $isValidated = true;
        $role = strtolower($user[static::FIELD_ROLE]);

        $account = [];
        $account['lang'] = 'en';

        if ($isGlobalGoUser) {
            $account['UID'] = $uID;
            $account['data'] = [
                'systemIDs' => [
                    [
                        'idType' => 'go'
                    ]
                ],
                'eduelt' => [
                    'instituteRole' => [
                        [
                            'default' => true,
                            'institute' => '',
                            'isVerified' => false,
                            'role' => $role,
                            'title' => null
                        ]
                    ]
                ]
            ];
        } else {
            $account['UID'] = "go$uID";
            $account['data'] = [
                'systemIDs' => [
                    [
                        'idType' => 'go',
                        'idValue' => $uID
                    ]
                ],
                'eduelt' => [
                    'instituteRole' => [
                        [
                            'default' => true,
                            'institute' => '',
                            'isVerified' => false,
                            'role' => $role,
                            'title' => null
                        ]
                    ]
                ]
            ];
        }

        $account['loginIDs'] = [
            'emails' => [$email]
        ];

        $account['profile'] = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'state' => $state,
            'zip' => $postCode
        ];

        $verifiedKey = $isValidated
            ? 'verified'
            : 'unverified';

        $account['emails'] = [
            $verifiedKey => [$email]
        ];

        $account['isVerified'] = $isValidated;
        $account['isActive'] = $isActive;
        $account['isRegistered'] = true;
        $account['password'] = ['compoundHashedPassword' => $password];

        $this->list[] = $account;
    }

    public function addInstitution($json)
    {
        $this->list = $json;
    }

    public function setCurrentFilename($filename)
    {
        $this->currentFileName = $filename;
    }

    public function getList()
    {
        $listToExport = null;
        switch ($this->mode) {
            case static::RESET_PASSWORD:
            case static::BULK_DELETE:
                $listToExport = implode("\n", $this->list);
                return $listToExport;
                break;
            case static::ATTRIBUTE_INSTITUTION:
            default:
                $listToExport = $this->list;
                return json_encode($listToExport);
                break;
        }
    }

    // GCAP-541 Campion modified by machua/mtanada 20191003
    public function exportToS3($providerId = null)
    {
        $listToExport = $this->getList();
        $path = ($providerId) ? GIGYA_S3_UPLOAD_PATH_LITE : GIGYA_S3_UPLOAD_PATH;

        switch ($this->mode) {
            case static::RESET_PASSWORD:
                $path = GIGYA_S3_UPLOAD_PATH_RESET_PASSWORD;
                break;
            case static::BULK_DELETE:
                $path = GIGYA_S3_UPLOAD_PATH_BULK_DELETE;
                break;
            case static::ATTRIBUTE_INSTITUTION:
                $path = GIGYA_S3_UPLOAD_PATH_ATTRIBUTE_INSTITUTION;
                break;
            case static::EDIT_INSTITUTION:
                $path = GIGYA_S3_UPLOAD_PATH_IM;
                break;
        }

        Loader::library('AWS/S3/S3Service');
        $s3Service = new S3Service();
        $s3Service->useGigyaDataFlowConnection();
        return $s3Service->upload(
            GIGYA_S3_BUCKET,
            $path . $this->currentFileName,
            $listToExport
        );
    }

    public function exportFromS3($path)
    {
        Loader::library('AWS/S3/S3Service');
        $s3Service = new S3Service();
        $s3Service->useGigyaDataFlowConnection();
        $currentBucket = GIGYA_S3_BUCKET;

        if ($this->s3Bucket) {
            $currentBucket = $this->s3Bucket;
        }

        $s3Object = $s3Service->getObject($currentBucket, $path);

        if (!$s3Object) {
            return false;
        }

        return $s3Object->get('Body')->getContents();
    }

    // GCAP-541 Campion added by machua/mtanada 20191003
    public function addLiteUser($user, $providerId, $isGlobalGoUser = false)
    {
        $this->setSytemIdValue($providerId);
        $uID = $user[static::FIELD_ID];
        $firstName = $user[static::FIELD_FIRST_NAME];
        $lastName = $user[static::FIELD_LAST_NAME];
        $email = $user[static::FIELD_EMAIL];
        $role = strtolower($user[static::FIELD_ROLE]);

        $account = [];
        $account['lang'] = 'en';
        $account['email'] = $email;

        if ($isGlobalGoUser) {
            $account['UID'] = $uID;
            $account['data'] = [
                'systemIDs' => [
                    [
                        'idType' => 'GO'
                    ],
                    [
                        'idType' => $providerId,
                        'idValue' => $this->idValue
                    ]
                ],
                'eduelt' => [
                    'instituteRole' => [
                        [
                            'default' => true,
                            'institute' => '',
                            'isVerified' => false,
                            'role' => $role,
                            'title' => null
                        ]
                    ]
                ]
            ];
        } else {
            $account['UID'] = "go$uID";
            $account['data'] = [
                'systemIDs' => [
                    [
                        'idType' => 'GO',
                        'idValue' => $uID
                    ],
                    [
                        'idType' => $providerId,
                        'idValue' => $email
                    ]
                ],
                'eduelt' => [
                    'instituteRole' => [
                        [
                            'default' => true,
                            'institute' => '',
                            'isVerified' => false,
                            'role' => $role,
                            'title' => null
                        ]
                    ]
                ]
            ];
        }

        $account['profile'] = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email
        ];

        $this->list[] = $account;
    }

    public function attributeInstituteRole($schoolDetails, $uids)
    {
        $oid = $schoolDetails['oid'];
        $name = $schoolDetails['name'];
        
        foreach ($uids as $role => $uid) {
            if ($uid === '') {
                continue;
            }
            foreach ($uid as $id) {
                $account['lang'] = 'en';
                $account['UID'] = $id;
                $account['data'] = [
                    'eduelt' => [
                        'instituteRole' => [
                            [
                                'default' => false,
                                'institute' => $name,
                                'isVerified' => true,
                                'role' => $role,
                                'key_s' => $oid
                            ]
                        ]
                    ]
                ];
                array_push($this->list, $account);
            }
        }
    }
}
