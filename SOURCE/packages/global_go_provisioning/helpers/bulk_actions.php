<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 18/05/2020
 * Time: 11:39 AM
 */

class BulkActionsHelper
{
    const PACKAGE_HANDLE = 'global_go_provisioning';

    const ERROR_NO_EMAIL = 0;
    const ERROR_NO_PASSWORD = 1;
    const ERROR_NO_FIRST_NAME = 2;
    const ERROR_NO_LAST_NAME = 3;
    const ERROR_NO_OID = 4;
    const ERROR_INVALID_PASSWORD = 5;
    const ERROR_INVALID_EMAIL = 6;
    const ERROR_NO_ROLE = 7;
    const ERROR_ROW_COUNT = 8;

    const ENTRY_ERROR_MESSAGES = [
        'No email supplied',
        'No first name supplied',
        'No last name supplied',
        'No password supplied',
        'No School OID provided.',
        'Weak password',
        'Email format is invalid',
        'No role supplied',
        'Not enough row count'
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

    // GCAP-1181 modified by mabrigos
    public function sanitizeEntry($row)
    {
        $sanitizedRow = [];

        foreach ($row as $index => $item) {
            $sanitizedItem = trim($item);
            if ($index === ERROR_NO_EMAIL) { // Email
                $sanitizedItem = strtolower($sanitizedItem);
            }

            if ($index === static::ERROR_NO_PASSWORD) { // Password
                $sanitizedItem = str_replace(' ', '', $sanitizedItem);
            }
            $sanitizedRow[] = $sanitizedItem;
        }
        return $sanitizedRow;
    }

    /**
     * Validates entries row per row.
     * Generally, records should not have any empty field.
     * SB-886 modified by mtanada 20210715 schoolOid
     *
     * @param $row
     * @return array Validation messages
     */
    public function validateEntry($row, $schoolOid)
    {
        $validationData = [];
        $hmSchoolIdIndex = 8;
        $requiredFieldIndexes = [0, 1, 2, 3, 7];
        $classNameIndexes = [9, 10, 11];
        $allowedRoles = ['student', 'teacher'];
        if ($schoolOid) {
            array_push($requiredFieldIndexes, 4);
        }

        if (count(array_filter($row)) < $this->rowElementCount) {
            $validationData[] = static::ENTRY_ERROR_MESSAGES[static::ERROR_ROW_COUNT];
            return $validationData;
        }

        // Check for empty fields
        foreach ($row as $index => $field) {
            if (empty($field) && in_array($index, $requiredFieldIndexes)) {
                $validationData[] = static::ENTRY_ERROR_MESSAGES[$index];
            }

            if ($index === static::ERROR_NO_EMAIL && !empty($field)) {
                if (!filter_var($field, FILTER_VALIDATE_EMAIL)) {
                    $validationData[] = static::ENTRY_ERROR_MESSAGES[static::ERROR_INVALID_EMAIL];
                }
            }

            // Check password strength. Following established Gigya password rules.
            if ($index === static::ERROR_NO_PASSWORD && !empty($field)) {
                $checkLength = preg_match('/^.{8,}$/', $field);
                $checkChar = preg_match('/[a-zA-Z]/', $field);
                $checkNumber = preg_match('/[0-9]/', $field);

                if (!$checkLength || !$checkChar || !$checkNumber) {
                    $validationData[] = static::ENTRY_ERROR_MESSAGES[static::ERROR_INVALID_PASSWORD];
                }
            }

            if ($index === static::ERROR_NO_ROLE && !empty($field)) {
                if (!in_array(strtolower($field), $allowedRoles)) {
                    $validationData[] = $field . ' role is not allowed';
                }
            }

            if (in_array($index, $classNameIndexes) && empty($row[$hmSchoolIdIndex]) && !empty($field)) {
                $validationData[] = 'School ID is required for class ' . $field;
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
