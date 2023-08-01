<?php

defined('C5_EXECUTE') || die(_('Access Denied.'));

Loader::library('hub-sdk/autoload');
use HubEntitlement\Models\Activation;
use HubEntitlement\Models\Permission;

class DashboardProvisioningArchiveController extends Controller
{
    const COLUMN_EMAIL = 0;
    const ARCHIVE_LIMIT = 1;
    const PURCHASE_TYPE_CODE = 'CODE';

    private $aModel;
    private $fileId;
    private $filePath;
    private $fileRecordId;
    private $pkgHandle = 'go_provisioning';

    public function on_start()
    {
        Loader::model('archive', $this->pkgHandle);
        $this->aModel = new ArchivingModel();

        Loader::helper('spreadsheet', $this->pkgHandle);

        $htmlHelper = Loader::helper('html');
        $cssPath = (string)$htmlHelper->css('styles.css', $this->pkgHandle)->file . "?v=1";
        $jsPath = (string)$htmlHelper->javascript('archive.js', $this->pkgHandle)->file . "?v=2";

        $this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . $cssPath . '">');
        $this->addFooterItem('<script type="text/javascript" src="' . $jsPath . '"></script>');
        $this->addFooterItem($htmlHelper->javascript('plugins/malsup/jquery.form.min.js', $this->pkgHandle));
    }

    private function uploadFile()
    {
        Loader::library('file/importer');
        $fi = new FileImporter();
        $tmpName = $_FILES['excel']['tmp_name'];
        $fileName = $_FILES['excel']['name'];

        $excelFile = $fi->import($tmpName, $fileName);
        $this->fileId = $excelFile->getFileID();
        $newFile = $excelFile->getFile();
        $this->filePath = $newFile->getPath();

        return $this->aModel->insertFileRecord($newFile->getFileID(), $fileName);
    }

    public function startArchiving()
    {
        $fileRecordID = $this->uploadFile();
        if ($fileRecordID) {
            $this->fileRecordId = $fileRecordID;

            Loader::helper('spreadsheet', $this->pkgHandle);
            $archivingRecord = $this->aModel->getFileRecord($fileRecordID);
            $spreadsheetHelper = new SpreadsheetHelper($archivingRecord['FileID']);
            $total = $spreadsheetHelper->getRowsCount();
            $total = $total > 0 ? $total : static::ARCHIVE_LIMIT;

            echo json_encode([
                'total' => $total,
                'pages' => $total / static::ARCHIVE_LIMIT,
                'pagination' => static::ARCHIVE_LIMIT,
                'fileRecordId' => $this->fileRecordId
            ]);
        }
        exit;
    }

    public function getUsersFromGigya($fileRecordId, $offset = 0)
    {
        session_write_close();
        $reg = Loader::helper('registration', $this->pkgHandle);
        Loader::library('gigya/GigyaAccount');

        $gigyaAccount = new GigyaAccount();
        $archivingRecord = $this->aModel->getFileRecord($fileRecordId);

        if (!$archivingRecord) {
            echo json_encode([
                'success' => false,
                'message' => 'File record not found.'
            ]);
            exit;
        }

        $this->fileId = $archivingRecord['FileID'];

        $spreadsheetHelper = new SpreadsheetHelper($this->fileId);
        $records = $spreadsheetHelper->getRows($offset, static::ARCHIVE_LIMIT);

        $emails = [];
        foreach ($records as $row) {
            $row = $reg->cleanData($row);
            $emails[] = $row[static::COLUMN_EMAIL];
        }

        $results = $gigyaAccount->searchUsersByEmails($emails);
        $compositeID = $results[0]['UID'];
        if ($results[0]['systemID']) {
            $compositeID .= ',' . $results[0]['systemID'];
        }

        $this->runArchive($compositeID, $records);
    }

    public function runArchive($compositeID, $records)
    {
        session_write_close();

        $entitlementIDs = $this->post('subsAvail');

        if (!$entitlementIDs) {
            echo json_encode([
                'success' => false,
                'message' => 'No entitlements submitted.'
            ]);
            exit;
        }

        $actID = array();

        $activations = Activation::where([
            'user_id' => $compositeID,
            'is_paginated' => 0
        ]);
        foreach ($activations as $activation) {
            if ($activation->metadata['PurchaseType'] !== static::PURCHASE_TYPE_CODE
                && $activation->metadata['Archive'] !== 'Y') {
                $entID = $activation->permission->entitlement_id;
                foreach ($entitlementIDs as $entitlementID) {
                    if ((int)$entID === (int)$entitlementID) {
                        $actID[] = $activation->id;
                    }
                }
            }
        }
        $activationIds = array_filter($actID);
        $this->aModel->updateActivation($activationIds);

        echo json_encode([
            'users' => count($records),
            'result' => count($activationIds)
        ]);
        exit;
    }
}
