<?php
	if (!defined('R')) {
		die('This script cannot be run directly');
	}
	require_once R . '/model/model.php';


	class LoginModel extends Model
	{
		public function delete($id)
		{
			$del = $this->pdo->prepare('DELETE FROM categories where id=:id');
			$del->bindValue(':id', $id, PDO::PARAM_INT);
			$del->execute();
		}

		/**
		 * @param $login      string pure user string
		 * @param $password   string pure user password
		 * @param $install_id string  the unique id generated by the application
		 *
		 * @return false|Session|null false if error. Session if OK, null if login error
		 */
		public function createSession($login, $password, $install_id)
		{
			$user = $this->login($login, $password);
			if (is_object($user)) {
				/** @var $installs_model InstallsModel */
				$installs_model = $this->loadModel('installs');

				$dbInstallID = $installs_model->getShortInstallID($install_id);

				if ($dbInstallID == false) {

					$installs_model->register($install_id);

					$dbInstallID = $installs_model->getShortInstallID($install_id);
				}


				// Checking if user is already logged-in
				$sql = $this->pdo->prepare(
					"SELECT COUNT(s._id) AS c FROM sessions AS s WHERE s.userID=:id AND s.installID=:install_id"
				);
				$sql->bindValue(':id', $user->id, PDO::PARAM_INT);
				$sql->bindValue(':install_id', $dbInstallID, PDO::PARAM_STR);
				$sql->execute();
				$r = $sql->fetch(PDO::FETCH_ASSOC);

				$session_count = $r['c'];


				// New, generated session_id
				$new_session_token = md5(uniqid(rand(), true));

				//TODO:error handling
				$success = false;
				if ($session_count == 0) {
					$s = $this->pdo->prepare(
						"INSERT INTO sessions(token,userID,installID) VALUES (:session_token,:userID,:install_id)"
					);
					$s->bindValue(':install_id', $install_id, PDO::PARAM_INT);
					$s->bindValue(':session_token', $new_session_token, PDO::PARAM_INT);
					$s->bindValue(':userID', $user->id, PDO::PARAM_INT);
					$success = $s->execute();

				} else {
					$s = $this->pdo->prepare(
						"UPDATE sessions SET token=:token,WHERE installID=:install_id"
					);
					$s->bindValue(':token', $new_session_token);
					$s->bindValue(':install_id', $dbInstallID, PDO::PARAM_INT);
					$success = $s->execute();
				}

				if ($success) {
					return new Session($new_session_token);
				} else {
					return false;
				}

			} else {
				return null;
			}
		}

		/**
		 * @param $login
		 * @param $password
		 *
		 * @deprecated
		 * @return bool|User
		 */
		public function login($login, $password)
		{
			$select = $this->pdo->prepare(
				"SELECT ID,PERMISSIONS,ACTIVATION,LOGIN FROM users as u where u.LOGIN=:login and u.PASSWORD =:md5password LIMIT 1"
			);
			$select->bindValue(':login', $login, PDO::PARAM_STR);
			$select->bindValue(':md5password', md5($password), PDO::PARAM_STR);
			$select->execute();
			$result = $select->fetch(PDO::FETCH_ASSOC);

			if ($result == false) {
				return false;
			} else {

				return new User($result['ID'], $result['LOGIN'], $result['ACTIVATION'], $result['PERMISSIONS']);
			}
		}

		/**
		 * @param $session Session
		 *
		 * @return bool
		 */
		public function isValidSession(Session $session)
		{
			$sql = $this->pdo->prepare("SELECT COUNT(*) AS c FROM sessions where token = :token");
			$sql->bindValue(':token', $session->token, PDO::PARAM_STR);
			$sql->execute();
			$result = $sql->fetch(PDO::FETCH_ASSOC);

			return $result['c'] > 0;
		}

		/**
		 * @param Session $session
		 *
		 * @return bool|User
		 */
		public function getUserFromSession(Session $session)
		{
			$select = $this->pdo->prepare(
				"SELECT ID,PERMISSIONS,ACTIVATION,LOGIN FROM users AS u,sessions AS s WHERE s.token=:token AND s.userID==u.ID LIMIT 1"
			);
			$select->bindValue(':token', $session->token, PDO::PARAM_STR);
			$select->execute();
			$result = $select->fetch(PDO::FETCH_ASSOC);

			if ($result == false) {
				return false;
			} else {

				return new User($result['ID'], $result['LOGIN'], $result['ACTIVATION'], $result['PERMISSIONS']);
			}
		}

	}