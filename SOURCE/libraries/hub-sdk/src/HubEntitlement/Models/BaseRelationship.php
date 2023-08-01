<?php

/**
 * Base Relationship Handler
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Models;


abstract class BaseRelationship
{
    /**
     * Related model
     *
     * @var
     */
    public $relatedModel;

    /**
     * Instance of the model
     *
     * @var
     */
    public $model;

    /**
     * BaseRelationship constructor.
     *
     * @param $modelClass
     */
    public function __construct($modelClass)
    {
        $this->relatedModel = $modelClass;
    }
}
