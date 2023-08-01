<?php

/**
 * Base model
 * Handles underlying connection to Hub
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Models;

use HubEntitlement\Authentication\JwtClient;
use HubEntitlement\Exceptions\Model\ModelNotFoundException;
use HubEntitlement\Exceptions\Model\ValidationException;
use HubEntitlement\Helpers\Utils;

/**
 * Class Model
 * Base model of HubEntitlement
 * This will handle underlying REST requests to Hub
 * @package HubEntitlement\Models
 */
class Model
{
    /**
     * Handles all request to Global Hub
     * @var JwtClient
     */
    private $connection;

    /**
     * Flag determines when an Exception will be
     * thrown or not as a result of recent transaction
     * @var bool
     */
    private $failSilent = false;

    /**
     * List of model fields
     * @var array
     */
    public $attributes = [];

    /**
     * List of all model fields without modification
     * @var array
     */
    public $originalAttributes = [];

    /**
     * List of attribute keys
     * @var array
     */
    public $fields = [];

    /**
     * List of related model
     * @var array
     */
    public $foreignFields = [];

    /**
     * List of foreign fields with values
     * @var array
     */
    public $foreignAttributes = [];

    /**
     * Specific path of resource
     */
    const REQUEST_PATH = '';

    /**
     * Status codes used by Hub
     */
    const HTTP_STATUS_OK = 200;
    const HTTP_STATUS_CREATED = 201;
    const HTTP_STATUS_VALIDATION_ERR = 422;
    const HTTP_STATUS_NOT_FOUND = 404;

    const KEY_METADATA = 'metadata';

    /**
     * Sets connection or bridge that will
     * handle REST-ful requests
     *
     * @param $attributes
     */
    public function __construct($attributes = [])
    {
        $this->connection = static::getConnection();

        if ($attributes) {
            $this->loadAttributes($attributes);
            $this->loadForeignAttributes($attributes);
        }
    }

    /**
     * Get the attribute value from self
     * $attributes or $foreignAttributes
     *
     * @param $name
     * @return array|mixed
     */
    public function __get($name)
    {
        $getterMethodName = Utils::makeGetterName($name);
        $getterMethod = array($this, $getterMethodName);
        $computedMethodName = Utils::makeComputedName($name);

        if (property_exists($this, $name)) {
            $value = $this->$name;
        } elseif (in_array($name, $this->fields)) {
            $value = $this->attributes[$name];
        } elseif (in_array($name, $this->foreignFields)) {
            $value = $this->foreignAttributes[$name];
        } elseif (
            in_array(static::KEY_METADATA, $this->fields) &&
            in_array($name, array_keys($this->attributes[static::KEY_METADATA]))
        ) {
            $value = $this->attributes[static::KEY_METADATA][$name];
        } elseif (is_callable(array($this, $computedMethodName))) {
            $value = $this->$computedMethodName();
        } else {
            $value = null;
        }

        if (is_callable($getterMethod)) {
            return $this->$getterMethodName($value);
        }

        return $value;
    }

    /**
     * Sets the attribute value to self
     * $attributes or $foreignAttributes
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $setterMethodName = Utils::makeSetterName($name);
        $setterMethod = array($this, $setterMethodName);

        if (is_callable($setterMethod)) {
            $value = $this->$setterMethodName($value);
        }

        $value = Utils::convertCoreObjectsToString($value);

        if (property_exists($this, $name)) {
            $this->$name = $value;
        } elseif (in_array($name, $this->fields)) {
            $this->attributes[$name] = $value;
        } elseif (in_array($name, $this->foreignFields)) {
            $this->foreignAttributes[$name] = $value;
        } elseif (
            in_array(static::KEY_METADATA, $this->fields) &&
            in_array($name, array_keys($this->attributes[static::KEY_METADATA]))
        ) {
            $this->attributes[static::KEY_METADATA][$name] = $value;
        } else {
            $this->fields[] = $name;
            $this->attributes[$name] = $value;
        }
    }

    /** Sets connection */
    public static function getConnection()
    {
        static $connection;

        if (!$connection) {
            $connection = new JwtClient();
        }

        return $connection;
    }

