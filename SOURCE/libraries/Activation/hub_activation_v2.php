<?php

/**
 * Hub Integrated Activation Library
 * Extends ActivationLibrary and override data
 * fetching methods to use Hub.
 *
 * @author jsunico@cambridge.org
 */

Loader::library('Activation/library');
Loader::library('HotMaths/collections_v2/product');
Loader::library('HotMaths/collections_v2/user');
Loader::library('hub-sdk/autoload');

use HubEntitlement\Models\Activation;
use HubEntitlement\Models\Permission;
// SB-364 added by jbernardez 20191021
use HubEntitlement\Models\Entitlement;

class HubActivation extends ActivationLibrary
{
    /**
     * @var \HubEntitlement\Models\Permission
     */
    protected $permission;

    /**
     * Determines if it should activate in Go
     *
     * @var bool
     */
    public $activateInGo = false;

    public $hmId;

    public $purchaseTypeHub = 'CODE';

    public $createdBy = null;

    /**
     * @var Activation
     */
    public $lastActivation;

    const PURCHASE_TYPE_CODE = 'CODE';
    const PURCHASE_TYPE_CMS = 'CMS';
    const PURCHASE_TYPE_PROVISION = 'PROVISION';
    const RETRY_PRODUCT_ACTIVATION = 2;

    // ANZGO-3817 added by mtanada 20180731
    public $edumarTitleID = 0;
    public $isForCodeCheck = false;

    // SB-2 added by mabrigos 20190116
    private $limitedEndDate;
    // SB-103 added by machua 20190320
    private $limitedDateDeactivated;

    // ANZGO-3610 Modified by John Renzo Sunico, 01/18/2018
    public function activateProduct()
    {
        // ANZGO-3854 added by jbernardez20180913
        $alertInfo = false;
        $activateReactivation = false;
        $result = $this->validateAccessCode();

        // ANZGO-3853 added by mtanada 20180907
        // ANZGO-3854 modified by jbernardez 20180913
        // for scenario print code first
        if ($this->reactivationCode !== null && $this->reactivationCode !== '' && $this->hasNoError === true) {
            // SB-364 modified by jbernardez/mtanada 20191023
            $this->processReactivationCheck($this->reactivationCode, static::REACTIVATION, $result[static::IS_PRINT_CODE]);
            if ($this->isPrintAndReactivationMatch === true) {
                $result = $this->validateAccessCode();
                $result[static::IS_REACTIVATION_CODE] = false;
            }

        // SB-364 modified by jbernardez/mtanada 20191023
        // ANZGO-3854 added by jbernardez 20180912
        // for scenario reactivation code first
        } elseif ($this->printAccessCode !== null && $this->printAccessCode !== '' && $this->hasNoError === true) {
            // SB-364 modified by jbernardez/mtanada 20191023
            $this->processReactivationCheck($this->printAccessCode, static::PRINT_TYPE, $result[static::IS_REACTIVATION_CODE]);
            if ($this->isPrintAndReactivationMatch === true) {
                $result = $this->validateAccessCode();
                $activateReactivation = true;
            }
        }

        if ($this->hasNoError && $result[static::IS_GO_PRODUCT]) {
            $this->hasNoError = false;
            $this->setUserID();

            // ANZGO-3830 Modified by mtanada 20180813
            if ($result[static::REACTIVATION_VALID] === true) {
                $this->messageCode = 23;
                // ANZGO-3854 added by jbernardez20180913
            } elseif (($result[static::IS_REACTIVATION_CODE] === true) && (!$activateReactivation)) {
                $this->messageCode = 26;
                $alertInfo = true;
            } elseif (!$this->userID) {
                // Added by Maryjes Tanada 02/07/2018 to pass HM_ID in session
                $this->setSession($result[static::HM_ID]);
                $this->messageCode = 13;
                $this->action = 'login';
            } else {
                // this is for final activation
                $this->activateAccessCode($result[static::HM_ID]);
                $this->unsetSession();
            }
        }

        // ANZGO-3759 mtanada 20180704
        if ($this->messageCode > 0) {
            $this->processMessages(
                $this->messageCode,
                $result[static::DATE_ACTIVATED],
                $this->promoCodeDetails['usageCount'],
                $this->promoCodeDetails['usageMax']
            );
        }

        return array(
            static::SUCCESS => $this->hasNoError,
            static::MESSAGE => $this->message,
            static::IS_PRINTACCESS => $this->isPrintAccessCode,
            static::ACTION => $this->action,
            static::ALERT_INFO => $alertInfo
        );
    }

