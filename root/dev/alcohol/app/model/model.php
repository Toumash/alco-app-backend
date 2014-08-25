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
				$config_file = R . '/config/config.ini.php';

				$config    = parse_ini_file($config_file, true);
				$server    = $config['db']['server'];
				$login     = $config['db']['login'];
				$password  = $config['db']['password'];
				$database  = $config['db']['database'];
				$this->pdo = new PDO('mysql:host=' . $server . ';dbname=' . $database, $login, $password);
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->pdo->query("SET NAMES 'utf8'");
				//$this->pdo->query("SET CHARACTER SET 'utf8_general_ci'");
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