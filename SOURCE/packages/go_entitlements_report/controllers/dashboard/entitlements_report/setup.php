<?php

defined('C5_EXECUTE') || die(_('Access Denied.'));

/**
 * Entitlement Reports Block Controller
 * SB-611 Added by mtanada 20200715
 */
Loader::library('hub-sdk/autoload');

class DashboardEntitlementsReportSetupController extends Controller
{
    const COLUMN_EMAIL = 0;
    const PROVISION_LIMIT = 1;
    const MAX_RECORDS_TO_COUNT = 20;

    private $eModel;
    private $fileId;
    private $filePath;
    private $fileRecordId;
    private $pkgHandle = 'go_entitlements_report';

    public function __construct()
    {
        parent::__construct();
        Loader::library('FileLog/FileLogger');
        Loader::model('entitlements_report', $this->pkgHandle);
        $this->eModel = new EntitlementsReportModel();
    }

    public function on_start()
    {
        $htmlHelper = Loader::helper('html');
        $cssPath = (string)$htmlHelper->css('styles.css', $this->pkgHandle)->file . "?v=1.1";
        $jsPath = (string)$htmlHelper->javascript('scripts.js', $this->pkgHandle)->file . "?v=1.41";

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

        return $this->eModel->insertFileRecord($newFile->getFileID(), $fileName);
    }

    public function startUpload()
    {
        $fileRecordID = $this->uploadFile();
        if ($fileRecordID) {
            $this->fileRecordId = $fileRecordID;

            Loader::helper('spreadsheet', $this->pkgHandle);
            $emailsRecord = $this->eModel->getFileRecord($fileRecordID);
            $spreadsheetHelper = new SpreadsheetHelper($emailsRecord['FileID']);

            $total = $spreadsheetHelper->getRowsCount();
            $total = $total > 0 ? $total : static::PROVISION_LIMIT;

            echo json_encode([
                'total' => $total,
                'pages' => $total / static::PROVISION_LIMIT,
                'fileRecordId' => $this->fileRecordId,
                'headerExists' => false,
                'csvData' => ''
            ]);
        }
        exit;
    }

    /**
     * SB-611 Added by mtanada 2020-07-17
     * Fetch user data using email/s
     * @param array
     */
    public function generateUsersDetailsAndEntitlements($emails) {
        Loader::library('gigya/GigyaAccount');
        $gAccount = new GigyaAccount();
        $userDetails = $gAccount->searchUsersByEmails($emails);
        $usersInfoEntitlementList = $this->getUserEntitlements($userDetails);

        return $usersInfoEntitlementList;
    }

    /**
     * SB-611 Added by mtanada 2020-07-20
     * Returns all user entitlements data from PEAS Database
     *
     * @param array $users
     * @return array
     */
    public function getUserEntitlements($users)
    {
        $subs = Loader::helper('subscription', $this->pkgHandle);
        $userInfoAndEntitlements = [];
        $userEntitlements = [];

        foreach ($users as $user) {
            $userActivations = $this->eModel->getUserActivations($user['UID']);
            if (empty($userActivations) ) {
                // Check for user activations without 'go' prefix
                $userIdWithoutGo = $subs->removeGoPrefix($user['UID']);
                $user['UID'] = $userIdWithoutGo ? $userIdWithoutGo : $user['UID'];
                $userActivations = $this->eModel->getUserActivations($user['UID']);
            }
            $userEntitlements['entitlements'] = $this->eModel->getUserEntitlementList($userActivations, $user['UID']);
            if (!is_null($userEntitlements['entitlements'])) {
                foreach ($userEntitlements['entitlements'] as $entitlements => $val) {
                    $userInfoAndEntitlements[] = array_merge($user, $val);
                }
            } else {
                $userInfoAndEntitlements[] = array_merge($user, array('entitlements' => 'No entitlements.'));
            }
        }

        return $userInfoAndEntitlements;
    }

    /**
     * SB-611 Added by mtanada 2020-07-20
     * Exports User Info and Entitlements
     *
     * @return string CSV comma separated data
     */
    public function exportToSheet($fileRecordId, $index)
    {
        Loader::helper('spreadsheet', $this->pkgHandle);
        $emailsRecord = $this->eModel->getFileRecord($fileRecordId);
        if (!$emailsRecord) {
            http_response_code(404);
            exit;
        }
        $spreadsheetHelper = new SpreadsheetHelper($emailsRecord['FileID']);
        $fileName = 'EntitlementsReport_' . date('Y-m-d-h:i:s') . '.csv';

        $records = $spreadsheetHelper->getRows(0, false);
        if (empty($records)) {
            echo json_encode([
                'filename'     => $fileName,
                'fileRecordId' => $fileRecordId,
                'csvData'      => 'No email or emails uploaded.',
                'headerExists' => false,
                'IsFinished'   => 1,
                'total'        => 0,
                'index'        => $index
            ]);
            exit;
        }

        $emails = array();
        foreach ($records as $row) {
            $emails[] = $row[EntitlementsReportModel::COLUMN_EMAIL];
        }

        $emailsCount = count($emails);
        if (($emailsCount > static::MAX_RECORDS_TO_COUNT) && ($index < $emailsCount)) {
            $batchEmails = array_slice($emails, $index, static::MAX_RECORDS_TO_COUNT);
        } else {
            $batchEmails = $emails;
        }

        try {
            $usersData = $this->generateUsersDetailsAndEntitlements($batchEmails);
        } catch (UnexpectedValueException $e) {
            return false;
        }

        $index = $index + static::MAX_RECORDS_TO_COUNT;
        if ($index >= $emailsCount) {
            $isFinished = 1;
        } else {
            $isFinished = 0;
        }

        // QAs prefer to have static headers for uniformity
        $csvHeader = array ('UID','Email','FirstName','LastName','Role','ProductName','Isbn13','Type','DateActivated',
            'EndDate','DaysRemaining','DateDeactivated','AccessCode','PurchaseType'
        );
        $headerExists = $this->post('headerExists');
        $csvData = $this->post('csvData');

        if ($headerExists === 'false') {
            $csvData .= implode(',', $csvHeader);
            $headerExists = true;
        }

        foreach ($usersData as $data => $value) {
            $csvData .= "\n" . implode(',', array_values($value));
        }

        echo json_encode([
            'filename'     => $fileName,
            'fileRecordId' => $fileRecordId,
            'csvData'      => $csvData,
            'headerExists' => $headerExists,
            'IsFinished'   => $isFinished,
            'total'        => $emailsCount,
            'index'        => $index
        ]);
        exit;
    }
}

