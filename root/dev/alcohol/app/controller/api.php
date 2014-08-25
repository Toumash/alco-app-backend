<?php
	require_once R . '/controller/controller.php';

	define('R_LOGIN_PASSWORD', 'login_password');
	define('R_ERROR', 'error');
	define('R_OK', 'ok');
	define('R_EMPTY', 'empty');

	class ApiController extends Controller
	{
		public function login()
		{
			$view = $this->getJSONView();
			$data = $this->getJSONData();
			if ($data == false) {
				$this->displayEmptyRQ($view, 'login');
			} else {

				$result = false;
				if (isset($data['login']) && isset($data['password']) && isset($data['install_id'])) {

					/** @var $model LoginModel */
					$model  = $this->loadModel('login');
					$result = $model->loginForApp($data['login'], $data['password'], $data['install_id']);
				}
				if ($result == false) {
					$view->index(array('result' => 'error'), 'login');
				} else {
					if (is_array($result)) {
						$view->index(array('result' => 'ok', 'session_token' => $result['session_token']), 'login');
					}
				}
			}
		}

		/**
		 * @return JSONView
		 */
		private function getJSONView()
		{
			$view = $this->loadView('json');

			return $view;
		}

		/**
		 * Pulls the JSON from the POST payload
		 * @return mixed|bool mixed is a JSON array(php array), returns bool false is theres no data
		 */
		public function getJSONData()
		{
			if (($input = file_get_contents("php://input")) != null) {


				$JSON = json_decode($input, true);

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

		public function upload()
		{

			$view = $this->getJSONView();
			$data = $this->getJSONData();
			if ($data == false) {
				$this->displayEmptyRQ($view, 'upload');
			} else {
				if (isset($data['login']) && isset($data['password'])) {
					$id = $this->fastLogin($data['login'], $data['password']);
					if (is_int($id)) {
						if (isset($data['alcohols'])) {
							$alcohols = $data['alcohols'];

							/** @var $model MainalcModel */
							$model = $this->loadModel('useralc');

							$view->index(
								array('result' => $model->insertSerial($model->JSONToAlcohols($alcohols), $id)),
								'upload'
							);

						} else {
							$this->index(array('result' => R_EMPTY), 'upload');
						}
					} else {
						$view->index(array('result' => R_LOGIN_PASSWORD), 'upload');
					}
				} else {
					$view->index(array('result' => R_LOGIN_PASSWORD), 'upload');
				}
			}
		}

		/**
		 * @param $login
		 * @param $password
		 *
		 * @see LoginModel
		 * @return bool|int|string false if login or password wrong. Int - userID, but string when the activation is not performed yet
		 */
		private function fastLogin($login, $password)
		{
			/** @var $model LoginModel */
			$model  = $this->loadModel('login');
			$result = $model->fastLogin($login, $password);

			return $result;
		}

		public function index()
		{
			$view = $this->getJSONView();
			$this->displayEmptyRQ($view, '');
		}

		private function requireSessionLogged($session_token)
		{
			/** @var $model LoginModel */
			$model  = $this->loadModel('login');
			$result = $model->requireSessionLogged($session_token);

			if ($result == false) {

				/** @var $view JsonView */
				$view = $this->loadView('json');
				$view->index(array('result' => 'void_session'), 'login');
			}
		}
	}