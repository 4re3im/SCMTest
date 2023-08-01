<?php

/**
 * Description of GoContentsPackage
 * @author  Gerard Paul Balila <pbalila@cambridge.org> and Ariel Tabag <atabag@cambridge.org> 2015
 */

define('MODEL', 'model');
define('C_NAME', 'cName');
define('SELECT', 'select');
define('HANDLE', 'handle');
define('AK_NAME', 'akName');
define('BOOLEAN', 'boolean');
define('AK_HANDLE', 'akHandle');
define('ATTRIBUTE', 'attribute');
define('ATTRIBUTES', 'attributes');
define('EXCLUDE_NAV', 'exclude_nav');
define('DISPLAY_ORDER', 'displayOrder');
define('AK_IS_EDITABLE', 'akIsEditable');
define('UAK_REGISTER_EDIT', 'uakRegisterEdit');
define('UAK_PROFILE_DISPLAY', 'uakProfileDisplay');
define('UAK_MEMBER_LIST_DISPLAY', 'uakMemberListDisplay');

class GoContentsPackage extends Package
{
    protected $pkgHandle = 'go_contents';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '3.0.1';

    protected $singlePages = array(
        '/go/menu' => array('data' => array(C_NAME => 'menu'), ATTRIBUTES => array(EXCLUDE_NAV => true)),
        '/go/signup' => array('data' => array(C_NAME => 'signup'), ATTRIBUTES => array(EXCLUDE_NAV => true)),
        '/go/login' => array('data' => array(C_NAME => 'go login'), ATTRIBUTES => array(EXCLUDE_NAV => true)),
        '/go/contact' => array('data' => array(C_NAME => 'contact'), ATTRIBUTES => array(EXCLUDE_NAV => true)),
        '/go/activate' => array('data' => array(C_NAME => 'activate'), ATTRIBUTES => array(EXCLUDE_NAV => true)),
        '/go/do_login' => array('data' => array(C_NAME => 'do login'), ATTRIBUTES => array(EXCLUDE_NAV => true)),
        '/go/terms' => array('data' => array(C_NAME => 'Terms of Use'), ATTRIBUTES => array(EXCLUDE_NAV => true)),
        '/activate' => array('data' => array(C_NAME => 'Activate Page'), ATTRIBUTES => array(EXCLUDE_NAV => true)),
        '/go/account' => array('data' => array(C_NAME => 'User Account'), ATTRIBUTES => array(EXCLUDE_NAV => true)),
        '/go/myresources' => array('data' => array(C_NAME => 'myresources'), ATTRIBUTES => array(EXCLUDE_NAV => true)),
        '/go/myresources_v2' => array('data' => array(C_NAME => 'myresources_v2'), ATTRIBUTES => array(EXCLUDE_NAV => true)),
        '/go/resend_verification' => array(
            'data' => array(C_NAME => 'Resend Verification'), 
            ATTRIBUTES => array(EXCLUDE_NAV => true)),
        '/go/privacy' => array(
            'data' => array(C_NAME => 'Privacy Statement'),
            ATTRIBUTES => array(EXCLUDE_NAV => true)
        ),
        '/go/notifications' => array(
            'data' => array(C_NAME => 'notifications'),
            ATTRIBUTES => array(EXCLUDE_NAV => true)
        ),
        '/go/user_landing' => array(
            'data' => array(C_NAME => 'user landing page'),
            ATTRIBUTES => array(EXCLUDE_NAV => true)
        ),
        '/go/register_success' => array(
            'data' => array(C_NAME => 'register success'),
            ATTRIBUTES => array(EXCLUDE_NAV => true)
        ),
        '/go/forgot_password/' => array(
            'data' => array(C_NAME => 'Forgot Password landing page'),
            ATTRIBUTES => array(EXCLUDE_NAV => true)
        ),
        '/go/interactive_textbook' => array(
            'data' => array(C_NAME => 'Interactive Textbook'),
            ATTRIBUTES => array(EXCLUDE_NAV => true)
        ),
        '/dashboard/notification/actions' => array(
            'data' => array(C_NAME => 'Search and Add Notifications'),
            ATTRIBUTES => array(EXCLUDE_NAV => true)
        ),
        '/dashboard/notification/edit' => array(
            'data' => array(C_NAME => 'Edit Notifications'),
            ATTRIBUTES => array(EXCLUDE_NAV => true)
        ),
        // ANZGO-3293
        '/redirect' => array(
            'data' => array(C_NAME => 'Redirector', 'cDescription' => 'Redirect for Epub'),
            ATTRIBUTES => array(EXCLUDE_NAV => true)
        ),
        // ANZGO-3789 added by jbernardez 20180706
        '/dashboard/notification/announce' => array(
            'data' => array(C_NAME => 'Announcement Banner'),
            ATTRIBUTES => array(EXCLUDE_NAV => true)
        )
    );

