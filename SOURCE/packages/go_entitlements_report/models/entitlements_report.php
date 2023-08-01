<?php
/**
 * Entitlement Reports Block Controller
 * SB-611 Added by mtanada 20200716
 */
Loader::library('hub-sdk/autoload');
Loader::library('Activation/hub_activation');

use HubEntitlement\Models\Activation;

class EntitlementsReportModel
{
    const COLUMN_EMAIL = 0;
    public $fileRecordID;

    private $db;
    const DATE_FORMAT = 'Y-m-d H:i:s';

    public function __construct()
    {
        Loader::library('Activation/library');
        $this->db = Loader::db();
    }

    public function insertFileRecord($fileId, $fileName)
    {
        $u = new User();
        $sql = 'INSERT INTO EntitlementsReportFiles (FileID,FileName,DateUploaded,StaffID) VALUES(?,?,NOW(),?)';
        $this->db->Execute($sql, array($fileId, $fileName, $u->uID));

        return $this->db->Insert_ID('ProvisioningFiles');
    }

    /**
     * Returns user entitlements report file uploaded
     *
     * @param int $fileId
     * @return bool|array
     */
    public function getFileRecord($fileId)
    {
        return $this->db->GetRow('SELECT * FROM EntitlementsReportFiles WHERE ID = ?', array($fileId));
    }

    /**
     * SB-611 Added by mtanada 2020-07-20
     * Returns user activations data from PEAS Database
     *
     * @param string $userId
     * @return array
     */
    public function getUserActivations($userId)
    {
        $userActivations = Activation::where([
            'user_id' => $userId,
            'is_paginated' => 0,
        ]);
        return $userActivations;
    }

    /**
     * SB-611 Added by mtanada 2020-07-20
     * List of entitlements and their associated data (susbcription, enddate, cms/code, specific code etc.)
     *
     * @param $userActivations
     * @param $userId
     * @return array $userEntitlements
     */
    public function getUserEntitlementList($userActivations, $userId) {
        foreach ($userActivations as $userActivation) {
            $permission  = $userActivation->permission()->fetch();
            $entitlement = $permission->entitlement()->fetch();
            $product     = $entitlement->product()->fetch();

            $isbn13 = !is_null($product->ISBN_13) && $product->ISBN_13 !== ''
                ? "'" . $product->ISBN_13
                : 'No ISBN 13.';
            $endDate = !is_null($userActivation->ended_at)
                ? $userActivation->ended_at->format(static::DATE_FORMAT)
                : 'No date.';
            $dateActivated = !is_null($userActivation->activated_at)
                ? $userActivation->activated_at->format(static::DATE_FORMAT)
                : 'No date.';
            $dateDeactivated = !is_null($userActivation->DateDeactivated) || !empty($userActivation->DateDeactivated)
                ? date("Y-m-d H:i:s", strtotime($userActivation->DateDeactivated))
                : 'No date.';
            $purchaseType = !is_null($userActivation->PurchaseType)
                ? $userActivation->PurchaseType
                : 'No type.';

            $userEntitlements[] = [
                'ProductName'     => str_replace(',', ' ', $product->CMS_Name),
                'Isbn13'          => $isbn13,
                'Type'            => $entitlement->Type,
                'DateActivated'   => $dateActivated,
                'EndDate'         => $endDate,
                'DaysRemaining'   => $userActivation->daysRemaining,
                'DateDeactivated' => $dateDeactivated,
                'AccessCode'      => $permission->proof,
                'PurchaseType'    => $purchaseType
            ];
        }
        return $userEntitlements;
    }

}
