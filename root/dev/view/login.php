<?php

	require_once R . '/view/view.php';

	class LoginView extends View
	{
		public function __construct()
		{
			parent::__construct();
			Rain\Tpl::configure('tpl_dir', 'templates/default/');

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
		public function login($login_result)
		{

			$this->tpl->assign('title', 'Trwa logowanie...');
			$this->tpl->assign('result', $login_result ? 'OK, Zalogowano pomyślnie' : 'Błąd');
			$this->tpl->draw('login');
		}
		/*		public function  add() {
					$this->render('addCategory');
				}*/
	}