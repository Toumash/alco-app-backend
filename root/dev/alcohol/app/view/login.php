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
					header("Refresh: 2; URL = /alcohol/pages/");
					$content = $this->tpl->draw('c_login_redirect', true);
					$this->tpl->assign('title_main', 'Przekierowywanie...');
					$this->tpl->assign('content', $content);
					$this->tpl->assign('header_off', ''); //disables the header
					$this->tpl->assign('menu_off', ''); //disables the menu
					$this->tpl->draw('default');
				}
			}
		}
		/*		public function  add() {
					$this->render('addCategory');
				}*/
	}