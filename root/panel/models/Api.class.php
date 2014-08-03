<?php
	define('R_LOGIN_PASSWORD', 'login_password');
	define('R_ERROR', 'error');
	define('R_OK', 'ok');
	define('R_EMPTY', 'empty');

	define('DB_TABLE_ALCOHOLS', 'main_alcohols');
	define('DB_TABLE_USER_ALCOHOLS', 'user_alcohols');
	define('DB_TABLE_USERS', 'users');
	define('DB_TABLE_FLAGS', 'alcohol_flags');
	define('DB_TABLE_COMMENTS', 'alcohol_comments');

	class Api
	{
		/** @var  array
		 * Profile data used by download profile. Pull it */
		public $profileData;
		/**
		 * @var int
		 */
		public $profileID;
		/**
		 * @var Database mysqli
		 */
		private $db;

		public function __construct($db)
		{
			$this->db = $db;
			date_default_timezone_set('Europe/Warsaw');

		}

		public function fetchMainDB()
		{
			$sql_select = "SELECT ID,NAME,PRICE,PERCENT,VOLUME,TYPE,SUBTYPE,DEPOSIT FROM main_alcohols ORDER BY NAME ASC";
			$query      = $this->db->query($sql_select);
			$result     = array();
			while ($row = $query->fetch_assoc()) {
				$alc            = array();
				$alc['NAME']    = $row['NAME'];
				$alc['ID']      = $row['ID'];
				$alc['TYPE']    = $row['TYPE'];
				$alc['SUBTYPE'] = $row['SUBTYPE'];
				$alc['VOLUME']  = $row['VOLUME'];
				$alc['PRICE']   = $row['PRICE'];
				$alc['PERCENT'] = $row['PERCENT'];
				$alc['DEPOSIT'] = $row['DEPOSIT'];
				$result[]       = $alc;
			}

			return $result;
		}

		public function fetchUserDB()
		{
			$zapytanie   = "SELECT ID,NAME,PRICE,PERCENT,VOLUME,TYPE,SUBTYPE,DEPOSIT FROM user_alcohols ORDER BY NAME ASC";
			$idzapytania = $this->db->query($zapytanie);
			$result      = array();
			while ($row = $idzapytania->fetch_assoc()) {
				$alc            = array();
				$alc['NAME']    = $row['NAME'];
				$alc['ID']      = $row['ID'];
				$alc['TYPE']    = $row['TYPE'];
				$alc['SUBTYPE'] = $row['SUBTYPE'];
				$alc['VOLUME']  = $row['VOLUME'];
				$alc['PRICE']   = $row['PRICE'];
				$alc['PERCENT'] = $row['PERCENT'];
				$alc['DEPOSIT'] = $row['DEPOSIT'];
				$result[]       = $alc;
			}

			return $result;
		}


		public function userUpload($JSON)
		{
			if (handleLogin($this->db, $JSON['login'], $JSON['password'], false) == 'ok') {
				$alcohols = $JSON['alcohols'];
				##TRANSACTION
				$this->db->query("BEGIN");
				$ok = true;
				//print_r($JSON_a);
				foreach ($alcohols as $row) {
					$name    = $this->db->real_escape_string($row['NAME']);
					$price   = $this->db->real_escape_string($row['PRICE']);
					$type    = $this->db->real_escape_string($row['TYPE']);
					$subtype = $this->db->real_escape_string($row['SUBTYPE']);
					$volume  = $this->db->real_escape_string($row['VOLUME']);
					$percent = $this->db->real_escape_string($row['PERCENT']);
					$deposit = $this->db->real_escape_string($row['DEPOSIT']);

					$ok = $this->db->query(
						"INSERT INTO user_alcohols(NAME,PRICE,TYPE,SUBTYPE,VOLUME,PERCENT,DEPOSIT) VALUES ('$name',$price,$type,$subtype,$volume,$percent,$deposit)"
					);
					if ($ok == false) {
						break;
					}
				}
				$okok = $this->db->query("COMMIT");
				if ($okok) {
					return R_OK;
				} else {
					return R_ERROR;
				}
			} else {
				return R_LOGIN_PASSWORD;
			}
		}

		public function flagAlcohol($input)
		{
			if (!empty($input)) {
				$login = $input['login']; // base64_decode($JSON['login']);
				if ($this->login($login, $input['password']) == R_OK) {

					if (!empty($input['id']) && !empty($input['content'])) {
						$id      = $this->db->real_escape_string($input['id']);
						$content = $input['content'];
						//$query = $this->db->query("SELECT ID FROM MAIN_ALCOHOLS WHERE ID='$id' LIMIT 1;");
						$query_exist = $this->db->query(
							"SELECT EXISTS(SELECT 1 FROM main_alcohols where ID=$id) as exist"
						);
						print_r(mysqli_error($this->db));
						$result = $query_exist->fetch_assoc();
						if ($result['exist'] == 1) {

							// if (mysqli_num_rows($query) == 1) {
							//$json_string = $query->fetch_assoc();
							//print_r($json_string);
							/*                        $db_json = json_decode($json_string['FLAGS'], true);
													$data = $db_json;
													$data[$login] = $input['info'];*/
							$time                = date("Y.m.d H:i:s");
							$query_string_insert = "INSERT INTO alcohol_flags(alcoholID,userID,content,time) VALUES ($id,{$this->profileID},'{$content}','{$time}')";
							$insert_result       = $this->db->query($query_string_insert);
							Log::d(mysqli_error($this->db));
							Log::d($query_string_insert);
							if ($insert_result == true) {
								return R_OK;
							} else {
								return R_ERROR;
							}
							//print_r($data);
							// $this->db->query("UPDATE MAIN_ALCOHOLS SET `FLAGS`='" . $this->db->real_escape_string(json_encode($data)) . "' WHERE ID=" . $input['id']);

						} else {
							return 'no_id';
						}
					} else {
						return 'empty_info';
					}
				} else {
					return R_LOGIN_PASSWORD;
				}
			} else {
				return R_EMPTY;
			}
		}

		/**
		 * @param string $login
		 * @param string $password
		 *
		 * @return string ok, login_password, activation, empty
		 */
		public function login($login, $password)
		{

			if ($login != '' && $password != '') {

				// dodaje znaki unikowe dla potrzeb poleceń SQL
				//$login = base64_decode($login);
				$login    = $this->db->real_escape_string($login);
				$password = $this->db->real_escape_string($password);

				$md5password = md5(utf8_encode($password));

				$result = $this->db->query(
					"SELECT PERMISSIONS,ACTIVATION,ID FROM users WHERE (LOGIN = '$login' OR EMAIL= '$login') AND PASSWORD = '$md5password'"
				);

				Log::d(mysqli_error($this->db));
				if ($result->num_rows == 1) {
					$row = $result->fetch_assoc();
					if (strlen($row['ACTIVATION']) < 2) {
						$this->profileID = $row['ID'];

						/*
						  $_SESSION['user'] = $login;
						  $_SESSION['auth'] = TRUE;
						  $_SESSION['PERMISSION'] = $row['PERMISSIONS'];
						 */

						return R_OK;
					} else {
						return 'activation';
					}
				} else {
					return R_LOGIN_PASSWORD;
				}
			} else {
				return R_EMPTY;
			}
		}

		public function reportIssue($input)
		{
			if (!isset($input['title'])) {
				return 'empty';
			} else {
				$title       = $input['title'];
				$description = isset($input['description']) ? $input['description'] : 'No description';
				$user        = isset($input['user']) ? $input['user'] : 'Anonymous';
				$priority    = isset($input['priority']) ? $input['priority'] : 'major';
				$kind        = isset($input['kind']) ? $input['kind'] : 'bug';

// Config Values:
				$basicAuth        = 'Q1NCdWdSZXBvcnRlcjphbGNvcmVwb3J0MzIx'; // Base64 encode of: username:password of your Read only user.
				$bitBucketAccount = 'code-sharks'; // Team account which contains your repo.
				$bitBucketRepo    = 'alcohol'; // Name of your repo.
				$companyName      = 'Code Sharks'; // The name of your company or department (used for the confirmation email).


				$status = submitBug(
					$title,
					$description,
					$user,
					$bitBucketAccount,
					$bitBucketRepo,
					$basicAuth,
					null,
					'new',
					$priority,
					$kind
				);
				if ($status === false) {
					return R_ERROR;
				} else {
					//echo("<span class='bugformsuccess'>Thank you, your bug <b># ".$status['issueid']."</b> has been submitted.</span>");
					sendBugEmail(
						$user,
						$status['issueid'],
						$companyName,
						$status['issueurl']
					); // Leave URL parameter blank if you don't want it in the email.
					return R_OK;
				}
			}
		}

		/**
		 * @param $input array ('login'=>string,'password'=>string)
		 *               to download profile data use $apiObj->profileData array
		 *
		 * @return string R_OK,R_LOGIN_PASSWORD
		 */
		public function downloadProfile($input)
		{
			if ($this->login($input['login'], $input['password']) == 'ok') {

				//$login    = $this->db->real_escape_string($input['login']);
				//	$password = $this->db->real_escape_string($input['password']);

				//$md5password = md5(utf8_encode($password));
				$result = $this->db->query(
				"SELECT SEX,WEIGHT,EMAIL,COUNT(alcoholID) as 'rat_count' FROM users,alcohol_ratings WHERE users.ID={$this->profileID} AND alcohol_ratings.userID={$this->profileID}"
				);

				if ($result->num_rows == 1) {
					$row = $result->fetch_assoc();
					$sex = -1;
					switch ($row['SEX']) {
						case 0:
							$sex = 0;
							break;
						case 1:
							$sex = 1;
							break;
					}
					$weight = -1;
					if (strlen($row['WEIGHT']) > 1) {
						$weight = $row['WEIGHT'];
					}
					$profile           = array(
						'sex'    => $sex,
						'weight' => $weight,
						'email'     => $row['EMAIL'],
						'rat_count' => $row['rat_count']
					);
					$this->profileData = $profile;

					return R_OK;
				} else {
					//not sure if it is necessary but i think it wont change
					return R_LOGIN_PASSWORD;
				}
			} else {
				return R_LOGIN_PASSWORD;
			}
		}

		/**
		 * @param $input array('id'=>int,'content'=>string,'login'=>string,'password'=>string)
		 *
		 * @return string 'no_id'|'empty_info'|R_LOGIN_PASSWORD|R_EMPTY
		 */
		public function rateAlcohol($input)
		{
			if (!empty($input)) {
				$login = $input['login']; // base64_decode($JSON['login']);
				if ($this->login($login, $input['password']) == R_OK) {

					if (!empty($input['id']) && !empty($input['content'])) {
						$id      = $this->db->real_escape_string($input['id']);
						$content = $this->db->real_escape_string($input['content']);
						$rate    = $this->db->real_escape_string($input['rate']);
						//$query = $this->db->query("SELECT ID FROM MAIN_ALCOHOLS WHERE ID='$id' LIMIT 1;");
						$query_exist = $this->db->query(
							"SELECT EXISTS(SELECT 1 FROM main_alcohols where ID=$id) as exist"
						);
						$result      = $query_exist->fetch_assoc();
						if ($result['exist'] == 1) {
							$query_string_insert = '';
							$time                = date("Y.m.d H:i:s");
							$query_user_exists   = $this->db->query(
								"SELECT EXISTS(SELECT 1 FROM alcohol_ratings where alcoholID={$id} and userID={$this->profileID}) as exist"
							);

							$res = $query_user_exists->fetch_assoc();

							if ($res['exist'] == 1) {
								global $query_string_insert;
								$query_string_insert = "UPDATE alcohol_ratings SET content='{$content}',time='{$time}',rate={$rate} where alcoholID ={$id} and userID={$this->profileID}";
							} else {
								global $query_string_insert;
								$query_string_insert = "INSERT INTO alcohol_ratings(alcoholID,userID,content,time,rate) VALUES ($id,{$this->profileID},'{$content}','{$time}',{$rate})";
							}


							$insert_result = $this->db->query($query_string_insert);

							if ($insert_result == true) {
								return R_OK;
							} else {
								Log::d("rating error: " . mysqli_error($this->db));
								Log::d("rating query causing errors: " . $query_string_insert);

								return R_ERROR;
							}
							//print_r($data);
							// $this->db->query("UPDATE MAIN_ALCOHOLS SET `FLAGS`='" . $this->db->real_escape_string(json_encode($data)) . "' WHERE ID=" . $input['id']);

						} else {
							return 'no_id';
						}
					} else {
						return 'empty_info';
					}
				} else {
					return R_LOGIN_PASSWORD;
				}
			} else {
				return R_EMPTY;
			}
		}

		public function fetchFlags()
		{
			$query_string = "SELECT main_alcohols.NAME,main_alcohols.PRICE,alcohol_flags.content FROM main_alcohols,alcohol_flags WHERE alcohol_flags.alcoholID = main_alcohols.ID";
			$query        = $this->db->query($query_string);
			$data         = array();
			$tmp          = array();
			while ($row = $query->fetch_assoc()) {
				$tmp['name']    = $row['NAME'];
				$tmp['price']   = $row['PRICE'];
				$tmp['content'] = $row['content'];
				$data[]         = $tmp;
				//no need to clean $tmp - new content always override old
			}
			$toReturn = array('result' => R_OK, 'data' => $data);

			return $toReturn;
		}

		/**
		 * @param $input array('alcohol_id'=>int)
		 *
		 * @return array array('result'=> result,
		 * 'data'=>array('date'=>string,
		 * 'content'=>string,
		 * 'author'=>string))
		 */
		public function fetchRatings($input)
		{
			if (isset($input['alcohol_id'])) {
				$alcohol_id = $this->db->real_escape_string($input['alcohol_id']);
				$limit      = 120;
				if (isset($input['count'])) {
					$limit = $this->db->real_escape_string($input['count']);
				}
				$query_string = "SELECT r.rate,r.time,r.content,u.LOGIN FROM alcohol_ratings AS r,users  AS u WHERE r.userID= u.ID AND r.alcoholID={$alcohol_id} ORDER BY r.time DESC LIMIT {$limit}";
				$query        = $this->db->query($query_string);
				if ($query->num_rows > 0) {
					$result = array();
					while ($row = $query->fetch_assoc()) {
						$alc      = array();
						$alc['d'] = $row['time'];
						$alc['c'] = $row['content'];
						$alc['a'] = $row['LOGIN'];
						$alc['r'] = $row['rate'];

						$result[] = $alc;
					}
					$toReturn = array('result' => R_OK, 'data' => $result);

					//print_r($toReturn);
					return $toReturn;
				} else {
					return array('result' => 'no_comments');
				}
			} else {
				return array('result' => R_EMPTY);
			}
		}
	}
