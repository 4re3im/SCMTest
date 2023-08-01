<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 09/11/2020
 * Time: 6:55 PM
 */

class SimpleModel
{
    private $db;

    public function __construct()
    {
        Loader::db();
        $this->db = Loader::db();
    }

    public function getAllTitleIDs()
    {
        $sql = "SELECT id FROM CupContentTitle";
        return $this->db->GetAll($sql);
    }

}