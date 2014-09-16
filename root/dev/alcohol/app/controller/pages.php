<?php
	if (!defined('R')) {
		die('This script cannot be run directly!');
	}
	require R . '/controller/controller.php';

	class PagesController extends Controller
	{

		public function index()
		{
			/** @var $view PagesView */
			$view = $this->loadView('pages');
			if ($this->action != '') {
				$view->index($this->action);
			} else {
				$view->index('hello');
			}
		}
	}