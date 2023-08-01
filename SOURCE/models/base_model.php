<?php

/**
 * BaseModel contains basic functions
 * that a model should have.
 *
 * Added by John Renzo S. Sunico, October 11, 2017
 */

abstract class BaseModel extends Model
{
    const GETTER = "get";
    const SETTER = "set";

    public $_table;
    protected $db;

    /**
     * Initializes table fields
     * using parent constructor.
     */
    public function __construct()
    {
        $this->db = Loader::db();
        parent::__construct($this->_table);
    }

    /**
     * Adds percent sign to parameter which
     * parameter can be matched anywhere from string.
     * @param $parameter
     * @return string
     */
    public function paddingLikeBoth($parameter)
    {
        return "%$parameter%";
    }

    /**
     * Handles dynamic getters and setters for table columns.
     * @param $name
     * @param $arguments
     * @return bool|string
     */
    public function __call($name, $arguments)
    {
        $prefix = substr($name, 0, 3);
        $property = substr($name, 3);
        $properties = get_object_vars($this);

        foreach ($properties as $key => $value) {
            if (strtolower($key) === strtolower($property)) {
                if ($prefix === static::GETTER) {
                    return $this->$key;
                } elseif ($prefix === static::SETTER) {
                    $this->$key = $arguments[0];
                    return true;
                } else {
                    break;
                }
            }
        }

        throw new BadMethodCallException(static::class . " has no method named $name");
    }
}
