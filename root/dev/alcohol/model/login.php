<?php

	require_once R . '/model/model.php';

	class LoginModel extends Model
	{

		public function login($login, $password)
		{
			$select = $this->pdo->prepare(
				"SELECT ID,PERMISSIONS FROM users as u where u.LOGIN=:login and u.PASSWORD =:md5password LIMIT 1"
			);
			$select->bindValue(':login', $login, PDO::PARAM_STR);
			$select->bindValue(':md5password', md5($password), PDO::PARAM_STR);
			$select->execute();
			$obj = $select->fetch(PDO::FETCH_ASSOC);
			//$success = $select->fetchAll(PDO::FETCH_NUM)==1;
			//$success = $select->fetchColumn()>0;
			if (is_array($obj)) {
				return $obj;
			} else {
				return false;
			}
			/*
			$ins = $this->pdo->prepare('INSERT INTO categories (name) VALUES (:name)');
			$ins->bindValue(':name', $data['name'], PDO::PARAM_STR);
			$ins->execute();*/
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