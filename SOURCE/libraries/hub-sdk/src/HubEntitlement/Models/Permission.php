<?php

/**
 * Permission Model
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Models;

use HubEntitlement\Helpers\Utils;

/**
 * Class Permission
 * @property int $id
 * @property int $entitlement_id
 * @property string $proof
 * @property int $limit
 * @property bool $is_active
 * @property \DateTime $expired_at
 * @property \DateTime $released_at
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \HubEntitlement\Models\Entitlement $entitlement
 * @property array $activations
 */
class Permission extends Model
{
    const REQUEST_PATH = '/v2/permissions/';

    public $fields = [
        'id',
        'entitlement_id',
        'proof',
        'limit',
        'is_active',
        'expired_at',
        'released_at',
        'created_at',
        'updated_at'
    ];

    public $foreignFields = [
        'entitlement',
        'activations'
    ];

    /**
     * Return last activation of permission.
     * @return null|\HubEntitlement\Models\Activation
     */
    public function getLastActivation()
    {
        if (!$this->activations) {
            return null;
        }

        $this->activations = Utils::sortListOfObjects(
            $this->activations,
            'id'
        );

        $lastItem = end($this->activations);
        reset($this->activations);

        return $lastItem;

    }

    /**
     * Returns if permission can still be used.
     *
     * @return bool
     */
    public function computedIsUsable()
    {
        return ($this->HasActivationLeft && !is_null($this->released_at));
    }

    /**
     * Returns true if permission can still be released.
     *
     * @return bool
     */
    public function computedIsReleasable()
    {
        return is_null($this->released_at) && $this->HasActivationLeft;
    }

    /**
     * Returns true if the number of activations
     * has not exceeded the limit.
     *
     * @return bool
     */
    public function computedHasActivationLeft()
    {
        return count($this->activations) < $this->limit;
    }

    /**
     * Related Model Activations
     *
     * @return \HubEntitlement\Models\HasManyRelation
     */
    public function activations()
    {
        return $this->hasMany('HubEntitlement\Models\Activation');
    }

    /**
     * Related Model Entitlement
     *
     * @return \HubEntitlement\Models\HasOneRelation
     */
    public function entitlement()
    {
        return $this->hasOne('HubEntitlement\Models\Entitlement');
    }

    /**
     * Getter for created_at property.
     *
     * @param $createdAt
     * @return \DateTime
     */
    public function getCreatedAt($createdAt)
    {
        if (is_string($createdAt)) {
            return Utils::toDateTime($createdAt);
        }

        return $createdAt;
    }

    /**
     * Getter for updated_at property.
     *
     * @param $updatedAt
     * @return \DateTime
     */
    public function getUpdatedAt($updatedAt)
    {
        if (is_string($updatedAt)) {
            return Utils::toDateTime($updatedAt);
        }

        return $updatedAt;
    }

    /**
     * Getter for released_at property.
     *
     * @param $releasedAt
     * @return \DateTime
     */
    public function getReleasedAt($releasedAt)
    {
        if (is_string($releasedAt)) {
            return Utils::toDateTime($releasedAt);
        }

        return $releasedAt;
    }
}
