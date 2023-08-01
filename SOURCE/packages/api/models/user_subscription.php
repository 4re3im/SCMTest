<?php

/**
 * ANZGO-3951 , Added by John Renzo S. Sunico, 1/10/2018
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'base_model.php';

class UserSubscription extends BaseModel
{
    public $_table = 'CupGoUserSubscription';

    public function __construct()
    {
        Loader::model('subscription_availability', 'api');
        Loader::model('subscription', 'api');
        Loader::model('access_code', 'api');
        parent::__construct();
    }

    public function findSubscriptionByAccessCode($accessCode)
    {
        $db = Loader::db();
        $latestID = $db->GetRow(
            'SELECT ID FROM ' . $this->_table . ' WHERE UPPER(AccessCode) = ? ORDER BY CreationDate DESC LIMIT 1',
            [$accessCode]
        )['ID'];

        return $this->Load('ID = ?', [$latestID]);
    }

    public function getBriefDetails()
    {
        $basicInfo = ['activated' => 'N', 'expiration' => '0000-00-00 00:00:00', 'productID' => '0'];

        if ($this->ID <= static::INVALID_ID) {
            return $basicInfo;
        }

        $basicInfo['activated'] = 'Y';
        $basicInfo['expiration'] = $this->EndDate;
        $basicInfo['productID'] = $this->getProductID();

        return $basicInfo;
    }

    public function getHMProductID()
    {
        $subscriptionAvailability = new SubscriptionAvailability();

        if (!$subscriptionAvailability->loadByID($this->SA_ID)) {
            return 0;
        }

        return $subscriptionAvailability->HmID;
    }

    public function getProductID()
    {
        $subscriptionAvailability = new SubscriptionAvailability();
        $subscriptionID = $subscriptionAvailability->S_ID;
        $subscription = new Subscription();
        $subscription->loadByID($subscriptionID);
        $tab = $subscription->getFirstTab();

        if (!$tab) {
            return 0;
        }

        return $tab->TitleID;
    }
}
