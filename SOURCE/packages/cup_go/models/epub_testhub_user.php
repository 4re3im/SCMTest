<?php

/**
 * Model for external User 
 * @author Ariel Tabag <atabag@cambridge.org>
 */
class EpubTesthubUser extends Model{
    
    public function __construct() { $this->db = Loader::db(); }
    
    /**
     * Check current User and access to testhub or epub
     * Return user data 
     * @param type $params
    */
    public function checkUser($sesskey){
        
        $result = CupGoPhpSession::fetchByKey($sesskey);

        if($result) return $this->unserialize_php($result->sessdataTNG);

    }
    
    private static function unserialize_php($session_data) {
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            if (!strstr(substr($session_data, $offset), "|")) {
                throw new Exception("invalid data, remaining: " . substr($session_data, $offset));
            }
            $pos = strpos($session_data, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($session_data, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }
}