    // ANZGO-3853 added by mtanada 20180907
    // ANZGO-3854 modified by jbernardez 20180913
    // SB-64 modified by mtanada 20190208
    public function processReactivationCheck($accessCode, $type, $isForReactivationMatching = null)
    {
        $this->isPrintAndReactivationMatch = $this->checkPrintAndReactivationMatch($accessCode, $type);

        // SB-364 added by jbernardez 20191022
        // if this is set to true, it means that a reactivation code was entered,
        // and it triggered asking of the print code
        // change value of the $this->isPrintAndReactivationMatch
        if ($isForReactivationMatching) {
            $this->isPrintAndReactivationMatch = $this->checkPrintAndReactivationMatchViaSubscription($accessCode, $type);
        }

        // SB-364 modified by jbernardez/mtanada 20191023
        if (($this->isPrintAndReactivationMatch === true) && ($this->printAccessCode === null)) {
            $this->printAccessCode = $this->accessCode;
            $this->accessCode = $this->reactivationCode;
            $this->reactivationCode = null;
            $this->permission = null;
        } elseif (($this->isPrintAndReactivationMatch === true) && ($this->printAccessCode != '')) {
            $this->reactivationCode = null;
            $this->permission = null;
        } else {
            // ANZGO-3854 modified by jbernardez 20180917
            if ($type === static::PRINT_TYPE) {
                // SB-391 added by mabrigos
                $this->isPrintAccessCode = true;
                $this->messageCode = 27;
                $this->hasNoError = false;
            } else {
                $this->printCode = $this->accessCode;
                $this->messageCode = 25;
                $this->hasNoError = false;
            }
        }
    }

    protected function getPermission()
    {
        if (!$this->permission) {
            $this->permission = Permission::where([
                'proof' => $this->accessCode
            ]);
            $this->permission = array_pop($this->permission);
        }

        return $this->permission;
    }

    /**
     * @return mixed
     */
    public function searchAccessCode()
    {
        $codeInfo = $this->getPermission();

        $params = array(
            static::ACTIVE => false,
            static::USABLE => false,
            static::DATE_ACTIVATED => null,
            static::FOUND => false
        );

        if (!$codeInfo) {
            // Used if not TNG code, check the code on HM side
            $response = $this->getHMCodeDetails($params);
        } else {
            $response = $this->getGoCodeDetails($params, $codeInfo);
        }
        return $response;
    }

    // SB-364 added by jbernardez 20191021
    // SB-430 updated by mabrigos
    protected function getSubscriptionIDsByProduct($productData)
    {
        foreach ($productData as $productID => $entitlementID) {
            $entitlement = Entitlement::find($entitlementID);
            $subscriptionIDs = explode(',', $entitlement->metadata['printSubscriptionID']);
        }

        return $subscriptionIDs;
    }

