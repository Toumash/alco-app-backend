<?php

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

		public function page()
		{
			/** @var $view PagesView */
			$view = $this->loadView('pages');
			if ($this->request[0] != '') {
				$view->index($this->request[0]);
			} else {
				$view->index('hello');
			}
		}
	}