    protected $blocks = array('go_notifications', 'go_support');

    protected $attributeSets = array(
        'uLoginDetails' => 'Login Details',
        'uContactDetails' => 'Contact Details',
        'uCheckBoxes' => 'Checkboxes',
        'uTeacherContactDetails' => 'Contact Details - Teachers Only',
        'uTeacherCheckBoxes' => 'Checkboxes - Teachers Only',
        'uAllowMarketing' => 'Allow Marketing'
    );

    /**
     * Array of Attributes to be added to Attribute Sets. Note that the key should be the
     * handle of that Attribute Set
     * @var Array
     */
    protected $attributes = array(
        'uLoginDetails' => array(
            array(
                HANDLE => 'text',
                ATTRIBUTE => array(
                    AK_HANDLE => 'uName',
                    AK_NAME => 'Username',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 1
                )
            ),
            array(
                HANDLE => 'text',
                ATTRIBUTE => array(
                    AK_HANDLE => 'uEmail',
                    AK_NAME => 'Email Address',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 2
                )
            ),
            array(
                HANDLE => 'text',
                ATTRIBUTE => array(
                    AK_HANDLE => 'uPassword',
                    AK_NAME => 'Password',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 3
                )
            ),
            array(
                HANDLE => 'text',
                ATTRIBUTE => array(
                    AK_HANDLE => 'uPasswordConfirm',
                    AK_NAME => 'Confirm Password',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 4
                )
            ),
            array(
                HANDLE => SELECT,
                ATTRIBUTE => array(
                    AK_HANDLE => 'uSecurityQuestion',
                    AK_NAME => 'Security Question',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 5
                )
            ),
            array(
                HANDLE => 'text',
                ATTRIBUTE => array(
                    AK_HANDLE => 'uSecurityAnswer',
                    AK_NAME => 'Security Answer',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 6
                )
            )
        ),
        'uContactDetails' => array(
            array(
                HANDLE => 'text',
                ATTRIBUTE => array(
                    AK_HANDLE => 'uFirstName',
                    AK_NAME => 'First Name',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 3
                )
            ),
            array(
                HANDLE => 'text',
                ATTRIBUTE => array(
                    AK_HANDLE => 'uLastName',
                    AK_NAME => 'Last Name',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 4
                )
            ),
            array(
                HANDLE => 'text',
                ATTRIBUTE => array(
                    AK_HANDLE => 'uSchoolName',
                    AK_NAME => 'School Name',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 5
                )
            )
        ),
        'uCheckBoxes' => array(
            array(
                HANDLE => BOOLEAN,
                ATTRIBUTE => array(
                    AK_HANDLE => 'uPMByEmail',
                    AK_NAME => 'Would like to receive promotional material by email ',
                    UAK_REGISTER_EDIT => 1
                )
            )
        ),
        'uTeacherContactDetails' => array(
            array(
                HANDLE => 'text',
                ATTRIBUTE => array(
                    AK_HANDLE => 'uPositionTitle',
                    AK_NAME => 'Position Title',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    'akSelectAllowMultipleValues' => 0,
                    DISPLAY_ORDER => 6
                )
            ),
            array(
                HANDLE => SELECT,
                ATTRIBUTE => array(
                    AK_HANDLE => 'uPositionType',
                    AK_NAME => 'Position Type',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 7
                )
            ),
            array(
                HANDLE => 'text',
                ATTRIBUTE => array(
                    AK_HANDLE => 'uSchoolPhoneNumber',
                    AK_NAME => 'School Phone Number',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 8
                )
            ),
            array(
                HANDLE => 'text',
                ATTRIBUTE => array(
                    AK_HANDLE => 'uSchoolAddress',
                    AK_NAME => 'School Address',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 9
                )
            ),
            array(
                HANDLE => 'text',
                ATTRIBUTE => array(
                    AK_HANDLE => 'uSuburb',
                    AK_NAME => 'Suburb',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 10
                )
            ),
            array(
                HANDLE => 'text',
                ATTRIBUTE => array(
                    AK_HANDLE => 'uState',
                    AK_NAME => 'State',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 0
                )
            ),
            array(
                HANDLE => SELECT,
                ATTRIBUTE => array(
                    AK_HANDLE => 'uStateUS',
                    AK_NAME => 'US State',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 0
                )
            ),
            array(
                HANDLE => SELECT,
                ATTRIBUTE => array(
                    AK_HANDLE => 'uStateCA',
                    AK_NAME => 'CA State',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 0
                )
            ),
            array(
                HANDLE => SELECT,
                ATTRIBUTE => array(
                    AK_HANDLE => 'uStateAU',
                    AK_NAME => 'AU State',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 0
                )
            ),
            array(
                HANDLE => SELECT,
                ATTRIBUTE => array(
                    AK_HANDLE => 'uStateNZ',
                    AK_NAME => 'NZ State',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 0
                )
            ),
            array(
                HANDLE => 'text',
                ATTRIBUTE => array(
                    AK_HANDLE => 'uPostcode',
                    AK_NAME => 'Postcode',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 12
                )
            ),
            array(
                HANDLE => SELECT,
                ATTRIBUTE => array(
                    AK_HANDLE => 'uCountry',
                    AK_NAME => 'Country',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    DISPLAY_ORDER => 13
                )
            ),
            array(
                HANDLE => SELECT,
                ATTRIBUTE => array(
                    AK_HANDLE => 'uSubjectsTaught',
                    AK_NAME => 'Subjects Taught',
                    AK_IS_EDITABLE => 1,
                    UAK_MEMBER_LIST_DISPLAY => 1,
                    UAK_PROFILE_DISPLAY => 1,
                    UAK_REGISTER_EDIT => 1,
                    'akSelectAllowMultipleValues' => 0,
                    DISPLAY_ORDER => 14
                )
            )
        ),
        'uTeacherCheckBoxes' => array(
            array(
                HANDLE => BOOLEAN,
                ATTRIBUTE => array(
                    AK_HANDLE => 'uPMByRegularPost',
                    AK_NAME => 'Would like to receive promotional material by regular post ',
                    UAK_REGISTER_EDIT => 1
                )
            ),
            array(
                HANDLE => BOOLEAN,
                ATTRIBUTE => array(
                    AK_HANDLE => 'uCustomerCare',
                    AK_NAME => 'Would like to receive customer care emails ',
                    UAK_REGISTER_EDIT => 1
                )
            )
        )
    );

