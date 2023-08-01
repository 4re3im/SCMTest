<?php 

$result = array('result'=>'error', 'error'=>'unknown');

$errors = array();
$post = $_POST;

if(!isset($post['first_name']) || strlen(trim($post['first_name'])) < 1){
        $errors[] = '"FIRST NAME" is required';
}

if(!isset($post['last_name']) || strlen(trim($post['last_name'])) < 1){
        $errors[] = '"LAST NAME" is required';
}

if(!isset($post['position']) || strlen(trim($post['position'])) < 1){
        $errors[] = '"POSITION" is required';
}

if(!isset($post['school_campus']) || strlen(trim($post['school_campus'])) < 1){
        $errors[] = '"SCHOOL/CAMPUS" is required';
}

if(!isset($post['campus_postcode']) || strlen(trim($post['campus_postcode'])) < 1){
        $errors[] = '"CAMPUS POSTCODE" is required';
}

if(!isset($post['phone']) || strlen(trim($post['phone'])) < 1){
        $errors[] = '"PHONE" is required';
}

if(!isset($post['email']) || strlen(trim($post['email'])) < 1){
        $errors[] = '"EMAIL" is required';
}elseif(!filter_var(trim($post['email']), FILTER_VALIDATE_EMAIL)){
        $errors[] = '"EMAIL" is invalid';
}

if(!isset($post['query']) || strlen(trim($post['query'])) < 1){
        $errors[] = '"QUERY" is required';
}

if(!isset($post['mailing_list']) || strlen(trim($post['mailing_list'])) < 1){
        $errors[] = '"Add to mailing_list" is required';
}



if(count($errors) > 0){
    
        $result = array('result'=>'error', 'error'=>implode("<br/>", $errors));
        echo json_encode($result);
        
} else {
   
        $result = array('result'=>'success', 'error'=>false);
        echo json_encode($result);

}



       







        
        
        

//class InspectionCopyRequestFormExternalFormBlockController extends BlockController {
//        
//        public function action_inspection_copy_request() {
//		
//                $fields = $_REQUEST;
//                
//                
//                echo '<pre>';
//                print_r($fields);
//                echo '</pre>';
//                
//                die();
//                
//                define("USERNAME", "itservices@cambridge.edu.au");
//                define("PASSWORD", "ZxAsQw21");
//                define("SECURITY_TOKEN", "XxiwyWCQw4KVniR6IdiW0cz7i");
//               
//
//                Loader::library('3rdparty/soapclient/SforcePartnerClient');
//
//                $mySforceConnection = new SforcePartnerClient();                
//                $mySforceConnection->createConnection(dirname(__FILE__)."/PartnerWSDL.xml");                
//                $mySforceConnection->login(USERNAME, PASSWORD.SECURITY_TOKEN);
//                               
//                //create
//              
//
//                //    'Phone' =>  $fields['position'],
//                //    'Email' => $fields['school_campus'],     
//                //    'Email' => $fields['campus_postcode'], 
//
//
//                $records[0] = new SObject();
//
//                $records[0]->fields = array(
//                    'Salutation'            =>  $fields['title'],
//                    'FirstName'             =>  $fields['first_name'],
//                    'LastName'              =>  $fields['last_name'],
//                    'Email'                 =>  $fields['email'],    
//                    'Phone'                 =>  $fields['phone'],
//                    'Description'           =>  $fields['query'],
//                    'HasOptedOutOfEmail'    =>  $fields['mailing_list'],
//                    'MailingStreet'         =>  $fields['address_line_1'],
//                    'MailingCity'           =>  $fields['city'],
//                    'MailingState'          =>  $fields['state'],    
//                    'MailingPostalCode'     =>  $fields['postcode'],
//                    'MailingCountry'        =>  $fields['country']
//                );
//
//                $records[0]->type = 'Contact';
//
//                $response = $mySforceConnection->create($records);
//                
//                //$this->set('response', t('Thanks!'));
//		return true;
//		
//	}
//        
//}