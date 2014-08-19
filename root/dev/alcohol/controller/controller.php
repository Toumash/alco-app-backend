<?php

	abstract class Controller
	{

		public $request;

		public function __construct()
		{
			if (!isset($_SESSION['auth'])) {
				$_SESSION['auth']       = false;
				$_SESSION['permission'] = 1;
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
		 * It loads the object with the view.
		 *
		 * @param string $name name class with the class
		 * @param string $path pathway to the file with the class
		 *
		 * @return object
		 */
		public function loadView($name, $path = 'view/')
		{
			$path = $path . $name . '.php';
			$name = $name . 'View';
			try {
				if (is_file($path)) {
					require $path;
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

		/**
		 * It loads the object with the model.
		 *
		 * @param string $name name class with the class
		 * @param string $path pathway to the file with the class
		 *
		 * @return object
		 */
		public function loadModel($name, $path = 'model/')
		{
			$path = $path . $name . '.php';
			$name = $name . 'Model';
			try {
				if (is_file($path)) {
					require $path;
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

		public function action($request)
		{
			try {

				array_shift($request);
				if (!empty($request)) {
					$action = $request[0];
					if (count($request) > 1) array_shift($request);
					//Deletes all trash and saves the parameters for use in next controller actions
					$this->request = $request;
					//Uses the method or goes to the index
					if ($action != 'action' && $action != 'loadModel' && $action != 'loadView' && method_exists(
							$this,
							$action
						)
					) {
						$this->$action();
					} else {
					$this->index();
				}

				} else {
					$this->index();
				}
			} catch (Exception $e) {
				echo $e->getMessage() . '<br/>' . $e->getTraceAsString();
			}
		}

		public abstract function  index();
	}