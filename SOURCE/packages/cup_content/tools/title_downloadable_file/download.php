<?php
Loader::model('title_downloadable_file/model', 'cup_content');
Loader::model('title_downloadable_file_order_record/model', 'cup_content');

$key = false;
if(isset($_GET['request'])){
	$key = $_GET['request'];
}

if($key){
	$fileRecord = CupContentTitleDownloadableFileOrderRecord::fetchByRequestKey($key);
	if($fileRecord){
		if($fileRecord->isExpired()){
			echo "The download link has expired";
		}else{
			$file = CupContentTitleDownloadableFile::fetchByID($fileRecord->fID);
			$file->downloadFile();
		}
		exit();
	}else{
		echo "The link is invalid";
		exit();
	}
}

header("Location: ".BASE_URL.DIR_REL);

?>