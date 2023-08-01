<?php
/*
 * SB-12 Created by mtanada 20190111
 * Update limit activations for ICE-EM products
 */
const PEAS = 'hub';
const DB_USER_DA = 'anzrds';
const DB_PASS = '7saN8jgetm';
const DB_SL_USERNAME = 'hub';
const DB_SL_PASS = 'ywJZFJcY';

// LOG FILES
const SUCCESS_LOG_FILE = 'update_limit_success_log.txt';
const ERROR_LOG_FILE = 'update_limit_error_log.txt';

function setConfig()
{
    if (isset($_SERVER["HOSTNAME"])) {
        $hostname = $_SERVER["HOSTNAME"];
        switch ($hostname) {
            case 'ip-10-111-33-73':
                define('DB_SERVER', 'hub-dev-tmp.crq5g2d5hryf.ap-southeast-2.rds.amazonaws.com');
                define('DB_USERNAME', DB_USER_DA);
                define('DB_PASSWORD', DB_PASS);
                define('PEAS_DB', PEAS);
                define('SOURCE_FILE_CSV', 'dev_batch_ids.csv');
                break;
            case 'ip-10-111-59-187':
                define('DB_SERVER', 'hub-dev-tmp.crq5g2d5hryf.ap-southeast-2.rds.amazonaws.com');
                define('DB_USERNAME', DB_USER_DA);
                define('DB_PASSWORD', DB_PASS);
                define('PEAS_DB', 'hub_uat');
                define('SOURCE_FILE_CSV', 'uat_batch_ids.csv');
                break;
            case 'ip-10-111-33-7':
                define('DB_SERVER', 'hub-staging-rds-db.crawg7tcejed.ap-southeast-2.rds.amazonaws.com');
                define('DB_USERNAME', DB_SL_USERNAME);
                define('DB_PASSWORD', DB_SL_PASS);
                define('PEAS_DB', PEAS);
                define('SOURCE_FILE_CSV', 'staging_batch_ids.csv');
                break;
            case 'ip-10-111-60-73':
                define('DB_SERVER', 'hub-production-rds-db-ap-southeast-2c.crawg7tcejed.ap-southeast-2.rds.amazonaws.com');
                define('DB_USERNAME', DB_SL_USERNAME);
                define('DB_PASSWORD', DB_SL_PASS);
                define('PEAS_DB', PEAS);
                define('SOURCE_FILE_CSV', 'live_batch_ids.csv');
                break;
            default:
                echo "Error Hostname : Not set.";
        }
    } else {
        define('DB_SERVER', '127.0.0.1');
        define('DB_USERNAME', 'root');
        define('DB_PASSWORD', 'password');
        define('PEAS_DB', PEAS);
        define('SOURCE_FILE_CSV', 'local_batch_ids.csv');

    }

}

/*
 *  Get data from exported file
 * @param filename
 * @return file content
*/
function getDataFromFile($filename)
{
    $file = fopen($filename, 'r');
    // STORE FILE DATA TO $data
    while ($row = fgetcsv($file)) {
        $data[] = $row;
    }
    fclose($file);
    return $data;
}

/*
 * Report Logging
 * @param log information, $filename
*/
function trackLogReport($filename, $logDetails)
{
    $file = fopen($filename, 'a+');
    $logDetails .= "\r\n";
    fwrite($file, $logDetails);
    fclose($file);
}

/* UPDATING PERMISSION LIMIT FIELD VIA BATCH ID
 * @param connection array_batch_id
 * @return boolean
 */
function updateLimitActivation($conn, $data)
{
    // BATCH IDs
    if (count($data) > 0) {
        $batchIds = implode(",", $data);
        $queryPermission = 'SELECT batch.id AS batchID, perm.id AS permID FROM ' . PEAS_DB . '.permissions perm '.
            'INNER JOIN ' . PEAS_DB . '.batches batch ON perm.batch_id = batch.id WHERE perm.proof IS NOT NULL AND ' .
            'perm.limit = 1 AND batch.id IN ('. $batchIds . ')';

        // ITERATE EACH PERMISSION IDs FOR UPDATING
        foreach ($conn->query($queryPermission) as $id) {
            $permId = $id['permID'];
            $batchId = $id['batchID'];
            trackLogReport(SUCCESS_LOG_FILE, "Updating batchID: $batchId with permissionID: $permId");

            $queryUpdate = 'UPDATE '. PEAS_DB .'.permissions AS perm SET perm.limit = 3 WHERE id = ' . $permId . ';';
            $result = $conn->query($queryUpdate);
            if ($result) {
                trackLogReport(SUCCESS_LOG_FILE, "Updated successfully.");
            } else {
                trackLogReport(
                    ERROR_LOG_FILE,
                    "batchID: $batchId with permissionID: $permId record not updated."
                );
                return false;
            }
        } return true;
    } else {
        trackLogReport(ERROR_LOG_FILE, 'Error on Batch IDs provided.');
        return false;
    }
}

/*
 *  MAIN SCRIPT
 */
// SETTING UP CONFIGURATIONS
setConfig();

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check connection
if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
}

// GET DATA ENTITLEMENT IDS FROM FILE
$data = getDataFromFile(SOURCE_FILE_CSV);

// REMOVE HEADER ON THE FILE
array_shift($data);

// RUN UPDATE
if (updateLimitActivation($conn, $data[0])) {
    trackLogReport(SUCCESS_LOG_FILE, "Finished!");
    echo "Finished!";
} else {
    trackLogReport(ERROR_LOG_FILE, "Unsuccessful Script Run.");
    echo "Unsuccessful Script Run.";
}