    /**
     * PEAS (HUB)
     * @param $response
     * @param $permission Permission
     * @return mixed
     */
    public function getGoCodeDetails($response, $permission)
    {
        $entitlement = $permission->entitlement()->fetch();
        $permission->entitlement = $entitlement;

        $product = $entitlement->product()->fetch();

        $lastActivation = $permission->getLastActivation();
        $dateActivated = $lastActivation
            ? $lastActivation->created_at->format('d/m/y')
            : null;

        $response[static::ACTIVE] = $permission->is_active;
        $response[static::USABLE] = $permission->IsUsable;
        $response[static::DATE_ACTIVATED] = $dateActivated;
        $response[static::IS_GO_PRODUCT] = true;
        $response[static::FOUND] = true;
        $response[static::USAGE_COUNT] = count($permission->activations);
        $response[static::USAGE_MAX] = $permission->limit;

        // PEAS: ANZGO-3760 modified by mtanada 20180713 PEAS Reactivation Integration
        $response[static::TYPE] = $permission->entitlement->Type;
        /* ANZGO-3830 modified by mtanada 20180813 Simplified Reactivation filtering
         * Reactivation Type, Used Access Code and Subscription edumarTitleID is tied up to a Product in Edumar
         */
        $response[static::EDUMARTITLEID] = $product->edumar_titleID;
        // ANZGO-3841 modified by jbernardez 20180830 removed access code type validation
        // ANZGO-3853 modified by mtanada 20180907 access code type validation for print code not reactivation type
        // ANZGO-3853 modified by mtanada 20180917 add $isEdumarTitleSet
        $isEdumarTitleSet = $response[static::EDUMARTITLEID] !== 0 && isset($response[static::EDUMARTITLEID])
            && !empty($response[static::EDUMARTITLEID]);

        if (($response[static::USAGE_COUNT] >= static::USAGE_COUNT_ONE)
            && ($response[static::TYPE] !== static::REACTIVATION) && ($isEdumarTitleSet)) {
            $response[static::REACTIVATION_VALID] = true;
        }

        // SB-364 added by jbernardez 20191021
        // verify print code if it has the reactivationSubscriptionID
        $reactivationSubscriptionIDdata = $product->metadata['reactivationSubscriptionID'];
        if ($reactivationSubscriptionIDdata) {
            $subscriptionIDs = $this->getSubscriptionIDsByProduct($reactivationSubscriptionIDdata);
            $inArrayResult = in_array($product->id, $subscriptionIDs);
            // SB-379/380 modified by jbernardez 20191024
            if ($inArrayResult && !$permission->IsUsable) {
                $response[static::REACTIVATION_VALID] = true;
            }
        }

        // SB-364 added by jbernardez/mtanada 20191023
        // verify access code if it has reactivationSubscriptionID
        if ($product->metadata['reactivationSubscriptionID']) {
            $response[static::IS_PRINT_CODE] = true;
        }

        // SB-364 added by jbernardez 20191022
        // verify reactivation code if it has the printSubscriptionID
        if ($entitlement->metadata['printSubscriptionID'] && ($response[static::TYPE] === static::REACTIVATION)) {
            $response[static::IS_REACTIVATION_CODE] = true;
        }

        if (!empty($product->Activate_Page_Message)) {
            $response[static::CUSTOM_MESSAGE] = $product->Activate_Page_Message;
        }

        // SB-16 added by mtanada 20190107 Setting of HM ID
        $hmIdArray = $this->getHmIdPerTab($entitlement->id, $product->Tabs);
        // More than 1 HM ID
        if (count($hmIdArray) > 1) {
            $response['hmID'] = array_map('intval', $hmIdArray);
            $this->hmId = $response['hmID'];
        } else {
            $response['hmID'] = (int)$hmIdArray[0];
            $this->hmId = $response['hmID'];
        }

        if ($permission->expired_at) {
            $today = date(static::TIME_FORMAT1);
            $eol = date(static::TIME_FORMAT1, strtotime($permission->expired_at));
            if ($today > $eol) {
                $response[static::USABLE] = false;
            }
        }
        return $response;
    }

