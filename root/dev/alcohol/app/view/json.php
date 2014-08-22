<?php
	require_once R . '/view/view.php';

	class JsonView extends View
	{
		public function __construct()
		{
			parent::__construct();
			Rain\Tpl::configure('tpl_dir', R . '/templates/default/');
		}

		/**
		 * @param $json   array
		 * @param $action string
		 */
		public function  index($json, $action)
		{
			$json['act'] = $action;
			$this->tpl->assign('json', json_encode($json));
			$this->tpl->draw('json');
		}
	}