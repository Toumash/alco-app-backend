<?php
	if (!defined('R')) {
		die('This script cannot be run directly!');
	}
	require_once R . '/controller/controller.php';

	class LoginController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
		}

		public function index()
		{


			if ($_SESSION['auth'] == true) {
				$this->redirectLocal('/pages/');
			} else {
				/** @var $view LoginView */
				$view = $this->loadView('login');


				$view->index();
			}
		}

		public function login()
		{
			$result = false;
			$model  = null;
			if (isset($_POST['login']) && isset($_POST['password'])) {

				/** @var $model LoginModel */
				$model = $this->loadModel('login');
				global $result;

				/** @var $result User */
				$result = $model->login($_POST['login'], $_POST['password']);
			}
			/** @var $view LoginView */
			$view = $this->loadView('login');
			if ($result == false) {
				$view->success(false);
			} else {
				if ($result == true) {
					$_SESSION['id']    = $result->id;
					$_SESSION['lvl']   = $result->permission_lvl;
					$_SESSION['auth']  = true;
					$_SESSION['login'] = $result->login;

					$view->success(true);
					$model->log->info(
						'User id:' . $result->id . ' \'' . $result->login . '\' lvl:' . $result->permission_lvl . ' logged-in'
					);
				}
			}
		}

		public function logout()
		{ /*
			$model = $this->loadModel('login');
			$model->insert($_POST);*/
			session_destroy();
			unset($_SESSION);
			$this->redirectLocal('/login');
		}
	}