<?php
require 'global.php';
require '../classes/Database.php';
require '../classes/Builder.php';
require '../classes/PHPExcel/IOFactory.php';

$tng_conn = dodbTNG();
$tng_db = new Database($tng_conn);
$bldr = new Builder();

// Handles upload of xls files and processing into database records.
if($_FILES['files']) {
  $upload_dir = 'uploads/';
  $files = $_FILES['files'];

  // Iterate through all uploaded files and process them.
  for ($i=0; $i < count($files['name']); $i++) {
    $file_path = $upload_dir . $files['name'][$i];

    // Upload to "uploads" directory.
    move_uploaded_file($files['tmp_name'][$i],$file_path);

    $xlReader = PHPExcel_IOFactory::load($file_path);
    $sheetData = $xlReader->getActiveSheet()->toArray(null,true,true,true);

    $fieldsArr = array();
    $insertData = array();
    $updateData = array();

    // Read the data from the active Excel sheet
    foreach ($sheetData as $sDKey => $sDVal) {
      // Create/reset temporary array for the DB insert data.
      $tmp = array();

      // Create/reset temporary ID for the DB update data array.
      $tmpUpdateId = 0;

      // Continue reading the data. We don't need the first-level IDs as they are the
      // Excel row numbers.
      foreach ($sDVal as $dataKey => $dataVal) {
        // Check if we are at first row. These are the headers.
        // If we are not, build the arrays.
        if($sDKey == 1) {
          $fieldsArr[] = $dataVal;
        } else {
          // Check if we are at the first Excel column and its data.
          if($dataKey == "A") {
            // If we are, check the data.
            // If the data is not empty, then there is a record to update with the specified ID.
            // Assign that ID to the temporary update array ID and set that as
            // an array key.
            if($dataVal != "") {
              $tmpUpdateId = $dataVal;
              $updateData[$tmpUpdateId] = array();
            }
          } else {
            // We are not in the first column so build the arrays.
            // Note if the temporary update ID is not zero, build the
            // array in that specified ID.
            if($tmpUpdateId != 0) {
              $updateData[$tmpUpdateId][] = $dataVal;
            } else {
              $tmp[] = "'" . $dataVal . "'";
            }
          }
        }
      }
      if(!empty($tmp)) {
        $tmp[] = 0;
        $tmp[] = "'" . date('Y-m-d') . "'";
        $insertData[] = implode(",",$tmp);
      }
    }
  }

  // The data for insertion and updating is now ready. Proceed to build the queries.
  $ins_id = $tng_db->insert_from_file($fieldsArr,$insertData);
  $update_flag = $tng_db->update_from_file($fieldsArr,$updateData);

  if($ins_id || $update_flag) {
    $results = $tng_db->get_epub_urls();
    echo $bldr->build_disp_table($results);
  } else {
    echo "Upload error";
  }
}
 ?>
