<?php
	require_once R . '/view/view.php';

	class PagesView extends View
	{
		public function __construct()
		{
			parent::__construct();
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
				/*
					$default = R.'/pages/'.'hello'.'.html.php';
					ob_start();
					require $default;
					$content = ob_get_clean();
					ob_end_clean();
					$this->tpl->assign('content',$content);
					$this->tpl->assign('title_main','Alcohol App');
					$this->tpl->draw('default');*/
			}
		}

		private function serveNotFound()
		{
			$error = "Przepraszamy, nie znaleziono żądanej strony";
			$this->tpl->assign('title_main', '404');
			$this->tpl->assign('error', $error);
			$this->tpl->draw('404');
		}
	}