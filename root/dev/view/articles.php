<?php
	require_once R . '/view/view.php';

	class ArticlesView extends View
	{
		public function  index()
		{
			$art = $this->loadModel('articles');
			$this->set('articles', $art->getAll());
			$this->render('indexArticle');
		}

		public function  one()
		{
			$art = $this->loadModel('articles');
			$this->set('articles', $art->getOne($_GET['id']));
			$this->render('oneArticle');
		}

		public function add()
		{
			$cat = $this->loadModel('categories');
			$this->set('catsData', $cat->getAll());
			$this->render('addArticle');
		}
	}