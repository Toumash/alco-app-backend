<?php
	require_once R . '/controller/controller.php';

	class UserdbController extends Controller
	{
		private $deleted;

		public function index()
		{
			$this->requirePermissionLvl(LVL_VIEW_MAIN);

			/** @var $model MainalcModel */
			$model    = $this->loadModel('useralc');
			$alcohols = $model->fetchAllWithTypes();

			/** @var $view UserdbView */
			$view = $this->loadView('userdb');
			$view->index($alcohols);
		}

		public function delete()
		{
			$this->requirePermissionLvl(LVL_MAIN_DELETE);
			print_r($this->request);
			if (count($this->request) > 0) {
				$id = $this->request[0];

				/** @var $model MainalcModel */
				$model = $this->loadModel('useralc');
				$model->delete($id);


			} else {
				$this->redirectLocal('/userdb');
			}
		}
	}