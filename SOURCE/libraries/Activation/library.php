<?php

/**
 * Activating of Access Code Library
 * ANZGO-3495 Added by Shane Camus 9/20/2017
 */

class ActivationLibrary
{
    const VALID_AC_STATUS       = 'CODE_NOT_ACTIVATED';
    const EM_BRAND              = 'EMACS';
    const SM_BRAND              = 'SENIORMATHS';
    const HM_BRAND              = 'HOTMATHS';
    const TIME_FORMAT1          = 'Y-m-d';
    const TIME_FORMAT2          = 'Y-m-d H:i:s';
    const FOUND                 = 'found';
    const USABLE                = 'usable';
    const ACTIVE                = 'active';
    const DATE_ACTIVATED        = 'dateActivated';
    const BRAND_CODE            = 'brandCode';
    const CUSTOM_MESSAGE        = 'customMessage';
    const MESSAGE               = 'message';
    const SUCCESS               = 'success';
    const ACTION                = 'action';
    const IS_GO_PRODUCT         = 'isGoProduct';
    const PERPETUAL             = 0;
    // ANZGO-3854 added by jbernardez 20180911
    const IS_REACTIVATION_CODE  = 'isReactivationCode';
    // SB-364 added by jbernardez/mtanada 20191023
    const IS_PRINT_CODE         = 'isPrintCode';
    // SB-391 added by mabrigos 20191120
    const IS_PRINTACCESS        = 'isPrintAccess';

    /**
     * Response codes;
     */
    const MSG_CODE_ACCESS_CODE_INCOMPLETE           = 1;
    const MSG_CODE_ACCESS_CODE_MORE_THAN_16         = 2;
    const MSG_CODE_ACCESS_CODE_HAS_INVALID_CHARS    = 3;
    const MSG_CODE_HAS_NOT_ACCEPTED_TERMS           = 4;
    const MSG_CODE_ACCESS_CODE_NOT_FOUND            = 5;
    const MSG_CODE_EMAC_ACCESS_CODE_ALREADY_USED    = 6;
    const MSG_CODE_SM_ACCESS_CODE_ALREADY_USED      = 7;
    const MSG_CODE_HM_ACCESS_CODE_ALREADY_USED      = 8;
    const MSG_CODE_EMAC_ACCESS_CODE_CAN_BE_USED     = 9;
    const MSG_CODE_SM_ACCESS_CODE_CAN_BE_USED       = 10;
    const MSG_CODE_HM_ACCESS_CODE_CAN_BE_USED       = 11;
    const MSG_CODE_GO_ACCESS_CODE_ALREADY_USED      = 12;
    const MSG_CODE_NOT_LOGGED_IN_BEFORE_ACTIVATE    = 13;
    const MSG_CODE_SUBSCRIPTION_ALREADY_REMOVED     = 14;
    const MSG_CODE_ERROR_UPDATING_ACCESS_CODE       = 15;
    const MSG_CODE_ERROR_UPDATING_SUBSCRIPTION      = 16;
    const MSG_CODE_ACTIVATION_SUCCESSFUL            = 17;
    const MSG_CODE_ACCESS_CODE_CAN_BE_USED          = 18;
    const MSG_CODE_USER_ID_INVALID                  = 19;
    const MSG_CODE_UNABLE_TO_ADD_HM_TO_USER         = 20;
    const MSG_CODE_INVALID_AVAILABILITY             = 21;
    const MSG_CODE_SUBSCRIPTION_INACTIVE            = 22;
    const MSG_CODE_REACTIVATION                     = 23;
    const MSG_CODE_CONNECTION_ERROR                 = 24;
    const MSG_CODE_PRINT_REACTIVATION_DO_NOT_MATCH  = 25;
    // ANZGO-3854 added by jbernardez 20180912
    const MSG_CODE_IS_REACTIVATION_CODE             = 26;
    // ANZGO-3854 added by jbernardez 20180917
    const MSG_CODE_REACTIVATION_PRINT_DO_NOT_MATCH  = 27;
    const END_DATE              = 'endDate';
    // ANZGO-3630 added by Maryjes Tanada 02/12/2018
    const HM_ID = 'hmID';

    // ANZGO-3757 added by jbernardez 20180620
    const TYPE                  = 'type';
    const USAGE_MAX             = 'usageMax';
    const USAGE_COUNT           = 'usageCount';
    const REACTIVATION          = 'reactivation';
    const REACTIVATION_VALID    = 'reactivationValid';
    // ANZGO-3758 added by jbernardez 20180629
    const EDUMARTITLEID         = 'edumarTitleID';
    // ANZGO-3830 Added by mtanada 20180813
    const USAGE_COUNT_ONE       = 1;
    const ALERT_INFO            = 'alertInfo';
    const PRINT_TYPE            = 'print';
    // SB-364 added by jbernardez 20191017
    const PRINTSUBSCRIPTIONID   = 'printSubsriptionID';

    protected $activationModel;
    protected $accessCode;
    protected $isTermChecked;
    protected $isForCodeCheck;
    protected $message;
    protected $messageCode = 0;
    protected $hasNoError = false;
    protected $action;
    protected $userID;
    protected $saID;
    protected $purchaseType;

    // Reactivation flag mtanada 2018/07/03
    protected $reactivationValid;
    protected $reactivationTitleID;
    protected $promoCodeDetails;

    // ANZGO-3758 added by jbernardez 20180629
    protected $edumarTitleID;
    protected $isReactivationValid;

    // ANZGO-3853 added by mtanada 20180905
    protected $reactivationCode = null;
    protected $printCode;
    protected $isPrintAndReactivationMatch = false;

    // ANZGO-3854 added by jbernardez 20180912
    protected $isReactivationCode;
    protected $printAccessCode;

