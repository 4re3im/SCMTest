<?php

class DashboardProvisioningResetPasswordController extends Controller
{
    const PACKAGE_HANDLE = 'go_provisioning';

    const STATUS_FILE_UPLOADED = 0;
    const STATUS_FILE_CONTENTS_VALIDATED = 1;
    const STATUS_RETRIEVING_STATUS_POST_GIGYA = 2;
    const STATUS_PASSWORD_RESET_STARTED = 3;
    const STATUS_USER_RECORDS_CLEANED = 4;
    const STATUS_FILE_UPLOAD_ERRROR = 5;
    const STATUS_GIGYA_ERROR = 6;
    const STATUS_GIGYA_DATAFLOW_ERROR = 7;
    const STATUS_USERS_NOT_FOUND_IN_GIGYA = 8;
    const STATUS_HAS_DUPLICATE_PASSWORDS = 9;
    const STATUS_DIFFERENT_DOMAIN_IN_EMAILS = 10;
    const STATUS_NO_VALID_RECORDS_FOR_RESET = 11;
    const STATUS_USERS_UPDATED_POST_GIGYA = 12;
    const STATUS_UNABLE_TO_UPLOAD_RESULTS = 13;
    const STATUS_RESULTS_UPLOADED_TO_S3 = 14;
    const STATUS_UNABLE_TO_DOWNLOAD_FILE = 15;


    const STATUS_MESSAGES = [
        'File uploaded',
        'The contents of the file has been validated',
        'Retrieving user status from Gigya',
        'Bulk reset password started',
        'User records cleaned up',
        'There was an error in uploading the file',
        'There was an error in Gigya',
        'There was an error in running the dataflow',
        'All users uploaded are not yet registered in Gigya',
        'Records show that common passwords has been used.',
        'There are emails with different domains.',
        'There are no valid records for the password change.',
        'Users updated post Bulk Password Reset',
        'Unable to upload results to S3',
        'Result file uploaded to S3',
        'Unable to download result file from S3.'
    ];

    const DISPLAY_LIMIT = 10;
    const FILE_ROWS_LIMIT = -1;

    const COMMON_JS_VERSION = 3;
    const SCRIPTS_JS_VERSION = 6.3;

    private $gpModel;
    private $bulkActionsHelper;
    private $registrationHelper;
    private $spreadsheetHelper;

    // SB-613 added by jbernardez 20200625
    private $users;
    const MAX_RECORDS_TO_COUNT = 100;

    public function __construct()
    {
        // SB-613 added by jbernardez 20200623
        Loader::model('users', static::PACKAGE_HANDLE);
        Loader::model('gigya_password', static::PACKAGE_HANDLE);
        $this->gpModel = new GigyaPasswordModel();
        $this->users = new UsersModel();
    }

    public function on_start()
    {
        $this->bulkActionsHelper = Loader::helper('bulk_actions', static::PACKAGE_HANDLE);
        $this->bulkActionsHelper->setMessages(static::STATUS_MESSAGES);

        // Initialize spreadsheet helper and registration helper
        Loader::helper('spreadsheet', static::PACKAGE_HANDLE);
        Loader::helper('registration', static::PACKAGE_HANDLE);

        // Load other assets
        $html = Loader::helper('html');
        $this->addHeaderItem($html->css('bulk_actions/styles.css', static::PACKAGE_HANDLE));
        $this->addFooterItem($html->javascript(
            'plugins/malsup/jquery.form.min.js',
            static::PACKAGE_HANDLE
        ));

        $commonJsHref = (string)$html->javascript(
            'bulk_actions/common.js',
            static::PACKAGE_HANDLE)
            ->href;
        $commonJsHref .= '?v=' . static::COMMON_JS_VERSION;

        $scriptsJsHref = (string)$html->javascript(
            'reset_password/scripts.js',
            static::PACKAGE_HANDLE)
            ->href;
        $scriptsJsHref .= '?v=' . static::SCRIPTS_JS_VERSION;

        $this->addFooterItem(
            '<script type="text/javascript" src="' . $commonJsHref . '" ></script>'
        );
        $this->addFooterItem(
            '<script type="text/javascript" src="' . $scriptsJsHref . '" ></script>'
        );
    }

