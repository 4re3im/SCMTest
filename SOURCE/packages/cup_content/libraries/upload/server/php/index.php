<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
$title_id = $_POST['hTitleID'];
$content_id = $_POST['hContentID'];
error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');
$options = array(
		// 'upload_dir' => '/var/www/vhosts/assets.cambridge.edu.au/assets/go/files/'. $title_id . '/' . $content_id . '/' // LIVE
		'upload_dir' => $_SERVER['DOCUMENT_ROOT'] . '/files/' . $title_id . '/' . $content_id . '/',
		'upload_url' => 'files/' . $title_id . '/' . $content_id . '/'
	);
$upload_handler = new UploadHandler($options);
