<?php

	class Session
	{
		public $token;

		public function __construct($token)
		{
			$this->token = $token;
		}
	}