    public function view()
    {
        global $u;
        $authorizedEmails = json_decode(GIGYA_BULK_ACTION_ADMIN_EMAILS, true);
        $lowercaseEmail = strtolower($u->getEmail());
        $isAllowedAdmin = in_array($lowercaseEmail, $authorizedEmails);
        $this->set('isAllowedAdmin', $isAllowedAdmin);
    }

    public function uploadFile()
    {
        $uploadResults = $this->bulkActionsHelper->uploadFile();

        if (!$uploadResults) {
            echo $this->bulkActionsHelper->buildStatus(static::STATUS_FILE_UPLOAD_ERRROR);
            exit;
        }

        $fileRecordID = $this->gpModel->insertFileRecord(
            $uploadResults['fileID'],
            $uploadResults['filePath']
        );
        echo $this->bulkActionsHelper->buildStatus(static::STATUS_FILE_UPLOADED, ['FileRecordID' => $fileRecordID]);
        exit;
    }

    /**
     * Checks records per row and save each one in the database.
     *
     * @param $fileRecordID
     * @param bool $userHasBeenReminded
     */
    public function validateFileContents($fileRecordID, $userHasBeenReminded = false)
    {
        $fileRecord = $this->gpModel->getFileRecord($fileRecordID);
        $fileID = $fileRecord['FileID'];

        $this->registrationHelper = new RegistrationHelper();
        $this->spreadsheetHelper = new SpreadsheetHelper($fileID);

        
        $fileRecords = $this->spreadsheetHelper->getRows(0, static::FILE_ROWS_LIMIT);

        // Check for same domain
        $emailsHaveSameDomain = $this->bulkActionsHelper->checkEmailsIfSameDomain($fileRecords);
        if (!$emailsHaveSameDomain) {
            echo $this->bulkActionsHelper->buildStatus(
                static::STATUS_DIFFERENT_DOMAIN_IN_EMAILS,
                [
                    'FileRecordID' => $fileRecordID
                ]
            );
            exit;
        }

        // Check for duplicate passwords
        $hasDuplicatePasswords = $this->bulkActionsHelper->checkIfDuplicatePasswords($fileRecords);
        if ($hasDuplicatePasswords && !$userHasBeenReminded) {
            echo $this->bulkActionsHelper->buildStatus(
                static::STATUS_HAS_DUPLICATE_PASSWORDS,
                [
                    'FileRecordID' => $fileRecordID,
                ]
            );
            exit;
        }

        $this->bulkActionsHelper->setRowElementCount(4);
        foreach ($fileRecords as $row) {
            $row = $this->bulkActionsHelper->sanitizeEntry($row);
            $validationData = $this->bulkActionsHelper->validateEntry($row);
            $status = 'Details: User skipped';

            if (count($validationData) > 0) {
                $status .= ' - ' . implode(',', $validationData);
            } else {
                $status = 'Details: To be submitted to Gigya';
            }

            $saveData = [
                'Email' => $row[0],
                'FirstName' => $row[1],
                'LastName' => $row[2],
                'TempPassword' => count($validationData) === 0 ? $row[3] : null,
                'Status' => $status,
                'IsValidForChange' => count($validationData) === 0,
                'FileID' => $fileID
            ];

            $this->gpModel->insertUserRecord($saveData);
        }
        echo $this->bulkActionsHelper->buildStatus(
            static::STATUS_FILE_CONTENTS_VALIDATED,
            [
                'FileRecordID' => $fileRecordID,
            ]
        );
        exit;
    }

