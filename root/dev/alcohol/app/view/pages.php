<?php
	require_once R . '/view/view.php';

	class PagesView extends View
	{
		public function __construct()
		{
			parent::__construct();
			/** @noinspection PhpUndefinedClassInspection */
			Rain\Tpl::configure('tpl_dir', R . '/templates/default/');
		}

		public function  index($path)
		{
			$file = R . '/pages/' . $path . '.html.php';
			if (is_file($file)) {
				ob_start();
				/** @noinspection PhpIncludeInspection */
				require $file;
				$content = ob_get_clean();
				ob_end_clean();
				$this->tpl->assign('content', $content);
				$this->tpl->assign('title_main', 'Alcohol App');
				$this->tpl->draw('default');
			} else {
				$this->serveNotFound();
			}
		}

		private function serveNotFound()
		{
			$error = "Przepraszamy, nie znaleziono żądanej strony";
			$this->tpl->assign('error', $error);
			$content = $this->tpl->draw('404', true);

			$this->tpl->assign('content', $content);
			$this->tpl->assign('title_main', 'Nie znaleziono');
			$this->tpl->draw('default');
		}
	}