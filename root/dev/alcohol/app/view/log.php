<?php

	require_once R . '/view/view.php';

	class LogView extends View
	{

		public function __construct()
		{
			parent::__construct();
			Rain\Tpl::configure('tpl_dir', R . '/templates/default/');

		}

		public function  index()
		{
			/** @var $log_model LogModel */
			$log_model = $this->loadModel('log');

			$this->tpl->assign('log', nl2br($log_model->read()));
			$log_model->cut();
			$content = $this->tpl->draw('c_log', true);


			$this->tpl->assign('title_main', 'Log');
			$this->tpl->assign('content', $content);
			$this->tpl->draw('default');
		}
	}