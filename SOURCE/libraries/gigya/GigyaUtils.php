<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 14/01/2019
 * Time: 10:29 AM
 */

class GigyaUtils
{
    const GIGYA_PROFILE_KEY = 'profile';
    const GIGYA_INSTITUTE_ROLE_KEY = 'data.eduelt.instituteRole';
    const GIGYA_SUBSCRIPTIONS_KEY = 'subscriptions';
    const GIGYA_IS_ACTIVE_KEY = 'isActive';
    const GIGYA_ADDLOGIN_EMAILS_KEY = 'addLoginEmails';
    const GIGYA_REMOVELOGIN_EMAILS_KEY = 'removeLoginEmails';

    /**
     * Defines mapping between the input and Gigya keys. This mapping is soleley based on the Go Users module.
     * Left side (keys) are input names
     * Right side (values) are Gigya keys
     * @var array
     */
    private $gigyaGoUsersUserSet = [
        'profile' => [
            'firstname' => 'firstName',
            'lastname' => 'lastName',
            'phonenumber' => 'phones',
        ],
        'data.eduelt.instituteRole' => [
            'usertype' => 'role',
            'school' => 'institute',
            'title' => 'title'
        ]
    ];

    /**
     * Reduces or removes array elements with empty values.
     *
     * @param array $data
     * @return array
     */
    public function reduceRequestData(array $data)
    {
        foreach ($data as $index => $datum) {
            if ($datum === '') {
                unset($data[$index]);
            }
        }
        return $data;
    }

    /**
     * Parses the request to a readily Gigya form.
     *
     * @param array $data
     * @return array
     */
    public function parseRequestData(array $data)
    {
        $profileKeys = array_keys($this->gigyaGoUsersUserSet[static::GIGYA_PROFILE_KEY]);
        $dataKeys = array_keys($this->gigyaGoUsersUserSet[static::GIGYA_INSTITUTE_ROLE_KEY]);

        $parsedData = $profile = $instituteRole = array();

        foreach ($data as $index => $datum) {
            if (in_array($index, $profileKeys)) {
                if ($index === 'phonenumber') {
                    $datum = ['type' => 'Personal', 'number' => $datum];
                }
                $profile[$this->gigyaGoUsersUserSet[static::GIGYA_PROFILE_KEY][$index]] = $datum;
            }

            if (in_array($index, $dataKeys)) {
                $instituteRole[$this->gigyaGoUsersUserSet[static::GIGYA_INSTITUTE_ROLE_KEY][$index]] = $datum;
            }

            if ($index === 'customercare') {
                $parsedData[static::GIGYA_SUBSCRIPTIONS_KEY] = ['media' => 'email', 'support' => $datum];
            }

            if ($index === 'verified') {
                if($datum === '0') {
                    $parsedData[static::GIGYA_REMOVELOGIN_EMAILS_KEY] = $data['email'];
                } else {
                    $parsedData[static::GIGYA_ADDLOGIN_EMAILS_KEY] = $data['email'];
                }
            }

            if ($index === 'active') {
                $parsedData[static::GIGYA_IS_ACTIVE_KEY] = $datum === '1' ? 'true' : 'false';
            }
        }

        $parsedData[static::GIGYA_PROFILE_KEY] = $profile;
        $parsedData[static::GIGYA_INSTITUTE_ROLE_KEY] = [$instituteRole];

        return $parsedData;
    }

    /**
     * Parses data from Provisioning to readily readable to Gigya form.
     *
     * @param array $data
     * @return array
     */
    public function parseRequestDataFromProvisioning(array $data)
    {
        $time = new DateTime();
        return [
            'password' => $data[1],
            'email' => $data[0],
            'profile' => [
                'firstName' => $data[2],
                'lastName' => $data[3],
                'email' => $data[0]
            ],
            'data' => [
                'eduelt' => [
                    'instituteRole' => [
                        'role' => strtolower($data[7])
                    ]
                ],
                'press' => [
                    'terms' => [
                        'v1' => [
                            'blnAccepted' => 'true',
                            'dateAccepted' => $time->format(DateTime::ATOM)
                        ]
                    ]
                ]
            ]
        ];
    }
}