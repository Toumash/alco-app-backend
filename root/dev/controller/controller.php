<?php

	abstract class Controller
	{

		public $request;

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
			array_shift($request);
			if (isset($request[0])) {
				$action = $request[0];
				//Deletes all trash and saves the parameters for use in next controller actions
				array_shift($request);
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

			}
		}

		public abstract function  index();
	}