<?php
/**
 * Provision Controller
 * @author Ariel Tabag <atabag@cambridge.org>
 * Last Edited : October 22, 2014
 */

defined('C5_EXECUTE') or die(_('Access Denied.'));

class ClientController extends Controller {
   
    //declare global variable
    protected $_params = array();
    
    protected $pkgHandle = 'cup_go';
    
    public function on_start() {
       
        parent::on_start();
        
        Loader::library('Zend/Rest/Server', $this->pkgHandle);
        
        Loader::library('Zend/Rest/Client', $this->pkgHandle);
        
        Loader::library('3rdparty/Zend/Loader/Autoloader');
        
        $autoloader = Zend_Loader_Autoloader::getInstance();
        
        $autoloader->setFallbackAutoloader(true);
        
    }
    
    public function view() {

        $client = new Zend_Rest_Client('http://localhost:8080/go_c5/index.php/api');
        
        $client->sayHello('Davey', 'Day'); // "Hello Davey, Good Day"
        print_r($client);
        //echo $client->get();
        
        
    }
    
}
?>