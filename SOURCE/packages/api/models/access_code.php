<?php

/**
 * ANZGO-3951 , Added by John Renzo S. Sunico, 1/12/2018
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'base_model.php';

class AccessCode extends BaseModel
{
    const ACTIVATED = 'activated';
    const PRODUCT_ID = 'productID';
    const EXPIRATION = 'expiration';
    const EXISTS = 'exists';

    public $_table = 'CupGoAccessCodes';

    public function __construct()
    {
        Loader::model('user_subscription', 'api');
        Loader::model('subscription_availability', 'api');
        Loader::model('subscription', 'api');

        parent::__construct();
    }

    public function loadByAccessCode($accessCode)
    {
        $this->AccessCode = $accessCode;

        return $this->Load('UPPER(AccessCode) = ?', [$accessCode]);
    }

    public function getSubscriptionInfo()
    {
        $basicInfo = [
            static::ACTIVATED => 'N',
            static::EXPIRATION => '0000-00-00 00:00:00',
            static::PRODUCT_ID => '0',
            static::EXISTS => false
        ];

        if ($this->ID <= static::INVALID_ID) {
            return $basicInfo;
        }

        $userSubscription = new UserSubscription();
        $userSubscription->findSubscriptionByAccessCode($this->AccessCode);

        $basicInfo[static::EXISTS] = true;
        $basicInfo[static::PRODUCT_ID] = $this->getGoProductID();
        if ($userSubscription->ID > static::INVALID_ID) {
            $basicInfo[static::ACTIVATED] = 'Y';
            $basicInfo[static::EXPIRATION] = $userSubscription->EndDate;
        }

        return $basicInfo;
    }

    public function getGoProductID()
    {
        $subscriptionAvailability = new SubscriptionAvailability();
        $subscriptionAvailability->loadByID($this->SA_ID);

        $subscription = new Subscription();
        $subscription->loadByID($subscriptionAvailability->S_ID);

        $tab = $subscription->getFirstTab();

        return $tab->TitleID;
    }

    /* ANZGO-3757 added by jbernardez 20180621
     * TNG: CupGoAccessCodeReactivations exists on GO
     * @param Print Access Code & new REACTIVATION CODE in saving eliminate dependency on PEAS for AC referencing
     * NOTE: Removed methods: getAccessCodeID, updateAccessCode & updateAccessCodePeas
     */
    public function processReactivationCode($promoCode, $reactivationCode)
    {
        $addResult = $this->addReactivationReference($promoCode, $reactivationCode);

        if ($addResult !== false) {
            return true;
        } else {
            return false;
        }
    }

    // ANZGO-3757 added by jbernardez 20180622
    private function addReactivationReference($promoCodeID, $reactivationCodeID)
    {
        $sql = 'INSERT INTO CupGoAccessCodeReactivations (AccessCodeParentID, AccessCodeChildID) VALUES (?, ?);';
        $params = array($promoCodeID, $reactivationCodeID);

        try {
            $this->db->Execute($sql, $params);
            return $this->db->Insert_ID('CupGoAccessCodeReactivations');
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        return false;
    }
}
