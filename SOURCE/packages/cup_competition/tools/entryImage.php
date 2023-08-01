<?php
function crop2size($srcPath, $dst_w, $dst_h){
	
	$src = imagecreatefromstring(file_get_contents($srcPath));
	$w = imagesx($src);
	$h = imagesy($src);

	//list($dst_w, $dst_h) = explode('x', $size);
	$dst = imagecreatetruecolor($dst_w, $dst_h);

	$dst_x = $dst_y = 0;
	$src_x = $src_y = 0;

	if ($dst_w/$dst_h < $w/$h) {
		$src_w = $h*($dst_w/$dst_h);
		$src_h = $h;
		$src_x = ($w-$src_w)/2;
		$src_y = 0;
	} else {
		$src_w = $w;
		$src_h = $w*($dst_h/$dst_w);
		$src_x = 0;
		$src_y = ($h-$src_h)/2;
	}

	imagecopyresampled($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

	//imagejpeg($dst, $dstPath);
	header('Content-Type: image/jpeg');
	imagejpeg ($dst, NULL, 80);
	imagedestroy($src);
	imagedestroy($dst);
}

function resize2width($srcPath, $dst_w){
	$src = imagecreatefromstring(file_get_contents($srcPath));
	$w = imagesx($src);
	$h = imagesy($src);

	//list($dst_w, $dst_h) = explode('x', $size);
	//$dst = imagecreatetruecolor($dst_w, $dst_h);

	$dst_x = $dst_y = 0;
	$src_x = $src_y = 0;
	
	$dst_h = $dst_w * $h / $w;
	
	$dst = imagecreatetruecolor($dst_w, $dst_h);
	
	$src_w = $w;
	$src_h = $h;

	imagecopyresampled($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

	//imagejpeg($dst, $dstPath);
	header('Content-Type: image/jpeg');
	imagejpeg ($dst, NULL, 80);
	imagedestroy($src);
	imagedestroy($dst);
}

function resize2height($srcPath, $dst_h){
	
	$src = imagecreatefromstring(file_get_contents($srcPath));
	$w = imagesx($src);
	$h = imagesy($src);

	//list($dst_w, $dst_h) = explode('x', $size);
	//$dst = imagecreatetruecolor($dst_w, $dst_h);

	$dst_x = $dst_y = 0;
	$src_x = $src_y = 0;
	
	$dst_w = $w * $dst_h / $h;
	
	$src_w = $w;
	$src_h = $h;

	imagecopyresampled($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

	//imagejpeg($dst, $dstPath);
	header('Content-Type: image/jpeg');
	imagejpeg ($dst, NULL, 80);
	imagedestroy($src);
	imagedestroy($dst);
}
	define('COMPETITION_FILE_DIR', DIR_BASE.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'cup_competition');
	Loader::model('event_entry/model', 'cup_competition');
	
	$entryID = $_GET['entry_id'];
	$width = false;
	if(isset($_GET['width'])){
		$width = $_GET['width'];
	}
	
	$height = false;
	if(isset($_GET['height'])){
		$height = $_GET['height'];
	}
	
	$entryObj = new CupCompetitionEventEntry($entryID);
	$path = COMPETITION_FILE_DIR.DIRECTORY_SEPARATOR.$entryObj->eventID;
	$path = $path.DIRECTORY_SEPARATOR.$entryID.".jpg";
	
	if($width && $height){	//crop to size
		crop2size($path, $width, $height);
		
	}elseif($width){	//resize to width
		resize2width($path, $width);
		
	}elseif($height){	//resize to height
		resize2height($path, $height);
	}
	

	
?>