    /**
     * Finds an instance of model by primary key
     *
     * @param $key
     * @return null|Model
     */
    public static function find($key)
    {
        $connection = static::getConnection();
        $requestPath = static::REQUEST_PATH . $key;

        $response = $connection->getService()->get($requestPath);

        if ($response->getStatusCode() === static::HTTP_STATUS_NOT_FOUND) {
            return null;
        }

        $attributes = $connection->parseResponse($response);
        $model = new static();
        $model->loadAttributes($attributes);
        $model->loadForeignAttributes($attributes);
        return $model;
    }

    /**
     * Finds an instance given a criteria
     *
     * @param $criteria
     * @return array
     */
    public static function where($criteria)
    {
        $connection = static::getConnection();
        $response = $connection->getService()->get(
            static::REQUEST_PATH,
            ['query' => $criteria]
        );

        $list = $connection->parseResponse($response);
        if (isset($list['data'])) {
            $list = $list['data'];
        }

        $list = $list ?: [];

        return array_map(function ($attributes) {
            $model = new static();
            $model->loadAttributes($attributes);
            $model->loadForeignAttributes($attributes);
            return $model;
        }, $list);
    }

    /**
     * Return table primary key
     */
    public static function getPrimaryKey()
    {
        return 'id';
    }

    /**
     * Serializes the object
     * @return array
     */
    public function toJSON()
    {
        $json = $this->attributes;
        $hasMetadata = in_array(static::KEY_METADATA, $this->fields);
        $isMetadataJsonEncoded = is_string($json[static::KEY_METADATA]);

        if ($hasMetadata && !$isMetadataJsonEncoded) {
            $json[static::KEY_METADATA] = json_encode($json[static::KEY_METADATA]);
        }

        return $json;
    }

    /**
     * Loads attribute fields to instance.
     * @param $attributes
     */
    protected function loadAttributes($attributes)
    {
        foreach ($this->fields as $field) {
            $isMetadata = $field === static::KEY_METADATA;
            if ($isMetadata) {
                $isNull = is_null($attributes[$field]);
                $value = $isNull ? '[]' : $attributes[$field];
                $attributes[$field] = is_string($value)
                    ? json_decode($value, true)
                    : $value;
            }

            if (isset($attributes[$field])) {
                $this->attributes[$field] = $attributes[$field];
                $this->originalAttributes[$field] = $attributes[$field];
            } else {
                $this->attributes[$field] = null;
                $this->originalAttributes[$field] = null;
            }
        }
    }

    /**
     * Load foreign attribute to instance.
     * @param $attributes
     */
    protected function loadForeignAttributes($attributes)
    {
        foreach ($this->foreignFields as $field) {

            if (!isset($attributes[$field])) {
                $this->foreignAttributes[$field] = null;
                continue;
            }

            if (!method_exists($this, $field)) {
                $this->foreignAttributes[$field] = $attributes[$field];
                continue;
            }

            $this->foreignAttributes[$field] = $this
                ->$field()
                ->generateRelatedModels($field, $attributes);
        }
    }

    /**
     * Return hasMany relation definition.
     *
     * @param $modelClass
     * @return HasManyRelation
     */
    public function hasMany($modelClass)
    {
        $relation = new HasManyRelation($modelClass);
        $relation->model =& $this;
        return $relation;
    }

    /**
     * Return hasOne relation definition.
     *
     * @param $modelClass
     * @return HasOneRelation
     */
    public function hasOne($modelClass)
    {
        $relation = new HasOneRelation($modelClass);
        $relation->model = &$this;
        return $relation;
    }

    /**
     * Checks if model has already been saved once
     */
    protected function isSaved()
    {
        $primaryKey = static::getPrimaryKey();
        return $this->$primaryKey;
    }

