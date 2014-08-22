<?php
	require_once R . '/controller/controller.php';

	class MaindbController extends Controller
	{
		public function index()
		{
			$this->requirePermissionLvl(LVL_VIEW_MAIN);

			/** @var $model MainalcModel */
			$model    = $this->loadModel('mainalc');
			$alcohols = $model->fetchAllWithTypes();

			/** @var $view MaindbView */
			$view = $this->loadView('maindb');
			$view->index($alcohols);
		}


	}