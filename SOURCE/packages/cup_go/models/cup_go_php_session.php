<?php

/**
 * Model for external User 
 * @author Ariel Tabag <atabag@cambridge.org>
 * ANZGO-3754 tagged by jbernardez 20181008
 * this is tagged for review and for deletion
 */
class CupGoPhpSession extends Object
{
    public function __construct() 
    { 
        session_set_save_handler(
            array('CupGoPhpSession', '_open'),
            array('CupGoPhpSession', '_close'),
            array('CupGoPhpSession', '_read'),
            array('CupGoPhpSession', '_write'),
            array('CupGoPhpSession', '_destroy'),
            array('CupGoPhpSession', '_clean')
        );
    }
    
    public function _conn() 
    {
        if ($_sess_db = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD)) {
            mysql_select_db(DB_DATABASE, $_sess_db);
        }
    }

    public function _open()
    {
        return FALSE;
    } 

    public function _close()
    {

    }

    public function _write($sesskey, $data)
    {
        $conn = CupGoPhpSession::_conn();
        $sesskey = mysql_real_escape_string($sesskey);
        $data = mysql_real_escape_string($data);

        // ANZGO-3764 modified by jbernardez 20181008
        $sql = "REPLACE INTO ? VALUES (?,DATE_ADD(NOW(), INTERVAL 1 HOUR),'',now(),'',?)";
        return mysql_query($sql, array(SESSION_DB_TABLE, $sesskey, $data));
    }

    public function _read($sesskey)
    {
        $conn = CupGoPhpSession::_conn();
        $sesskey = mysql_real_escape_string($sesskey);

        // ANZGO-3764 modified by jbernardez 20181008
        $sql = "SELECT sessdata FROM ? WHERE sesskey = ?";
        if ($result = mysql_query($sql, array(SESSION_DB_TABLE, $sesskey))) {
            if (mysql_num_rows($result)) {
                $record = mysql_fetch_assoc($result);
                return $record['sessdata'];
            }
        }

        return '';
    }

    public function _destroy($sesskey)
    {
        $conn = CupGoPhpSession::_conn();
        $sesskey = mysql_real_escape_string($sesskey);

        // ANZGO-3764 modified by jbernardez 20181008
        $sql = "DELETE FROM ? WHERE sesskey = ?";

        return mysql_query($sql, array(SESSION_DB_TABLE, $sesskey));
    }

    public function _clean($max)
    {  
        $conn = CupGoPhpSession::_conn();
        $old = time() - $max;
        $old = mysql_real_escape_string($old);

        // ANZGO-3764 modified by jbernardez 20181008
        $sql = "DELETE FROM ? WHERE expiry < ?";

        return mysql_query($sql, array(SESSION_DB_TABLE, $old));
    }
}