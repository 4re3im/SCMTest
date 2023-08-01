<?php

use HubEntitlement\Models\Activation;
use HubEntitlement\Models\Product;

/**
 * Class HubActivationList
 * @author jsunico@cambridge.org
 */

Loader::model('cup_go_user_subscription_list', 'go_contents');

class HubActivationList extends CupGoUserSubscriptionList
{
    /**
     * @var array
     */
    protected $activations = [];

    /**
     * @var array
     */
    protected $subscriptions = [];

    const DATE_FORMAT = 'Y-m-d H:i:s';

    public function getUserSubscriptions($userId)
    {
        $this->activations = Activation::where([
            'is_paginated' => 0,
            'user_id' => $userId
        ]);

        return $this->activations;
    }

    public function removeArchivedSubscriptions()
    {
        $this->activations = array_filter($this->activations, function ($a) {
            return is_null($a->Archive);
        });
    }
    //SB-2 modified by mabrigos 20190116 added limited flag
    public function transformListForDisplay()
    {
        return array_map(function ($activation) {
            $stringCreatedAt = $activation
                ->activated_at
                ->format(static::DATE_FORMAT);
            $stringEndDate = $activation
                ->ended_at
                ->format(static::DATE_FORMAT);
            $permission = $activation->permission()->fetch();
            $entitlement = $permission->entitlement()->fetch();
            $entitlementCreationDate = $entitlement->created_at
                ->format(static::DATE_FORMAT);
            $entitlementStartDate = !empty($entitlement->StartDate)
                ? $entitlement->StartDate->format(static::DATE_FORMAT)
                : null;
            $entitlementEndDate = !empty($entitlement->EndDate)
                ? $entitlement->EndDate->format(static::DATE_FORMAT)
                : null;
            $entitlementDuration = $entitlement->Duration;
            $entitlementType = $entitlement->Type;

            $product = $entitlement->product()->fetch();
            $isActive = $activation->isActive ? 'Y' : 'N';

            return [
                'US_ID'                 => $activation->id,
                'SA_ID'                 => $entitlement->id,
                'UserSubscriptionID'    => $activation->id,
                'USubCreationDate'      => $stringCreatedAt,
                'USubEndDate'           => $stringEndDate,
                'limited'               => $activation->Limited,
                'USubDateDeactivated'   => $activation->DateDeactivated,
                'ISBN_13'               => $product->ISBN_13,
                'AccessCode'            => $permission->proof,
                'Active'                => $isActive,
                'DaysRemaining'         => $activation->daysRemaining,
                'CreationDate'          => $entitlementCreationDate,
                'StartDate'             => $entitlementStartDate,
                'EndDate'               => $entitlementEndDate,
                'Duration'              => $entitlementDuration,
                'Type'                  => $entitlementType,
                'SubscriptionAvailID'   => $entitlement->id,
                'Name'                  => $product->Name,
                'Description'           => $product->Description,
                'CMS_Name'              => $product->CMS_Name,
                'SubscriptionID'        => $product->id,
                'HmID'                  => $entitlement->HmID,
                'Source'                => 'Go'
            ];
        }, $this->activations);
    }

    // ANZGO-3947 modified by scamus 20181212 Removed sorting of Tabs based on created dates
    public function fetchMyResourcesList($userId)
    {
        $this->getUserSubscriptions($userId);
        $this->removeArchivedSubscriptions();
        $goSubscriptions = $this->transformListForDisplay();
        $hotMathsSubscriptions = $this->getExternalSubscriptionsByUserId($userId);
        $resources = array_merge($goSubscriptions, $hotMathsSubscriptions);

        $this->subscriptions = $this->getResourcesTitleInfo($resources);
        return $this->subscriptions;
    }

    public function fetchMyResourcesListByActivationId($activationId)
    {
        $this->getUserSubscriptionsByActivationId($activationId);
        $this->removeArchivedSubscriptions();
        $resources = $this->transformListForDisplay();
        $userSubscriptions = $this->getResourcesTitleInfo($resources);
        $this->subscriptions = $userSubscriptions;
        $this->sortSubscriptionTabs();

        return $this->subscriptions;
    }

    public function getUserSubscriptionsByActivationId($activationId)
    {
        $this->activations = [Activation::find($activationId)];

        return $this->activations;
    }

    public function getResourcesTitleInfo($resources)
    {
        $userSubscriptions = [];
        foreach ($resources as $resource) {
            if ($resource['Source'] === 'Go') {
                $product = Product::find($resource['SubscriptionID']);
                $tabIds = $product->Tabs;
                $tabsWithTitleInfo = $this->getTitleInfoFromTabs($tabIds);

                foreach ($tabsWithTitleInfo as $tabs) {
                    $titleId = (int)$tabs['titleID'];
                    $userSubscriptions[$titleId][] = array_merge($tabs, $resource);
                }
            } else {
                $titleId = (int)$resource['titleID'];
                $userSubscriptions[$titleId][] = $resource;
            }
        }

        return $userSubscriptions;
    }

