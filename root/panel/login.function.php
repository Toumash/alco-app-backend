<?php
	/**
	 * @param mysqli $db            database whic contains table users
	 * @param        $login
	 * @param        $password
	 * @param bool   $isNormalOrApi select handle type. true - normal, only used in web login, false udes in public api calls
	 *
	 * @return string for normal returns error or nothing, FOR API returns OK
	 */
	function handleLogin($db, $login, $password)
	{

		if ($login != '' && $password != '') {

			// dodaje znaki unikowe dla potrzeb poleceń SQL
			$login    = $db->real_escape_string($login);
			$password = $db->real_escape_string($password);

			$md5password = md5(utf8_encode($password));

			$result = $db->query(
				"SELECT PERMISSIONS,ACTIVATION,ID FROM users WHERE (LOGIN = '$login' OR EMAIL= '$login') AND PASSWORD = '$md5password'"
			);
			if ($db->affected_rows == 1) {
				$row = $result->fetch_assoc();
				if (strlen($row['ACTIVATION']) < 2) {
					$_SESSION['user']       = $login;
					$_SESSION['auth']       = true;
					$_SESSION['PERMISSION'] = $row['PERMISSIONS'];

					return 'ok';
				} else {
					return 'Twoje konto nie zostało jeszcze aktywowane. Prosimy sprawdzić emaila wraz z folderem <b style="color:red;">SPAM</b>';
				}
			} else {
				return 'Hasło/Login niepoprawne';
			}
		} else {
			return 'EMPTY';
		}
	}
