<?php

	class Alcohol
	{
		public $name;
		public $price;
		public $type;
		public $subtype;
		public $volume;
		public $percent;
		public $deposit;
		public $userID;

		/**
		 * @param $name
		 * @param $price
		 * @param $type
		 * @param $subtype
		 * @param $volume
		 * @param $percent
		 * @param $deposit
		 *
		 * @internal param $userID
		 */
		public function __construct($name,$price,$type,$subtype,$volume,$percent,$deposit){
			$this->name = $name;
			$this->price = $price;
			$this->type = $type;
			$this->subtype = $subtype;
			$this->volume = $volume;
			$this->percent = $percent;
			$this->deposit = $deposit;
		}

		public function setUserID($userID){
			$this->userID = $userID;
		}
	}