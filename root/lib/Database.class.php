<?php

	class Database extends mysqli
	{

		public static $query_count = 0;

		public function __construct()
		{
			$config_file = '';
			if ($_SERVER['SERVER_NAME'] == '192.168.0.111' || $_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'localhost:8080') {
				$config_file = ROOT . '/config/config_local.ini';
			} else {
				$config_file = ROOT . '/config/config.ini';
			}
			$config   = parse_ini_file($config_file, true);
			$server   = $config['db']['server'];
			$login    = $config['db']['login'];
			$password = $config['db']['password'];
			$database = $config['db']['database'];

			parent::__construct(
				$server,
				$login,
				$password,
				$database
			); //or die("MySQL connection error. Please try again later".$db_info->DB_LOGIN.$db_info->DB_PASS.$db_info->DB_SERVER.$db_info->DB_DB);


			if (mysqli_connect_error()) {
				die('Connecting Error : ' . mysqli_connect_error());
			}

			$this->query("SET NAMES 'utf8'");
			$this->query("SET CHARACTER SET 'utf8_general_ci'");
		}

		public function query($str)
		{
			self::$query_count++;

			return parent::query($str);
		}
	}