    /**
     * Triggers the dataflow run from upload to S3 bucket
     * up to the programmatic run of the dataflow
     *
     * @param $fileRecordID
     */
    public function runBulkPasswordReset($fileRecordID)
    {
        $fileRecord = $this->gpModel->getFileRecord($fileRecordID);
        $fileID = $fileRecord['FileID'];

        $userRecords = $this->gpModel->getAllValidUsersByFileID($fileID);

        if (count($userRecords) === 0) {
            echo  $this->bulkActionsHelper->buildStatus(
                static::STATUS_NO_VALID_RECORDS_FOR_RESET,
                [
                    'FileRecordID' => $fileRecordID
                ]
            );
            exit;
        }

        // Add headings for the CSV file
        $csvContents[] = [
            'UID',
            'email',
            'firstName',
            'lastName',
            'newPassword'
        ];
        foreach ($userRecords as $userRecord) {
            $csvContents[] = [
                $userRecord['GigyaUID'],
                $userRecord['Email'],
                $userRecord['FirstName'],
                $userRecord['LastName'],
                $userRecord['TempPassword']
            ];
        }

        Loader::library('gigya/GigyaExport');
        Loader::library('gigya/GigyaDataFlow');
        Loader::library('gigya/GigyaSchedule');

        $filename = 'PW_CHANGE-' . date('YmdHis');
        $filename .= '-' . $this->bulkActionsHelper->generateRandomString();
        $filename .= '.csv';
        $exporter = new GigyaExport();
        $exporter->setMode($exporter::RESET_PASSWORD);
        $exporter->addUsers($csvContents);
        $exporter->setCurrentFilename($filename);
        $isExportSuccessful = $exporter->exportToS3();

        $dataFlow = new GigyaDataFlow();
        if ($isExportSuccessful) {
            $schedule = new GigyaSchedule();
            $dataFlow->name = 'Cambridge Go Password Reset';
            $dataFlow->description = 'Bulk Password Reset for Cambridge Go.';
            $dataFlow->steps = $dataFlow->getSteps($dataFlow::$RESET_PASSWORD);
            $errorCode = $dataFlow->update(GIGYA_RESET_PASSWORD_DATAFLOW_ID);
            $schedule->dataFlowId = GIGYA_RESET_PASSWORD_DATAFLOW_ID;
        } else {
            echo $this->bulkActionsHelper->buildStatus(
                static::STATUS_GIGYA_DATAFLOW_ERROR,
                [
                    'FileRecordID' => $fileRecordID,
                ]
            );
            exit;
        }

        if ($errorCode === 0) {
            $adminEmails = json_decode(GIGYA_BULK_ACTION_ADMIN_EMAILS, true);
            $adminEmailsString = implode(',', $adminEmails);

            $schedule->name = $filename;
            $schedule->successEmailNotification = $adminEmailsString;
            $schedule->failureEmailNotification = $adminEmailsString;
            $schedule->nextJobStartTime = date(
                'Y-m-d\TH:i:s.000\Z',
                strtotime('+30 seconds', strtotime(gmdate("Y-m-d H:i:s")))
            );

            $scheduleId = $schedule->save();
            echo $this->bulkActionsHelper->buildStatus(
                static::STATUS_PASSWORD_RESET_STARTED,
                [
                    'FileRecordID' => $fileRecordID,
                    'Data' => ['scheduleID' => $scheduleId]
                ]
            );
            exit;
        } else {
            echo $this->bulkActionsHelper->buildStatus(
                static::STATUS_GIGYA_DATAFLOW_ERROR,
                [
                    'FileRecordID' => $fileRecordID,
                ]
            );
            exit;
        }
    }

    // SB-613 added by jbernardez 20200629
    // add an additional call so that it won't burden runBulkPasswordReset
    public function runBulkPasswordResetC5($fileRecordID, $index)
    {
        $status = 'starting';
        $fileRecord = $this->gpModel->getFileRecord($fileRecordID);
        $fileID = $fileRecord['FileID'];

        $userRecords = $this->gpModel->getAllValidUsersByFileID($fileID);

        if (count($userRecords) === 0) {
            echo  $this->resetPwHelper->buildStatus(
                static::STATUS_NO_VALID_RECORDS_FOR_RESET,
                [
                    'FileRecordID' => $fileRecordID
                ]
            );
            exit;
        }

        $recordsCount = count($userRecords);

        if (($recordsCount > static::MAX_RECORDS_TO_COUNT) && ($index < $recordsCount)) {
            $batchRecords = array_slice($userRecords, $index, static::MAX_RECORDS_TO_COUNT);
        } else {
            $batchRecords = $userRecords;
        }

        $this->users->changePasswordByBulk($batchRecords);

        $index = $index + static::MAX_RECORDS_TO_COUNT;
        if ($index >= $recordsCount) {
            $isFinished = 1;
        } else {
            $isFinished = 0;
        }

        echo $this->bulkActionsHelper->buildStatus(
            static::STATUS_PASSWORD_RESET_STARTED,
            [
                'FileRecordID' => $fileRecordID,
                'IsFinished' => $isFinished,
                'Data' => [
                    'index' => $index,
                    'batch' => $batchRecords
                ]
            ]
        );
        exit;
    }

