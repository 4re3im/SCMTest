<?php

/**
 * EntitlementType Model
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Models;

/**
 * Class EntitlementType
 * @property int $id
 * @property string $name
 * @property string $description
 */
class EntitlementType extends Model
{
    const REQUEST_PATH = '/v2/entitlement-types/';

    public $fields = [
        'id',
        'name',
        'description'
    ];

    public $foreignFields = [];
}
