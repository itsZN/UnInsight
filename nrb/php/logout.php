<?php
	session_start();
	//ini_set('display_errors',1);
	//ini_set('display_startup_errors',1);
	error_reporting(0);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	
	if (isset($_SESSION['USER_ID'])) {
		unset($_SESSION['USER_ID']);
	}
	if (isset($_SESSION['USER_DATA'])) {
		unset($_SESSION['USER_DATA']);
	}
?>