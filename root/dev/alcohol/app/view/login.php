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

			$this->tpl->assign('title', 'Logowanie');
			$content = $this->tpl->draw('c_login', true);
			$this->tpl->assign('title_main', 'Logowanie');
			$this->tpl->assign('content', $content);
			$this->tpl->draw('default');
		}

		/**
		 * @param $login_result bool
		 */
		public function success($login_result)
		{
			if ($login_result == false) {
				$this->tpl->assign('result', 'Błąd');
				$content = $this->tpl->draw('c_login', true);

				$this->tpl->assign('title_main', 'Logowanie');
				$this->tpl->assign('content', $content);
				$this->tpl->draw('default');
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