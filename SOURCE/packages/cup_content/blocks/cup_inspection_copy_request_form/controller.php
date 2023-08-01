<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::block('library_file');

class CupInspectionCopyRequestFormBlockController extends BlockController {
	public function getBlockTypeDescription() {
		return t("Cup Inspection Copy Request Form");
	}

	public function getBlockTypeName() {
		return t("Cup Inspection Copy Request Form");
	}

	protected $btTable = 'btCupInspectionCopyRequestForm';
	protected $btInterfaceWidth = "500";
	protected $btInterfaceHeight = "400";
	
	protected $title = false;
	
	public function view(){
		$this->set('title', $this->getTitle());
	}
	
	public function submit(){
		echo "hello world";
		exit();
	}
	
	public function add(){
	
	}
	
	public function edit(){
		$this->set('title', $this->getTitle());
	}
	
	public function getTitle(){
		if($this->title === false){
			$this->retrieveConfig();
		}
		
		return $this->title;
	}
	
	protected function retrieveConfig(){
		$db = Loader::db();
		$q = "select * from btCupInspectionCopyRequestForm where bID = ?";
		$result = $db->getRow($q, array($this->bID));
		if($result){
			$this->title = $result['title'];
		}
	}
        
        
//        public function inspection_copy_request(){
//            
//            define("USERNAME", "itservices@cambridge.edu.au");
//            define("PASSWORD", "ZxAsQw21");
//            define("SECURITY_TOKEN", "XxiwyWCQw4KVniR6IdiW0cz7i");
//
//            Loader::library('3rdparty/soapclient/SforcePartnerClient');
//
//            //include dirname(__FILE__).'/soapclient/SforcePartnerClient.php';
//
//            $mySforceConnection = new SforcePartnerClient();                
//            $mySforceConnection->createConnection(dirname(__FILE__)."/PartnerWSDL.xml");                
//            $mySforceConnection->login(USERNAME, PASSWORD.SECURITY_TOKEN);
//
//            echo dirname(__FILE__); 
//            
//            $post = $_POST;
//            
//            echo '<pre>';
//            print _r($post);
//            echo '</pre>';
//
//            die();
//            
//            $records[0] = new SObject();
//
//            $records[0]->fields = array(
//                'Salutation'            =>  $post['title'],
//                'FirstName'             =>  $post['first_name'],
//                'LastName'              =>  $post['last_name'],
//                'Email'                 =>  $post['email'],    
//                'Phone'                 =>  $post['phone'],
//                'Description'           =>  $post['query'],
//                'HasOptedOutOfEmail'    =>  $post['mailing_list'],
//                'MailingStreet'         =>  $post['address_line_1'],
//                'MailingCity'           =>  $post['city'],
//                'MailingState'          =>  $post['state'],    
//                'MailingPostalCode'     =>  $post['postcode'],
//                'MailingCountry'        =>  $post['country']
//            );
//
//            $records[0]->type = 'Contact';
//
//            $response = $mySforceConnection->create($records);
//	
//	}
        
}