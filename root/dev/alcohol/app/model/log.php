<?php
	if (!defined('R')) {
		die('This script cannot be run directly!');
	}
	require_once R . '/model/model.php';

	class LogModel
	{
		public $DEFAULT_LOG = 'app/logs/Log.log';

		public function __construct()
		{
			$this->DEFAULT_LOG = R . '/logs/Log.log';
		}

		public function read($file = ' ')
		{
			$file    = ($file == ' ') ? $this->DEFAULT_LOG : $file;
			$content = file_get_contents($file);

			return $content;
		}

		public function cut($file = ' ')
		{
			$file = ($file == ' ') ? $this->DEFAULT_LOG : $file;
			$f    = file($file);

// get first 40 elements
			$fa = array_slice($f, 0, 200);

// rewrite usernames file
			file_put_contents($file, implode('', $fa));
		}

		public function getLines($file = ' ')
		{
			$file  = ($file == ' ') ? $this->DEFAULT_LOG : $file;
			$f     = fopen($file, 'rb');
			$lines = 0;

			while (!feof($f)) {
				$lines += substr_count(fread($f, 8192), "\n");
			}

			fclose($f);

			return $lines;
		}
	}