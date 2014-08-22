<?php
	require_once R . '/controller/controller.php';

	class CategoriesController extends Controller
	{
		public function index()
		{
			$view = $this->loadView('categories');
			$view->index();
		}

		public function add()
		{
			$view = $this->loadView('categories');
			$view->add();
		}

		public function insert()
		{
			$model = $this->loadModel('categories');
			$model->insert($_POST);
			$this->redirect('?module=categories&action=index');
		}

		public function delete()
		{
			$model = $this->loadModel('categories');
			$model->delete($_GET['id']);;
			$this->redirect('?module=categories&action=index');
		}
	}