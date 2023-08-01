<?php
Loader::model('title_downloadable_file/model', 'cup_content');

$result = array("result"=>"failure", "error"=>"unknown");

if(isset($_REQUEST['fid'])){
	$file = CupContentTitleDownloadableFile::fetchByID($_REQUEST['fid']);
	if($file->delete()){
		$result = array("result"=>"success", "error"=>"");
	}else{
		$result = array("result"=>"failure", "error"=>implode("\n",$file->getErrors()));
	}
}

echo json_encode($result);
exit();