    /**
     * Checks if there are changes in fields
     * @return bool
     */
    protected function isModified()
    {
        foreach ($this->originalAttributes as $field => $value) {
            if ($this->attributes[$field] !== $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get key value pairs of modified attributes
     *
     * @return array
     */
    protected function getModifiedFields()
    {
        $modifiedFields = [];
        foreach ($this->attributes as $key => $value) {
            if ($key === static::KEY_METADATA && !is_string($value)) {
                $value = json_encode($value);
            }

            if ($this->originalAttributes[$key] !== $value) {
                $modifiedFields[$key] = $value;
            }
        }

        return $modifiedFields;
    }

    /**
     * Checks the response of transaction
     *
     * @param $response
     * @return bool
     * @throws ModelNotFoundException
     * @throws ValidationException
     */
    protected function validateResponse($response)
    {
        $responseCode = $response->getStatusCode();
        $data = $this->connection->parseResponse($response);

        if ($responseCode === static::HTTP_STATUS_OK) {
            return true;
        }

        if ($responseCode === static::HTTP_STATUS_CREATED) {
            return true;
        }

        if ($this->failSilent) {
            return false;
        }

        if ($responseCode === static::HTTP_STATUS_VALIDATION_ERR) {
            throw new ValidationException(
                json_encode($data),
                $data['data']['code']
            );
        }

        if ($responseCode === static::HTTP_STATUS_NOT_FOUND) {
            throw new ModelNotFoundException(
                json_encode($data),
                $data['data']['code']
            );
        }
    }

    /**
     * Create a new instance of model in Hub
     *
     * @return bool
     * @throws \HubEntitlement\Exceptions\Model\ModelNotFoundException
     * @throws \HubEntitlement\Exceptions\Model\ValidationException
     * @throws \ReflectionException
     */
    protected function create()
    {
        $response = $this->connection->getService()->post(
            static::REQUEST_PATH,
            ['json' => $this->toJSON()]
        );

        $isSuccess = $this->validateResponse($response);

        if ($isSuccess) {
            $data = $this->connection->parseResponse($response);
            $primaryKey = static::getPrimaryKey();

            if (isset($data['data'][$primaryKey])) {
                $this->$primaryKey = $data['data'][$primaryKey];
            } else {
                $className = new \ReflectionClass($this);
                $className = strtolower($className->getShortName());

                // GCAP-1372 added by mabrigos
                if ($className === 'siteactivation') {
                    $className = 'activation';
                }

                $this->$primaryKey = $data['data'][$className][$primaryKey];
            }

            $this->reload();
        }

        return $isSuccess;
    }

    /**
     * Reloads the current instance to repopulate
     * fields created by Hub after save.
     */
    protected function reload()
    {
        $primaryKey = static::getPrimaryKey();
        $id = $this->$primaryKey;

        $instance = static::find($id);
        $data = $instance->jsonSerializeAll();

        $this->loadAttributes($data);
        $this->loadForeignAttributes($data);
    }

    /**
     * Updates instance of model in Hub
     *
     * @return bool
     * @throws \HubEntitlement\Exceptions\Model\ModelNotFoundException
     * @throws \HubEntitlement\Exceptions\Model\ValidationException
     */
    protected function update()
    {
        $primaryKey = $this->attributes[self::getPrimaryKey()];
        $updateRequestPath = static::REQUEST_PATH . $primaryKey;

        $response = $this->connection->getService()->put(
            $updateRequestPath,
            ['json' => $this->getModifiedFields()]
        );

        return $this->validateResponse($response);
    }

    public function delete()
    {
        $primaryKey = $this->attributes[self::getPrimaryKey()];
        $updateRequestPath = static::REQUEST_PATH . $primaryKey;

        $response = $this->connection->getService()->delete(
            $updateRequestPath,
            []
        );

        return $this->validateResponse($response);
    }

    /**
     * Create or update the model.
     *
     * @param bool $failSilent
     * @return bool
     * @throws \HubEntitlement\Exceptions\Model\ModelNotFoundException
     * @throws \HubEntitlement\Exceptions\Model\ValidationException
     * @throws \ReflectionException
     */
    public function save($failSilent = false)
    {
        $this->failSilent = $failSilent;

        if (!$this->isSaved()) {
            return $this->create();
        }

        if ($this->isModified()) {
            return $this->update();
        }

        return true;
    }

    /**
     * Return JSON object of all attributes and
     * foreign attributes.
     *
     * @return string
     */
    public function jsonSerializeAll()
    {
        $json = $this->attributes;
        foreach ($this->foreignAttributes as $model => $content) {
            if ($content instanceof Model) {
                $json = array_merge(
                    $json,
                    [$model => $content->toJSON()]
                );
            } elseif (is_array($content)) {
                $relatedModels = [];

                foreach ($content as $foreignValue) {
                    $relatedModels[] = $foreignValue->toJSON();
                }

                $json = array_merge(
                    $json,
                    [$model => $relatedModels]
                );
            } else {
                $json[$model] = $content;
            }
        }

        return $json;
    }
}