    // SB-364 added by jbernardez 20191018
    protected $printSubscriptionID;

    // SB-16 added by mtanada 20190109 Counter flag function call per activation HM id
    protected $hmCount;

    // SB-391 updated by mabrigos 20191120
    protected $isPrintAccessCode = false;

    /**
     * ANZGO-3630 Modified by Maryjes Tanada 02/12/2018
     * ANZGO-3642 Modified by John Renzo Sunico, 02/22/2018
     * @param array $data
     */
    public function __construct($data = array())
    {
        Loader::library('HotMaths/api');
        Loader::library('Activation/model');

        $this->activationModel = new ActivationModel();
        $this->accessCode = trim(strtoupper(filter_var($data['accessCode'], FILTER_SANITIZE_STRING)), ' ');
        $this->isTermChecked = filter_var($data['terms'], FILTER_SANITIZE_STRING);
        $this->userID = $data['userID'];
        $this->saID = isset($data['saID']) ? $data['saID'] : 0;
        $this->purchaseType = isset($data['purchaseType']) ? $data['purchaseType'] : 0;
        // ANZGO-3758 added by jbernardez 20180629
        // ANZGO-3817 modified by mtanada 2018731
        $this->edumarTitleID = isset($data[static::EDUMARTITLEID]) ? $data[static::EDUMARTITLEID] : 0;
        $this->isForCodeCheck = isset($data['isForCodeCheck']) ? $data['isForCodeCheck'] : false;
        // ANZGO-3853 added by mtanada 20180905
        $this->reactivationCode = isset($data['reactivationCode']) ?
            trim(strtoupper(filter_var($data['reactivationCode'], FILTER_SANITIZE_STRING)), ' ') : null;
        // ANZGO-3854 added by jbernardez 20180912
        $this->printAccessCode = isset($data['printAccessCode']) ?
            trim(strtoupper(filter_var($data['printAccessCode'], FILTER_SANITIZE_STRING)), ' ') : null;
    }

    /* SB-16 modified by mtanada 20190110
     * These are the list of functions that are not being called in this parent class, as it is now on hub_activation
     * activateProduct(), searchAccessCode(), getGoCodeDetails(), getHMParams(), proceedHotmathsActivation(),
     * processReactivationCheck()
     */

    public function checkCodeHealth()
    {
        $result = $this->validateAccessCode();

        if ($this->hasNoError && $result[static::IS_GO_PRODUCT]) {
            // ANZGO-3830 Modified by mtanada 20180813
            if ($result[static::REACTIVATION_VALID] === true) {
                $this->messageCode = 23;
                $this->hasNoError = false;
            } else {
                // ANZGO-3830 Modified by mtanada 20180813
                $this->messageCode = 18;
                $this->action = 'allowed-activation';
                $this->setSession();
            }
        }

        if ($this->messageCode > 0) {
            $this->processMessages($this->messageCode, $result[static::DATE_ACTIVATED]);
        }

        // ANZGO-3757 modified by jbernardez 20180620
        return array (
            static::SUCCESS => $this->hasNoError,
            static::MESSAGE => $this->message,
            static::ACTION => $this->action,
            static::REACTIVATION_VALID => $this->isReactivationValid
        );
    }

    /**
     * Only on library
     * @return array|int
     */
    public function validateAccessCode()
    {
        $this->basicValidation();
        if ($this->hasNoError) {
            return $this->advancedValidation();
        }

        return 0;
    }

    public function basicValidation()
    {
        if ($this->getAccessCodeLength() < 19) {
            $this->messageCode = 1;
            $this->action = 'check-length-less';
        } elseif ($this->getAccessCodeLength() > 19) {
            $this->messageCode = 2;
            $this->action = 'check-length-more';
        } elseif (!$this->checkInvalidCharacter()) {
            $this->messageCode = 3;
            $this->action = 'invalid-character';
        } elseif ($this->isTermChecked == 'false' && !$this->isForCodeCheck) {
            $this->messageCode = 4;
            $this->action = 'term-box-not-checked';
        } else {
            $this->hasNoError = true;
        }
    }

    /* ANZGO-3760 modified by mtanada 20180719
     * PEAS Integration, will only pass through this method for access code validation
     */
    public function advancedValidation()
    {
        $this->hasNoError = false;
        $this->isReactivationValid = false;
        $this->isReactivationCode = false;
        // SB-364 added by jbernardez/mtanada 20191023
        $this->isPrintCode = false;

        $response = $this->searchAccessCode();
        $this->promoCodeDetails = $response;

        // ANZGO-3758 added by jbernardez 20180629
        if (isset($response[static::EDUMARTITLEID]) && !($response[static::EDUMARTITLEID] == null)) {
            $this->reactivationTitleID = $response[static::EDUMARTITLEID];
        }

        /* ANZGO-3757 added by jbernardez 20180620
         * ANZGO-3830 modified by mtanada 20180810 Codecheck API has param edumartitleID, none for activate
         * ANZGO-3841 modified by jbernardez 20180830 removed access code type validation
         * ANZGO-3853 modified by mtanada 2010907 reactivation code type validation
         */
        if ($this->isForCodeCheck && !empty($this->edumarTitleID)) {
            if (($response[static::EDUMARTITLEID] == $this->edumarTitleID)
                && ($response[static::TYPE] !== static::REACTIVATION)) {
                $this->isReactivationValid = true;
                $this->hasNoError = true;
            }
        } else {
            if (($response[static::REACTIVATION_VALID]) && ($response[static::TYPE] !== static::REACTIVATION)) {
                $this->isReactivationValid = true;
                $this->hasNoError = true;
            }
        }

        // ANZGO-3854 modified by jbernardez 2010910 reactivation code validation
        if (($response[static::USABLE])
            // SB-364 modified by jbernardez 20191022
            && (($response[static::EDUMARTITLEID] != '') || ($response[static::IS_REACTIVATION_CODE]))
            && ($response[static::TYPE] === static::REACTIVATION)) {
            $this->isReactivationCode = true;
        }

        // SB-364 added by jbernardez/mtanada 20191023
        if (!$response[static::USABLE] && $response[static::IS_PRINT_CODE]) {
            $this->isPrintCode = true;
        }

        if (!$response[static::FOUND]) {
            $this->messageCode = 5;
            $this->action = 'not-found';
        } else {
            if (isset($response['isHMProduct'])) {
                $this->validateHMCode($response[static::USABLE], $response[static::BRAND_CODE]);
            } else {
                $this->validateGoCode($response[static::CUSTOM_MESSAGE], $response);
            }

            // ANZGO-3757 modified by jbernardez 20180620
            // SB-364 modified by jbernardez/mtanada 20191023
            return array(
                static::IS_GO_PRODUCT => $response[static::IS_GO_PRODUCT],
                static::DATE_ACTIVATED => $response[static::DATE_ACTIVATED],
                'hmID' => $response['hmID'],
                static::REACTIVATION_VALID => $this->isReactivationValid,
                static::IS_REACTIVATION_CODE => $this->isReactivationCode,
                static::IS_PRINT_CODE => $this->isPrintCode
            );
        }
        return 0;
    }

