<?php

	class ImageList
	{

		public function index()
		{
			$dirf = './images/demo';
			$dir  = scandir($dirf);
			foreach ($dir as $file) {
				if ($file != '.' && $file != '..' && $file[1] != 'h' && $file[2] != 't') {
					echo '<img  src="http://dev.code-sharks.pl/images/demo/' . $file . '" alt="photo"/>';
				}
			}
		}
	}