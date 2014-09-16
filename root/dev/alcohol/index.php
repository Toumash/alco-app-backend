<?php
	define('R', dirname(__FILE__) . '/app');
	define('WR', dirname(__FILE__));

	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
	error_reporting(E_ALL | E_STRICT);

	require_once 'app/ControllerLoader.php';
	session_start();

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

				/** @var $controller Controller */
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
	}
