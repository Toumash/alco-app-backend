<?php
	require_once R . '/view/view.php';

	class DbchooserView extends View
	{
		public function __construct()
		{
			parent::__construct();
			Rain\Tpl::configure('tpl_dir', R . '/templates/default/');
		}

		/**
		 * Shows the chooser screen
		 */
		public function  index()
		{
			/** @var $user_model UseralcModel */
			$user_model = $this->loadModel('useralc');
			/** @var $main_model MainalcModel */
			$main_model = $this->loadModel('mainalc');

			$this->tpl->assign('count_main', $main_model->getCount());
			$this->tpl->assign('count_user', $user_model->getCount());
			$content = $this->tpl->draw('c_chooser', true);


			$this->tpl->assign('title_main', 'Wybierz bazę danych');
			$this->tpl->assign('content', $content);
			$this->tpl->draw('default');
		}

	}