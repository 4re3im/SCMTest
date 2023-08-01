<?php
// NEVER SHOW ERRORS HERE.
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Get the URL that user typed in
$referer = strtolower($_SERVER["REQUEST_URI"]);

// Split it into elements
$referer_array = explode("/", $referer);

// Count the number of elements
$referer_array_elements = count($referer_array);

// Set variables
$environment = "www";
$department = "";
$database = "";
$link_reference = "";

$url_home = "http://www.cambridge.org/";

$link = false;

// Characters no allowed in directory name
$badCharacters = array(" ","$","#",".","@","%","^","&","*","(",")","{","}","[","]","!","/","\\","|",";",":","'","\"",
    "<",">",",",".","~","`","=","+");

// READ IN THE EDUCATION ROOT FOLDERS FROM THE CONFIG FILE.
// PRODUCTION STAFF CAN UPDATE THIS FILE TO SUIT THEIR NEEDS.
// C:\inetpub\wwwroot\Websites\Cambridge University Press\config\education_root_redirects.txt
$rootEducationFolders = array();
$configFile = "education_root_redirects.txt";

// ANZGO-3836 modified by mtanada 20180828
$rootAcademicFolders = array();
$configAcademicFile = "academic_root_redirects.txt";
//$configAcademicFile = "../academic_root_redirects.txt"; // Use this line to work on local

// Open file for reading
$file = fopen($configFile, "r");
// ANZGO-3836 modified by mtanada 20180828
$fileAcademic = fopen($configAcademicFile, "r");

if ($file) {
    $line = "";
    while (!feof($file)) {
        // Grab a line, if it is not a comment.
        $line = fgets($file, 4096)."\n";
        if (substr($line, 0, 1)!="#" && strlen(trim($line))>0) {
            // Check for illegal characters that may be in the file.
            $fail = false;
            foreach ($badCharacters as $key => $value) {
                if (strpos($line, $value)) {
                    $fail = true;
                }
            }
            if (!$fail && $file) {
                array_push($rootEducationFolders, trim($line));
            }
        }
    }
}
// ANZGO-3836 added by mtanada 20180828
if ($fileAcademic) {
    $line = "";
    while (!feof($fileAcademic)) {
        // Grab a line, if it is not a comment.
        $line = fgets($fileAcademic, 4096)."\n";

        if (substr($line, 0, 1)!="#" && strlen(trim($line))>0) {
            // Check for illegal characters that may be in the file.
            $fail = false;
            foreach ($badCharacters as $key => $value) {
                if (strpos($line, $value)) {
                    $fail = true;
                }
            }
            if (!$fail && $fileAcademic) {
                array_push($rootAcademicFolders, trim($line));
            }
        }
    }
}

// LOOK FOR A SECOND LEVEL FOLDER: REQUEST_FOLDER
// HTTP://DOMAIN/FOLDER/REQUEST_FOLDER
// FOLDER MUST NOT BE IN THE EDUCATION ROOT FOLDER ARRAY, IF THE EDUCATION
// REDIRECT TABLE IS TO BE USED.

//added check if referrer is empty
if (!empty($referer_array[1])) {
    if (($referer_array_elements > 4)
        && (!in_array($referer_array[3], $rootEducationFolders) ||
            !in_array($referer_array[3], $rootAcademicFolders))) {
        // ACADEMIC ROOT REQUEST
        // Check for the extra slash on the end where the fifth element (haha) has no value.
        // http://domain/first-folder/<<nothing here>>
        // Basically the second folder was not found, so we treat this like an acadmemic
        // root redirect.
        if ($referer_array[4] == "") {
            // echo "<h2>ACADEMIC ROOT REDIRECT 1</h2>";
            // $department = "academic";
            // $database = "academic/include/db/connection.php";
            // $link_reference = $referer_array[3];
            // Otherwise it does have a value, so check if it's education or academic
        } else {
            // EDUCATION
            // Check first folder after domain
            // http://domain/first-folder
            if ($referer_array[3] == "education") {
                $department = "education";
                $database = true;
                $link_reference = $referer_array[1];
                // ACADEMIC or TEXTBOOK
                // Check first folder after domain
                // http://domain/first-folder/
            } elseif (($referer_array[3] == "textbooks") || ($referer_array[3] == "academic")) {
                // $department = "academic";
                // $database = "academic/include/db/connection.php";
                // $link_reference = $referer_array[4];
            }
        }
        // OK, IT'S DEFINITELY A ROOT REDIRECT
        // HTTP://DOMAIN/FOLDER
    } else {
        // EDUCATION ROOT REDIRECT
        // Found in the array
        if (in_array($referer_array[1], $rootEducationFolders)) {
            $department = "education";
            $database = true;
            $link_reference = $referer_array[1];

            // END OF THE LINE, IT'S AN ACADEMIC ROOT REDIRECT
        } elseif (in_array($referer_array[1], $rootAcademicFolders)) {
            $department = "academic";
            $database = true;
            $link_reference = $referer_array[1];
        }
    }
}
// check the database to see if there's are URL to load.
// is this checking for boolean value? because this will always be true even if the URL is incorrect
if ($database) {
    require 'libraries/Redirect/global.php';
    require 'libraries/Redirect/classes/Database.php';

    $tng_conn = dodbTNG();
    $tng_db = new RedirectDatabase($tng_conn);

    $result = $tng_db->get_edu_url($link_reference);
    // ANZGO-3836 added by mtanada 20180828
    $resultAcademic = $tng_db->getAcaUrl($link_reference);

    if (is_array($result) || is_array($resultAcademic)) {
        $link = true;
    } else {
        $link = false;
    }
}

// If an entry has been found, go to that URL, otherwise default to main CUP homepage.
// ANZGO-3836 modified by mtanada 20180828
if ($link === true) {
    if ($department === "education") {
      $location = html_entity_decode($result['url'], ENT_HTML5);
      header("Location: ". $location);
      exit;
    } elseif ($department === "academic") {
        $location = html_entity_decode($resultAcademic['url'], ENT_HTML5);
        header("Location: " . $location);
        exit;
    }
} else {
  // no redirect, just continue to Concrete5
}

