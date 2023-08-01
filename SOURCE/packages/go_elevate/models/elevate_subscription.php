<?php defined('C5_EXECUTE') or die(_('Access Denied.'));

class ElevateSubscription
{
    public static function getAllByUserID($uID)
    {
        // GCAP-789 modified by machua 20032020 get subscriptions in PEAS
        $subscriptions = self::fetchElevateSubscriptions($uID);
        return $subscriptions;
    }

    // HUB-160 Modified by John Renzo S. Sunico, 09/04/2018
    public static function getTabAccessByISBN($uID, $isbn)
    {
        Loader::library('Activation/user_activation');
        $db = Loader::db();
        $sql = 'SELECT * FROM CupGoTabs WHERE ElevateProduct = \'Y\' AND Private_TabText LIKE ? LIMIT 1;';

        $result = $db->GetRow($sql, ['%' . $isbn . '%']);

        if (!$result) {
            return [];
        }

        $activationLib = new UserActivation();
        $activationLib->setUserId($uID);
        $subscribedTabIds = $activationLib->getSubscribedTabIds();
        $hasAccessToTab = !empty(array_intersect($subscribedTabIds, [$result['ID']]));

        return $hasAccessToTab ? $result : [];
    }

    public static function isElevateSample($isbn)
    {
        $db = Loader::db();
        $sql = "SELECT id FROM CupGoTabs WHERE Public_TabText LIKE '%/go/ereader/" . mysql_escape_string($isbn) . "/%' LIMIT 1;";
        $result = $db->getRow($sql);
        return $result;
    }

    // GCAP-789 added by machua 20032020 get the ISBN and Expiration of user Elevate subscriptions
    public function fetchElevateSubscriptions($userId)
    {
        Loader::library('hub-sdk/autoload');
        $activations = \HubEntitlement\Models\Activation::where([
            'is_paginated' => 0,
            'user_id' => $userId,
        ]);

        $activations = array_filter($activations, function ($a) {
            return is_null($a->Archive);
        });

        $subscriptions = self::getElevateSubscriptions($activations);

        return $subscriptions;
    }

    public function getElevateSubscriptions($activations)
    {
        $formattedSubscriptions = array();
        $subscriptions = array();
        foreach ($activations as $activation) {
            $stringEndDate = $activation
                ->ended_at
                ->format('Y-m-d H:i:s');
            if ($stringEndDate > date('Y-m-d H:i:s')
                && $activation->DateDeactivated === null
                && $activation->isActive) {
                $permission = $activation->permission()->fetch();
                $entitlement = $permission->entitlement()->fetch();
                $product = $entitlement->product()->fetch();
                $productTabs = $product->Tabs;
                $tabIds = array_column($productTabs, 'id');
                $tabsWithTitleInfo = self::getElevateTabs($tabIds);
                foreach ($tabsWithTitleInfo as $tabs) {
                    $tabISBN = $tabs['ISBN'];
                    if(array_key_exists($tabISBN, $subscriptions)
                        && $subscriptions[$tabISBN] > $stringEndDate) {
                        continue;
                    } else {
                        $subscriptions[$tabISBN] = $stringEndDate;
                    }
                }
            }
        }

        //Format subscriptions
        foreach($subscriptions as $isbn => $endDate) {
            $subscription['ISBN'] = (string)$isbn;
            $subscription['EndDate'] = $endDate;
            $formattedSubscriptions[] = $subscription;
        }

        return $formattedSubscriptions;
    }

    public function getElevateTabs($tabIds)
    {
        $tabIds = implode(',', $tabIds);
        $db = Loader::db();
        $query = <<<QUERY
            SELECT
              cgt.ID as TabID,
              cct.id as titleID,
              cgt.ResourceURL,
              REPLACE(SUBSTRING_INDEX(cgt.ResourceURL, '/', -2), '/', '') as ISBN,
              cgt.ElevateProduct
            FROM CupGoTabs cgt
            INNER JOIN CupContentTitle cct ON cgt.TitleID = cct.id
            WHERE cgt.ID IN ($tabIds) and cgt.ElevateProduct = 'Y'
QUERY;

        return $db->GetAll($query);
    }
}
