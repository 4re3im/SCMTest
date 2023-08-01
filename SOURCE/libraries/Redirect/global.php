<?php

// Database connection strings
include("connections.php");
require_once 'Zend/Db.php';
// require_once 'Zend/Date.php';

function dodbAca()
{

    global $serverName;
    // Set connection options
    $connectionOptions = getConnectionInfo("academic");

    try {

        $conn = Zend_Db::factory('pdo_mysql', $connectionOptions);

        if( $conn === false ){
           return false;
        }else{
           return $conn;
        }

    } catch (Exception $e) {

        return false;

    }
}


function dodbEDU()
{

    global $serverName;
    // Set connection options
    $connectionOptions = getConnectionInfo("education");

    try {

        $conn = Zend_Db::factory('pdo_mysql', $connectionOptions);

        if( $conn === false ){
           return false;
        }else{
           return $conn;
        }

    } catch (Exception $e) {
        print_r( $e->getMessage() );
        return false;

    }
}

function dodbEPUB()
{

    global $serverName;
    // Set connection options
    $connectionOptions = getConnectionInfo("epub");

    try {

        $conn = Zend_Db::factory('pdo_mysql', $connectionOptions);

        if( $conn === false ){
           return false;
        }else{
           return $conn;
        }

    } catch (Exception $e) {
        print_r( $e->getMessage() );
        return false;

    }
}

function dodbTNG()
{
    // Set connection options
    $connectionOptions = getConnectionInfo("tngdb");

    try {

        $conn = Zend_Db::factory('pdo_mysql', $connectionOptions);

        if( $conn === false ){
           return false;
        }else{
           return $conn;
        }

    } catch (Exception $e) {
        print_r( $e->getMessage() );
        return false;

    }
}

function sendQuery($conn,$query,$params = array())
{
    try {

        $data = $conn->query($query, $params);

        return $data;

    } catch (Exception $e) {

        die( print_r( $e->getMessage() ) );
        return false;

    }

}

function mssql_escape($data) {
		$data = htmlentities($data,ENT_QUOTES);
        if ( !isset($data) or empty($data) ) return '';
        if ( is_numeric($data) ) return $data;

        $non_displayables = array(
            '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/',             // url encoded 16-31
            '/[\x00-\x08]/',            // 00-08
            '/\x0b/',                   // 11
            '/\x0c/',                   // 12
            '/[\x0e-\x1f]/'             // 14-31
        );
        foreach ( $non_displayables as $regex )
            $data = preg_replace( $regex, '', $data );
        $data = str_replace("'", "''", $data );
        return $data;
    }

function mssql_escape_no_entities($data) {

        if ( !isset($data) or empty($data) ) return '';
        if ( is_numeric($data) ) return $data;

        $non_displayables = array(
            '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/',             // url encoded 16-31
            '/[\x00-\x08]/',            // 00-08
            '/\x0b/',                   // 11
            '/\x0c/',                   // 12
            '/[\x0e-\x1f]/'             // 14-31
        );
        foreach ( $non_displayables as $regex )
            $data = preg_replace( $regex, '', $data );
        $data = str_replace("'", "''", $data );
        return $data;
}

?>
