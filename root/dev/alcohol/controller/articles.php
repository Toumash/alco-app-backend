<?php

	require R . '/controller/controller.php';

	class ArticlesController extends Controller
	{

		public function index()
		{
			$view = $this->loadView('articles');
			$view->index();
		}

		public function one()
		{
			$view = $this->loadView('articles');
			$view->one();
		}

		public function add()
		{
			$view = $this->loadView('articles');
			$view->add();
		}

		public function insert()
		{
			$model = $this->loadModel('articles');
			$model->insert($_POST);
			$this->redirect('?module=articles&action=index');
		}

		public function delete()
		{
			$model = $this->loadModel('articles');
			$model->delete($_GET['id']);
			$this->redirect('?module=articles&action=index');
		}
	}