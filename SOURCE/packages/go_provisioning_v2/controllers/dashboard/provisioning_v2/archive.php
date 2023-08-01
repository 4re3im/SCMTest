<?php

defined('C5_EXECUTE') || die(_('Access Denied.'));

Loader::library('hub-sdk/autoload');
use HubEntitlement\Models\Activation;
use HubEntitlement\Models\Permission;

class DashboardProvisioningV2ArchiveController extends Controller
{
    const COLUMN_EMAIL = 0;
    const ARCHIVE_LIMIT = 1;
    const PURCHASE_TYPE_CODE = 'CODE';

    private $aModel;
    private $fileId;
    private $filePath;
    private $fileRecordId;
    private $pkgHandle = 'go_provisioning_v2';

    public function on_start()
    {
        Loader::model('archive', $this->pkgHandle);
        $this->aModel = new ArchivingModel();

        $htmlHelper = Loader::helper('html');
        $cssPath = (string)$htmlHelper->css('styles.css', $this->pkgHandle)->file . "?v=1";
        $jsPath = (string)$htmlHelper->javascript('archive.js', $this->pkgHandle)->file . "?v=1";

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

    public function runArchive($fileRecordId, $offset = 0)
    {
        session_write_close();

        $archivingRecord = $this->aModel->getFileRecord($fileRecordId);
        if (!$archivingRecord) {
            echo json_encode([
                'success' => false,
                'message' => 'File record not found.'
            ]);
            exit;
        }

        Loader::helper('spreadsheet', $this->pkgHandle);
        $reg = Loader::helper('registration', $this->pkgHandle);

        $this->fileRecordId = $fileRecordId;
        $this->aModel->fileRecordID = $fileRecordId;
        $this->fileId = $archivingRecord['FileID'];

        $spreadsheetHelper = new SpreadsheetHelper($this->fileId);
        $this->filePath = $spreadsheetHelper->getSpreadsheetPath();
        $records = $spreadsheetHelper->getRows($offset, static::ARCHIVE_LIMIT);
        
        $entitlementIDs = $this->post('subsAvail');
        $actID = array();
        
        foreach ($records as $row) {
            $row = $reg->cleanData($row);
            $userId = $this->aModel->getUserId($row);
            // SB-285 added by mabrigos -- added is_paginated parameter when fetching activations
            $activations = Activation::where([
                'user_id' => $userId[uID], 
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
        }
        $this->aModel->updateActivation($activationIds);

        echo json_encode([
            'users' => count($records),
            'result' => count($activationIds)
        ]);
        exit;
    }
}
