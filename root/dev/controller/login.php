<?php
	require_once R . '/controller/controller.php';

	class LoginController extends Controller
	{
		public function index()
		{
			$view = $this->loadView('login');
			$view->index();
		}

		public function login()
		{

			if (isset($_GET['logout'])) {
				session_destroy();
				unset($_SESSION);
			}

			if (!isset($_SESSION['auth'])) {
				$_SESSION['auth']       = false;
				$_SESSION['permission'] = 1;
			}
			$result = null;

			if (isset($_POST['login']) && isset($_POST['password'])) {
				$model = $this->loadModel('login');
				global $result;
				$result = $model->login($_POST['login'], $_POST['password']);
			}
			$view = $this->loadView('login');
			if ($result == false) {
				$view->login(false);
			} else {
				$_SESSION['id']         = $result['ID'];
				$_SESSION['permission'] = $result['PERMISSIONS'];
				$_SESSION['auth']       = true;
				$view->login(true);
			}


		}

		public function logout()
		{ /*
			$model = $this->loadModel('login');
			$model->insert($_POST);*/
			$this->redirect('login/logout/');
		}

		public function register()
		{

		}
	}