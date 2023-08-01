<?php

/**
 * SalesForce Library which will use SalesForce API
 * ANZGO-3678 added by jbernardez 20180406
 */

class SalesForceLibrary
{
    protected $mySforceConnection;
    protected $contact;
    protected $data;

    public function __construct($data = array())
    {
        Loader::library('3rdparty/SFDC/soapclient/SforcePartnerClient');

        $this->mySforceConnection = new SforcePartnerClient();
        $this->mySforceConnection->createConnection(DIR_LIBRARIES_3RDPARTY . "/SFDC/PartnerWSDL.xml");
        $this->mySforceConnection->login(SF_USERNAME, SF_PASSWORD . SF_SECURITY_TOKEN);
        $this->contact = new sObject();
        $this->data = $data;
    }

    /**
     * Sends Lead information to Salesforce API
     * @return array
     */
    public function sendLead()
    {
        // Submitting the Lead to Salesforce
        $this->contact->type = 'Lead';

        $this->contact->fields = array(
            'Title'                 =>  $this->data['title'],
            'FirstName'             =>  $this->data['first_name'],
            'LastName'              =>  $this->data['last_name'],
            'Email'                 =>  $this->data['email'],
            'Phone'                 =>  $this->data['phone'],
            'HasOptedOutOfEmail'    =>  0,
            'HardCopyOptOut__c'     =>  0, // ANZGO-3827 modified by jbernardez 20180813
            'PostalCode'            =>  $this->data['postcode'],
            'Company'               =>  $this->data['school_campus'],
            'Description'           =>  $this->data['query']
        );

        $result = $this->mySforceConnection->create(array($this->contact), 'Contact');

        if ($result[0]->success) {
            return array('result' => $result[0], 'error' => false);
        } else {
            return array('result' => $result[0], 'error' => true);
        }
    }

    // ANZGO-3718 modified by jbernardez 20180516

    /**
     * Sends Contact information to Salesforce API
     * @return array
     */
    public function sendContact()
    {
        // Submitting the Contact to Salesforce
        $this->contact->type = 'Contact';

        // SB-26 modified by jbernardez 20190129
        $this->contact->fields = array(
            'Title'                     => $this->data['title'],
            'FirstName'                 => $this->data['firstName'],
            'LastName'                  => $this->data['lastName'],
            'Email'                     => $this->data['email'],
            // ANZGO-3818 Modified by Shane Camus 09/11/2018
            'HasOptedOutOfEmail'        => 1,
            'Description'               => $this->data['schoolCampus'] . ' ' . $this->data['phone'],
            'LeadSource'                => 'Cambridge GO Website',
            // ANZGO-3818 Modified by Shane Camus 09/11/2018
            'Email_consent_source__c'   => 'CambridgeGO HOTmaths',
            // SB-26 modified by jbernardez 20190129
            'Products_Using__c'         => $this->data['productsUsing'],
            'Cambridge_GO_User__c'      => 1,
            'Customer_Care_Opt_Out__c'  => $this->data['customerCare'] == 1 ? 0 : 1,
            // SB-26 modified by jbernardez 20190129
            'MailingPostalCode'         => $this->data['schoolPostCode'],
        );
        
        $result = $this->mySforceConnection->create(array($this->contact), 'Contact');

        if ($result[0]->success) {
            return array('result' => $result[0], 'error' => false);
        } else {
            return array('result' => $result[0], 'error' => true);
        }
    }
}
