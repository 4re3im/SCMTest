<?php

/**
 * HasOne Relationship
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Models;

use HubEntitlement\Helpers\Utils;

class HasOneRelation extends BaseRelationship implements Relationship
{
    public function generateRelatedModels($field, $attributes)
    {
        $model = $this->relatedModel;
        return (new $model($attributes[$field]));
    }

    public function fetch()
    {
        $relatedModelClass = $this->relatedModel;
        $relatedModelInstance = new \ReflectionClass(new $relatedModelClass());
        $relatedModelName = $relatedModelInstance->getShortName();
        $foreignField = Utils::makeForeignFieldName($relatedModelName);

        return $relatedModelClass::find($this->model->$foreignField);
    }
}
