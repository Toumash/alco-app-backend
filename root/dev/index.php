<?php
	define('R', dirname(__FILE__));

	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
	error_reporting(-1);
	session_start();
	/*	if ($_GET['module'] == 'categories') {
			require R . '/controller/categories.php';
			$ob = new CategoriesController();
			if(method_exists($ob,$_GET['action'])){
				echo 'lololo';
			}else{
				echo 'xxxx';
			}
			$ob->$_GET['action']();
		} else {
			if ($_GET['module'] == 'articles') {
				require R . '/controller/articles.php';
				$ob = new ArticlesController();
				$ob->$_GET['action']();
			} elseif ($_GET['module'] == 'login') {

				require_once R.'/controller/login.php';
				$ob = new LoginController();
				$ob->index();
			} else {
				require R . '/controller/articles.php';
				$ob = new ArticlesController();
				$ob->index();
			}
		}*/
	$path = ltrim($_SERVER['REQUEST_URI'], '/'); // Trim leading slash(es)
	$path .= '/';
	$args = explode('/', $path); // Split path on slashes
	print_r($args);
	if ($args[0] == '') // No path elements means home
	{
		require_once R . '/controller/articles.php';
		$ob = new ArticlesController();
		$ob->index();
	} else {
		switch ($args[0]) {
			case 'login':
				require_once R . '/controller/login.php';
				$ob = new LoginController();
				$ob->action($args);
				break;
			case 'more':
				echo 'x';
				break;
			default:
				header('HTTP/1.1 404 Not Found');
				echo '404';

		}
	}