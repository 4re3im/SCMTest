<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 24/05/2019
 * Time: 3:18 PM
 */

Loader::library('hub-sdk/autoload');
use HubEntitlement\Models\Product;

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

        return $productNames;
    }

    /*
     * GCAP-848 modified by mtanada 20200504
     * Get title IDs and tab IDs
     * @param array tabs
     */
    public function getTitleIds($tabs)
    {
        if (!empty($tabs)) {
            $query = 'SELECT TitleID, id AS tabId FROM CupGoTabs
                      WHERE ID IN ('. implode(",", $tabs) .')';
            return $this->db->GetAll($query);
        }
        return false;
    }

    /*
     * GCAP-848 modified by mtanada 20200504
     * Get series ID for every unique title IDs
     * @param array titles with tabs
     */
    public function getSeriesIds($titleIds)
    {
        $tmpTitles = array();
        foreach ($titleIds as $titleId) {
            if (isset($titleId['TitleID'])) {
                array_push($tmpTitles, $titleId['TitleID']);
            }
        }
        $query = 'SELECT cct.id AS titleId, ccs.ID AS seriesId FROM CupContentTitle cct
              INNER JOIN CupContentSeries ccs ON cct.series = ccs.name
              WHERE cct.id IN ('. implode(',', $tmpTitles) .') GROUP BY titleId';

        return $this->db->GetAll($query);
    }
}