<?php 
/**
 * Model for SeriesAnswers 
 * @author Ariel Tabag <atabag@cambridge.org>
 */
class Oauth extends Model{

    var $staff_id; var $db; var $ip;
    
    var $file_location = "";
    
    var $oAuthLogs = "OauthLogs";
    
    public function __construct() {
        
        $this->db = Loader::db();
        
        $this->ip = $_SERVER['HTTP_CLIENT_IP'];
        
    }

    /**
     * Store provision data to table from excel
     */
    //public function insertAuthLogs(array $values, $status=''){
    public function insertAuthLogs($key, $type, $message, $isbn, $email){
        
        $values = implode(',', $values);
        
        $values = "('', $key, '$type', '$message', '$isbn','$email', 'now()', '$this->ip')";
        
        //insert data to Go.Provision_Codes table
        $sql = "INSERT INTO $this->oAuthLogs VALUES $values";

        if($this->db->Execute($sql)) return true;
        
        return false;
        
    }

}