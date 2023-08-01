<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class Redirector extends Object
{
    function __construct($id = false)
    {
        // nothing here
    }

    // ANZGO-3045
    public function redirectUrlByID($id)
    {
        $db = Loader::db();
        $sql = "SELECT url FROM redirect WHERE id = ? ";
        $result = $db->GetRow($sql, array($id));

        return $result;
    }
}
