<?php

/**
 * Relationship Interface
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Models;


interface Relationship
{
    public function generateRelatedModels($field, $attributes);

    public function fetch();
}
