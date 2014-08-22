<?php
	require_once R . '/controller/controller.php';

	class DbchooserController extends Controller
	{
		public function index()
		{
			/** @var $view DbchooserView */
			$view = $this->loadView('dbchooser');
			$view->index();
		}

		public function main()
		{
			$this->redirectLocal('/maindb/');
		}
		public function user()
		{
			$this->redirectLocal('/userdb/');
		}
	}