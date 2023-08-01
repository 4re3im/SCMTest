<?php

/**
 * Handles database functions.
 */
class RedirectDatabase
{
    private $mysql;

    function __construct($conn)
    {
        $this->mysql = $conn;
    }

    public function get_edu_urls($offset = 0)
    {
        $sql = "SELECT url ";
        $sql .= "FROM redirect_edu_mkt ORDER BY id DESC ";
        $sql .= "LIMIT 20 ";
        $sql .= "OFFSET " . $offset;
        $result = $this->mysql->query($sql);
        return $result->fetchAll();
    }

    public function get_edu_url($referrer)
    {
        $sql = "SELECT url ";
        $sql .= "FROM redirect_edu_mkt ";
        $sql .= "WHERE page = ? ";
        $result = $this->mysql->query($sql, array($referrer));
        return $result->fetch();
    }

    // ANZGO-3836 added by mtanada 20180828
    public function getAcaUrls($offset = 0)
    {
        $sql = 'SELECT url ';
        $sql .= 'FROM redirect_aca_mkt ORDER BY id DESC ';
        $sql .= 'LIMIT 20 ';
        $sql .= 'OFFSET ?';
        $result = $this->mysql->query($sql, array($offset));
        return $result->fetchAll();
    }

    // ANZGO-3836 added by mtanada 20180828
    public function getAcaUrl($referrer)
    {
        $sql = 'SELECT url ';
        $sql .= 'FROM redirect_aca_mkt ';
        $sql .= 'WHERE page = ? ';
        $result = $this->mysql->query($sql, array($referrer));
        return $result->fetch();
    }
}
