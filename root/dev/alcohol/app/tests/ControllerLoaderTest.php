<?php
	/**
	 * Created by PhpStorm.
	 * User: Tomasz
	 * Date: 22.08.14
	 * Time: 00:27
	 */

	namespace app\tests;


	class ControllerLoaderTest extends \PHPUnit_Framework_TestCase
	{

		public function testLoadController()
		{
			$this->assertTrue(false);
		}

		/**
		 * @expectedException \Exception
		 */
		public function test()
		{
			throw new \Exception();
		}

		public function testYay()
		{
			$this->assertNotFalse(false);
		}

	}
 