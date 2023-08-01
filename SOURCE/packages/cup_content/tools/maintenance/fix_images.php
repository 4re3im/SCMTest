<?php
// Nick Ingarsia
// 03/05/14
// Iterate through all series/titles and re-create the images using the fixed
// re-size method.

// Only an admin can run this...
$u = new User();
$userGroups = $u->getUserGroups();
if(!in_array('Administrators', $userGroups))
{
	exit;
}
// Give the script lots of time to run.
set_time_limit(0);
?>
<h1>Title/Image fixer</h1>
<p style="font-weight: bold">Nick Ingarsia, 03/05/14</p>
<p>This script will iterate through all title/series products found in the following tables:</p>
<ul>
	<li>CupContentSeries</li>
	<li>CupContentTitle</li>
</ul>
<p>It will then re-create the thumb image for each product.</p>
<ul>
	<li>180px</li>
	<li>90px</li>
	<li>60px</li>
</ul>
<p style="color:red">Please note this may take 15 minutes to run... so let it do it's thing :)</p>
<p><a href='?action=run'>Rebuild all images now</a></p>
<?php

if (isset($_GET['action']) && strtolower($_GET['action']) == 'run') {

	define('SERIES_IMAGES_FOLDER', DIR_BASE.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'cup_content'.DIRECTORY_SEPARATOR.
					'images'.DIRECTORY_SEPARATOR.'series'.DIRECTORY_SEPARATOR);

	define('TITLE_IMAGES_FOLDER', DIR_BASE.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'cup_content'.DIRECTORY_SEPARATOR.
					'images'.DIRECTORY_SEPARATOR.'titles'.DIRECTORY_SEPARATOR);

	Loader::model('series/model','cup_content');

	// Get all series ids and try to create the new images
	$db = Loader::db();
	$q = "select * from CupContentSeries";	
	$result = $db->getAll($q);
	$i=1;
	foreach ($result as $obj) {
		saveImage($obj['seriesID'],SERIES_IMAGES_FOLDER,$i);
		$i++;
	}

	// Get all series ids and try to create the new images
	$db = Loader::db();
	$q = "select * from CupContentTitle";	
	$result = $db->getAll($q);
	$i=1;
	foreach ($result as $obj) {
		saveImage($obj['isbn13'],TITLE_IMAGES_FOLDER,$i);
		$i++;
	}
}



// Find the original uploaded image, then re-create a set of better versions
// Original function lifted from the series model
function saveImage($seriesID,$targetPath,$count=null){
	$dest_filename_original = $seriesID;
	
	$dest_filename_180 = $seriesID.'_180.jpg';
	$dest_filename_90 = $seriesID.'_90.jpg';
	$dest_filename_60 = $seriesID.'_60.jpg';

	$dest_filename_original = $targetPath.$dest_filename_original;
	
	$dest_filename_180 = $targetPath.$dest_filename_180;
	$dest_filename_90 = $targetPath.$dest_filename_90;
	$dest_filename_60 = $targetPath.$dest_filename_60;

	if(file_exists($dest_filename_original))
	{
		$imgHelper = Loader::helper('image', 'cup_content');
		$imgHelper::resize2width($dest_filename_original, 180, $dest_filename_180);
		$imgHelper::resize2width($dest_filename_original, 90, $dest_filename_90);
		$imgHelper::resize2width($dest_filename_original, 60, $dest_filename_60);
		chmod($dest_filename_180 , 0777);
		chmod($dest_filename_90 , 0777);
		chmod($dest_filename_60 , 0777);
		echo "<div style='color:white;background-color:green;padding:5px;margin-bottom:10px'>$count : $targetPath"."$seriesID - Created new image</div>";
	}
	else
	{
		echo "<div style='color:white;background-color:red;padding:5px;margin-bottom:10px'>$count : $targetPath"."$seriesID - Source image not found</div>";
	}

}

?>