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

/*
if(!isset($post['mailing_list']) || strlen(trim($post['mailing_list'])) < 1){
        $errors[] = '"Add to mailing_list" is required';
}
*/


if(!isset($post['address_line_1']) || strlen(trim($post['address_line_1'])) < 1){
        $errors[] = '"Address Line 1" is required';
}

if(!isset($post['city']) || strlen(trim($post['city'])) < 1){
        $errors[] = '"City" is required';
}


if(!isset($post['postcode']) || strlen(trim($post['postcode'])) < 1){
        $errors[] = '"Postcode" is required';
}



if(count($errors) > 0){
    
        $result = array('result'=>'error', 'error'=>implode("<br/>", $errors));
        echo json_encode($result);
        
} else {

	//Save to SalesForce Contact    
        define("USERNAME", "itservices@cambridge.edu.au");
        define("PASSWORD", "ZxAsQw21Cv43Df"); 
        define("SECURITY_TOKEN", "IQ8Fcf3wiANKAj3DvX2oLK36");

        //require_once ('soapclient/SforcePartnerClient.php');
	loader::library('soapclient/SforcePartnerClient', 'cup_content');        
	$xml = (dirname(__FILE__)."/../libraries/soapclient/partner.wsdl.xml");

        $mySforceConnection = new SforcePartnerClient();
        $mySforceConnection->createConnection($xml);
        $mySforceConnection->login(USERNAME, PASSWORD.SECURITY_TOKEN);
		
		$hasOptedOutOfEmail = 1;
		$hardCopyOptOut = 1;
		
		if(isset($post['no_email']) && $post['no_email']){
			$hasOptedOutOfEmail = 0;
		}
		
		if(isset($post['no_post']) && $post['no_post']){
			$hardCopyOptOut = 0;
		}
		

            $records[0] = new SObject();

            $records[0]->fields = array(
                'Salutation'            =>  $post['title'],
                'FirstName'             =>  ucfirst($post['first_name']),
                'LastName'              =>  ucfirst($post['last_name']),
                'Email'                 =>  $post['email'],    
                'Phone'                 =>  $post['phone'],
                'Description'           =>  $post['query'],
                'HasOptedOutOfEmail'    =>  $hasOptedOutOfEmail,
				'Mailings__c'			=>	$hardCopyOptOut,
                'MailingStreet'         =>  $post['address_line_1'],
                'MailingCity'           =>  $post['city'],
                'MailingState'          =>  $post['state'],    
                'MailingPostalCode'     =>  $post['postcode'],
                'MailingCountry'        =>  $post['country']
            );

            $records[0]->type = 'Contact';

            $response = $mySforceConnection->create($records);
        

        if($response){

            $result = array('result'=>'success', 'error'=>false);
            echo json_encode($result);

        } else {

            $result = array('result'=>'error', 'error'=>implode("<br/>", $errors));
            echo json_encode($result);

        }
            

}


