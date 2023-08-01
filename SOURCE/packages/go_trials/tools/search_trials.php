<?php
// GCAP-1272 Added by Shane Camus 04/13/21
defined('C5_EXECUTE') || die(_("Access Denied."));

Loader::library('hub-sdk/autoload');
use HubEntitlement\Models\Product;

class SearchTrialsTool
{
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

            $entitlements = $product->entitlements()->fetch();

            foreach ($entitlements as $entitlement) {
                if ($entitlement->Active !== 'Y') {
                    continue;
                }

                $isTrial = $entitlement->Type === 'trial';
                if (!$isTrial) {
                    continue;
                }

                if ($entitlement->Duration > 0) {
                    $due = $entitlement->Duration . ' days';
                } else {
                    $due = 'perpetual';
                }

                $description = $entitlement->Description;

                $obj = new stdclass;
                $obj->label = "$cmsName : $name : $description / $isbn13 ($due)";
                $obj->value = "$cmsName : $name : $description / $isbn13 ($due)";
                $obj->id = $entitlement->id;
                $obj->s_id = $product->id;
                $obj->p_id = $titleId;
                $obj->isTrial = $isTrial;
                $obj->name = $cmsName;
                $productNames[] = $obj;
            }

            if (!is_array($productNames)) {
              $productNames[] = "No Trial Entitlement for this keyword/isbn.";
            }
        }

        echo json_encode($productNames);
    }
}

$searchTrials = new SearchTrialsTool();
$searchTrials->createAutoCompleteList();
