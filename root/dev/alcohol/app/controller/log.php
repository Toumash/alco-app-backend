<?php
	if (!defined('R')) {
		die('This script cannot be run directly!');
	}
	require_once R . '/controller/controller.php';

	class LogController extends Controller
	{
		public function index()
		{
			$this->requirePermissionLvl(LVL_VIEW_LOG);

			/** @var $view LogView */
			$view = $this->loadView('log');
			$view->index();
		}
	}