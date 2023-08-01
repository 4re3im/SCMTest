<?php

$result = array('result' => 'error', 'error' => 'unknown');

$errors = array();
$post = $_POST;

if (!isset($post['first_name']) || strlen(trim($post['first_name'])) < 1) {
    $errors[] = '"FIRST NAME" is required';
}

if (!isset($post['last_name']) || strlen(trim($post['last_name'])) < 1) {
    $errors[] = '"LAST NAME" is required';
}

if (!isset($post['position']) || strlen(trim($post['position'])) < 1) {
    $errors[] = '"POSITION" is required';
}

if (!isset($post['school_campus']) || strlen(trim($post['school_campus'])) < 1) {
    $errors[] = '"SCHOOL/CAMPUS" is required';
}

if (!isset($post['postcode']) || strlen(trim($post['postcode'])) < 1) {
    $errors[] = '"POSTCODE" is required';
}

if (!isset($post['phone']) || strlen(trim($post['phone'])) < 1) {
    $errors[] = '"PHONE" is required';
}

if (!isset($post['email']) || strlen(trim($post['email'])) < 1) {
    $errors[] = '"EMAIL" is required';
} elseif (!filter_var(trim($post['email']), FILTER_VALIDATE_EMAIL)) {
    $errors[] = '"EMAIL" is invalid';
}

if (!isset($post['query']) || strlen(trim($post['query'])) < 1) {
    $errors[] = '"QUERY" is required';
}

if (!isset($post['mailing_list']) || strlen(trim($post['mailing_list'])) < 1) {
    $errors[] = '"Add to mailing_list" is required';
}



if (count($errors) > 0) {
    $result = array('result' => 'error', 'error' => implode("<br/>", $errors));
    echo json_encode($result);
} else {

    define("USERNAME", "itservices@cambridge.edu.au");
    define("PASSWORD", "ZxAsQw21Cv43Df");
    define("SECURITY_TOKEN", "IQ8Fcf3wiANKAj3DvX2oLK36");

    Loader::library('3rdparty/SFDC/soapclient/SforcePartnerClient');

    $mySforceConnection = new SforcePartnerClient();
    $mySforceConnection->createConnection(DIR_LIBRARIES_3RDPARTY . "/SFDC/PartnerWSDL.xml");
    $mySforceConnection->login(USERNAME, PASSWORD . SECURITY_TOKEN);

    $post = $_POST;

    $contact = new sObject();
    $contact->type = 'Lead';

    $contact->fields = array(
        'Title' => $post['title'],
        'FirstName' => $post['first_name'],
        'LastName' => $post['last_name'],
        'Email' => $post['email'],
        'Phone' => $post['phone'],
        'HasOptedOutOfEmail' => $post['mailing_list'],
        'PostalCode' => $post['postcode'],
        'Company' => $post['school_campus'],
        'Description' => $post['query']
    );

    /* Submitting the Lead to Salesforce */
    $result = $mySforceConnection->create(array($contact), 'Contact');

    /* Debug */
    print_r($result);
    die();

    $result = array('result' => 'success', 'error' => false);
    //echo json_encode($result);
    echo 'OK';
}