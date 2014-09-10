<?php
	if (!defined('R')) {
		die ('This script cannot be executed directly');
	}

	require_once R . '/controller/controller.php';

	define('R_LOGIN_PASSWORD', 'login_password');
	define('R_ERROR', 'error');
	define('R_OK', 'ok');
	define('R_EMPTY', 'empty');
	define('R_VOID_SESSION', 'void_session');
	define('R_NO_SESSION_DATA', 'no_session_data');
	define('R_NOT_EXISTS', 'not_exists');

	date_default_timezone_set('Europe/Warsaw');

	/**
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

		public function login()
		{
			$view = $this->jsonView;
			$data = $this->getJSONData();


			if ($data == false) {
				$this->displayEmptyRQ($view, 'login');
			} else {

				$result = false;
				if (isset($data['login']) && isset($data['password']) && isset($data['install_id'])) {

					/** @var $login_model LoginModel */
					$login_model = $this->loadModel('login');
					$result      = $login_model->createSession($data['login'], $data['password'], $data['install_id']);
				}
				if ($result == null || ($result == null && $result == false)) {
					$view->index(array('result' => 'error'), 'login');
				} else {
					$view->index(array('result' => 'ok', 'session_token' => $result->token), 'login');
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
				$this->loadModel('log');
				LogModel::d('API RQ: ' . json_encode($JSON_dump), LogModel::$API_LOG);

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
			$view->index(array('result' => 'emptyRQ'), $action);
		}

		public function checkSessionState()
		{
			$view = $this->jsonView;
			$data = $this->getJSONData();

			if ($data == false) {
				$this->displayEmptyRQ($view, 'checkSession');
			} else {

				$result = false;
				if (isset($data['session_token'])) {
					$result = $this->requireActiveSession($data, 'checkSessionState', true);
				}
				if ($result == false) {
					$view->index(array('result' => R_VOID_SESSION), 'checkSession');
				} else {
					$view->index(array('result' => R_OK), 'checkSession');
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
				/** @var $model LoginModel */
				$model  = $this->loadModel('login');
				$result = $model->isValidSession(new Session($json['session_token']));

				if ($result == false) {
					if ($return == false) {
						$view = $this->jsonView;
						$view->index(array('result' => R_VOID_SESSION), $action);
					}

					return false;
				}

				return true;
			} else {
				if ($return == false) {
					$view = $this->jsonView;
					$view->index(array('result' => R_NO_SESSION_DATA), $action);
				}

				return false;
			}
		}

		/**
		 * <b>REQUIRES</b> IN JSON:<br>
		 * session_token<br>
		 * data:Alcohol[]-><br>
		 * NAME,<br>
		 * VOLUME,<br>
		 * PRICE,<br>
		 * TYPE,<br>
		 * SUBTYPE,<br>
		 * PERCENT<br>
		 */
		public function uploadAlcohols()
		{
			$actionUpload = 'upload';
			$view         = $this->jsonView;
			$data         = $this->getJSONData();
			if ($data == false) {
				$this->displayEmptyRQ($view, $actionUpload);
			} else {
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
						$this->index(array('result' => R_EMPTY), $actionUpload);
					}
				}
			}
		}

		/**
		 * @param $session_token
		 *
		 * @return bool|User
		 */
		private function getUserFromSession($session_token)
		{
			/** @var $login_model LoginModel */
			$login_model = $this->loadModel('login');

			return $login_model->getUserFromSession($session_token);
		}

		public
		function index()
		{
			$view = $this->jsonView;
			$this->displayEmptyRQ($view, '');
		}

		/**
		 * <b>REQUIRES</b> IN JSON:<br>
		 * session_token<br>
		 * id<br>
		 * content<br>
		 * rate<br>
		 */
		public function rateAlcohol()
		{
			$actionRate = 'rate';
			$view       = $this->jsonView;
			$data       = $this->getJSONData();
			$return     = R_ERROR;
			if ($data == false) {
				$this->displayEmptyRQ($view, $actionRate);
			} else {

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
							$return = R_NOT_EXISTS;
						}
						//endif EXISTS


					} else {
						$return = R_EMPTY;
					}
					//endif issets
				}

				$view->index(array('result' => $return), $actionRate);
			}
		}

		public function flagAlcohol()
		{
			$actionFlag = 'flagAlcohol';
			$view       = $this->jsonView;
			$data       = $this->getJSONData();
			if ($data == false) {
				$this->displayEmptyRQ($view, $actionFlag);
			} else {
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

		public function downloadMainDB()
		{
			$actionDownload = 'downloadMainDB';
			$view           = $this->jsonView;
			$data           = $this->getJSONData();

			if ($data == false) {
				$this->displayEmptyRQ($view, $actionDownload);
			} else {

				if ($this->requireActiveSession($data, $actionDownload)) {
					/** @var $mainModel MainalcModel */
					$mainModel   = $this->loadModel('mainalc');
					$allAlcohols = $mainModel->fetchAll();
					$view->index(array('result' => R_OK, 'data' => $allAlcohols), $actionDownload);
				}

			}
		}

		/**
		 * Now it only returns json that is stored in the updates/version.json
		 * //TODO:Upgrage checking for updates
		 * @return mixed json array - exact copy of /updates/version.json
		 */
		public
		function checkUpdate()
		{
			if (file_exists(R . '/updates/version.json')) {
				$file = file_get_contents(R . '/updates/version.json');
				if ($file != false) {
					$json = json_decode($file, true);

					$this->jsonView->index($json, 'checkUpdate');

					return $json;
				}
			}
			$this->jsonView->index(array('result' => R_ERROR), 'checkUpdate');

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
				if ($this->requireActiveSession($data, $actionDownload)) {
					/** @var $mainModel MainalcModel */
					$mainModel   = $this->loadModel('useralc');
					$allAlcohols = $mainModel->fetchAll();
					$view->index(array('result' => R_OK, 'data' => $allAlcohols), $actionDownload);
				}
			}
		}

		/*		/**
				 * @param $login
				 * @param $password
				 *
				 * @see         LoginModel
				 * @return bool||User false if login or password wrong<br>
				 *              User - if everything good
				 * @see         User
				 *
				private function apiFastLogin($login, $password)
				{
					/** @var $model LoginModel *
					$model  = $this->loadModel('login');
					$result = $model->login($login, $password);

					return $result;
				}
			*/
	}