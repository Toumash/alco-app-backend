<?php
	if (!defined('R')) {
		die('This script cannot be run directly');
	}
	require_once R . '/model/model.php';

	class ApitokenModel extends Model
	{
		public function __construct()
		{
			parent::__construct();
			$this->log = Logger::getLogger(__CLASS__);
		}

		/**
		 * @param $token
		 *
		 * @throws PDOException
		 * @return bool
		 */
		public function checkExistence($token)
		{
			try {
				$sql = $this->pdo->prepare(
					"SELECT EXISTS(SELECT 1 FROM api_tokens where token=:token) as exist LIMIT 1"
				);
				$sql->bindValue(':token', $token, PDO::PARAM_STR);
				$sql->execute();
				$data = $sql->fetch(PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				$this->log->error('DB in ApitokenModel->checkExistence', $e);

				return false;
			}

			return $data['exist'] == 1;
		}
	}