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
				echo '<h2>Lista wszystkich flag</h2><br>';

				require_once(ROOT . '/panel/models/Api.class.php');
				$api   = new Api($this->db);
				$flags = $api->fetchAllFlags();
				if ($flags['result'] == R_OK) {

					foreach ($flags['data'] as $flag) {
						echo $flag['name'] . $flag['price'] . $flag['content'];

					}

				} else {
					echo 'error fetching flags';
				}

			}

		}


	}