    // ANZGO-3853 modified by mtanada 2010907
    public function getAccessCodeLength()
    {
        if ($this->reactivationCode !== null) {
            return (strlen($this->reactivationCode));
        }
        return (strlen($this->accessCode));
    }

    // ANZGO-3853 modified by mtanada 2010907
    public function checkInvalidCharacter()
    {
        if ($this->reactivationCode !== null) {
            return !preg_match('/[^A-Za-z0-9-]/', $this->reactivationCode);
        }
        return !preg_match('/[^A-Za-z0-9-]/', $this->accessCode);
    }

    /* Only in library
     *
     */
    protected function getHMCodeDetails($response)
    {
        $hmParams = $this->getHMParams();
        $hmAPI = new HotMathsApi($hmParams);
        $hmResponse = $hmAPI->validateAccessCode();

        if (!isset($hmResponse->success) && isset($hmResponse->activationState)) {
            $response[static::USABLE] = ($hmResponse->activationState === static::VALID_AC_STATUS);
            if (isset($hmResponse->usedDate)) {
                $epoch = $hmResponse->usedDate / 1000;
                $dateObj = new DateTime("@$epoch");
                $dateCreated = $dateObj->format('d/m/y');
                $response[static::DATE_ACTIVATED] = $dateCreated;
            }
            $response['isHMProduct'] = true;
            $response[static::BRAND_CODE] = $hmResponse->brandCode;
            $response[static::FOUND] = true;
        }

        return $response;
    }

    protected function validateHMCode($isUsable, $brandCode)
    {
        if (!$isUsable) {
            switch ($brandCode) {
                case static::EM_BRAND:
                    $this->messageCode = 6;
                    $this->action = 'fail-activate-EM';
                    break;
                case static::SM_BRAND:
                    $this->messageCode = 7;
                    $this->action = 'fail-activate-SM';
                    break;
                case static::HM_BRAND:
                    $this->messageCode = 8;
                    $this->action = 'fail-activate-HM';
                    break;
                default:
                    break;
            }
        } else {
            switch ($brandCode) {
                case static::EM_BRAND:
                    $this->messageCode = 9;
                    $this->action = 'can-activate-EM';
                    break;
                case static::SM_BRAND:
                    $this->messageCode = 10;
                    $this->action = 'can-activate-SM';
                    break;
                case static::HM_BRAND:
                    $this->messageCode = 11;
                    $this->action = 'can-activate-HM';
                    break;
                default:
                    break;
            }
            $this->hasNoError = true;
        }
    }

    protected function validateGoCode($customMessage, $response)
    {
        $isUsable = $response[static::USABLE];
        $isActive = $response[static::ACTIVE];

        if (isset($customMessage)) {
            $this->message = $customMessage;
            $this->action = 'activated-code-with-custom-message';
        } elseif (!$isActive) {
            // line 267: non-existent on GO
            $this->messageCode = static::MSG_CODE_SUBSCRIPTION_INACTIVE;
        } elseif (!$isUsable) {
            $this->messageCode = 12;
            $this->action = 'subscription-inactive';
        } else {
            $this->hasNoError = true;
        }
    }

    protected function setSession($hmID = null)
    {
        $_SESSION['accesscode'] = $this->accessCode;
        list (
            $_SESSION['form-accesscode_s1'],
            $_SESSION['form-accesscode_s2'],
            $_SESSION['form-accesscode_s3'],
            $_SESSION['form-accesscode_s4']
        ) = explode('-', $this->accessCode);

        // ANZGO-3630 added by Tanada
        if (isset($hmID)) {
            $_SESSION['hmID'] = $hmID;
        }

        // ANZGO-3854 added by jbernardez 20180913
        $_SESSION['printAccessCode'] = $this->printAccessCode;
    }

    protected function unsetSession()
    {
        $_SESSION['accesscode'] = '';
        $_SESSION['form-accesscode_s1'] = '';
        $_SESSION['form-accesscode_s2'] = '';
        $_SESSION['form-accesscode_s3'] = '';
        $_SESSION['form-accesscode_s4'] = '';
        // ANZGO-3630 added by Tanada
        $_SESSION['hmID'] = "";
        // ANZGO-3854 added by jbernardez 20180913
        $_SESSION['printAccessCode'] = '';
    }

