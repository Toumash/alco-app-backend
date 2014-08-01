<?php
	define("P_LVL_FLAGS", 5);

	class FlagsView
	{
		private $db;
		private $permission_lvl;

		public function __construct($db, $permission_lvl)
		{
			$this->db             = $db;
			$this->permission_lvl = $permission_lvl;
		}

		public function index()
		{
			if ($this->permission_lvl < P_LVL_FLAGS) {
				showPermissionError(P_LVL_FLAGS, $this->permission_lvl);

				return;
			} else {
				require_once(ROOT . '/panel/models/Api.class.php');
				$api = new Api($this->db);
				$api->fetchFlags();


			}

		}


	}