<?php

	require R . '/controller/controller.php';

	class PagesController extends Controller
	{

		public function index()
		{
			$view = $this->loadView('pages');
			if ($this->request[0] != '') {
				$view->index($this->request[0]);
			} else {
				$view->index('hello');
			}
		}

		public function page()
		{

		}
	}