    /**
     * Called when dataflow run has been done.
     * To be GDPR compliant, we remove all saved
     * temporary passwords.
     *
     * @param $fileRecordID
     */
    public function removeTempPasswords($fileRecordID)
    {
        $fileRecord = $this->gpModel->getFileRecord($fileRecordID);
        $fileID = $fileRecord['FileID'];

        $this->gpModel->updateUserAfterPwChange($fileID);

        echo $this->bulkActionsHelper->buildStatus(
            static::STATUS_USER_RECORDS_CLEANED,
            [
                'FileRecordID' => $fileRecordID
            ]
        );
        exit;
    }

    /**
     * Get the status of the running dataflow
     *
     * @param $scheduleId
     */
    public function getJobDetails($scheduleId)
    {
        Loader::library('gigya/GigyaJobStatus');
        $job = new GigyaJobStatus($scheduleId);
        $status = $job->getStatusByScheduleId();
        $jobID = $job->getJobIDByScheduleID();

        echo $this->bulkActionsHelper->buildStatus(
            static::STATUS_RETRIEVING_STATUS_POST_GIGYA,
            [
                'Data' => [
                    'status' => $status,
                    'scheduleID' => $scheduleId,
                    'jobID' => $jobID
                ]
            ]
        );
        exit;
    }

    /**
     * Get success and error results from S3
     *
     * @param $jobID
     * @param $fileRecordID
     */
    public function getUserStatusFromS3 ($jobID, $fileRecordID)
    {
        $fileRecord = $this->gpModel->getFileRecord($fileRecordID);
        $fileID = $fileRecord['FileID'];

        $users = $this->gpModel->getAllValidUsersByFileID($fileID);
        $userEmails = array_column($users, 'Email');
        $gigyaEmails = [];

        Loader::library('gigya/GigyaExport');

        $exporter = new GigyaExport();
        $successFilePath = 'reset_pw_success_' . $jobID . '.json';
        $errorFilePath = 'reset_pw_error_' . $jobID . '.json';

        $successResults = $exporter->exportFromS3(
            GIGYA_S3_RESULT_PATH_RESET_PASSWORD . $successFilePath
        );
        $errorResults = $exporter->exportFromS3(
            GIGYA_S3_RESULT_PATH_RESET_PASSWORD . $errorFilePath
        );

        if (!$successResults && !$errorResults) {
            echo $this->bulkActionsHelper->buildStatus(
                static::STATUS_GIGYA_ERROR,
                [
                    'FileRecordID' => $fileRecordID
                ]
            );
            exit;
        }

        $successJson = $errorJson = false;
        if ($successResults) {
            $successJson = json_decode($successResults);
        }

        if ($errorResults) {
            $errorJson = json_decode($errorResults);
        }

        if ($successJson) {
            $emails = [];
            foreach ($successJson as $sJson) {
                $emails[] = $sJson->email;
                $gigyaEmails[] = strtolower($sJson->email);
            }

            $data = ['Status' => 'Password changed'];

            $this->gpModel->updateSuccessfulPwChangedUsers($emails, $fileID, $data);
        }
        
        if ($errorJson) {
            foreach ($errorJson as $eJson) {
                $status = 'An error occurred for this user.';
                $errorDetails = $eJson->_errorDetails;

                if ($errorDetails->errorCode === 404000) {
                    $status = 'Details: User has a different first or last name';
                } else {
                    $errorMsg = $errorDetails->errorMessage;
                    $errorArr = explode('.', $errorMsg);
                    $status = $errorArr[1];
                }

                $data = ['Status' => $status];
                $gigyaEmails[] = strtolower($eJson->email);
                $this->gpModel->updateUserByEmail($eJson->email, $fileID, $data);
            }
        }

        if (!empty($gigyaEmails)) {
            $unfoundEmails = [];
            foreach ($userEmails as $userEmail) {
                if (!in_array($userEmail, $gigyaEmails)) {
                    $unfoundEmails[] = $userEmail;
                }
            }

            $this->gpModel->updateUnfoundEmails($unfoundEmails, $fileID);
        }

        echo $this->bulkActionsHelper->buildStatus(
            static::STATUS_USERS_UPDATED_POST_GIGYA,
            [
                'IsFinished' => 1
            ]
        );
        exit;
    }