    protected function setUserID()
    {
        $u = new User();
        $this->userID = $u->getUserID();
    }

    /**
     * Only in library
     * @param $hmID
     * SB-16 modified by mtanada 20190109 Iterate each HM ID to if multiple ids are found
     */
    protected function activateAccessCode($hmID)
    {
        if ((isset($hmID)) && ($hmID > 0)) {
            $this->hmCount = 0;
            // Multiple HM ID, iterate and add individually
            if (is_array($hmID)) {
                foreach ($hmID as $key => $hm) {
                    $this->hmId = $hm;
                    $isProductActivated = $this->proceedHotmathsActivation();
                }
            } else {
                $isProductActivated = $this->proceedHotmathsActivation();
            }
            $this->action = 'fail-activate-DynamicProduct';
        } else {
            $isProductActivated = $this->proceedTNGProductActivation();
            $this->action = 'fail-activate-TNGProduct';
        }

        if ($isProductActivated) {
            $this->hasNoError = true;
            $this->messageCode = 17;
            $this->action = 'activate-TNGProduct';
        }
    }

    /** ANZGO-3721 Added by Maryjes Tanada, 05/22/2018
     * Terminate trigger (boolean) when activating student access code if Teacher subscription already exists
     */
    protected function filterActivationSubscriptionExist($user, $tab)
    {
        $tabName = strtolower($tab['TabName']);

        $activeSubscriptionCount = $this->activationModel->countSubscriptionByTitleId($user->uID, $tab['TitleID']);
        $activeTabName = strtolower($activeSubscriptionCount['TabName']);

        // Tab name means already have a teacher subscription/Product
        // ANZGO-3723 modified by jbernardez 20180524
        // added online teacher edition to filter
        if (($activeSubscriptionCount['subscriptionCount'] === '1' && $user->uGroups[5] === 'Teacher') &&
            (strpos($activeTabName,
                    'teacher') !== false || $activeTabName === 'online teacher resource' || $activeTabName === 'online teacher edition') &&
            (strpos($tabName, 'student') !== false || $tabName === 'online resource')) {
            return true;
        } else {
            return false;
        }
    }

    /* ANZGO-3558 Jeszy Tanada 11/02/2017
     * Modify setting of endDate as it returns 1970 when using  multiple strtotime
     * and if duration is 0 as perpetual
     * ANZGO-3748 modified by mtanada 2018/07/03 Added Reactivation case
     */
    protected function getEndDate($type, $endDate, $breakpoint, $breakpointOffset, $duration)
    {
        $currMonth = date('n');
        $currYear = date('Y');

        switch ($type) {
            case 'start-end':
                $endDateByType = $endDate . " 12:00:00 AM";
                break;
            case 'end-of-year':
            case 'reactivation':
                $breakpointOffset -= 1;
                $breakpointOffset = ($breakpointOffset < 0) ? 0 : $breakpointOffset;

                if ($currMonth <= $breakpoint) {
                    $endDateByType = ($currYear + $breakpointOffset) . "-12-31 12:00:00";
                } else {
                    $endDateByType = ($currYear + 1 + $breakpointOffset) . "-12-31 12:00:00";
                }
                break;
            case 'trial':
            case 'duration':
                if ($this->checkIfTeacherResourcePackage() || $duration === static::PERPETUAL) {
                    $endDateByType = date(static::TIME_FORMAT2, strtotime('2020-12-31')) . "";
                } else {
                    $dateToConvert = date(static::TIME_FORMAT1);
                    $endDateByType = date(static::TIME_FORMAT2, strtotime($dateToConvert . "+" . $duration . " days"));
                }
                break;
            default:
                $endDateByType = '';
        }

        return $endDateByType;
    }

    /**
     * ANZGO-3630 Added by Maryjes Tanada 02/08/2018
     * Checking: HM user type and Access Code user type
     * Cannot use HmAPI checkHmUserCompatibility since they have different
     * message
     */
    public function checkHmUserProductType()
    {
        $hmParams = $this->getHMParams();
        $hmAPI = new HotMathsApi($hmParams);

        $hmAPI->getHmUser();
        $hmUser = $hmAPI->hmUser;
        $hmProduct = $hmAPI->getHmProduct();
        if ($hmUser->subscriberType !== $hmProduct->subscriberType &&
            $hmUser->subscriberType === 'STUDENT') {
            $studentMessage = "Your code $this->accessCode was NOT activated. <br/>";
            $studentMessage .= "You are trying to activate a teacher product on a student account. <br/>";
            $studentMessage .= "You need to create a teacher account on Cambridge GO to activate this code.";
            return $studentMessage;
        } else {
            return 'user-type-success';
        }
    }

