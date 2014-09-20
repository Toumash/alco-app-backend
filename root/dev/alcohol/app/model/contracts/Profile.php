<?php

	class Profile
	{
		public $email;
		public $ratings_count;
		public $weight;
		public $sex;

		public function __construct($email, $count, $weight, $sex)
		{
			$this->email  = $email;
			$this->count  = $count;
			$this->weight = $weight;
			$this->sex    = $sex;
		}

		/**
		 * @return array
		 */
		public function toAPIArray()
		{
			$data = array(
				'email'   => $this->email,
				'ratings' => $this->ratings_count,
				'weight'  => $this->weight,
				'sex'     => $this->sex
			);

			return $data;
		}
	}