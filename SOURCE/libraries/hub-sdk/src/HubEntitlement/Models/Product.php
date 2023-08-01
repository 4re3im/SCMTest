<?php

/**
 * Product Model
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Models;

/**
 * Class Product
 * @property int $id
 * @property int $platform_id
 * @property array $metadata
 * @property \DateTime $archived_at
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property int $activations_count
 * @property array $entitlements
 */
class Product extends Model
{
    const REQUEST_PATH = '/v2/products/';

    public $fields = [
        'id',
        'platform_id',
        'metadata',
        'archived_at',
        'created_at',
        'updated_at',
        'activations_count'
    ];

    public $foreignFields = [
        'entitlements'
    ];

    public function entitlements()
    {
        return $this->hasMany('HubEntitlement\Models\Entitlement');
    }
}
