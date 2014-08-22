<?php

	/**
	 * Created by PhpStorm.
	 * User: Tomasz
	 * Date: 22.08.14
	 * Time: 01:23
	 */
	class ControllerLoader
	{
		/**
		 * It loads the object with the controller.
		 *
		 * @param string $name name class with the class
		 * @param string $path pathway to the file with the class
		 *
		 * @return object
		 */
		public static function loadController($name, $path = null)
		{
			if ($path == null) {
				$path = R . '/controller/';
			}
			$path = $path . $name . '.php';
			$name = $name . 'Controller';
			$name = ucfirst($name);
			try {
				if (is_file($path)) {
					/** @noinspection PhpIncludeInspection */
					require_once $path;
					$ob = new $name();
				} else {
					throw new Exception('Can not open controller ' . $name . ' in: ' . $path);
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