<?php
	require_once R . '/controller/controller.php';

	class ApiController extends Controller
	{
		public function index()
		{
			$view = $this->loadView('login');
			if ($_SESSION['auth'] == true) {
				$this->redirectLocal('/a');
			} else {
				$view->index();
			}
		}

		public function login()
		{
			$view = $this->getJSONView();
			$data = $this->getJSONData();
			if ($data == false) {
				$view->index(array('result' => 'emptyRQ'), 'login');
			} else {

				$result = false;
				if (isset($data['login']) && isset($data['password'])) {
					$model  = $this->loadModel('login');
					$result = $model->login($data['login'], $data['password']);
				}
				if ($result == false) {
					$view->index(array('result' => 'error'), 'login');
				} else {
					if ($result == true) {
						$view->index(array('result' => 'ok'), 'login');
					}
				}
			}
		}

		private function getJSONView()
		{
			$view = $this->loadView('json');

			return $view;
		}

		/**
		 * @param $view View
		 * @param $action string
		 */
		private function displayEmptyRQ($view, $action)
		{
			$view->index(array('result' => 'emptyRQ'), $action);
		}
		public function getJSONData()
		{
			if (($input = file_get_contents("php://input")) != null) {
				$this->loadModel('log');

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
				LogModel::d('API RQ: ' . json_encode($JSON_dump), LogModel::$API_LOG);

				return $JSON;
			} else {
				return false;
			}
		}

		public function upload()
		{

			$view = $this->getJSONView();
			$data = $this->getJSONData();
			if ($data == false) {

			}


		}
	}