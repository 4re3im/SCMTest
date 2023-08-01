<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 18/05/2020
 * Time: 11:39 AM
 */

class BulkActionsHelper
{
    const PACKAGE_HANDLE = 'go_provisioning_hotmaths';

    const ERROR_NO_EMAIL = 0;
    const ERROR_NO_FIRST_NAME = 1;
    const ERROR_NO_LAST_NAME = 2;
    const ERROR_NO_PASSWORD = 3;
    const ERROR_ROW_COUNT = 4;
    const ERROR_INVALID_PASSWORD = 5;

    const ENTRY_ERROR_MESSAGES = [
        'No email supplied',
        'No first name supplied',
        'No last name supplied',
        'No password supplied',
        'Not enough row count',
        'Weak password'
    ];

    /**
     * Holds all status messages. The array comes from the controller
     * and sent here.
     *
     * @var array
     */
    private $statusMessages = [];

    /**
     * Set the number of elements in a row for checking.
     * @var int
     */
    private $rowElementCount;

    /**
     * Saves the array of messages from the controller.
     *
     * @param $messages
     */
    public function setMessages($messages)
    {
        $this->statusMessages = $messages;
    }

    /**
     * Generates a JSON encoded status array
     * depending on the passed status number.
     * Options can be passed to override some default values.
     *
     * @param $statusNumber
     * @param null $options
     * @return string
     */
    public function buildStatus($statusNumber, $options = null)
    {
        $FileRecordID = $Data = null;
        $IsFinished = false;

        if ($options) {
            // Extract same variables as above from $options array
            extract($options);
        }

        $currentStatus = [
            'Status' => $statusNumber,
            'Message' => $this->statusMessages[$statusNumber],
            'IsFinished' => $IsFinished,
            'Data' => json_encode($Data),
            'FileRecordID' => $FileRecordID
        ];

        return json_encode($currentStatus);
    }

    public function setRowElementCount($count)
    {
        $this->rowElementCount = $count;
    }

    public function uploadFile()
    {
        Loader::library('file/importer');
        $fi = new FileImporter();
        $tmpName = $_FILES['excel']['tmp_name'];
        $fileName = $_FILES['excel']['name'];

        try {
            $importedFile = $fi->import($tmpName, $fileName);
            $fileID = $importedFile->getFileID();
            $filePath = File::getRelativePathFromID($fileID);

            return [
                'fileID' => $fileID,
                'filePath' => $filePath
            ];
        } catch (Exception $e) {
            return false;
        }
    }

    public function sanitizeEntry($row)
    {
        if (count($row) > $this->rowElementCount) {
            array_splice($row, $this->rowElementCount);
        }
        
        $sanitizedRow = [];

        foreach ($row as $index => $item) {
            $sanitizedItem = trim($item);
            if ($index === 0) { // Email
                $sanitizedItem = strtolower($sanitizedItem);
            }

            if ($index === 3) { // Password
                $sanitizedItem = str_replace(' ', '', $sanitizedItem);
            }
            $sanitizedRow[] = $sanitizedItem;
        }
        return $sanitizedRow;
    }

    /**
     * Validates entries row per row.
     * Generally, records should not have any empty field.
     *
     * @param $row
     * @return array Validation messages
     */
    public function validateEntry($row)
    {
        $validationData = [];

        if (count($row) < $this->rowElementCount) {
            $validationData[] = static::ENTRY_ERROR_MESSAGES[static::ERROR_ROW_COUNT];
            return $validationData;
        }

        // Check for empty fields
        foreach ($row as $index => $field) {
            if (empty($field)) {
                $validationData[] = static::ENTRY_ERROR_MESSAGES[$index];
            }

            // Check password strength. Following established Gigya password rules.
            if ($index === 3 && !empty($field)) {
                $checkLength = preg_match('/^.{8,}$/', $field);
                $checkChar = preg_match('/[a-zA-Z]/', $field);
                $checkNumber = preg_match('/[0-9]/', $field);

                if (!$checkLength || !$checkChar || !$checkNumber) {
                    $validationData[] = static::ENTRY_ERROR_MESSAGES[static::ERROR_INVALID_PASSWORD];
                }
            }
        }

        return $validationData;
    }

    public function checkIfDuplicatePasswords($rows)
    {
        $passwordIndex = 3;
        $passwords = array_column($rows, $passwordIndex);
        return count($passwords) !== count(array_unique($passwords));
    }

    public function checkEmailsIfSameDomain($rows)
    {
        $emailIndex = 0;
        $emails = array_column($rows, $emailIndex);

        // Remove empty emails
        $emails = array_filter($emails);

        $domains = array_map(function ($email) {
            $email = trim($email);
            $emailArr = explode('@', $email);
            return $emailArr[1];
        }, $emails);

        return count(array_unique($domains)) === 1;
    }

    public function generateRandomString()
    {
        $bytes = random_bytes(5);
        return bin2hex($bytes);
    }

    // SB-613 added by jbernardez 20200623
    public function encryptPassword($password)
    {
        global $u;
        return $u->encryptPassword($password);
    }
}
