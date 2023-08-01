<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

class CupContentImageHelper { 
	
	// Nick Ingarsia, 03/05/14
	// Set default compression to 90, maintains a nice image and reduces size
	public static function resize2width($filename, $width, $dest_filename = false, $image_quality = 90, $output_type = "jpg") {
		$image = imagecreatefromstring(file_get_contents($filename));
		$orig_width = imagesx($image);
		$orig_height = imagesy($image);

		// Calc the new height
		$height = (($orig_height * $width) / $orig_width);

		// Create new image to display
		$new_image = imagecreatetruecolor($width, $height);

		// Nick Ingarsia, 03/05/14
		// Use "imagecopyresampled()" to resize the image
		// The image created is much better quality than imagecopyresized()
		// Create new image with changed dimensions
		imagecopyresampled($new_image, $image,
			0, 0, 0, 0,
			$width, $height,
			$orig_width, $orig_height);

		$save_result = false;
		if($output_type == "gif"){
			$save_result = imagegif($new_image, $dest_filename);
		}elseif($output_type == "png"){
			$save_result = imagepng($new_image, $dest_filename);
		}else{
			$save_result = imagejpeg($new_image, $dest_filename, $image_quality);
		}
		
		return $save_result;
	}
	
}
