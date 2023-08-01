<?php
/**
 * Provision Controller
 * @author Ariel Tabag <atabag@cambridge.org>
 * Last Edited : October 22, 2014
 */

defined('C5_EXECUTE') or die(_('Access Denied.'));

class EducationProvisionController extends Controller {
   
    //declare global variable
    protected $_params = array();
    protected $pkgHandle = 'cup_go';
    
    public function on_start() {
        
        parent::on_start();
        
        $this->html = Loader::helper('html2', $this->pkgHandle);
        
        $this->addHeaderItem($this->html->css('style.css', $this->pkgHandle));
        
        $this->addHeaderItem($this->html->css('dataTables.bootstrap.css', $this->pkgHandle));
        
        $v = View::getInstance();
        
        $v->setTheme(PageTheme::getByHandle("cup_go"));
        
    }
    
    public function view() {
        

        $u = new User();

        //chekc if user is not logged in
        
        if (!$u->isLoggedIn() || !$this->isValidUser($u)){
            
            $page = Page::getCurrentPage();
            
            $this->set('rcID', $page->cID);
            
            $this->render('/education/go_login');
        
        }
        
        $this->addHeaderItem($this->html->javascript('global.js', $this->pkgHandle, array(), true));
        
        $groups = $u->getUserGroups();
        
        $this->set('groups', $groups);
        
        $this->set('sample_excel', BASE_URL.DIR_REL."/packages/$this->pkgHandle/files/List to Add.xls");
        
        $this->addHeaderItem($this->html->javascript('jquery.dataTables.js', $this->pkgHandle));   
        
        $this->addHeaderItem($this->html->javascript('dataTables.bootstrap.js', $this->pkgHandle)); 
        
    }

    public function addUserSubscription(){
        
        try{
            
            $Provision = new Provision();
            
            $ProvisionHelper = Loader::helper('provision', $this->pkgHandle);

            $params = $this->request();

            $user_id = $params['user_id'];

            $search_id = explode('_', $params['search_id']);

            $s_id = $search_id[0];

            $sa_id = $search_id[1];
            
            $subscriptions = $Provision->addUserSubscription($user_id, $s_id, $sa_id);
            $html = $ProvisionHelper->formatProvision($subscriptions['provisions'], $subscriptions['user_ids']);
            /*
            if(count($subscriptions['dupes']) > 0) {
                $html = $ProvisionHelper->formatProvisionWithDupes($subscriptions);
            } else {
                
            }
             * 
             */
            
            echo json_encode($html);

        } catch (Exception $e) {
            
            echo 'Caught exception: ',  $e->getMessage();
            
        }
        
         exit;
        
    }
    
    /**
     * search for product in Go.CMS_Product
     */
    public function searchProduct(){
        
        try{
        
            $Provision = new Provision();

            $params = $this->request();

            $product_name = $params['product_name'];

            $products = $Provision->getAllProductByname($product_name);
            
            $ProvisionHelper = Loader::helper('provision', $this->pkgHandle);
            
            $html = $ProvisionHelper->formatSearchProduct($product_name, $products);

            echo json_encode($html);
            
        } catch (Exception $e) {
            
            echo json_encode("<ul><li>No results found for $product_name</li></ul>");
            
        }
        
        exit;
        
    }    
    
    public function logout() {
        
            $u = new User();
            
            $u->logout();
            
            $this->redirect('/education/provision');
            
    }
    
    public function sendEmail($file_id){
        
        $Provision = new Provision();
        
        if($Provision->sendEmail($file_id)) echo "sent";
        
        //SNS docs
        //http://stackoverflow.com/questions/22908693/can-aws-ses-send-bounce-complaint-objects-to-an-http-endpoint
        //https://github.com/aws/aws-sdk-php
        
        exit;
        
    }
    
    public function uploadFile(){
        
        try{
            
            $user_only = $this->request('user_only');
            
            $ProvisionHelper = Loader::helper('provision', $this->pkgHandle);

            $Provision = new Provision();

            $provisions = $Provision->ProcessExcel($user_only);
            
            // echo var_dump($provisions); exit;
                                
            $html = $ProvisionHelper->formatProvision($provisions);
            
            echo json_encode($html);
        
        } catch (Exception $e) {
            
            echo 'Caught exception: ',  $e->getMessage();
            
        }
        
        exit;
    
    }
    
    public function verification($id){
        
        $this->addHeaderItem($this->html->css('verification.css', $this->pkgHandle));
        
        $this->addHeaderItem($this->html->javascript('verification.js', $this->pkgHandle)); 
        
        $user_id = EncryptionHelper::decrypt(str_replace('-', '/', $id));
        
        $Provision = new Provision(); 
        
        $user = $Provision->getUser($user_id); 

        $password = $user['uPassword'];
        
        $activated_date = $user['uDateAdded'];

        $this->set('name',  $user['ak_uFirstname'] . " " . $user['ak_uLastname']);

        $this->set('user_id', $user_id);

        $this->set('password', $password);

        if($activated_date) $this->set('verification_error', 'Account already activated');
        
        $this->render('verification'); 
        
    }
    
    public function verifyAccount(){
        
        try{
        
            $params = $this->request(); 
            
            $Provision = new Provision(); 

            $user = $Provision->verifyAccount($params); 
            
            echo "true";
            
        } catch (Exception $e) {
            
            echo 'Caught exception: ',  $e->getMessage();
            
        }
        
        exit;
        
    }

        /**
     * Check for valid user, user groups
     * Super Administrators
     * Customer Service
     * School Admins
     * Booksellers
     */
    private function isValidUser($user){
        
        $groups = $user->getUserGroups();
        
        list($id1, $group, $id2) = array_values($groups);

        $valid_groups = array('Booksellers', 'Customer service', 'Administrators', 'School Admins');
        
        if(in_array($group, $valid_groups)) return true;
        
        return false;
        
    }
 
}
?>
