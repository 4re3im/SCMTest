<?php

defined('C5_EXECUTE') || die(_("Access Denied."));

Loader::library('hub-sdk/autoload');
use HubEntitlement\Models\Product;

/**
 * ANZGO-3365 Modified by Maryjes Tanada, 01/30/2018
 * Search using ISBN13 reference from dashboard autocomplete
 */

class AutoCompleteTool
{
    private $term;

    public function __construct()
    {
        $this->term = filter_input(INPUT_GET, 'term', FILTER_SANITIZE_STRING);
    }

    public function createAutoCompleteList()
    {
        $db = Loader::db();
        $term = &$this->term;

        if (!$term) {
            return;
        }

        // HUB-153 modified by Carl Lewi R. Godoy 08/28/2018
        $products = Product::where([
            'is_paginated' => 0,
            'keyword' => $term,
            'metaFields' => 'ID, ISBN_13, CMS_Name',
            'isArchived' => false
        ]);

        $productNames = [];
        foreach ($products as $product) {
            $productId = $product->id;
            $cmsName = $product->CMS_Name;
            $name = $product->Name;
            $isbn13 = $product->ISBN_13;

            $query = 'SELECT id FROM CupContentTitle WHERE isbn13 = ?'; 
            $titleId = (int)$db->GetOne($query, array($isbn13));

            $productEntitlements = $product->entitlements()->fetch();

            foreach ($productEntitlements as $productEntitlement) {
                if ($productEntitlement->Active !== 'Y') {
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
                $productNames[] = $obj;
            }
        }

        echo json_encode($productNames);
    }
}

$autoComplete = new AutoCompleteTool();
$autoComplete->createAutoCompleteList();
