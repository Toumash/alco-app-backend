<?php

	abstract class View
	{
		/**
		 * @var Rain\Tpl
		 */
		public $tpl;

		public function __construct()
		{
			require R . "/vendors/Rain/autoload.php";


			$config = array(
				//"tpl_dir"   => "views/default/",
				"cache_dir" => R . "/cache/",
				"debug"     => false, // set to false to improve the speed
			);

			Rain\Tpl::configure($config);

			//Rain\Tpl::registerPlugin(new Rain\Tpl\Plugin\PathReplace());

			$this->tpl = new Rain\Tpl();

			Rain\Tpl::configure("auto_escape", false);
			Rain\Tpl::configure("php_enabled", true);
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

		/**
		 * It includes template file.
		 *
		 * @param string $name name template file
		 * @param string $path pathway
		 *
		 * @return void
		 */
		public function render($name, $path = 'templates/')
		{
			$path = $path . $name . '.html.php';
			try {
				if (is_file($path)) {
					require $path;
				} else {
					throw new Exception('Can not open template ' . $name . ' in: ' . $path);
				}
			} catch (Exception $e) {
				echo $e->getMessage() . '<br />
                File: ' . $e->getFile() . '<br />
                Code line: ' . $e->getLine() . '<br />
                Trace: ' . $e->getTraceAsString();
				exit;
			}
		}

		public function set($name, $value)
		{
			$this->$name = $value;
		}

		public function get($name)
		{
			return $this->$name;
		}

		abstract function index();
	}