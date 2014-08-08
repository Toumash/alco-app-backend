<?php
	define('ROOT', dirname(__FILE__));

	session_start();

	header('Content-Type: text/html; charset=utf-8');
	Header("Cache-Control: must-revalidate");

	$offset = 60 * 60 * 24 * 3;
	$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
	header($ExpStr);


	require_once(ROOT . '/lib/Database.class.php');
	require_once(ROOT . '/panel/Log.class.php');
	require_once(ROOT . '/panel/Views.php');
	require_once(ROOT . '/controllers/Main.php');
	$db = new Database();

	/**
	 * LOGGING IN
	 */
	if (isset($_GET['logout'])) {
		session_destroy();
		unset($_SESSION);
	}

#RESETTING LOGINS DATA TO DEFAULT
	if (!isset($_SESSION['auth'])) {
		$_SESSION['auth']       = false;
		$_SESSION['PERMISSION'] = 0;
	}
	$ERRORS = array();
	$INFO   = array();


	if ($_SESSION['auth'] == false) {
		require_once ROOT . '/panel/login.function.php';
		if (isset($_POST['login']) && isset($_POST['password'])) {
			array_push($ERRORS, handleLogin($db, $_POST['login'], $_POST['password'], true));
		}
	}


	require ROOT . "/lib/Rain/autoload.php";

	use Rain\Tpl;

	$config = array(
		"tpl_dir"   => "views/default/",
		"cache_dir" => "cache/",
		"debug"     => false, // set to false to improve the speed
	);

	Tpl::configure($config);

	Tpl::registerPlugin(new Tpl\Plugin\PathReplace());


// create the Tpl object
	$tpl = new Tpl;

	Tpl::configure("auto_escape", false);
//Tpl::configure("php_enabled", true );

	$tpl->assign("title_main", "Alcohol Panel");

	ob_start();
	$_GET['v']      = isset($_GET['v']) ? $_GET['v'] : "";
	$_GET['action'] = isset($_GET['action']) ? $_GET['action'] : "";
	$_GET['id']     = isset($_GET['id']) ? $_GET['id'] : "";
	getContent($_GET['v'], $db, $_SESSION['PERMISSION'], $_GET['action'], $_GET['id']);
	$content = ob_get_contents();
	ob_end_clean();

	$tpl->assign("MENU", Views\create_menu());

	$tpl->assign("LOGIN_FORM", Views\create_login_form());

	$tpl->assign("CONTENT", $content);

	$tpl->assign("FOOTER", Views\create_footer($db));

	$tpl->draw("default");
