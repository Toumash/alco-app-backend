<?php

	class Alcohol
	{
		/**
		 * @var string
		 */
		public $name;
		/**
		 * @var float
		 */
		public $price;
		/**
		 * @var int
		 */
		public $type;
		/**
		 * @var string
		 */
		public $typeString;
		/**
		 * @var int
		 */
		public $subtype;
		/**
		 * @var string
		 */
		public $subtypeString;
		/**
		 * @var int
		 */
		public $volume;
		/**
		 * @var float
		 */
		public $percent;
		/**
		 * @var int
		 */
		public $deposit;
		/**
		 * @var int
		 */
		public $userID;

		/**
		 * @var int id in the database
		 */
		public $id;

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
		public function __construct($name, $price, $type, $subtype, $volume, $percent, $deposit)
		{
			$this->name    = $name;
			$this->price   = $price;
			$this->type    = $type;
			$this->subtype = $subtype;
			$this->volume  = $volume;
			$this->percent = $percent;
			$this->deposit = $deposit;
		}

		/**
		 * @param int $id
		 */
		public function setId($id)
		{
			$this->id = $id;
		}

		/**
		 * @deprecated
		 *
		 * @param $userID
		 */
		public function setUserID($userID)
		{
			$this->userID = $userID;
		}

		/**
		 * @param string $subtypeString
		 */
		public function setSubtypeString($subtypeString)
		{
			$this->subtypeString = $subtypeString;
		}

		/**
		 * @param string $typeString
		 */
		public function setTypeString($typeString)
		{
			$this->typeString = $typeString;
		}
	}