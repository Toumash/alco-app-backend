<?php

	include R . '/model/model.php';

	class CategoriesModel extends Model
	{

		public function insert($data)
		{
			$ins = $this->pdo->prepare('INSERT INTO categories (name) VALUES (:name)');
			$ins->bindValue(':name', $data['name'], PDO::PARAM_STR);
			$ins->execute();
		}

		public function getAll()
		{
			return $this->select('categories');
		}

		public function delete($id)
		{
			$del = $this->pdo->prepare('DELETE FROM categories where id=:id');
			$del->bindValue(':id', $id, PDO::PARAM_INT);
			$del->execute();
		}
	}