    protected $selectOptionsAttribute = array(
        'uSecurityQuestion' => array(
            'What is your mothers maiden name?',
            'What was the name of your first pet?',
            'Where were you born?',
            'What is the name of the first street lived in?'
        ),
        'uPositionType' => array(
            'Head of Department',
            'Teacher',
            'Student Teacher',
            'Librarian',
            'Principal',
            'Other'
        ),
        'uSubjectsTaught' => array('All','All: Primary','Arts: Drama','Arts: Media','Arts: Music','Arts: Other',
            'Arts: Snr Art','Arts: Studio Arts','Arts: Visual Arts','Arts: Visual Communication and Design',
            'Bus/Com: Accounting','Bus/Com: Business','Bus/Com: Commerce','Bus/Com: Economics','Bus/Com: Legal',
            'Cam Hit Whiteboard','ELT','English','English: Primary','Geography','Graphics NZ','Health & PE: 7-10',
            'Health & PE: Senior','History: 7-10','History: Senior','Hospitality','Hum/HSIE: Humanities Jnr',
            'Hum/HSIE: International','Hum/HSIE: Philosophy','Hum/HSIE: Politics','Hum/HSIE: Religion','ICT: Junior',
            'ICT: Senior','Languages','Maths: 7-10','Maths: Primary','Maths: Senior Secondary',
            'Other','Science - Primary','Science Junior','Science: Biology','Science: Chemistry',
            'Science: Environmental','Science: General','Science: Physics','Science: Psychology','Social Science',
            'Special Needs','Technology: Design','Technology: Food','Technology: Other','VET: Careers','VET: VCAL'
        )
    );

