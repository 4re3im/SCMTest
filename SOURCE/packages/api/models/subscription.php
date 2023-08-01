<?php

/**
 * ANZGO-3951 , Added by John Renzo S. Sunico, 1/12/2018
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'base_model.php';

Loader::library('hub-sdk/autoload');

use HubEntitlement\Models\Product;

class Subscription extends BaseModel
{
    public $_table = 'CupGoSubscription';

    public function __construct()
    {
        Loader::model('subscription_tab', 'api');
        parent::__construct();
    }

    public function loadByID($id)
    {
        return $this->Load('ID = ?', [$id]);
    }

    public function getFirstTab()
    {
        $db = Loader::db();
        $sql = 'SELECT ID FROM CupGoSubscriptionTabs WHERE S_ID = ?';
        $tabID = $db->GetOne($sql, [$this->ID]);

        if ($tabID) {
            $subscriptionTab = new SubscriptionTab();
            $subscriptionTab->loadByID($tabID);

            return $subscriptionTab;
        }
    }

    public function getSubscriptions()
    {
        $products = Product::where([
            'is_paginated' => 0
        ]);

        if (!$products) {
            return [];
        }

        $results = array_map(function ($product) {
            return ['ID' => $product->id, 'CMS_Name' => $product->CMS_Name];
        }, $products);

        return $results;
    }

    /* ANZGO-3760 added by mtanada 20180719
     * PEAS saving of edumar_TitleID in Product
     */
    public function saveEdumarTitleIdToProduct($edumarTitleId, $pid)
    {
        $product = Product::find($pid);
        $product->metadata = array_merge($product->metadata, ['edumar_titleID' => (int)$edumarTitleId]);

        try {
            $product->save();
            return true;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        return false;
    }
}
