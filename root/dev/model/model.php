<?php

	abstract class Model
	{
		/**
		 * object of the class PDO
		 * @var PDO
		 */
		protected $pdo;

		/**
		 *  Sets connect with the database.
		 */
		public function  __construct()
		{
			try {
				$config_file = '';
				if ($_SERVER['SERVER_NAME'] == '192.168.0.111' || $_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'localhost:8080') {
					$config_file = R . '/config/config_local.ini';
				} else {
					$config_file = R . '/config/config.ini';
				}
				$config    = parse_ini_file($config_file, true);
				$server    = $config['db']['server'];
				$login     = $config['db']['login'];
				$password  = $config['db']['password'];
				$database  = $config['db']['database'];
				$this->pdo = new PDO('mysql:host=' . $server . ';dbname=' . $database, $login, $password);
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
				echo 'The connect can not create: ' . $e->getMessage();
			}
		}

		/**
		 * @param string $name name class with the class
		 * @param string $path pathway to the file with the class
		 *
		 * @return object model
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
		 * @param string $from   Table
		 * @param string $select Records to select (default * (all))
		 * @param string $where  Condition to query
		 * @param string $order  Order ($record ASC/DESC)
		 * @param string $limit  LIMIT
		 *
		 * @return array
		 */
		public function select($from, $select = '*', $where = null, $order = null, $limit = null)
		{
			$query = 'SELECT ' . $select . ' FROM ' . $from;
			if ($where != null) {
				$query = $query . ' WHERE ' . $where;
			}
			if ($order != null) {
				$query = $query . ' ORDER BY ' . $order;
			}
			if ($limit != null) {
				$query = $query . ' LIMIT ' . $limit;
			}

			$select = $this->pdo->query($query);
			$data   = array();
			foreach ($select as $row) {
				$data[] = $row;
			}
			$select->closeCursor();

			return $data;
		}
	}