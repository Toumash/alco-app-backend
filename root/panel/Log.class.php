<?php

	/**
	 * @author Toumash <dev.code-sharks.pl/about?p=toumash>
	 */
	class Log
	{

		public static $DEFAULT_LOG = 'logs/main.log';
		public static $API_LOG = 'logs/api.log';

		public static function d($data, $file = ' ')
		{
			$file = ($file == ' ') ? self::$DEFAULT_LOG : $file;
			$fp   = fopen($file, "a");
// blokada zapisu
			flock($fp, 2);
			fwrite($fp, date("Ymd H:i") . ' ' . $data . "\n");
// odblokowanie
			flock($fp, 3);
			fclose($fp);
		}

		public static function read($file = ' ')
		{
			$file    = ($file == ' ') ? self::$DEFAULT_LOG : $file;
			$content = file_get_contents($file);

			return $content;
		}

		public static function cut($file = ' ')
		{
			$file = ($file == ' ') ? self::$DEFAULT_LOG : $file;
			$f    = file($file);

// get first 40 elements
			$fa = array_slice($f, 0, 200);

// rewrite usernames file
			file_put_contents($file, implode('', $fa));
		}

		public static function getLines($file = ' ')
		{
			$file  = ($file == ' ') ? self::$DEFAULT_LOG : $file;
			$f     = fopen($file, 'rb');
			$lines = 0;

			while (!feof($f)) {
				$lines += substr_count(fread($f, 8192), "\n");
			}

			fclose($f);

			return $lines;
		}

	}
