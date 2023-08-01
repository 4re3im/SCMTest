<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class GoDpsPackage extends Package {

    protected $pkgHandle = 'go_dps';
    protected $appVersionRequired = '5.6.0';
    protected $pkgVersion = '1.1.0';
    
    protected $attributes = array(
        array(
            'type' => 'text',
            'attribute' => array(
                'akHandle' => 'gdAccess',
                'akName' => 'Access',
                'displayOrder' => 1
            )
        ),
        array(
            'type' => 'date_time',
            'attribute' => array(
                'akHandle' => 'gdActivatedDate',
                'akName' => 'Activated Date',
                'displayOrder' => 2
            )
        ),
        array(
            'type' => 'text',
            'attribute' => array(
                'akHandle' => 'gdActivationCode',
                'akName' => 'Activation Code',
                'displayOrder' => 3
            )
        ),
        array(
            'type' => 'boolean',
            'attribute' => array(
                'akHandle' => 'gdAllowMarketingContact',
                'akName' => 'Allow Marketing Contact',
                'displayOrder' => 4
            )
        ),
        array(
            'type' => 'text',
            'attribute' => array(
                'akHandle' => 'gdAuthToken',
                'akName' => 'Authorization Token',
                'displayOrder' => 5
            )
        ),
        array(
            'type' => 'date_time',
            'attribute' => array(
                'akHandle' => 'gdDeactivatedDate',
                'akName' => 'Deactivated Date',
                'displayOrder' => 6
            )
        ),
        array(
            'type' => 'text',
            'attribute' => array(
                'akHandle' => 'gdEntAppID',
                'akName' => 'Enterprise App ID',
                'displayOrder' => 7
            )
        ),
        array(
            'type' => 'text',
            'attribute' => array(
                'akHandle' => 'gdEntAuthToken',
                'akName' => 'Enterprise Authorization Token',
                'displayOrder' => 8
            )
        ),
        array(
            'type' => 'boolean',
            'attribute' => array(
                'akHandle' => 'gdEntIntegrator',
                'akName' => 'Enterprise Integrator',
                'displayOrder' => 9
            )
        ),
        array(
            'type' => 'text',
            'attribute' => array(
                'akHandle' => 'gdEntOffer',
                'akName' => 'Enterprise Offer',
                'displayOrder' => 10
            )
        ),
        array(
            'type' => 'text',
            'attribute' => array(
                'akHandle' => 'gdEntProductID',
                'akName' => 'Enterprise Product ID',
                'displayOrder' => 11
            )
        ),
        array(
            'type' => 'date_time',
            'attribute' => array(
                'akHandle' => 'gdEntSubscriptionRenew',
                'akName' => 'Enterprise Subscription Renew',
                'displayOrder' => 12
            )
        ),
        array(
            'type' => 'text',
            'attribute' => array(
                'akHandle' => 'gdEntSubscriptionStart',
                'akName' => 'Enterprise Subscription Start',
                'displayOrder' => 13
            )
        ),
        array(
            'type' => 'text',
            'attribute' => array(
                'akHandle' => 'gdEntUuID',
                'akName' => 'Enterprise UU ID',
                'displayOrder' => 14
            )
        ),
        array(
            'type' => 'number',
            'attribute' => array(
                'akHandle' => 'gdLink',
                'akName' => 'Link',
                'displayOrder' => 15
            )
        ),
        array(
            'type' => 'boolean',
            'attribute' => array(
                'akHandle' => 'gdManuallyActivated',
                'akName' => 'Manually Activated',
                'displayOrder' => 16
            )
        ),
        array(
            'type' => 'number',
            'attribute' => array(
                'akHandle' => 'gdMAStaffID',
                'akName' => 'Manual Activation Staff ID',
                'displayOrder' => 17
            )
        ),
        array(
            'type' => 'text',
            'attribute' => array(
                'akHandle' => 'gdNotes',
                'akName' => 'Notes',
                'displayOrder' => 18
            )
        ),
        array(
            'type' => 'number',
            'attribute' => array(
                'akHandle' => 'gdPublisherID',
                'akName' => 'Publisher ID',
                'displayOrder' => 19
            )
        ),
        array(
            'type' => 'text',
            'attribute' => array(
                'akHandle' => 'gdSessionID',
                'akName' => 'Session ID',
                'displayOrder' => 20
            )
        ),
        array(
            'type' => 'date_time',
            'attribute' => array(
                'akHandle' => 'gdSubscriptionRenew',
                'akName' => 'Subscription Renew',
                'displayOrder' => 21
            )
        ),
        array(
            'type' => 'date_time',
            'attribute' => array(
                'akHandle' => 'gdSubscriptionStart',
                'akName' => 'Subscription Start',
                'displayOrder' => 22
            )
        ),
        array(
            'type' => 'text',
            'attribute' => array(
                'akHandle' => 'gdUuID',
                'akName' => 'UU ID',
                'displayOrder' => 23
            )
        )
    );


    public function getPackageDescription() {
        return t("Install GO Users' DPS attribute.");
    }

    public function getPackageName() {
        return t("Go User DPS Attributes");
    }

    public function install() {
        $pkg = parent::install();
        $this->installGoDpsAttributes($pkg);
    }

    public function upgrade() {
        $pkg = Package::getByHandle('go_dps');
        parent::upgrade($pkg);
    }
        
    protected function installGoDpsAttributes($pkg) {
        $go_dps = AttributeKeyCategory::getByHandle('go_dps');
        
        if (!$go_dps) {
            // Create the 'go_dps' key category object.
            $allowSets = false;
            AttributeKeyCategory::add('go_dps', $allowSets, $pkg);

            // Create the table for the 'go_dps' object.
            $db = Loader::db();
            $godpsAttrKeyQuery = "create table if not exists GoDpsAttributeKeys (
                    akID int unsigned not null default 0,
                    displayOrder int unsigned not null default 0,
                    primary key (akID));";
            $db->Execute($godpsAttrKeyQuery);
            
            $godpsAttrValueQuery = "create table if not exists GoDpsAttributeValues (
                    gdID int unsigned not null default 0,
                    akID int unsigned not null default 0,
                    avID int unsigned not null default 0,
                    primary key (gdID, avID, akID));";
            $db->Execute($godpsAttrValueQuery);
            
            

            // Add attributes to the newly created object
            $go_dps = AttributeKeyCategory::getByHandle('go_dps');
            foreach ($this->attributes as $attribute) {
                $attributeType = AttributeType::getByHandle($attribute['type']);
                $attributeKey = GoDpsAttributeKey::add($attributeType,$attribute['attribute'],$pkg);
            }
        }
    }
  
}
