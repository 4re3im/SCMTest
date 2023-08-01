<?php

/**
 * Model for external User 
 * @author Ariel Tabag <atabag@cambridge.org>
 * ANZGO-3764 tagged by jbernardez 20181011
 */
class PhpSession extends Object
{
    public function __construct()
    {
        session_set_save_handler(
            array('PhpSession', '_open'),
            array('PhpSession', '_close'),
            array('PhpSession', '_read'),
            array('PhpSession', '_write'),
            array('PhpSession', '_destroy'),
            array('PhpSession', '_clean')
        );
    }
    
    public function _conn($conn=null)
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

    /**
    Whenever the Session is updated, it will require the Write method. 
    The Write method takes the Session Id and the Session data from the Global Session array. 
    The access token is the current time stamp.

    Again, in order to prevent SQL injection, we bind the data to the query before it is executed.
    If the query is executed correctly, we return true, otherwise we return false.
    **/
    
    public function _write($sesskey, $data)
    {
        $conn = PhpSession::_conn();
        $sesskey = mysql_real_escape_string($sesskey);
        $data = mysql_real_escape_string($data);

        // ANZGO-3764 modified by jbernardez 20181008
        $sql = "REPLACE INTO ? VALUES (?,DATE_ADD(NOW(), INTERVAL 1 HOUR),'',now(),'',?,?)";

        return mysql_query($sql, array(SESSION_DB_TABLE, $sesskey, $data, $data));
    }

    /**
    The Read method takes the Session Id and queries the database. 
    This method is the first example of where we bind data to the query. 
    By binding the id to the :id placeholder, and not using the variable directly, we use the PDO method for preventing SQL injection.
    **/
    public function _read($sesskey)
    {
        $conn = PhpSession::_conn();
        $sesskey = mysql_real_escape_string($sesskey);

        // ANZGO-3764 modified by jbernardez 20181008
        $sql = "SELECT sessdata FROM ? WHERE  sesskey = ?";

        if ($result = mysql_query($sql, array(SESSION_DB_TABLE, $sesskey))) {
            if (mysql_num_rows($result)) {
                $record = mysql_fetch_assoc($result);

                return $record['sessdata'];
            }
        }

        return '';
    }
    /**
    The Destroy method simply deletes a Session based on it’s Id. 
    **/
    public function _destroy($sesskey)
    {
        $conn = PhpSession::_conn();
        $sesskey = mysql_real_escape_string($sesskey);

        // ANZGO-3764 modified by jbernardez 20181008
        $sql = "DELETE FROM ? WHERE  sesskey = ?";

        return mysql_query($sql, array(SESSION_DB_TABLE, $sesskey));
    }

    public function _clean($max)
    {
        $conn = PhpSession::_conn();
        $old = time() - $max;
        $old = mysql_real_escape_string($old);

        // ANZGO-3764 modified by jbernardez 20181008
        $sql = "DELETE FROM ? WHERE  expiry < ?";

        return mysql_query($sql, array(SESSION_DB_TABLE, $old));
    }
}