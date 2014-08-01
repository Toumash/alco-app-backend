<?php
	define('ROOT', dirname(__FILE__));
	define('UPLOAD_DIR', ROOT . '/uploads/');

	$filename = '';
	if (isset($_GET['file']) && file_exists(UPLOAD_DIR . $_GET['file'])) {
		$filename = $_GET['file'];
	} else {
		$filename = 'Alcohol.apk';
	}
	$file = UPLOAD_DIR . $filename;
	$size = filesize($file);
	header('Content-type: application/vnd.android.package-archive');
	header("Content-length: $size");
	header("C-L: $size");
	header('Content-Disposition: attachment; filename="' . $filename . '"');

	readfile($file);