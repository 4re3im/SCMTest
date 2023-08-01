<?php

class GraphicsImageHelper
{

    public static function resizeToWidth(
        $filename,
        $width,
        $destinationFilename = false,
        $quality = 90,
        $outputType = 'jpg'
    ) {
        $image = imagecreatefromstring(file_get_contents($filename));
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        $height = (($originalHeight * $width) / $originalWidth);

        $newImage = imagecreatetruecolor($width, $height);

        imagecopyresampled(
            $newImage,
            $image,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $originalWidth,
            $originalHeight
        );

        if ($outputType === 'gif') {
            $saveResult = imagegif($newImage, $destinationFilename);
        } elseif ($outputType === 'png') {
            $saveResult = imagepng($newImage, $destinationFilename);
        } else {
            $saveResult = imagejpeg($newImage, $destinationFilename, $quality);
        }

        return $saveResult;
    }

}