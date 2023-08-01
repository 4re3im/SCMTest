<?php

/**
 * ANZGO-3649 Modified by John Renzo S. Sunico, 03/06/2018
 */
abstract class BaseModel extends Model
{
    const INVALID_ID = 0;
    protected $db;
    public $pkgHandle = 'api';

    public function __construct($data = array())
    {
        parent::__construct();
        $this->db = Loader::db();
        $this->unserialize($data);
    }

    /**
     * Converts object to a json serializable array
     * @return array
     */
    public function serialize()
    {
        $serialize = array();
        $cols = $this->db->MetaColumns($this->_table);
        foreach ($cols as $col) {
            $colName = $col->name;
            $serialize[$colName] = $this->$colName;
        }

        return $serialize;
    }

    public function unserialize($data)
    {
        if (is_array($data)) {
            foreach ($data as $property => $value) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Essentially converts array to string wrapped in square brackets
     * e.g [1][2][3]
     * @param $array
     * @return string
     */
    public function braceArray($array)
    {
        if (!count($array)) {
            return '';
        }

        return '[' . implode('][', $array) . ']';
    }
}