    public function on_start()
    {
        $classes = array(
            'Signup' => array(MODEL, 'signup', $this->pkgHandle),
            'CupGoLogs' => array(MODEL, 'cup_go_logs', $this->pkgHandle),
            'Redirector' => array(MODEL, 'redirector', $this->pkgHandle),
        );
        Loader::registerAutoload($classes);
    }

    public function getPackageDescription()
    {
        return t('Package for Cambridge Go Contents.');
    }

    public function getPackageName()
    {
        return t('Cambridge Go Contents');
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installSinglePages($pkg);
        $this->installBlocks($pkg);
        $this->installUserAttributes($pkg);
        $this->installNotificationModel();
    }

    //this will need to be overridden by child classes. they can call parent::upgrade to get the pkgID
    public function upgrade()
    {
        parent::upgrade();
        $pkg = Package::getByHandle($this->pkgHandle);
        $this->installSinglePages($pkg);
        // $this->installBlocks($pkg);
        // $this->installUserAttributes($pkg);
        // $this->installNotificationModel();
        return $pkg;
    }

    protected function installSinglePages($pkg)
    {
        foreach ($this->singlePages as $path => $options) {
            $cID = Page::getByPath($path)->getCollectionID();

            if (intval($cID) > 0 && $cID !== 1) {
                $sql = 'UPDATE Pages SET pkgID = ? WHERE cID = ?';
                Loader::db()->execute($sql, array($pkg->pkgID, $cID));
                // break;
                continue;
            }

            $p = SinglePage::add($path, $pkg);
            if (is_object($p) && !$p->isError()) {
                $p->update($options['data']);
                if (isset($options[ATTRIBUTES])) {
                    foreach ($options[ATTRIBUTES] as $k => $v) {
                        $p->setAttribute($k, $v);
                    }
                }
            }
        }
    }

    protected function installBlocks($pkg)
    {
        foreach ($this->blocks as $block) {
            $bID = BlockType::getByHandle($block);
            if (intval($bID) > 0 && $bID !== 1) {
                Loader::db()->Execute('update BlockTypes set pkgID = ? where btID = ?',
                    array($pkg->pkgID, $bID->getBlockTypeID()));
            } else {
                BlockType::installBlockTypeFromPackage($block, $pkg);
            }
        }
    }

