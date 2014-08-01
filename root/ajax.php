<?php
	session_start();
	define('ROOT', dirname(__FILE__));
	require_once('db/DB.class.php');
	require_once('panel/Log.class.php');
	$db = new Database();

	function get_content($db)
	{
		require_once('controllers/Main.php');
		$json   = file_get_contents('php://input');
		$JSON   = json_decode($json, true);
		$action = (isset($_GET['action'])) ? $_GET['action'] : '';
		$id     = (isset($_GET['id'])) ? $_GET['id'] : '';
		getContent($JSON['v'], $db, $_SESSION['PERMISSION'], $action, $id);
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>AJAX</title></head>
<body>
<div class="content">
	<?php
		/* AJAX check  */
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower(
				$_SERVER['HTTP_X_REQUESTED_WITH']
			) == 'xmlhttprequest'
		) {
			/* special ajax here */
			get_content($db);
		} else {
			echo "Go away, that's our ajax, not for viewing, FUCKER";
		}
	?>
</div>
</body>
</html>