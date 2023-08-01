<?php

Loader::library('AWS/S3/S3Service');

class CWSExport
{
    /**
     * List of users
     * @var
     */
    public $list = [];

    /**
     * Current used filename
     * @var
     */
    private $currentFileName = null;

    /**
     * S3 Bucket to use
     * @var
     */
    private $s3Bucket = null;

    /**
     * Define what function to run
     *
     * @var
     */
    private $mode;

    /**
     * Global S3 Service
     * @var S3Service
     */
    private $S3Service;

    /**
     * default system id value
     * @var idValue
     */
    private $idValue;

    const EDIT_INSTITUTION = 'EDIT_INSTITUTION';
    const ATTRIBUTE_INSTITUTION = 'ATTRIBUTE_INSTITUTION';
    const ATTRIBUTE_IDP = 'ATTRIBUTE_IDP';

    public function __construct($config = null)
    {
        if ($config) {
            if (isset($config['bucket'])) {
                $this->setBucket($config['bucket']);
            }

            if (isset($config['filename'])) {
                $this->setCurrentFilename($config['filename']);
            }

            if (isset($config['data'])) {
                $this->setData($config['data']);
            }
        }

        $this->S3Service = new S3Service();
        $this->S3Service->useCWSConnection();
    }

    public function setBucket($bucket)
    {
        $this->s3Bucket = $bucket;
    }

    public function setCurrentFilename($filename)
    {
        $this->currentFileName = $filename;
    }

    public function setData($data)
    {
        $this->list = $data;
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
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

    public function exportToS3()
    {
        if (!$this->currentFileName ||
            !$this->s3Bucket ||
            empty($this->list) ||
            !$this->mode)
        {
            throw new Error('Please define needed data');
        }


        switch ($this->mode) {
            case static::EDIT_INSTITUTION:
                $path = CWS_GIGYA_IMPORT_SYSREF_UPLOAD_PATH;
                break;
            case static::ATTRIBUTE_INSTITUTION:
            case static::ATTRIBUTE_IDP:
                $path = CWS_GIGYA_SHARED_IMPORT_UPLOAD_PATH;
                break;
        }

        return $this->S3Service->upload(
            $this->s3Bucket,
            $path . $this->currentFileName,
            json_encode($this->list)
        );
    }


    public function attributeInstituteRole($schoolDetails, $uids)
    {
        $oid = $schoolDetails['oid'];

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

    /**
     * SB-868 added by mtanada 2021-06-22
     * Add or create user with IdP attribution using Shared dataflow
     **/
    public function attributeIdP($users, $idp)
    {
        $this->setSytemIdValue($idp);

        foreach ($users as $user) {
            $uID = $user['uID'];
            $firstName = $user['firstName'];
            $lastName = $user['lastName'];
            $state = $user['state'];
            $postCode = $user['postCode'];
            $email = $user['email'];
            $password = $user['password'];
            $isActive = true;
            $isValidated = true;
            $role = strtolower($user['role']);
            $provUID = $user['providerUID'];

            $account = [];
            $account['lang'] = 'en';

            $account['UID'] = $uID;
            $account['data'] = [
                'systemIDs' => [
                    [
                        'idType' => 'go'
                    ],
                    [
                        'idType' => $idp,
                        'idValue' => $this->idValue
                    ]
                ],
                'eduelt' => [
                    'instituteRole' => [
                        [
                            'default' => false,
                            'role' => $role,
                            'isVerified' => false,
                            'title' => null,
                            'institute' => null,
                            'key_s' => null
                        ]
                    ]
                ]
            ];

            if ($idp === 'oidc-lilydalebooks') {
                $account['identities'] = [
                    [
                        "providerUID"=> $provUID,
                        "provider" => $idp,
                        "email" => $email
                    ]
                ];
            } else {
                $account['identities'] = [
                    [
                        "providerUID"=> $email,
                        "provider" => $idp
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

            array_push($this->list, $account);
        }
    }

    /**
     * SB-886 added by mtanada 2021-07-14
     * Add or create user with Institution using Shared dataflow
     **/
    public function attributeInstituteToUsers($users)
    {
        foreach ($users as $user) {
            $uID = $user['uID'];
            $firstName = $user['firstName'];
            $lastName = $user['lastName'];
            $state = $user['state'];
            $postCode = $user['postCode'];
            $email = $user['email'];
            $password = $user['password'];
            $isActive = true;
            $isValidated = true;
            $role = strtolower($user['role']);
            $schoolOid = $user['schoolOid'];

            $account = [];
            $account['lang'] = 'en';

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
                            'default' => false,
                            'isVerified' => true,
                            'role' => $role,
                            'key_s' => $schoolOid
                        ]
                    ]
                ]
            ];

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

            array_push($this->list, $account);
        }
    }
}
