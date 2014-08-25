<?php
	require_once R . '/view/view.php';

	class UserdbView extends View
	{
		public function __construct()
		{
			parent::__construct();
			Rain\Tpl::configure('tpl_dir', R . '/templates/default/');
		}


		/**
		 * @param $alcohols
		 */
		public function index($alcohols)
		{

			$this->tpl->assign('alcohols', $alcohols);
			$this->tpl->assign('lvl', $_SESSION['lvl']);
			$this->tpl->assign('lvl_delete', LVL_MAIN_DELETE);
			$this->tpl->assign('lvl_add', LVL_MAIN_ADD);
			$content = $this->tpl->draw('c_user_view', true);


			$this->tpl->assign('title_main', 'Baza uzytkowników');
			$this->tpl->assign('content', $content);
			$this->tpl->draw('default');
		}


	}