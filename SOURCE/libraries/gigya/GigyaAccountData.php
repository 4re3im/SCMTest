<?php

Loader::library('gigya/GSSDK');

class GigyaAccountData
{
    const FIELD_PROFILE = 'profile';
    const FIELD_DATA = 'data';
    const FIELD_DATA_SYSTEM_ID = 'systemIDs.ID';
    const FIELD_DATA_EDU_ELT = 'eduelt';
    const FIELD_DATA_EDU_ELT_INSTITUTE_ROLE = 'instituteRole';
    const FIELD_DATA_EDU_ELT_INSTITUTE = 'institute';
    const FIELD_DATA_EDU_ELT_ROLE = 'role';
    const FIELD_PROFILE_FIRST_NAME = 'firstName';
    const FIELD_PROFILE_LAST_NAME = 'lastName';
    const FIELD_PROFILE_STATE = 'state';
    const FIELD_DATA_SYSTEM_IDS = 'systemIDs';
    const FIELD_DATA_SYSTEM_ID_ID_TYPE = 'idType';
    const FIELD_DATA_SYSTEM_ID_ID_VALUE = 'idValue';
    const DEFAULT_DATA_SYSTEM_ID_ID_TYPE = 'GO';
    const FIELD_UID = 'UID';
    // GCAP-839 added by mtanada 20200428
    const FIELD_PREFERENCES = 'preferences';

    private $uid;
    private $data;
    private $isValidUser = false;

    public function __construct(GSResponse $data)
    {
        $this->data = $data;

        if ($data->getInt('errorCode') === 0) {
            $this->isValidUser = true;
        }
    }

    public function getFullName()
    {
        $profile = $this->getProfile();

        if (!$profile) {
            return null;
        }

        return sprintf(
            '%s %s',
            $profile->getString(static::FIELD_PROFILE_FIRST_NAME),
            $profile->getString(static::FIELD_PROFILE_LAST_NAME)
        );
    }

    public function getFirstName()
    {
        $profile = $this->getProfile();

        if (!$profile) {
            return null;
        }

        try {
            return $profile->getString(static::FIELD_PROFILE_FIRST_NAME);
        } catch (GSKeyNotFoundException $e) {
            return null;
        }
    }

    public function getLastName()
    {
        $profile = $this->getProfile();

        if (!$profile) {
            return null;
        }

        try {
            return $profile->getString(static::FIELD_PROFILE_LAST_NAME);
        } catch (GSKeyNotFoundException $e) {
            return null;
        }
    }


    public function getLocale()
    {
        $profile = $this->getProfile();

        if (!$profile) {
            return null;
        }

        try {
            $locale = $profile->getString('locale');
        } catch (GSKeyNotFoundException $e) {
            return null;
        }

        if ($locale === 'en' || !$locale) {
            return 'en_US';
        }

        return $locale;
    }

    public function getProfile()
    {
        try {
            return $this->data->getArray(static::FIELD_PROFILE);
        } catch (GSKeyNotFoundException $e) {
            return null;
        }
    }

    public function isValid()
    {
        return $this->isValidUser;
    }

    public function getSystemID()
    {
        try {
            $data = $this->data->getArray(static::FIELD_DATA);

            if (!$data) {
                throw new GSKeyNotFoundException('Missing data field.');
            }

            $systemIDs = $data->getArray(static::FIELD_DATA_SYSTEM_IDS);

            if (!$systemIDs) {
                throw new GSKeyNotFoundException('Missing systemIDs field.');
            }

            $systemIDs = json_decode($systemIDs->toString(), true);

            $goSystemID = array_filter($systemIDs, function ($systemId) {
                if (!is_array($systemId)) {
                    return false;
                }

                $idType = strtoupper($systemId[static::FIELD_DATA_SYSTEM_ID_ID_TYPE]);

                return $idType === static::DEFAULT_DATA_SYSTEM_ID_ID_TYPE;
            });

            if (!$goSystemID) {
                throw new GSKeyNotFoundException('No GO systemID.');
            }

            $goSystemID = array_pop($goSystemID);

            if (!isset($goSystemID[static::FIELD_DATA_SYSTEM_ID_ID_VALUE])) {
                throw new GSKeyNotFoundException('SystemID has no value.');
            }

            return $goSystemID[static::FIELD_DATA_SYSTEM_ID_ID_VALUE];
        } catch (GSKeyNotFoundException $e) {
            return false;
        }
    }