    protected function proceedTNGProductActivation()
    {
        if ($this->activateInGo) {
            parent::proceedTNGProductActivation();
        }

        $permissionInstance = $this->getPermission();
        $entitlement = $permissionInstance->entitlement;

        $product = $entitlement->product()->fetch();

        if (!$product->Tabs) {
            $this->messageCode = static::MSG_CODE_SUBSCRIPTION_ALREADY_REMOVED;
            return false;
        }

        $entitlementEndDate = ($entitlement->EndDate instanceof DateTime)
            ? $entitlement->EndDate->format(static::TIME_FORMAT1)
            : $entitlement->EndDate;

        $calculatedActivationEndDate = $this->getEndDate(
            $entitlement->Type,
            $entitlementEndDate,
            $entitlement->EndOfYearBreakPoint,
            $entitlement->EndOfYearOffset,
            $entitlement->Duration
        );

        $calculatedActivationEndDate = date(
            static::TIME_FORMAT2,
            strtotime($calculatedActivationEndDate)
        );

        // GCAP-839 modified by mtanada - to accept string for user id
        try {
            $activation = new Activation();
            $activation->permission_id = $permissionInstance->id;
            $activation->user_id = $this->userID;
            $activation->activated_at = date(static::TIME_FORMAT2);
            $activation->ended_at = $calculatedActivationEndDate;
            $activation->metadata = [
                'Notes' => null,
                'Archive' => null,
                'ArchivedDate' => null,
                'PurchaseType' => $this->purchaseTypeHub,
                'CreatedBy' => $this->createdBy,
                // SB-103 modified by machua 20190320
                'DateDeactivated' => $this->limitedDateDeactivated
            ];

            // SB-2 added by Michael Abrigos 20190116
            if ($this->limitedEndDate !== "" && $this->limitedEndDate !== null) {
                $limited_date = new DateTime();
                $activation->ended_at = $this->limitedEndDate . ' 23:59:00';
                $activation->metadata = array_merge($activation->metadata, array('Limited' => true));
            } else {
                $activation->metadata = array_merge($activation->metadata, array('Limited' => false));
            }
            $activation->save();
            $this->lastActivation = $activation;
        } catch (Exception $e) {
            error_log($e);
            $this->messageCode = static::MSG_CODE_CONNECTION_ERROR;
            return false;
        }


        if (is_null($activation->id)) {
            $this->messageCode = static::MSG_CODE_CONNECTION_ERROR;
            return false;
        }

        try {
            if ($entitlement->LimitActivation === 'Y') {
                $permissionInstance->released_at = null;
            } else {
                $permissionInstance->released_at = date(static::TIME_FORMAT2);
            }

            $permissionInstance->save();
        } catch (Exception $e) {
            error_log($e);
            $activation->delete();
            return false;
        }

        $setEndDate = date('Y-m-d', strtotime($calculatedActivationEndDate));
        return [
            static::END_DATE => $setEndDate
        ];
    }

    public function setActivationOwner($userId)
    {
        $this->userID = $userId;
    }

    public function setPermission($permission)
    {
        $this->permission = $permission;
    }

    protected function setUserID()
    {
        if (!$this->userID) {
            $u = new User();
            $this->userID = $u->getUserID();
        }
    }

    protected function getHMParams()
    {
        $userId = isset($this->userID) ? $this->userID : 0;
        return array(
            'userId' => $userId,
            'hmProductId' => $this->hmId,
            'accessCode' => $this->accessCode,
            'response' => 'STRING'
        );
    }

    protected function proceedHotmathsActivation()
    {
        $hmParams = $this->getHMParams();
        $hmAPI = new HotMathsApi($hmParams);
        $this->setUserID();
        $user = User::getByUserID($this->userID);

        $tabIds = array_map(function ($tab) {
            return $tab['id'];
        }, $this->getTabsByPermission());
        $tab = $this->activationModel->getHotMathsTabsByTabIds($tabIds);
        $tab = $tab[0];

        $isTeacherActivatingStudent = $this->hasTeacherResourceAndActivatingStudentResource($user, $tab);
        if ($isTeacherActivatingStudent) {
            $this->message = 'You are trying to activate a Student Product.';
            $this->message .= 'You already have an existing Teacher Version of the same product.';
            return false;
        }
        // validating HM product
        $hmAPI->activationHmSubscription();
        $hmResponse = $hmAPI->getResponse();
        $hmError = $hmAPI->getError();

        if (is_array($hmResponse)) {
            $this->message = $hmResponse[static::MESSAGE];
        } elseif (is_array($hmError)) {
            $this->message = $hmError[static::MESSAGE];
        } else {
            if ($this->hmCount < 1) {
                $this->hmCount++;
                $tngActivationResult = $this->proceedTNGProductActivation();
            }
            $hmAPI->setEndDate($tngActivationResult[static::END_DATE]);
            // adding HM Product to user
            $hmAPI->resumeActivationHmSubscription();

            return true;
        }
        return false;
    }

