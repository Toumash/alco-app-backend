<?php

	define('LVL_VIEW_MAIN', 1);
	define('LVL_MAIN_DELETE', 4);
	define('LVL_MAIN_ADD', 4);

	abstract class Controller
	{

		public $request;
		public $action;

		public function __construct()
		{
			if (!isset($_SESSION['auth'])) {
				$_SESSION['auth'] = false;
				$_SESSION['lvl']  = 1;
			}
		}

		/**
		 * It redirects URL.
		 *
		 * @param string $url URL to redirect
		 *
		 * @return void
		 */
		public function redirect($url)
		{
			header("location: " . $url);
		}

		/**
		 * It redirects to local alcohol project URL.
		 *
		 * @param string $url URL to redirect
		 *
		 * @return void
		 */
		public function redirectLocal($url)
		{
			header("location: " . '/alcohol' . $url);
		}

		/**
		 * It loads the object with the model.
		 *
		 * @param string $name name class with the class
		 * @param string $path pathway to the file with the class
		 *
		 * @return object
		 */
		public function loadModel($name, $path = null)
		{
			if ($path == null) {
				$path = R . '/model/';
			}
			$path = $path . $name . '.php';
			$name = $name . 'Model';
			try {
				if (is_file($path)) {
					/** @noinspection PhpIncludeInspection */
					require_once $path;
					$ob = new $name();
				} else {
					throw new Exception('Can not open model ' . $name . ' in: ' . $path);
				}
			} catch (Exception $e) {
				echo $e->getMessage() . '<br />
                File: ' . $e->getFile() . '<br />
                Code line: ' . $e->getLine() . '<br />
                Trace: ' . $e->getTraceAsString();
				exit;
			}

			return $ob;
		}

		public abstract function  index();

		public function requirePermissionLvl($required)
		{
			$lvl = $_SESSION['lvl'];
			if ($_SESSION['lvl'] < $lvl) {

				/** @var $view PermwarningView */
				$view = $this->loadView('permwarning');
				$view->index($required, $lvl);
				exit();
			}
		}

		/**
		 * It loads the object with the view.
		 *
		 * @param string $name name class with the class
		 * @param string $path pathway to the file with the class
		 *
		 * @return object
		 */
		public function loadView($name, $path = null)
		{
			if ($path == null) {
				$path = R . '/view/';
			}
			$path = $path . $name . '.php';
			$name = $name . 'View';
			try {
				if (is_file($path)) {
					/** @noinspection PhpIncludeInspection */
					require_once $path;
					$ob = new $name();
				} else {
					throw new Exception('Can not open view ' . $name . ' in: ' . $path);
				}
			} catch (Exception $e) {
				echo $e->getMessage() . '<br />
                File: ' . $e->getFile() . '<br />
                Code line: ' . $e->getLine() . '<br />
                Trace: ' . $e->getTraceAsString();
				exit;
			}

			return $ob;
		}
	}