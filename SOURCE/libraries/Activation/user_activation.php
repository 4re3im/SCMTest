<?php

/**
 * Class UserActivation
 * Library to help in retrieving User Activations
 */
Loader::library('hub-sdk/autoload');

use HubEntitlement\Models\Activation;

class UserActivation
{
    /**
     * @var $userId
     */
    private $userId;

    private $activations;

    const ACTIVE_ACTIVATIONS = 1;
    const NOT_PAGINATED = 0;

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getActiveSubscriptions()
    {
        if ($this->activations) {
            return $this->activations;
        }

        if (!$this->userId) {
            return [];
        }

        return $this->activations = Activation::where([
            'user_id' => $this->userId,
            'is_active' => static::ACTIVE_ACTIVATIONS,
            'is_paginated' => static::NOT_PAGINATED
        ]);
    }

    public function getSubscribedTabIds()
    {
        $subscriptions = $this->getActiveSubscriptions();

        if (!$subscriptions) {
            return [];
        }

        $tabIds = [];
        foreach ($subscriptions as $subscription) {
            if (!is_null($subscription->DateDeactivated)) {
                continue;
            }

            $tabIds = array_merge(
                $tabIds,
                array_column(
                    $subscription->permission->entitlement->product->Tabs,
                    'id'
                )
            );
        }

        return $tabIds;
    }
}
