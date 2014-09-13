<?php
	define('R', dirname(__FILE__) . '/app');
	define('WR', dirname(__FILE__));

	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
	error_reporting(E_ALL | E_STRICT);

	require_once 'app/ControllerLoader.php';
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
	$path = trim($_SERVER['REQUEST_URI'], '/');
	//$path .= '/';

	$args = explode('/', $path); // Split path on slashes
	array_shift($args);
	/*	print_r($args);*/
	if (empty($args) || $args[0] == '') // No path elements means home
	{
		require_once R . '/controller/pages.php';
		$ob = new PagesController();
		$ob->index();
	} else {
		if ($args[0] != 'controller') {
			$name = $args[0];
			$file = R . '/controller/' . $name . '.php';
			if (is_file($file)) {
				$controller = ControllerLoader::loadController($name);
				ControllerLoader::execAction($controller, $args);
			} else {
				/** @var $controller PagesController */
				$controller = ControllerLoader::loadController('pages');
				ControllerLoader::execAction($controller, $args);
			}
		} else {

			/** @var $controller PagesController */
			$controller = ControllerLoader::loadController('pages');
			ControllerLoader::execAction($controller, $args);
		}
		/*		switch ($args[0]) {
					case 'login':
						require_once R . '/controller/login.php';
						$ob = new LoginController();
						$ob->action($args);
						break;
					case 'more':
						echo 'x';
						break;
					case 'articles':
						require_once R . '/controller/articles.php';
						$ob = new ArticlesController();
						$ob->action($args);
						break;
					case 'a':
						require_once R . '/controller/pages.php';
						$ob = new PagesController();
						$ob->action($args);
						break;
					case 'api':
						require_once R . '/controller/api.php';
						$ob = new ApiController();
						$ob->action($args);
						break;
					case 'database':
						require_once R . '/controller/database.php';
						$ob = new DatabaseController();
						$ob->action($args);
						break;
					case 'dbchooser':
						require_once R . '/controller/dbchooser.php';
						$ob = new DbchooserController();
						$ob->action($args);
						break;

					default:
						header('HTTP/1.1 404 Not Found');
						echo '404';

				}*/

	}



