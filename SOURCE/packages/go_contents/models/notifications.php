<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of notifications
 *
 * @author paulbalila
 */
class NotificationsModel
{
    private $db;
    
    public function __construct()
    {
        $this->db = Loader::db();
    }
    
    public function getGeneralNotifications()
    {
        $sql = 'SELECT * FROM CupGoNotifications WHERE linkedTitles = "0"';
        $result = $this->db->Execute($sql);
        return ($result->NumRows() > 0) ? $result->GetAll() : FALSE;
    }
    
    public function getResourceNotification($id)
    {
        // ANZGO-3764 modified by jbernardez 20181011
        $sql = 'SELECT * FROM CupGoNotifications WHERE linkedTitles LIKE ?';
        $result = $this->db->Execute($sql, array("%$id%"));
        return ($result->NumRows() > 0) ? $result->GetAll() : FALSE;
    }
}
