<?php

	require_once R . '/view/view.php';

	class LoginView extends View
	{
		public function __construct()
		{
			parent::__construct();
			Rain\Tpl::configure('tpl_dir', R . '/templates/default/');

		}

		public function  index()
		{
			//$cat=$this->loadModel('categories');
			//$this->set('catsData', $cat->getAll());
			$this->tpl->assign('title', 'Logowanie');
			$this->tpl->assign('content', 'heuheuehue');
			$this->tpl->draw('login');
			//$this->render('indexLogin');
		}

		/**
		 * @param $login_result bool
		 */
		public function success($login_result)
		{
			$this->tpl->assign('title', 'Trwa logowanie...');
			if ($login_result == false) {
				$this->tpl->assign('result', 'Błąd');
				$this->tpl->draw('login');
			} else {
				if ($login_result == true) {
					header("Refresh: 3; URL = /alcohol/articles/");
					$this->tpl->draw('loginRedirect');
				}
			}
		}
		/*		public function  add() {
					$this->render('addCategory');
				}*/
	}