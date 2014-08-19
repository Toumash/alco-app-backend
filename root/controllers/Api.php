<?php
	ob_start();
	header('Content-Type: text/html; charset=utf-8');
	require_once(ROOT . '/lib/Database.class.php');
	require_once(ROOT . '/panel/models/Api.class.php');
	require_once(ROOT . '/panel/Log.class.php');
	require_once(ROOT . '/panel/login.function.php');
	require_once(ROOT . '/panel/models/Registration.class.php');
	require_once(ROOT . '/lib/bitbucket/bitbucket.lib.php');

	date_default_timezone_set('Europe/Warsaw');
	$db = new Database();
	$api = new Api($db, '');

	/**
	 * @param $db Database
	 *
	 * @return string whole output of the api request
	 */
	function runAPI($db)
	{
		ob_start();
		if (($input = file_get_contents("php://input")) != null) {
			$JSON = json_decode($input, true);
			global $api;
			$api       = new Api($db, $JSON);
			$JSON_dump = $JSON;
			if (isset($JSON_dump['password'])) {
				$JSON_dump['password'] = '---';
			}
			if (isset($JSON_dump['api_token'])) {
				$JSON_dump['api_token'] = '---';
			}
			Log::d('API RQ: ' . json_encode($JSON_dump), Log::$API_LOG);
			$result         = array();
			$JSON['action'] = isset($JSON['action']) ? $JSON['action'] : "";
			if (isset($JSON['api_token'])) {
				$api_token = base64_decode(base64_decode($JSON['api_token']));
				$token_query = $db->query(
					"SELECT EXISTS(SELECT 1 FROM api_tokens where token='{$api_token}') as exist"
				);
				$q           = $token_query->fetch_assoc();

				if ($q['exist'] == 1) {

					switch ($JSON['action']) {
						case 'login':
							$result['result'] = $api->login($JSON['login'], $JSON['password']);
							break;
						case 'register':
							$reg    = new Registration($db, false);
							$result = $reg->index(
								$JSON['login'],
								$JSON['email'],
								$JSON['password'],
								$JSON['password']
							);
							break;
						case 'upload':
							$result['result'] = $api->userUpload($JSON);
							break;
						case 'flag':
							$result['result'] = $api->flagAlcohol($JSON);
							break;
						case 'rate':
							$result['result'] = $api->rateAlcohol($JSON);
							break;
						case 'fetchRatings':
							$result = $api->fetchRatings($JSON);
							break;
						case 'issue':
							$result['result'] = $api->reportIssue(
								$JSON['title'],
								$JSON['description'],
								$JSON['user'],
								$JSON['kind'],
								$JSON['priority']
							);
							break;
						case 'profileDownload':
							$result = $api->downloadProfile($JSON);
							break;
						case 'reg_installation':
							$result['result'] = $api->registerInstallation($JSON['id']);
							break;
						case 'update':
							$result = $api->checkUpdate($JSON);
							break;
						default :
							$result['result'] = 'EMPTY_ACTION';
					}
				} else {
					$result['result'] = 'token';
				}
			} else {
				$result['result'] = 'token';
			}
			$result['ACTION'] = $JSON['action'];
			echo '<json>';
			echo json_encode($result);
			echo '</json>';
		} else {
			if (isset($_GET['db'])) {
				global $api;
				$result = array();
				switch ($_GET['db']) {
					case 'main':
						$result = $api->fetchMainDB();
						break;
					case 'users':
						$result = $api->fetchUserDB();
						break;
				}
				echo '<json>';
				echo json_encode($result);
				echo '</json>';
			}
		}
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

?>
	<!doctype html>
	<html>
	<body>

	<content>
		<?php
			echo runAPI($db);

			$db->close();
		?>
	</content>
	</body>
	</html>
<?
	$length = ob_get_length();
	header("C-L: $length");
	ob_flush();