    // ANZGO-3642 Modified by John Renzo Sunico, 02/22/2018
    // ANZGO-3943 Modified by mtanada 20181210
    protected function processMessages($messageCode, $dateActivated = null, $usageCount = null, $usageMax = null)
    {
        $messageTemplate = '<br> If you have purchased this code and believe it has been activated in error ';
        $messageTemplate .= 'please contact customer service on 1800 005 210. <br>';
        $messageTemplate .= 'Otherwise, you should purchase a new code from:<ol type="a" align="left">';
        $messageTemplate .= '<li>Your Educational Bookseller </li>';
        $messageStore = '<li>Our online <a href="/education/subjects/Mathematics/Secondary">Store</a></li> ';

        switch ($messageCode) {
            case static::MSG_CODE_ACCESS_CODE_INCOMPLETE:
                $this->message = 'This access code is incomplete. Please enter it again.';
                break;
            case static::MSG_CODE_ACCESS_CODE_MORE_THAN_16:
                $this->message = 'This access code is more than 16 characters. Please enter it again.';
                break;
            case static::MSG_CODE_ACCESS_CODE_HAS_INVALID_CHARS:
                $this->message = 'Access code contains invalid characters.';
                break;
            case static::MSG_CODE_HAS_NOT_ACCEPTED_TERMS:
                $this->message = 'You must accept and agree the Terms of Use.';
                break;
            case static::MSG_CODE_ACCESS_CODE_NOT_FOUND:
                $this->message = 'Access code not found. Please try again.';
                break;
            case static::MSG_CODE_EMAC_ACCESS_CODE_ALREADY_USED:
                $this->message = "Your code has already been activated on $dateActivated.<br>";
                $this->message .= $messageTemplate . $messageStore;
                $this->message .= '<li>Through the account settings area of your existing Essential Mathematics ';
                $this->message .= '<a href="https://emac.hotmaths.com.au/">account</a></li>';
                $this->message .= '<li>Customer service on 1800 005 210 </li></ol>';
                $this->message .= '<strong>NOTE: </strong>If you have purchased a second-hand textbook you will need ';
                $this->message .= 'to purchase a</br>re-activation code from our online store or customer service.';
                break;
            case static::MSG_CODE_SM_ACCESS_CODE_ALREADY_USED:
                $this->message = "Your code has already been activated on $dateActivated.<br>";
                $this->message .= $messageTemplate . $messageStore;
                $this->message .= '<li>Through the account settings area of your existing Senior Mathematics ';
                $this->message .= '<a href="https://seniormaths.cambridge.edu.au/">account</a></li>';
                $this->message .= '<li>Customer service on 1800 005 210 </li></ol>';
                $this->message .= '<strong>NOTE: </strong>If you have purchased a second-hand textbook you will need ';
                $this->message .= 'to purchase a re-activation code from our online store or customer service.';
                break;
            case static::MSG_CODE_HM_ACCESS_CODE_ALREADY_USED:
                $this->message = "Your code has already been activated on $dateActivated.";
                $this->message .= $messageTemplate;
                $this->message .= '<li>Our online <a href="https://www.cambridge.edu.au/education/"> Store</a></li>';
                $this->message .= '<li>Through the account settings area of your existing Cambridge HOTmaths ';
                $this->message .= '<a href ="https://www.hotmaths.com.au/">account</a></li>';
                $this->message .= '<li>Customer service on 1800 005 210</li></ol>';
                $this->message .= '<strong>NOTE: </strong>If you have purchased a second-hand textbook you will need ';
                $this->message .= 'to purchase a <br>re-activation code from our online store or customer service.';
                break;
            case static::MSG_CODE_EMAC_ACCESS_CODE_CAN_BE_USED:
                $this->message = 'Your code can be used, please <a href="https://emac.hotmaths.com.au/">LOGIN </a> ';
                $this->message .= 'or <a href="https://emac.hotmaths.com.au/home/register">JOIN NOW</a> ';
                $this->message .= 'to your Essential Mathematics account to activate.';
                break;
            case static::MSG_CODE_SM_ACCESS_CODE_CAN_BE_USED:
                $this->message = 'Your code can be used, please <a href="https://seniormaths.cambridge.edu.au/">LOGIN ';
                $this->message .= '</a> or <a href="https://seniormaths.cambridge.edu.au/home/register">JOIN NOW</a> ';
                $this->message .= 'to your Senior Mathematics account to activate.';
                break;
            case static::MSG_CODE_HM_ACCESS_CODE_CAN_BE_USED:
                $this->message = 'Your code can be used, please <a href="https://www.hotmaths.com.au">LOGIN </a>';
                $this->message .= 'or <a href="https://www.hotmaths.com.au/home/register">JOIN NOW</a> ';
                $this->message .= "to your Cambridge HOTmaths account to activate.";
                break;
            case static::MSG_CODE_GO_ACCESS_CODE_ALREADY_USED:
                $this->message = "Your code has already been activated on $dateActivated. You can purchase a new ";
                $this->message .= 'code at your educational bookseller or online <a href="https://www.cambridge.edu.au';
                $this->message .= '/education">here</a> or if you have purchased this code and believe it has ';
                $this->message .= 'been activated in error, please contact customer service on 1800 005 210.';
                break;
            case static::MSG_CODE_NOT_LOGGED_IN_BEFORE_ACTIVATE:
                $this->message = 'You need to be logged in to activate a code. ';
                $this->message .= 'Please wait while we transfer you to the login page.';
                break;
            case static::MSG_CODE_SUBSCRIPTION_ALREADY_REMOVED:
                $this->message = 'Subscription has been removed from system.';
                break;
            case static::MSG_CODE_ERROR_UPDATING_ACCESS_CODE:
                $this->message = 'An error occurred while updating your access code.';
                break;
            case static::MSG_CODE_ERROR_UPDATING_SUBSCRIPTION:
                $this->message = 'An error occurred while updating your subscription.';
                break;
            case static::MSG_CODE_ACTIVATION_SUCCESSFUL:
                $this->message = 'Activation successful. Please wait a moment while we re-direct you to ';
                $this->message .= "your 'My Resources'.";
                break;
            case static::MSG_CODE_ACCESS_CODE_CAN_BE_USED:
                $this->message = 'Your code is ready for activation. Would you like to activate this code now?';
                $this->message .= "<br/> <a href='/activate/'>Yes</a> <a href='/codecheck/'>No</a>";
                break;
            case static::MSG_CODE_USER_ID_INVALID:
                $this->message = 'Invalid user ID.';
                break;
            case static::MSG_CODE_UNABLE_TO_ADD_HM_TO_USER:
                $this->message = 'Unable to add HM product to user. Subscription not added.';
                break;
            case static::MSG_CODE_INVALID_AVAILABILITY:
                $this->message = 'Invalid subscription availability.';
                break;
            case static::MSG_CODE_SUBSCRIPTION_INACTIVE:
                $this->message = "The code you are trying to activate is no longer valid. </br>
                    Please contact your Education Resource consultant or
                    Customer service on 1800 005 210.";
                break;
            case static::MSG_CODE_REACTIVATION:
                // ANZGO-3760 added by mtanada 20180712
                $reactTitleID = $this->reactivationTitleID;

                // ANZGO-3853 modified by mtanada 20180905
                // ANZGO-3913 modified by jbernardez 20181108
                // SB-391 updated by mabrigos 20191127
                $this->message = <<<MESSAGE
                <div><div>
                    This code was last activated on $dateActivated
                    and has been activated $usageCount time/s in total. <br>
                    This code may be reactivated $usageMax times in total
                    in consecutive years by combining this code <br>
                    with a reactivation code for this title.</br></br>

                    You can purchase a reactivation code from:
                    <ol type="a" align="left">
                        <li>Your Education Bookseller</li>
                        <form action="/education/cart/update/" name="cartForm" id="cartForm" method="POST">
                        <li>Our online
                        <input form="cartForm" type="hidden" name="titleID" id="titleID" value="$reactTitleID" />
                        <input form="cartForm" type="hidden" name="reactivationPromoCode[$reactTitleID]"
                        value="$this->accessCode" />
                        <input form="cartForm" type="hidden" name="rcID" value="1">
                        <a href="#" onclick="document.getElementById('cartForm').submit();">store.</a><br>
                        Before purchasing, <a href="/go/login/" target="_blank">log in </a>
                        to your Cambridge GO account to ensure it appears in your My resources.</li></form>
                        <li>Customer service on 1800 005 210.</li>
                    </ol>
                    If you already have a reactivation code, please enter below and activate: <br></div>
                    <div class="go-reactivate">
                    <div class="form-group animated bounceInDown" style="padding-right: 3px;">
                        <label for="First" class="sr-only access-code-field">First</label>
                        <input type="text" name="reactivationcode[1]"
                            id="reactivationCode1"
                            class="form-control reactivationcode smallForm"
                            value=""
                            maxlength="4"
                            placeholder="* * * *" />
                    </div>
                    <div class="form-group animated bounceInDown" style="padding-right: 3px;">
                        <label for="Second" class="sr-only access-code-field">Second</label>
                        <input type="text" name="reactivationcode[2]"
                            id="reactivationCode2"
                            class="form-control reactivationcode smallForm"
                            value=""
                            maxlength="4"
                            placeholder="* * * *" />
                    </div>
                    <div class="form-group animated bounceInDown" style="padding-right: 3px;">
                        <label for="Third" class="sr-only access-code-field">Third</label>
                        <input type="text" name="reactivationcode[3]"
                            id="reactivationCode3"
                            class="form-control reactivationcode smallForm"
                            value=""
                            maxlength="4" placeholder="* * * *" />
                    </div>
                    <div class="form-group animated bounceInDown">
                        <label for="Fourth" class="sr-only access-code-field">Fourth</label>
                        <input type="text" name="reactivationcode[4]"
                            id="reactivationCode4"
                            class="form-control reactivationcode smallForm"
                            value=""
                            maxlength="4"
                            placeholder="* * * *" />
                    </div>
                    <div class="form-group animated bounceInDown">
                        <span id="reactivation-refresh"
                            class="glyphicon glyphicon-refresh"
                            data-toggle="tooltip"
                            title="Activate Another Code">
                        </span>
                    </div>
                    </div></div>
MESSAGE;
                break;
            case static::MSG_CODE_CONNECTION_ERROR:
                $this->message = <<<MESSAGE
                    Sorry, we encountered an error.
                    Please try again later or contact Customer service on
                    <a href="mailto:enquiries@cambridge.org">enquiries@cambridge.org</a>.
MESSAGE;
                break;
            case static::MSG_CODE_PRINT_REACTIVATION_DO_NOT_MATCH:
                $reactTitleID = $this->reactivationTitleID;
                // ANZGO-3913 modified by jbernardez 20181108
                // SB-391 updated by mabrigos 20191127
                $this->message = <<<MESSAGE
                <div><div>
                Print Access Code and Reactivation Access Code does not match. <br><br>
                    You can purchase a reactivation code from:
                    <ol type="a" align="left">
                        <li>Your Education Bookseller</li>
                        <form action="/education/cart/update/" name="cartForm" id="cartForm" method="POST">
                        <li>Our online
                        <input form="cartForm" type="hidden" name="titleID" id="titleID" value="$reactTitleID" />
                        <input form="cartForm" type="hidden" name="reactivationPromoCode[$reactTitleID]"
                        value="$this->printCode" />
                        <input form="cartForm" type="hidden" name="rcID" value="1">
                        <a href="#" onclick="document.getElementById('cartForm').submit();">store.</a><br>
                        Before purchasing <a href="/go/login/" target="_blank">login </a>
                        to your Cambridge GO account to ensure it appears in your My Resources.</li></form>
                        <li>Customer service on 1800 005 210.</li>
                    </ol>
                    If you already have a reactivation code, please enter below and activate: <br></div>
                   <div class="go-reactivate">
                   <div class="form-group animated bounceInDown" style="padding-right: 3px;">
                        <label for="First" class="sr-only access-code-field">First</label>
                        <input type="text" name="reactivationcode[1]"
                            id="reactivationCode1"
                            class="form-control reactivationcode smallForm"
                            value=""
                            maxlength="4"
                            placeholder="* * * *" />
                    </div>
                    <div class="form-group animated bounceInDown" style="padding-right: 3px;">
                        <label for="Second" class="sr-only access-code-field">Second</label>
                        <input type="text" name="reactivationcode[2]"
                            id="reactivationCode2"
                            class="form-control reactivationcode smallForm"
                            value=""
                            maxlength="4"
                            placeholder="* * * *" />
                    </div>
                    <div class="form-group animated bounceInDown" style="padding-right: 3px;">
                        <label for="Third" class="sr-only access-code-field">Third</label>
                        <input type="text" name="reactivationcode[3]"
                            id="reactivationCode3"
                            class="form-control reactivationcode smallForm"
                            value=""
                            maxlength="4" placeholder="* * * *" />
                    </div>
                    <div class="form-group animated bounceInDown">
                        <label for="Fourth" class="sr-only access-code-field">Fourth</label>
                        <input type="text" name="reactivationcode[4]"
                            id="reactivationCode4"
                            class="form-control reactivationcode smallForm"
                            value=""
                            maxlength="4"
                            placeholder="* * * *" />
                    </div>
                    <div class="form-group animated bounceInDown">
                        <span id="reactivation-refresh"
                            class="glyphicon glyphicon-refresh"
                            data-toggle="tooltip"
                            title="Activate Another Code">
                        </span>
                    </div>
                    </div></div>
MESSAGE;
                break;
            // ANZGO-3854 added by jbernardez 20180912
            // SB-391 updated by mabrigos 20191127
            case static::MSG_CODE_IS_REACTIVATION_CODE:
                $reactTitleID = $this->reactivationTitleID;
                $this->message = <<<MESSAGE
                    <span style="color:#3C3C3C; text-align: center;">
                    Please enter the code found in your print text book, sealed pocket or email.</span><br>
                    <div class="go-reactivation">
                    <div class="form-group animated bounceInDown" style="padding-right: 3px;">
                        <label for="First" class="sr-only access-code-field">First</label>
                        <input type="text" name="printAccessCode[1]"
                               id="printAccessCode1"
                               class="form-control printAccessCode smallForm"
                               value=""
                               maxlength="4"
                               placeholder="* * * *" />
                    </div>
                    <div class="form-group animated bounceInDown" style="padding-right: 3px;" >
                        <label for="Second" class="sr-only access-code-field">Second</label>
                        <input type="text" name="printAccessCode[2]"
                               id="printAccessCode2"
                               class="form-control printAccessCode smallForm"
                               value=""
                               maxlength="4"
                               placeholder="* * * *" />
                    </div>
                    <div class="form-group animated bounceInDown" style="padding-right: 3px;">
                        <label for="Third" class="sr-only access-code-field">Third</label>
                        <input type="text" name="printAccessCode[3]"
                               id="printAccessCode3"
                               class="form-control printAccessCode smallForm"
                               value=""
                               maxlength="4" placeholder="* * * *" />
                    </div>
                    <div class="form-group animated bounceInDown">
                        <label for="Fourth" class="sr-only access-code-field">Fourth</label>
                        <input type="text" name="printAccessCode[4]"
                               id="printAccessCode4"
                               class="form-control printAccessCode smallForm"
                               value=""
                               maxlength="4"
                               placeholder="* * * *" />
                    </div>&nbsp
                    <div class="form-group animated bounceInDown">
                        <span id="printAccessCode-refresh"
                              class="glyphicon glyphicon-refresh"
                              data-toggle="tooltip"
                              title="Activate Another Code">
                        </span>
                    </div>
                    </div>
MESSAGE;
                break;
            // ANZGO-3854 added by jbernardez 20180917
            // SB-391 updated by mabrigos 20191127
            case static::MSG_CODE_REACTIVATION_PRINT_DO_NOT_MATCH:
                $reactTitleID = $this->reactivationTitleID;
                $this->message = <<<MESSAGE
                    To activate this product you need to enter the 16 character code found in your print textbook
                    sealed pocket or email first. <br>
                    <div class="go-reactivation">
                    <div class="form-group animated bounceInDown" style="padding-right: 3px;">
                        <label for="First" class="sr-only access-code-field">First</label>
                        <input type="text" name="printAccessCode[1]"
                               id="printAccessCode1"
                               class="form-control printAccessCode smallForm"
                               value=""
                               maxlength="4"
                               placeholder="* * * *" />
                    </div>
                    <div class="form-group animated bounceInDown" style="padding-right: 3px;" >
                        <label for="Second" class="sr-only access-code-field">Second</label>
                        <input type="text" name="printAccessCode[2]"
                               id="printAccessCode2"
                               class="form-control printAccessCode smallForm"
                               value=""
                               maxlength="4"
                               placeholder="* * * *" />
                    </div>
                    <div class="form-group animated bounceInDown" style="padding-right: 3px;">
                        <label for="Third" class="sr-only access-code-field">Third</label>
                        <input type="text" name="printAccessCode[3]"
                               id="printAccessCode3"
                               class="form-control printAccessCode smallForm"
                               value=""
                               maxlength="4" placeholder="* * * *" />
                    </div>
                    <div class="form-group animated bounceInDown">
                        <label for="Fourth" class="sr-only access-code-field">Fourth</label>
                        <input type="text" name="printAccessCode[4]"
                               id="printAccessCode4"
                               class="form-control printAccessCode smallForm"
                               value=""
                               maxlength="4"
                               placeholder="* * * *" />
                    </div>&nbsp
                    <div class="form-group animated bounceInDown">
                        <span id="printAccessCode-refresh"
                              class="glyphicon glyphicon-refresh"
                              data-toggle="tooltip"
                              title="Activate Another Code">
                        </span>
                    </div>
                    </div>
MESSAGE;
                break;
            default:
                $this->message = '';
        }
    }

