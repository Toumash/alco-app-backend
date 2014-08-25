<?php

	abstract class View
	{
		/**
		 * @var Rain\Tpl
		 */
		public $tpl;

		/**
		 * Configures Rain template engine to work
		 * enables cache
		 * auto_escape->false
		 * php_enabled->true
		 * You need to manually set the tpl_dir
		 * Default is /default
		 */
		public function __construct()
		{
			require_once R . "/vendors/Rain/autoload.php";


			$config = array(
				"tpl_dir"   => R . "/templates/default/",
				"cache_dir" => R . "/cache/",
				"debug"     => false, // set to false to improve the speed
			);

			Rain\Tpl::configure($config);

			//Rain\Tpl::registerPlugin(new Rain\Tpl\Plugin\PathReplace());

			$this->tpl = new Rain\Tpl();

			Rain\Tpl::configure("auto_escape", false);
			Rain\Tpl::configure("php_enabled", true);
		}

		public function configDefaultPath()
		{
			Rain\Tpl::configure("tpl_dir", R . "/templates/default/");
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

		/**
		 * It includes template file.
		 *
		 * @param string $name name template file
		 * @param string $path pathway
		 *
		 * @deprecated
		 * @return void
		 */
		public function render($name, $path = null)
		{
			if ($path == null) {
				$path = R . '/templates/';
			}
			$path = $path . $name . '.html.php';
			try {
				if (is_file($path)) {
					/** @noinspection PhpIncludeInspection */
					require_once $path;
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
	}