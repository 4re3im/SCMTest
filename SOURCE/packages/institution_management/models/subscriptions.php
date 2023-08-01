<?php
// GCAP-1372 added by mabrigos
Loader::library('hub-sdk/autoload');

use HubEntitlement\Models\Product;
use HubEntitlement\Models\Activation;

class Subscriptions
{
    private $db;

    public function __construct()
    {
        $this->db = Loader::db();
    }

    public function getSubscriptionsByTerm($term)
    {
        $products = Product::where([
            'is_paginated' => 0,
            'keyword' => $term,
            'metaFields' => 'ID,ISBN_13,CMS_Name',
            'isArchived' => false
        ]);

        $productNames = [];
        foreach ($products as $product) {
            $productId = $product->id;
            $cmsName = $product->CMS_Name;
            $name = $product->Name;
            $isbn13 = $product->ISBN_13;

            $query = 'SELECT id FROM CupContentTitle WHERE isbn13 = ?';
            $titleId = (int)$this->db->GetOne($query, array($isbn13));

            $productEntitlements = $product->entitlements()->fetch();

            foreach ($productEntitlements as $productEntitlement) {
                if ($productEntitlement->Active !== 'Y') {
                    continue;
                }

                if ($productEntitlement->entitlement_type_id !== 2) {
                  continue;
                }

                $description = $productEntitlement->Description;
                $entitlementId = $productEntitlement->id;

                if ($productEntitlement->Type === 'duration') {
                    if ($productEntitlement->Duration > 0) {
                        $due = $productEntitlement->Duration . ' days';
                    } else {
                        $due = 'perpetual';
                    }
                } elseif ($productEntitlement->Type === 'start-end') {
                    $due = $productEntitlement->StartDate->format('Y/m/d H:i:s') .
                        ' to ' . $productEntitlement->EndDate->format('Y/m/d H:i:s');
                } elseif ($productEntitlement->Type === 'end-of-year') {
                    $due = 'school year';
                } else {
                    $due = '';
                }

                $obj = new stdclass;
                $obj->label = "$cmsName : $name : $description / $isbn13 ($due)";
                $obj->value = "$cmsName : $name : $description / $isbn13 ($due)";
                $obj->id = $entitlementId;
                $obj->s_id = $productId;
                $obj->p_id = $titleId;
                $obj->entitlement_id = $productEntitlement->entitlement_type_id;
                $productNames[] = $obj;
            }
        }
        return $productNames;
    }

    public function getInstitutionSubscriptions($oid)
    {
        return Activation::where([
            'institution_id' => [$oid],
            'orderField' => 'created_at',
            'orderDirection' => 'DESC',
            'is_paginated' => 0
        ]);
    }
}