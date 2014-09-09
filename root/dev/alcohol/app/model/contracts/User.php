<?php

	/**
	 * Represents single user from the database ONLY.
	 * Class User
	 */
	class User
	{
		/**
		 * @var int
		 */
		public $id;
		/**
		 * @var string 36 chars long
		 */
		public $activation;
		/**
		 * @var int
		 */
		public $permission_lvl;
		/**
		 * @var string
		 */
		public $login;

		public function __construct($id, $login, $act, $permlvl)
		{
			$this->activation     = $act;
			$this->login          = $login;
			$this->permission_lvl = $permlvl;
			$this->id             = $id;
			$this->login          = $login;
		}

		/**
		 * check if the user has activated his account, by entering mail inbox
		 * @return bool
		 */
		public function isActivated()
		{
			return strlen($this->activation) > 2 ? false : true;
		}
	}