    /**
     * ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
     * Creates UserSubscription without AccessCode
     * @return array|bool
     */
    private function proceedTNGManualActivation()
    {
        $todayDate = date('Y-m-d h:i:s');
        $subscriptionAvailability = $this->activationModel->getSubscriptionAvailabilityByID($this->saID);
        $subscriptionID = $subscriptionAvailability['S_ID'];
        $subscriptionAvailabilityID = $subscriptionAvailability['ID'];
        $subscriptionTabIDs = $this->activationModel->getTabIDs($subscriptionID);

        if (!$subscriptionTabIDs) {
            $this->messageCode = 14;

            return false;
        }

        $endDateByType = $this->getComputedEndDate($subscriptionAvailability);

        $lastUserSubscriptionID = $this->activationModel->createUserSubscription(
            $this->userID,
            $todayDate,
            $endDateByType,
            $this->accessCode,
            $subscriptionAvailabilityID,
            $this->purchaseType
        );

        if (!$lastUserSubscriptionID) {
            $this->messageCode = 16;

            return false;
        }

        $user = new User();
        $this->activationModel->updateUserSubscriptionCreatorByID($lastUserSubscriptionID, $user->getUserID());

        $tabCount = 0;
        foreach ($subscriptionTabIDs as $row) {

            $tabID = $row['tabID'];

            $daysRemaining = $this->activationModel->getUserSubscriptionDaysRemainingByID($lastUserSubscriptionID);

            $lastTabID = $this->activationModel->assignTabsToUser(
                $this->userID,
                $tabID,
                $subscriptionID,
                $subscriptionAvailabilityID,
                $endDateByType,
                $lastUserSubscriptionID,
                $daysRemaining
            );

            if ($lastTabID) {
                $tabCount++;
            }
        }

        if (count($subscriptionTabIDs) != $tabCount) {
            $this->messageCode = 16;

            return false;
        }

        $setEndDate = date('Y-m-d', strtotime($endDateByType));

        return array(
            'endDate' => $setEndDate
        );
    }

