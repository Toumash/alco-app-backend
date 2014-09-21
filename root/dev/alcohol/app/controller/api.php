<?php
	if (!defined('R')) {
		die ('This script cannot be executed directly');
	}

	require_once R . '/controller/controller.php';

	/* Constants
	* ===========*/
	define('R_ERROR', 'error');
	define('R_OK', 'ok');
	define('R_NO_METHOD', 'no_method');

	define('R_BAD_RQ', 'bad_request');
	define('R_NOT_EXISTS', 'not_exists');

	define('R_VOID_SESSION', 'void_session');
	define('R_NO_SESSION_DATA', 'no_session_token');
	define('R_NO_JSON', 'no_input');
	define('R_DB_ERROR', 'db_error');
	define('R_INVALID_LOGIN', 'inv_login');

	define('R_NO_API_KEY', 'no_api_key');
	define('R_BAD_API_KEY', 'bad_api_key');


	date_default_timezone_set('Europe/Warsaw');

	/**
	 * EACH request should include <b>api_token</b><br>
	 * Every public method is created to be used ONLY by the webAPI, not by the internal functions, so DO NOT rely on them. This is MVC, so look at the models.
	 * Class ApiController
	 */
	class ApiController extends Controller
	{
		/**
		 * @var JSONView JSONView
		 */
		private $jsonView;

		public function __construct()
		{
			parent::__construct();
			$this->jsonView = $this->getJSONView();
		}

		/**
		 * @return JSONView
		 */
		private function getJSONView()
		{
			$view = $this->loadView('json');

			return $view;
		}

		/***
		 * login<br>
		 * password<br>
		 * install_id<br>
		 */
		public function login()
		{
			$view = $this->jsonView;
			$data = $this->getJSONData();

			$array = array();
			if ($data == false) {
				$this->displayEmptyRQ($view, 'login');
			} else {
				if ($this->requireGoodApiToken($data)) {
					$result = null;
					if (isset($data['login']) && isset($data['password']) && isset($data['install_id'])) {

						/** @var $login_model UserModel */
						$login_model = $this->loadModel('user');
						$result      = $login_model->createSession(
							$data['login'],
							$data['password'],
							$data['install_id']
						);
					}
					if ($result == null) {
						$array = $this->buildErrorArray(R_INVALID_LOGIN);
					} elseif ($result == false) {

						$array = $this->buildErrorArray(R_DB_ERROR);
					} else {
						$array = array('result' => R_OK, 'session_token' => $result->token);
					}
					$view->index($array, 'login');
				}
			}
		}

		/**
		 * Pulls the JSON from the POST payload
		 * @return mixed|bool mixed JSON array(php array)<br>
		 * bool false is there is no data
		 */
		private function getJSONData()
		{
			if (($input = file_get_contents("php://input")) != null) {


				$JSON = json_decode($input, true);
				if ($JSON == false) {
					return false;
				}

				$JSON_dump = $JSON;
				if (isset($JSON_dump['password'])) {
					$JSON_dump['password'] = '---';
				}
				if (isset($JSON_dump['api_token'])) {
					$JSON_dump['api_token'] = '---';
				}
				if (isset($JSON_dump['install_id'])) {
					$JSON_dump['install_id'] = '---';
				}
				/** @var $model UseralcModel */
				$model = $this->loadModel('useralc');
				$model->log->error('Bad json: ' . json_encode($JSON_dump));

				return $JSON;
			} else {
				return false;
			}
		}

		/**
		 * @param $view   JSONView
		 * @param $action string
		 */
		private function displayEmptyRQ($view, $action)
		{
			$view->index($this->buildErrorArray(R_NO_JSON), $action);
		}

		private function buildErrorArray($nfo)
		{
			return array('result' => R_ERROR, 'error_info' => $nfo);
		}

		/**
		 * @param $data array
		 *
		 * @return bool
		 */
		private function requireGoodApiToken($data)
		{
			if (isset($data['api_token'])) {
				/** @var $apiModel ApitokenModel */
				$apiModel = $this->loadModel('apitoken');

				$result = $apiModel->checkExistence($data['api_token']);
				if ($result == false) {
					$this->jsonView->index(
						$this->buildErrorArray(R_BAD_API_KEY),
						isset($data['action']) ? $data['action'] : 'null'
					);

					return false;
				} else {
					return true;
				}
			} else {
				$this->jsonView->index(
					$this->buildErrorArray(R_NO_API_KEY),
					isset($data['action']) ? $data['action'] : 'null'
				);

				return false;
			}
		}

		/**
		 * STANDARD
		 * <br/>
		 * RETURNS<br/>
		 *'email'   => email string
		 * 'ratings' => ratings_count integer
		 * 'weight'  => weight float
		 * 'sex'     => sex bool 1 or 0
		 */
		public function fetchProfile()
		{
			$view = $this->jsonView;
			$data = $this->getJSONData();


			if ($data == false) {
				$this->displayEmptyRQ($view, 'login');
			} else {
				if ($this->requireGoodApiToken($data)) {
					if ($this->requireActiveSession($data, 'fetchRatings')) {
						$session_token = $data['session_token'];
						$user          = $this->getUserFromSession($session_token);

						if ($user == false) {
							$view->index($this->buildErrorArray(R_VOID_SESSION), 'fetchProfile');
						} else {
							/** @var $model UserModel */
							$model   = $this->loadModel('user');
							$profile = $model->getPublicUserProfile($user->id);
							$view->index(array('result' => R_OK, 'data' => $profile), 'fetchProfile');
						}
					}
				}
			}
		}

		/**
		 * @param array  $json whole json from the user to save writing more code
		 *                     and show the warning for not session_token included
		 * @param string $action
		 * @param bool   $return
		 *
		 * @return bool default false - handle the error page display
		 */
		private
		function requireActiveSession(
			&$json,
			$action,
			$return = false
		) {
			if (isset($json['session_token'])) {
				/** @var $model UserModel */
				$model = $this->loadModel('user');
				$result = $model->isValidSession(new Session($json['session_token']));

				if ($result == false) {
					if ($return == false) {
						$view = $this->jsonView;
						$view->index($this->buildErrorArray(R_VOID_SESSION), $action);
					}

					return false;
				}

				return true;
			} else {
				if ($return == false) {
					$view = $this->jsonView;
					$view->index($this->buildErrorArray(R_NO_SESSION_DATA), $action);
				}

				return false;
			}
		}

		/**
		 * @param $session_token string
		 *
		 * @return bool|User false of USER
		 */
		private
		function getUserFromSession(
			$session_token
		) {
			/** @var $login_model UserModel */
			$login_model = $this->loadModel('user');

			return $login_model->getUserFromSession(new Session($session_token));
		}

		public function reportBug()
		{

			$view = $this->jsonView;
			$data = $this->getJSONData();


			if ($data == false) {
				$this->displayEmptyRQ($view, 'reportbug');
			} else {
				if ($this->requireGoodApiToken($data)) {
					if (isset($data['title']) && isset($data['description']) && isset($data['user']) && isset($data['kind']) && isset($data['priority'])) {
						$title       = $data['title'];
						$description = $data['description'];
						$user        = $data['user'];
						$kind        = $data['kind'];
						$priority    = $data['prority'];

						/** @var $model BugsModel */
						$model = $this->loadModel('bugs');
						$model->reportBug($title, $description, $user, $kind, $priority);
					} else {
						$view->index($this->buildErrorArray(R_BAD_RQ), 'reportBug');

					}
				}
			}
		}

		public function fetchRatings()
		{

			$view = $this->jsonView;
			$data = $this->getJSONData();


			if ($data == false) {
				$this->displayEmptyRQ($view, 'login');
			} else {
				if ($this->requireGoodApiToken($data)) {
					if ($this->requireActiveSession($data, 'fetchRatings')) {
						$session_token = $data['session_token'];
						/** @noinspection PhpUnusedLocalVariableInspection */
						$array = array();
						if (isset($data['id'])) {
							$alcohol_id = $data['id'];
							$limit      = isset($data['count']) ? $data['count'] : 120;
							/** @var $ratings_model MainalcModel */
							$ratings_model = $this->loadModel('mainalc');
							$ratings       = $ratings_model->fetchRatings($alcohol_id, $limit);
							if ($ratings != null) {
								$array = array('result' => R_OK, 'data' => $ratings);

							} else {
								$array = array('result' => R_OK);
							}
						} else {
							$array = $this->buildErrorArray(R_BAD_RQ);
						}
						$view->index($array, 'fetchRatings');
					}
				}
			}
		}

		/**
		 * STANDARD
		 * @see ApiController
		 */
		public
		function checkSessionState()
		{
			$view = $this->jsonView;
			$data = $this->getJSONData();

			if ($data == false) {
				$this->displayEmptyRQ($view, 'checkSession');
			} else {
				if ($this->requireGoodApiToken($data)) {
					$result = false;
					if (isset($data['session_token'])) {
						$result = $this->requireActiveSession($data, 'checkSessionState', true);
					}
					if ($result == false) {
						$view->index($this->buildErrorArray(R_VOID_SESSION), 'checkSession');
					} else {
						$view->index(array('result' => R_OK), 'checkSession');
					}
				}
			}
		}

		/**
		 * STANDARD
		 * @see ApiController
		 * data:Alcohol[]-><br>
		 * NAME,<br>
		 * VOLUME,<br>
		 * PRICE,<br>
		 * TYPE,<br>
		 * SUBTYPE,<br>
		 * PERCENT<br>
		 */
		public
		function uploadAlcohols()
		{
			$actionUpload = 'upload';
			$view         = $this->jsonView;
			$data         = $this->getJSONData();
			if ($data == false) {
				$this->displayEmptyRQ($view, $actionUpload);
			} else {
				if ($this->requireGoodApiToken($data)) {
					if ($this->requireActiveSession($data, $actionUpload)) {
						$session_token = $data['session_token'];
						if (isset($data['data'])) {
							$alcohols = $data['data'];

							/** @var $model UseralcModel */
							$model = $this->loadModel('useralc');
							$view->index(
								array(
									'result' => $model->insertSerial(
											$model->JSONToAlcohols($alcohols),
											$this->getUserFromSession($session_token)
										) ? R_OK : R_ERROR
								),
								'upload'
							);
						} else {
							$this->index($this->buildErrorArray(R_BAD_RQ), $actionUpload);
						}
					}
				}
			}
		}

		public
		function index()
		{
			$view = $this->jsonView;
			$view->index($this->buildErrorArray(R_NO_METHOD), 'index');
		}

		/**
		 * STANDARD+
		 * @see ApiController
		 * id<br>
		 * content<br>
		 * rate<br>
		 */
		public
		function rateAlcohol()
		{
			$actionRate = 'rate';
			$view       = $this->jsonView;
			$data       = $this->getJSONData();
			$return     = R_ERROR;
			$error_info = '';
			if ($data == false) {
				$this->displayEmptyRQ($view, $actionRate);
			} else {
				if ($this->requireGoodApiToken($data)) {
					if ($this->requireActiveSession($data, $actionRate)) {
						$session_token = $data['session_token'];

						if (isset($data['id']) && isset($data['content']) && isset($data['rate'])) {
							$id      = $data['id'];
							$content = $data['content'];
							$rate    = $data['rate'];

							/** @var $main_model MainalcModel */
							$main_model = $this->loadModel('mainalc');

							if ($main_model->exists($id)) {
								$user = $this->getUserFromSession($session_token);
								if ($user != false) {
									$result = $main_model->rate($id, $content, $rate, $user);

									if ($result == true) {
										$return = R_OK;
									} else {
										$return = R_ERROR;
									}
								} else {

									$main_model->log->error('User cannot be retrieved, but session is ok');
								}
							} else {
								$error_info = R_NOT_EXISTS;
							}
							//endif EXISTS


						} else {
							$error_info = R_BAD_RQ;
						}
						//endif issets
					}

					if ($return == R_OK) {
						$view->index(array('result' => R_OK), $actionRate);
					} else {
						$view->index($this->buildErrorArray($error_info), $actionRate);
					}
				}
			}
		}

		public
		function flagAlcohol()
		{
			$actionFlag = 'flagAlcohol';
			$view       = $this->jsonView;
			$data       = $this->getJSONData();
			if ($data == false) {
				$this->displayEmptyRQ($view, $actionFlag);
			} else {
				if ($this->requireGoodApiToken($data)) {
					if ($this->requireActiveSession($data, $actionFlag, false)) {
						if (isset($data['id']) && isset($data['content'])) {
							$id      = $data['id'];
							$content = $data['content'];

							/** @var $main_model MainalcModel */
							$main_model = $this->loadModel('mainalc');

							$user   = $this->getUserFromSession($data['session_token']);
							$result = $main_model->flag($id, $content, $user->id);

							if ($result == false) {
								$this->jsonView->index(array('result' => R_ERROR), $actionFlag);
							} else {
								$view->index(array('result' => R_OK), $actionFlag);
							}

						}
					}
				}
			}
		}

		/**
		 * RETURNS<br/>
		 * $alc['n']    = $this->name;
		 * $alc['id']   = $this->id;
		 * $alc['cost'] = $this->price;
		 * $alc['vol']  = $this->volume;
		 * $alc['t']    = $this->type;
		 * $alc['st']   = $this->subtype;
		 * $alc['depo'] = $this->deposit;
		 * $alc['pct']  = $this->percent;
		 */
		public
		function downloadMainDB()
		{
			$actionDownload = 'downloadMainDB';
			$view           = $this->jsonView;
			$data           = $this->getJSONData();

			if ($data == false) {
				$this->displayEmptyRQ($view, $actionDownload);
			} else {
				if ($this->requireGoodApiToken($data)) {
					if ($this->requireActiveSession($data, $actionDownload)) {
						/** @var $mainModel MainalcModel */
						$mainModel   = $this->loadModel('mainalc');
						$allAlcohols = $mainModel->fetchAll();
						$view->index(array('result' => R_OK, 'data' => $allAlcohols), $actionDownload);
					}
				}
			}
		}

		/**
		 * Now it only returns json that is stored in the updates/version.json
		 * @return mixed json array - exact copy of /updates/version.json
		 */
		public
		function checkUpdate()
		{
			if (file_exists(WR . '/updates/version.json')) {
				$file = file_get_contents(WR . '/updates/version.json');
				if ($file != false) {
					$json = json_decode($file, true);

					$this->jsonView->index($json, 'checkUpdate');

					return $json;
				}
			}
			$this->jsonView->index($this->buildErrorArray(R_ERROR), 'checkUpdate');

			return false;
		}

		public
		function downloadUserDB()
		{

			$actionDownload = 'downloadUserDB';
			$view           = $this->jsonView;
			$data           = $this->getJSONData();


			if ($data == false) {
				$this->displayEmptyRQ($view, $actionDownload);
			} else {
				if ($this->requireGoodApiToken($data)) {
					if ($this->requireGoodApiToken($data)) {
						if ($this->requireActiveSession($data, $actionDownload)) {
							/** @var $mainModel MainalcModel */
							$mainModel   = $this->loadModel('useralc');
							$allAlcohols = $mainModel->fetchAll();
							$view->index(array('result' => R_OK, 'data' => $allAlcohols), $actionDownload);
						}
					}
				}
			}
		}
	}