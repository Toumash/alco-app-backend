<?php

	require_once R . '/view/view.php';

	class PermwarningView extends View
	{
		public function __construct()
		{
			parent::__construct();
			Rain\Tpl::configure('tpl_dir', R . '/templates/default/');

		}

		public function  index($required, $lvl)
		{
			$this->tpl->assign('title', 'Błąd Autoryzacji');
			$this->tpl->assign(
				'content',
				'<h1>Brak dostępu</h1><p>Twój poziom uprawnień (' . $lvl . ') jest niewystarczający, aby oglądać tę stronę. WYMAGANY: ' . $required . 'lvl</p>'
			);
			$this->tpl->draw('default');
		}

	}