    /**
     * ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
     * Adds HM Product to User
     * @return bool|HotMathsApi
     */
    private function proceedHotMathsManualActivation()
    {
        $this->messageCode = 0;
        $hmParams = $this->getHMParams();
        $hmParams['saId'] = $this->saID;
        $hmAPI = new HotMathsApi($hmParams);

        // ANZGO-3723 added by jbernardez 20180522
        // Filter and terminate activating of student access code if Teacher subscription already exists
        $user = User::getByUserID($this->userID);
        $tab = $this->activationModel->getTitleIdBySaID($this->saID);

        $filterToTerminate = $this->filterActivationSubscriptionExist($user, $tab);
        if ($filterToTerminate === true) {
            // error message when activating student product with existing teacher subscription TBD: Carina
            $this->message = 'You are trying to activate a student Product but you already have an active';
            $this->message .= 'Teacher Product version of the same Title';
            $this->hasNoError = false;
            $this->action = 'TNGProvisionError';
            return false;
        } else {
            $hmAPI->activationHmSubscription();

            $hmResponse = $hmAPI->getResponse();
            $hmError = $hmAPI->getError();

            if (is_array($hmResponse)) {
                $this->hasNoError = false;
                $this->message = $hmResponse[static::MESSAGE];
                $this->action = 'HMProductProblem';
            } elseif (is_array($hmError)) {
                $this->hasNoError = false;
                $this->message = $hmError[static::MESSAGE];
                $this->action = 'HMProvisionCompatibility';
            } else {
                $subscriptionAvailability = $this->activationModel->getSubscriptionAvailabilityByID($this->saID);
                $hmAPI->setEndDate($this->getComputedEndDate($subscriptionAvailability));

                if (!$hmAPI->resumeActivationHmSubscription()) {
                    $this->hasNoError = false;
                    $this->action = 'HMProvisionError';

                    $hmError = $hmAPI->getError();
                    if (is_array($hmError)) {
                        $this->message = $hmError[static::MESSAGE];
                    } else {
                        $this->message = $hmResponse;
                    }
                    return false;
                }

                $hmAPI->forceRenewHmUser();
                $this->hasNoError = true;
                return true;
            }
        }
    }

    // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
    private function rollbackHothMathsSubscription()
    {
        $hmParams = $this->getHMParams();
        $hmParams['saId'] = $this->saID;
        $hmAPI = new HotMathsApi($hmParams);

        return $hmAPI->removeProductToUser();
    }

    /**
     * ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
     * @param $availability
     * @depends activationModel->getSubscriptionAvailabilityByID
     * @return false|string
     */
    private function getComputedEndDate($availability)
    {
        $type = $availability['Type'];
        $endDate = date(static::TIME_FORMAT1, strtotime($availability['EndDate']));
        $eoyBreakpoint = $availability['EndOfYearBreakPoint'];
        $eoyOffset = $availability['EndOfYearOffset'];
        $duration = $availability['Duration'];

        $subscriptionEndDate = $this->getEndDate($type, $endDate, $eoyBreakpoint, $eoyOffset, $duration);
        return date(static::TIME_FORMAT1, strtotime($subscriptionEndDate));
    }

    /**
     * ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
     * Validates manual activation
     * @return bool
     */
    private function validateManualActivation()
    {
        if (!$this->userID) {
            $this->messageCode = 19;
            return false;
        }

        if (!$this->saID) {
            $this->messageCode = 21;
            return false;
        }

        $this->hasNoError = true;
        return $this->hasNoError;
    }

    /**
     * ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
     * Checks if subscription has HM Product
     * @return bool
     */
    public function hasHotMathsProduct()
    {
        $subscription = $this->activationModel->getSubscriptionAvailabilityByID($this->saID);

        return !empty($subscription['HmID']);
    }
}
