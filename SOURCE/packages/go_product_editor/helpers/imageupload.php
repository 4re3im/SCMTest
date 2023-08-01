<?php
/*
* SB-118
* Added by machua 20190404
* Upload images from CKEDITOR to /files folder
*/
$currentPath = dirname(realpath($_SERVER['SCRIPT_FILENAME']));
$currentDir = '/packages/go_product_editor/helpers';
$baseDir = str_replace($currentDir, '', $currentPath);
define('CKEDITOR_IMAGES_FOLDER', $baseDir . DIRECTORY_SEPARATOR . 'files' .
		DIRECTORY_SEPARATOR . 'cup_content' . DIRECTORY_SEPARATOR . 'images' .
		DIRECTORY_SEPARATOR . 'ckeditor' . DIRECTORY_SEPARATOR);

if (file_exists(CKEDITOR_IMAGES_FOLDER . $_FILES["upload"]["name"]))
{
	// Required: anonymous function reference number
	$funcNum = $_GET['CKEditorFuncNum'] ;

	// Check the $_FILES array and save the file. Assign the correct path to a variable ($filePath).
	$filePath = "/files/cup_content/images/ckeditor/" . $_FILES["upload"]["name"];
	// Usually you will only assign something here if the file could not be uploaded.
	$message = 'The image already exists. Image from server will be loaded.';

	echo "<script type='text/javascript'>";
	echo "window.parent.CKEDITOR.tools.callFunction($funcNum, '$filePath', '$message');";
	echo "</script>";

} else {
	if (!is_dir(CKEDITOR_IMAGES_FOLDER)) {
		mkdir(CKEDITOR_IMAGES_FOLDER, 0777, true);
	}

	move_uploaded_file($_FILES["upload"]["tmp_name"],
		CKEDITOR_IMAGES_FOLDER . $_FILES["upload"]["name"]);
	chmod(CKEDITOR_IMAGES_FOLDER . $_FILES["upload"]["name"] , 0777);
	// Required: anonymous function reference number
	$funcNum = $_GET['CKEditorFuncNum'] ;

	// Check the $_FILES array and save the file. Assign the correct path to a variable ($filePath).
	$filePath = "/files/cup_content/images/ckeditor/" . $_FILES["upload"]["name"];
	// Usually you will only assign something here if the file could not be uploaded.
	$message = 'The image has been uploaded';

	echo "<script type='text/javascript'>";
	echo "window.parent.CKEDITOR.tools.callFunction($funcNum, '$filePath', '$message');";
	echo "</script>";
}