   public function saveResultsToS3($fileRecordID)
    {
        $fileRecord = $this->gpModel->getFileRecord($fileRecordID);
        $fileID = $fileRecord['FileID'];
        $users = $this->gpModel->getAllUsersByFileID($fileID);
        $csvContents = [];
        // Add headings for the CSV file
        $csvContents[] = implode(',', [
            'Email',
            'FirstName',
            'LastName',
            'Status'
        ]);

        foreach ($users as $user) {
            $csvContents[] = implode(',', [
                $user['Email'],
                $user['FirstName'],
                $user['LastName'],
                $user['Status']
            ]);
        }

        $filename = $fileID . '-' . date('YmdHis');
        $filename .= '-' . $this->bulkActionsHelper->generateRandomString();
        $filename .= '.csv';

        Loader::library('AWS/S3/S3Service');
        $s3Service = new S3Service();
        $s3Service->useGigyaDataFlowConnection();

        $isUploadSuccessful = $s3Service->upload(
            GIGYA_S3_BUCKET,
            GIGYA_S3_UPLOAD_PATH_RESET_PASSWORD_DOWNLOAD . $filename,
            implode("\n", $csvContents)
        );

        if ($isUploadSuccessful) {
            echo $this->bulkActionsHelper->buildStatus(
                static::STATUS_RESULTS_UPLOADED_TO_S3,
                [
                    'Data' => [
                        'resultsFilename' => $filename
                    ]
                ]
            );
        } else {
            echo $this->bulkActionsHelper->buildStatus(
                static::STATUS_UNABLE_TO_UPLOAD_RESULTS,
                [
                    'FileRecordID' => $fileRecordID
                ]
            );
        }
        exit;
    }


    /**
     * Gets all users saved in the Excel file
     *
     * @param $fileRecordID
     * @param int $page
     */
    public function getUsers($fileRecordID, $page = 1)
    {
        $fileRecord = $this->gpModel->getFileRecord($fileRecordID);
        $fileID = $fileRecord['FileID'];
        $users = $this->gpModel->getUsersByFileID($fileID, $page, static::DISPLAY_LIMIT);
        $totalUsers = $this->gpModel->getUserCountByFileID($fileID);

        ob_start();
        Loader::packageElement(
            'bulk_actions/users_display_body',
            static::PACKAGE_HANDLE,
            ['users' => $users]
        );
        $usersHtml = ob_get_clean();

        ob_start();
        Loader::packageElement(
            'bulk_actions/pager',
            static::PACKAGE_HANDLE,
            [
                'page' => $page,
                'limit' => static::DISPLAY_LIMIT,
                'total' => $totalUsers
            ]
        );
        $pagerHtml = ob_get_clean();

        echo json_encode([
            'users' => $usersHtml,
            'pager' => $pagerHtml
        ]);

        exit;
    }

    
    /**
     * Download generated result file from S3
     *
     * @param $filename Valid file from the S3 bucket
     */
    public function downloadResults($filename)
    {
        Loader::library('AWS/S3/S3Service');
        $s3Service = new S3Service();
        $s3Service->useGigyaDataFlowConnection();

        $fullPath = GIGYA_S3_UPLOAD_PATH_RESET_PASSWORD_DOWNLOAD . $filename;

        $result = $s3Service->getObject(GIGYA_S3_BUCKET, $fullPath);

        if (!$result) {
            echo $this->bulkActionsHelper->buildStatus(
                static::STATUS_UNABLE_TO_DOWNLOAD_FILE,
                [
                    'Data' => [
                        'fileName' => $filename
                    ]
                ]
            );
        }

        // Enable file download
        header("Content-Type: {$result['ContentType']}");
        header('Content-Disposition: filename="' . basename($filename) . '"');
        echo $result['Body'];
        exit;
    }


}
?>