    protected function installUserAttributes($pkg)
    {
        $attributeCategory = AttributeKeyCategory::getByHandle('user');
        $availableAttributeSets = $attributeCategory->getAttributeSets();

        if (count($availableAttributeSets) <= 0) {
            foreach ($this->attributeSets as $handle => $name) {
                $attributeSet = $attributeCategory->addSet($handle, $name, $pkg);
                $this->installAttributes($attributeSet, $pkg);
            }
        } else {
            foreach ($availableAttributeSets as $handle => $name) {
                $attributeSet = AttributeSet::getByHandle($handle);
                if ($attributeSet) {
                    $sql = 'UPDATE AttributeSets SET pkgID = ? WHERE asID = ?';
                    Loader::db()->Execute($sql, array($pkg->pkgID, $attributeSet->getAttributeSetID()));
                } else {
                    $attributeSet = $attributeCategory->addSet($handle, $name, $pkg);
                }
                $this->installAttributes($attributeSet, $pkg);
            }
        }
    }

    private function installAttributes($set, $pkg)
    {
        $handle = $set->getAttributeSetHandle();
        if (isset($this->attributes[$handle])) {
            foreach ($this->attributes[$handle] as $attribute) {
                if ($attributeKey = UserAttributeKey::getByHandle($attribute[ATTRIBUTE][AK_HANDLE])) {
                    $sql = 'UPDATE AttributeKeys SET pkgID = ? WHERE akID = ?';
                    Loader::db()->Execute($sql, array($pkg->pkgID, $attributeKey->getAttributeKeyID()));
                } else {
                    $attributeType = AttributeType::getByHandle($attribute[HANDLE]);
                    $attributeKey = UserAttributeKey::add($attributeType, $attribute[ATTRIBUTE], $pkg);
                    $set->addKey($attributeKey);
                }

                if ($attribute[HANDLE] === SELECT) {
                    $this->installSelectOptions($attribute[ATTRIBUTE][AK_HANDLE]);
                }
            }

        }
    }

    private function installSelectOptions($attributeHandle)
    {
        $countriesHelper = Loader::helper('lists/countries', $this->pkgHandle);
        $statesHelper = Loader::helper('lists/states_provinces', $this->pkgHandle);
        $attribute = UserAttributeKey::getByHandle($attributeHandle);
        $options = array();
        switch ($attributeHandle) {
            case 'uCountry':
                $countries = $countriesHelper->getCountries();
                foreach ($countries as $country) {
                    $options[] = $country;
                }
                break;
            case 'uStateUS':
                $states = $statesHelper->getStateProvinceArray('US');
                foreach ($states as $state) {
                    $options[] = $state;
                }
                break;
            case 'uStateCA':
                $states = $statesHelper->getStateProvinceArray('CA');
                foreach ($states as $state) {
                    $options[] = $state;
                }
                break;
            case 'uStateAU':
                $states = $statesHelper->getStateProvinceArray('AU');
                foreach ($states as $state) {
                    $options[] = $state;
                }
                break;
            case 'uStateNZ':
                $states = $statesHelper->getStateProvinceArray('NZ');
                foreach ($states as $state) {
                    $options[] = $state;
                }
                break;
            default:
                $options = $this->selectOptionsAttribute[$attributeHandle];
                sort($options);
                break;
        }

        foreach ($options as $option) {
            $opt = SelectAttributeTypeOption::getByValue($option);
            if (!$opt) {
                SelectAttributeTypeOption::add($attribute, $option);
            }
        }
    }

    private function installNotificationModel()
    {
        $db = Loader::db();
        $sql = <<<sql
            CREATE TABLE IF NOT EXISTS `CupGoNotifications` (
                `nID` INT NOT NULL AUTO_INCREMENT,
                `nTitle` VARCHAR(100) NULL,
                `nDate` DATETIME NULL,
                `nStatus` TINYINT(1) NULL,
                `nContent` LONGTEXT NULL,
                `linkedTitles` VARCHAR(100) NULL,
                `dateCreated` DATETIME NULL,
                `dateModified` DATETIME NULL,
                PRIMARY KEY (`nID`)
            )
sql;
        $db->Execute($sql);
    }
}
