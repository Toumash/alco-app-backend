<?php
	session_start();
	if (!isset($_SESSION['c'])) {
		$_SESSION['c'] = 1;


	} else {
		$_SESSION['c'] += 1;
	}
	echo $_SESSION['c'];