    public function getIsActive()
    {
        try {
            return $this->data->getBool('isActive');
        } catch (GSKeyNotFoundException $e) {
            return null;
        }
    }

    public function getTimezone()
    {
        try {
            $profile = $this->getProfile();
            if(!$profile) {
                return null;
            }
            return $profile->getString('timezone');
        } catch (GSKeyNotFoundException $e) {
            return null;
        }
    }

    public function getIsVerified()
    {
        try {
            return $this->data->getBool('isVerified');
        } catch (GSKeyNotFoundException $e) {
            return null;
        }
    }

    public function setUID($uid)
    {
        $this->uid = $uid;
    }

    public function getUID()
    {
        return $this->uid;
    }

    public function getEmail()
    {
        $profile = $this->getProfile();

        if (!$profile) {
            return null;
        }

        try {
            return $profile->getString('email');
        } catch (GSKeyNotFoundException $e) {
            return null;
        }
    }

    public function getData()
    {
        try {
            return $this->data->getArray('data');
        } catch (GSKeyNotFoundException $e) {
            return null;
        }
    }

    public function getRole()
    {
        try {
            $data = $this->data->getArray(static::FIELD_DATA);

            if (!$data) {
                return null;
            }

            $eduElt = $data->getArray(static::FIELD_DATA_EDU_ELT);

            if (!$eduElt) {
                return null;
            }

            $instituteRole = $eduElt->getArray(
                static::FIELD_DATA_EDU_ELT_INSTITUTE_ROLE
            );


            if (!$instituteRole) {
                return null;
            }

            // For now we always get the initial entry in the array.
            $instituteRole = $instituteRole->getObject(0);

            return $instituteRole->getString(static::FIELD_DATA_EDU_ELT_ROLE);
        } catch (GSKeyNotFoundException $e) {
            return null;
        }
    }

    public function getSchoolName()
    {
        try {
            $data = $this->data->getArray(static::FIELD_DATA);

            if (!$data) {
                return null;
            }

            $eduElt = $data->getArray(static::FIELD_DATA_EDU_ELT);

            if (!$eduElt) {
                return null;
            }

            $instituteRole = $eduElt->getArray(
                static::FIELD_DATA_EDU_ELT_INSTITUTE_ROLE
            );

            if (!$instituteRole) {
                return null;
            }

            $instituteRole = $instituteRole->getObject(0);

            return $instituteRole->getString(static::FIELD_DATA_EDU_ELT_INSTITUTE);
        } catch (GSKeyNotFoundException $e) {
            return null;
        }
    }

    public function getPostcode()
    {
        $profile = $this->getProfile();

        if (!$profile) {
            return null;
        }

        try {
            return $profile->getString(static::FIELD_PROFILE_STATE);
        } catch (GSKeyNotFoundException $e) {
            return null;
        }

    }

    // GCAP-784 addded by machua 30032020 get Gigya UID when user is logged in using Gigya API
    public function getUIDFromData()
    {
        try {
            $uid = $this->data->getString(static::FIELD_UID);

            if (!$uid) {
                throw new GSKeyNotFoundException('Missing UID field.');
            }

            return $uid;
        } catch (GSKeyNotFoundException $e) {
            return false;
        }
    }

    // SB-572 added by machua 20200520 to check if the login has an error
    public function getErrorCode()
    {
        return $this->data->GetErrorCode();
    }

    /* GCAP-839 added by mtanada 20200428
     * Get the origin platform from the user's agreed terms
     * based on preferences gigya data
     */
    public function getOriginPlatform()
    {
        $platforms = array();
        try {
            $preferences = $this->data->getArray(static::FIELD_PREFERENCES);

            if (!$preferences) {
                return null;
            }

            $preference = json_decode($preferences);
            if (!$preference->terms) {
                return 'No terms agreed.';
            }
            foreach ($preference->terms as $key => $value) {
                $platform = $key === 'hub' || $key === 'Hub' ? 'ANZ' : $key;
                $platforms[] = $platform;
            }

            return join(' , ', $platforms);
        } catch (GSKeyNotFoundException $e) {
            return null;
        }
    }
}