    protected function getTabsByPermission()
    {
        $entitlement = $this->getPermission()->entitlement()->fetch();
        $product = $entitlement->product()->fetch();

        return $product->Tabs;
    }

    protected function getPurchaseType()
    {
        return $this->purchaseTypeHub;
    }

    public function setPurchaseType($purchaseType)
    {
        $this->purchaseTypeHub = $purchaseType;
    }

    protected function getCreatedby()
    {
        return $this->getCreatedby;
    }

    public function setCreatedby($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    protected function hasTeacherResourceAndActivatingStudentResource($user, $tab)
    {
        $tabName = strtolower($tab['TabName']);

        $activations = Activation::where([
            'user_id' => $user->uID,
            'is_paginated' => 0
        ]);

        $activations = array_filter($activations, function ($activation) {
            $notExpired = $activation->DaysRemaining > 0;
            $notDeactivated = is_null($activation->DateDeactivated);
            $notArchived = is_null($activation->Archive);

            return $notExpired && $notDeactivated && $notArchived;
        });

        $tabs = [];
        foreach ($activations as $activation) {
            $tabs = array_merge(
                $tabs,
                $activation->permission->entitlement->product->Tabs
            );
        }
        $tabs = array_column($tabs, 'id');

        $activationsWithTitleId = $this->activationModel->getTabsFromTabIdsWithTitleId($tabs, $tab['TitleID']);
        $activatedTabNames = array_map(
            'strtolower',
            array_column($activationsWithTitleId, 'TabName')
        );

        $hasHmActivation = count($activationsWithTitleId) >= 1;
        $isTeacher = $user->uGroups[5] === 'Teacher';
        $hasHmActivationAndTeacher = $hasHmActivation && $isTeacher;

        $activatedTabHasTeacherTab = array_intersect(
            $activatedTabNames,
            [
                'online teacher resource',
                'online teacher edition'
            ]
        );
        $hasTeacherInTabName = strpos(implode('', $activatedTabNames), 'teacher');
        $hasActiveTeacherResource = $activatedTabHasTeacherTab || $hasTeacherInTabName;

        $tabNameHasStudent = strpos($tabName, 'student') !== false;
        $tabNameIsForStudent = $tabName === 'online resource';
        $isActivatingAStudentResource = $tabNameHasStudent || $tabNameIsForStudent;

        if ($hasHmActivationAndTeacher && $hasActiveTeacherResource && $isActivatingAStudentResource) {
            return true;
        }

        return false;
    }

    /* ANZGO-3853 added by mtanada 20180906
     * Matching of Product metadata of edumar_titleID
     * for Print Access code and Reactivation access code
     * ANZGO-3854 modifed by jbernardez 20180913
     */
    public function checkPrintAndReactivationMatch($accessCode, $type)
    {
        // Print Access Code
        $entitlement = $this->permission->entitlement;
        $product = $entitlement->product()->fetch();
        $printCodeEdumarTitleID = $product->metadata['edumar_titleID'];

        // Reactivation Access Code
        $reactPermission = Permission::where([
            'proof' => $accessCode
        ]);
        if (empty($reactPermission)) {
            return false;
        }
        $reactPermission = array_pop($reactPermission);
        $reactEntitlement = $reactPermission->entitlement;
        $reactProduct = $reactEntitlement->product()->fetch();

        $reactCodeEdumarTitleID = $reactProduct->metadata['edumar_titleID'];

        // SB-364 modified by jbernardez/mtanada 20191023
        // corrected the matching as there will be a null === null match
        if ($printCodeEdumarTitleID && $reactCodeEdumarTitleID) {
            $matchEdumarTitleID = $printCodeEdumarTitleID === $reactCodeEdumarTitleID;
        } else {
            $matchEdumarTitleID = false;
        }

        if ($type === static::REACTIVATION) {
            return $reactEntitlement->Type === $type && $matchEdumarTitleID;
        } elseif ($type === static::PRINT_TYPE) {
            return $reactEntitlement->Type !== static::REACTIVATION && $matchEdumarTitleID;
        }
        return false;
    }

    /* SB-364 added by jbernardez/mtanada 20191023
     * Matching of Product metadata of reactivationSubscriptionID
     * for Print Access code and Reactivation access code
     */
    public function checkPrintAndReactivationMatchViaSubscription($accessCode, $type)
    {
        // if print code was called first
        if ($type === static::REACTIVATION) {
            // print code
            $entitlement = $this->permission->entitlement;
            $product = $entitlement->product()->fetch();
            $printID = $product->id;

            // reactivation Code
            $reactivationPermission = Permission::where([
                'proof' => $accessCode
            ]);
            if (empty($reactivationPermission)) {
                return false;
            }

            $reactivationPermission = array_pop($reactivationPermission);
            $reactivationEntitlement = $reactivationPermission->entitlement;
            // SB-430 updated by mabrigos
            $reactivationPrintIds = explode(',', $reactivationEntitlement->metadata['printSubscriptionID']);

            foreach ($reactivationPrintIds as $reactivationPrintId) {
                if ((int)$reactivationPrintId === (int)$printID) {
                    return true;
                }
            }
            return false;
        }

        // if reactivation code was called first
        if ($type === static::PRINT_TYPE) {
            // Reactivation code
            $entitlement = $this->permission->entitlement;
            $product = $entitlement->product()->fetch();
            $printID = $entitlement->id;

            // print Code
            $printPermission = Permission::where([
                'proof' => $accessCode
            ]);
            if (empty($printPermission)) {
                return false;
            }

            $printPermission = array_pop($printPermission);
            $printEntitlement = $printPermission->entitlement;
            $printProduct = $printEntitlement->product()->fetch();

            $tempEntitlements = array();
            if ($printProduct->metadata['reactivationSubscriptionID']) {
                foreach ($printProduct->metadata['reactivationSubscriptionID'] as $entitlement) {
                    $tempEntitlements[] = (int)$entitlement;
                }
            } else {
                return false;
            }

            $inArrayResult = in_array($printID, $tempEntitlements);
            if ($inArrayResult && !$printPermission->IsUsable) {
                return true;
            } 
        }

        return false;
    }

    /**
     * ANZGO-3914 Added by Shane Camus 11/27/18
     * @param null $type
     * @return array
     */
    public function provisionProductInGo($userID = null, $type = null, $email = null, $limitedEndDate = null, $limitedDateDeactivated = null)
    {
        $meta = array();
        $this->permission = $this->getPermission();
        $entitlement = $this->permission->entitlement()->fetch();
        $this->permission->entitlement = $entitlement;
        // SB-14 added by machua 20190111 to get HMIDs from the product tabs
        $product = $entitlement->product()->fetch();
        $hmIdArray = $this->getHmIdPerTab($entitlement->id, $product->Tabs);
        //SB-2 added by mabrigos 20190116 
        $this->limitedEndDate = $limitedEndDate;
        //SB-103 added by machua 20190320
        $this->limitedDateDeactivated = $limitedDateDeactivated;
        //SB-14 modified by machua 20190110 to accommodate multiple hmIDs
        if (count($hmIdArray) >= 1 && $hmIdArray[0]) {
            foreach ($hmIdArray as $key => $hmId) {
                $hmMeta = array();
                $this->hmId = 0;
                $this->checkUserAndProductCompatibilityWithHM($type, (int)$hmId);

                if (!$this->hasNoError) {
                    return array(
                        static::SUCCESS => $this->hasNoError,
                        static::MESSAGE => $this->message,
                        static::ACTION => $this->action
                    );
                }

                // $this->checkUserAccountInHMIfValid($userID, $email);

                // if (!$this->hasNoError) {
                //     return array(
                //         static::SUCCESS => $this->hasNoError,
                //         static::MESSAGE => $this->message,
                //         static::ACTION => $this->action
                //     );
                // }

                $hmIdArray[$key] = $this->hmId;
                $hmMeta['limitedProduct'] = $this->action === 'AddingStudentProductForTeacher';
                $meta[] = $hmMeta;
            }
        }
        $this->hasNoError = false;
        $this->action = 'TNGProvisionError';
        $this->message = 'Failed adding subscription';

        // mtanada 20200213 SB-493
        $remainingRetryCount = HubActivation::RETRY_PRODUCT_ACTIVATION;
        do {
            $response = $this->preTNGProductActivation();
            if ($response && isset($response['endDate'])) {
                $responseMeta = $this->successTNGProductActivation($response, $hmIdArray, $meta);
                $this->hasNoError = true;
                $this->message = 'Subscription added in Go.';
                $this->action = 'Provisioned';
                $remainingRetryCount = 0;
            } else {
                $remainingRetryCount--;
            }
        } while ($remainingRetryCount > 0);

//        if ($response) {
//            //SB-14 modified by machua 20190110 to accommodate multiple hmIDs
//            if (count($hmIdArray) >= 1 && $hmIdArray[0]) {
//                $hmIdCount = 0;
//                foreach ($hmIdArray as $hmId) {
//                    $meta[$hmIdCount]['productExpiryDate'] = $response['endDate'];
//                    $meta[$hmIdCount]['productId'] = (int)$hmId;
//                    $hmIdCount++;
//                }
//
//            }
//            $this->hasNoError = true;
//            $this->message = 'Subscription added in Go.';
//            $this->action = 'Provisioned';
//        }
//        else {
//            $retryCount--;
//            if ($retryCount < 1) {
//                $this->proceedTNGProductActivation();
//            }
//        }

        return array(
            static::SUCCESS => $this->hasNoError,
            static::MESSAGE => $this->message,
            static::ACTION => $this->action,
            'meta' => $responseMeta,
            'remainingRetryCount' => $remainingRetryCount
        );
    }

    // mtanada 20200213 SB-493
    public function preTNGProductActivation() {
        return $this->proceedTNGProductActivation();
    }
    // mtanada 20200213 SB-493
    public function successTNGProductActivation($response, $hmIdArray, $meta) {
        //SB-14 modified by machua 20190110 to accommodate multiple hmIDs
        if (count($hmIdArray) >= 1 && $hmIdArray[0]) {
            $hmIdCount = 0;
            foreach ($hmIdArray as $hmId) {
                $meta[$hmIdCount]['productExpiryDate'] = $response['endDate'];
                $meta[$hmIdCount]['productId'] = (int)$hmId;
                $hmIdCount++;
            }
        }
        return $meta;
    }

    /**
     * ANZGO-3914 Added by Shane Camus 11/27/18
     * @param $type
     */
    public function checkUserAndProductCompatibilityWithHM($type, $hmId)
    {
        // $hmAPI = new HMProduct(array(
        //     'userID' => '',
        //     'hmProductID' => $hmId
        // ));

        // $hmProduct = $hmAPI->getProduct();
        // var_dump($hmProduct); die;

        $hmProduct = $this->getProductByHMID($hmId);
        $productUserType = strtolower($hmProduct['subscriberType']);
        $userType = strtolower($type);

        $this->hmId = $hmProduct['productId'];
        $this->hasNoError = true;

        if ($userType !== $productUserType) {
            $this->action = 'HMProvisionCompatibility';
            $this->message = 'Hotmaths product is incompatible.';
            $this->hasNoError = false;

            if (
                ($userType === 'teacher' &&
                $productUserType === 'student') &&
                !is_null($hmProduct['teacherProductId'])
            ) {
                $user = User::getByUserID($this->userID);

                $tabIds = array_map(function ($tab) {
                    return $tab['id'];
                }, $this->getTabsByPermission());
                $tab = $this->activationModel->getHotMathsTabsByTabIds($tabIds);
                $tab = $tab[0];

                if (!$this->hasTeacherResourceAndActivatingStudentResource($user, $tab)) {
                    $this->action = 'AddingStudentProductForTeacher';
                    $this->hasNoError = true;
                    $this->hmId = $hmProduct['teacherProductId'];
                } else {
                    $this->message = 'Teacher already has student product';
                }
            }

        } 

        // if ($userType === 'student' && $productUserType === 'teacher') {
        //     $this->message = 'Hotmaths product is incompatible.';
        // } elseif ($userType === 'teacher' && $productUserType === 'student') {
        //     if (!is_null($hmProduct['teacherProductId'])) { 
        //         $user = User::getByUserID($this->userID);

        //         $tabIds = array_map(function ($tab) {
        //             return $tab['id'];
        //         }, $this->getTabsByPermission());
        //         $tab = $this->activationModel->getHotMathsTabsByTabIds($tabIds);
        //         $tab = $tab[0];

        //         if (!$this->hasTeacherResourceAndActivatingStudentResource($user, $tab)) {
        //             $this->action = 'AddingStudentProductForTeacher';
        //             $this->hasNoError = true;
        //             $this->hmId = $hmProduct['teacherProductId'];
        //         } else {
        //             $this->message = 'Teacher already has student product';
        //         }
        //     } else {
        //         $this->message = 'Hotmaths product is incompatible.';
        //     }
        // } else {
        //     $this->hmId = $hmProduct['productId'];
        //     $this->hasNoError = true;
        // }
    }

    public function getProductByHMID($hmPID)
    {
        $db = Loader::db();
        $sql = "SELECT * FROM HMProducts WHERE productId = ?";
        return $db->GetRow($sql, array($hmPID)); 
    }

    /**
     * @param $userID
     * @param $email
     */
    public function checkUserAccountInHMIfValid($userID, $email)
    {
        $this->hasNoError = false;

        $hmAPI = new HMUser(
            array(
                'userID' => $userID,
                'hmProductID' => '',
                'responseType' => 'JSON'
            )
        );

        $hmUser = $hmAPI->getUser();

        // SB-14 modified by machua 20190111 to check if there is an existing externalID in HotMaths
        if ($hmUser->success === "" && ($email !== $hmUser->username)) {
            $this->action = 'Existing';
            $this->message = 'User already created in HM';
        } else {
            $this->hasNoError = true;
        }
    }

    /**
     * ANZGO-3910 addded by mtanada 20190108
     * Array return of entitlement id, tab id and HM id
     * @param $entitlementID
     * @param $tabIds
     * @return array
     */
    public function getHmIdPerTab($entitlementID, $tabIds)
    {
        $hmIdArray = array();
        foreach ($tabIds as $key => $tabId) {
            $hmid = CupGoTabHmIds::getHmIdByEntitlementIdAndTabId($entitlementID, $tabId['id']);
            // Get all unique HM IDs
            if (!in_array($hmid, $hmIdArray)) {
                if ($hmid) {
                    $hmIdArray[] = $hmid;
                }
            }
        }
        return $hmIdArray;
    }

    // GCAP-416 Added by mtanada, 05/20/2019
    protected function checkIfTeacherResourcePackage()
    {
        $isTRP = false;

        if (empty($this->accessCode)) {
            $result = $this->activationModel->getTabNamesBySAID($this->saID);
        } else {
            $tabIds = $this->getTabsByPermission();
            if ($tabIds) {
                $tabs = array();
                foreach ($tabIds as $tabId) {
                    array_push($tabs, $tabId['id']);
                }
                $result[] = $this->activationModel->getTabNameById(array_values($tabs));
            }
        }

        foreach ($result as $row) {
            if (strtolower($row['TabName']) == "teacher resource package") {
                $isTRP = true;
                break;
            }
        }

        return $isTRP;
    }
}