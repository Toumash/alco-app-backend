<?php
	define('ROOT', dirname(__FILE__));
	define('UPLOAD_DIR', ROOT . '/uploads/');
	header('Content-Type: text/html; charset=utf-8');
	require_once(ROOT . '/lib/Database.class.php');
	require_once(ROOT . '/panel/models/Api.class.php');
	$db  = new Database();
	$api = new Api($db, '');
	if (isset($_GET['id'])) {
		$id = $db->real_escape_string($_GET['id']);
		$api->updateUserAppUpdates($id);
	}

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