    public function sortSubscriptionTabs()
    {
        foreach ($this->subscriptions as &$tabs) {
            usort($tabs, function ($a, $b) {
                if (strtotime($a['USubCreationDate']) === strtotime($b['USubCreationDate'])) {
                    return 0;
                }

                return (strtotime($a['USubCreationDate']) > strtotime($b['USubCreationDate'])) ? -1 : 1;
            });
        }
    }

    public function getPage($offset, $limit)
    {
        return array_slice($this->subscriptions, $offset, $limit, true);
    }

    public function getTitleInfoFromTabs(array $tabs)
    {
        if (!$tabs) {
            return [];
        }

        $tabIds = implode(
            ',',
            array_map(function ($tab) {
                return $tab['id'];
            }, $tabs)
        );

        $db = Loader::db();
        $query = <<<QUERY
            SELECT
              cgt.ID as TabID,
              cct.id as titleID,
              cct.name,
              cct.isbn13,
              cct.displayName,
              cct.prettyUrl
            FROM CupGoTabs cgt
            INNER JOIN CupContentTitle cct ON cgt.TitleID = cct.id
            WHERE cgt.ID IN ($tabIds)
QUERY;

        return $db->GetAll($query);
    }

    public function getTitleByBrandCode($brandCode)
    {
        $db = Loader::db();
        $sql = <<<SQL
            SELECT
                cct.id AS titleID,
                cct.name,
                cct.isbn13,
                cct.displayName,
                cct.prettyURL
            FROM CupContentTitle cct
            INNER JOIN CupGoBrandCodeTitles cgbct ON cct.id = cgbct.titleID
            WHERE cgbct.brandCode = ?;
SQL;

        return $db->GetAll($sql, [$brandCode]);
    }

    public function getExternalSubscriptionsByUserId($userId)
    {
        $db = Loader::db();
        // SB-9 modified by jbernardez 20191105
        // added tokenExpiryDate as USubEndDate
        // added UpdateDate
        // SB-424 modified by jbernardez 20200127
        // changed tokenExpiryDate to brandExpiryDate
        $sql = <<<SQL
            SELECT
                cgeu.ID AS UserSubscriptionID,
                cgeu.CreationDate as USubCreationDate,
                cgeu.brandExpiryDate as USubEndDate,
                null as ISBN_13,
                null as AccessCode,
                null as Active,
                null as DaysRemaining,
                null as CreationDate,
                null as StartDate,
                null as EndDate,
                null as Duration,
                null as Type,
                0 as SubscriptionAvailID,
                null as Name,
                null as Description,
                null as CMS_Name,
                0 as SubscriptionID,
                cgeu.authToken,
                cgeu.externalID,
                cgeu.brandCodes,
                cgeu.UpdateDate,
                cgbct.titleID,
                cct.name,
                cct.isbn13,
                cct.displayName,
                cct.prettyUrl,
                'HOTMATHS' as Source
            FROM CupGoExternalUser cgeu
            INNER JOIN CupGoBrandCodeTitles cgbct ON FIND_IN_SET(brandCode, cgeu.brandCodes)
            INNER JOIN CupContentTitle cct ON cct.id = cgbct.titleID
            WHERE cgeu.uID = ?;
SQL;
        return $db->GetAll($sql, [$userId]);
    }

    /* SB-239
     * Added by machua 20190628
     * Used in title page retrieve the corresponding user subscription for the specific title
     * @param titleID
     */
    public function getSubscriptionByTitleID($titleID)
    {
        if (!$this->subscriptions) {
            return [];
        }

        $formattedSubscriptions = [];
        foreach ($this->subscriptions as $subTitleId => $tabsSubscribed) {
            if (!is_null($tabsSubscribed['USubDateDeactivated'])) {
                continue;
            }

            if ((int)$subTitleId === (int)$titleID) {
                foreach ($tabsSubscribed as $tabSubscriptionData) {
                    $daysRemaining  = $tabSubscriptionData['DaysRemaining'];
                    $isExpired       = is_null($daysRemaining) ? 0 : $daysRemaining;
                    $isExpired       = $isExpired <= 0;
                    $isDeactivated   = !is_null($tabSubscriptionData['USubDateDeactivated']);
                    $tabID           = $tabSubscriptionData['TabID'];
                    $entitlementID   = $tabSubscriptionData['SA_ID'];
                    if (!$isExpired && !$isDeactivated) {
                        if (!array_key_exists($tabID, $formattedSubscriptions) ||
                            ((array_key_exists($tabID, $formattedSubscriptions))
                            && ($formattedSubscriptions[$tabID]['DaysRemaining'] > $daysRemaining))) {
                            $formattedSubscriptions[$tabID] = array(
                                'entitlementID' => $entitlementID,
                                'daysRemaining' => $daysRemaining
                            );
                        }
                    }
                }
            }
        }

        return $formattedSubscriptions;
    }
}
