<?php

	require_once R . '/model/model.php';

	class InstallsModel extends Model
	{

		public function register($id)
		{
			$del = $this->pdo->prepare('INSERT INTO app_installs(id) VALUES (:id)');
			$del->bindValue(':id', $id, PDO::PARAM_INT);
			$del->execute();
		}
	}