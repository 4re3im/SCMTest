<?php

/**
 * HasMany Relationship
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Models;

use HubEntitlement\Helpers\Utils;

class HasManyRelation extends BaseRelationship implements Relationship
{
    public function generateRelatedModels($field, $attributes)
    {
        $model = $this->relatedModel;
        $relatedModels = [];
        foreach ($attributes[$field] as $attribute) {
            $relatedModels[] = new $model($attribute);
        }

        return $relatedModels;
    }

    public function fetch()
    {
        $relatedModelClass = $this->relatedModel;

        $modelReflection = new \ReflectionClass($this->model);
        $modelShortName = $modelReflection->getShortName();

        $searchField = Utils::makeForeignFieldName($modelShortName);
        $primaryKey = call_user_func([
            get_class($this->model),
            'getPrimaryKey'
        ]);

        return $relatedModelClass::where([
            $searchField => $this->model->$primaryKey
        ]);
    }
}
