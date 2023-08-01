<?php

/**
 * Entitlement Model
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Models;

use HubEntitlement\Helpers\Utils;

/**
 * Class Entitlement
 * @property int $id
 * @property int $product_id
 * @property int $entitlement_type_id
 * @property array $metadata
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \HubEntitlement\Models\Product $product
 * @property \HubEntitlement\Models\EntitlementType $entitlementType
 * @property array $permissions
 */
class Entitlement extends Model
{
    const REQUEST_PATH = '/v2/entitlements/';

    public $fields = [
        'id',
        'product_id',
        'entitlement_type_id',
        'metadata',
        'created_at',
        'updated_at'
    ];

    public $foreignFields = [
        'product',
        'entitlementType',
        'permissions'
    ];

    public function product()
    {
        return $this->hasOne('HubEntitlement\Models\Product');
    }

    public function entitlementType()
    {
        return $this->hasOne('HubEntitlement\Models\EntitlementType');
    }

    public function permissions()
    {
        return $this->hasMany('HubEntitlement\Models\Permission');
    }

    /**
     * Getter for startDate property.
     *
     * @param $startDate
     * @return \DateTime
     */
    public function getStartDate($startDate)
    {
        if (is_string($startDate)) {
            return Utils::toDateTime($startDate);
        }

        return $startDate;
    }

    /**
     * Getter for endDate property.
     *
     * @param $endDate
     * @return \DateTime
     */
    public function getEndDate($endDate)
    {
        if (is_string($endDate)) {
            return Utils::toDateTime($endDate);
        }

        return $endDate;
    }

    /**
     * Getter for createdAt property.
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
}
