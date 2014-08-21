<?php
	session_start();
	//ini_set('display_errors',1);
	//ini_set('display_startup_errors',1);
	error_reporting(-1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	
	if (isset($_SESSION['USER_ID']) and $_SESSION['USER_ID']>=0) {
		if (isset($_SESSION['USER_AGENT']) and $_SESSION['USER_AGENT']==$_SERVER['HTTP_USER_AGENT']) {
			$id = $_SESSION['USER_ID'];
			$USER['id'] = $id;
			if (!isset($_SESSION['USER_DATA'])) {
				$_SESSION['USER_DATA']=array();
			}
			if (isset($_SESSION['USER_DATA']) and isset($_SESSION['USER_DATA']['name']) and !(isset($_SESSION['REFRESH_USER']) and $_SESSION['REFRESH_USER'])) {
				$USER['name'] = $_SESSION['USER_DATA']['name'];
			}
			else {
				$con=mysqli_connect("localhost","nrb","nrb2014","nrb",3306);
				$result = mysqli_query($con,"SELECT * FROM `users` WHERE id=".$id);
				$USER['name']="";
				while($row = mysqli_fetch_array($result)) {
					$USER['name'] = $row['name'];
				}
				$_SESSION['USER_DATA']['name']=$USER['name'];
			}
			if (isset($_SESSION['USER_DATA']) and isset($_SESSION['USER_DATA']['keys']) and !(isset($_SESSION['REFRESH_USER']) and $_SESSION['REFRESH_USER'])) {
				$USER['keys'] = $_SESSION['USER_DATA']['keys'];
			}
			else {
				$con=mysqli_connect("localhost","nrb","nrb2014","nrb",3306);
				$result = mysqli_query($con,"SELECT * FROM `keys` WHERE userId=".$id);
				$USER['keys']=array();
				while($row = mysqli_fetch_array($result)) {
					$USER['keys'][$row['key']] = true;
				}
				$_SESSION['USER_DATA']['keys']=$USER['keys'];
			}
			if (isset($_SESSION['USER_DATA']) and isset($_SESSION['USER_DATA']['admin']) and !(isset($_SESSION['REFRESH_USER']) and $_SESSION['REFRESH_USER'])) {
				$USER['admin'] = $_SESSION['USER_DATA']['admin'];
			}
			else {
				if (!isset($con)) {
					$con=mysqli_connect("localhost","nrb","nrb2014","nrb",3306);
				}
				$result = mysqli_query($con,"SELECT * FROM sites WHERE admin=".$id);
				$USER['admin']=array();
				while($row = mysqli_fetch_array($result)) {
					$USER['admin'][$row['key']] = true;
				}
				$_SESSION['USER_DATA']['admin'] = $USER['admin'];
			}
			$_SESSION['REFRESH_USER']=false;
			if (isset($con)) {
				$con->close();
			}
		}
		else {
			include('php/logout.php');
			$USER['id']=0;
			$USER['keys']= array();
			$USER['admin']= array();
		}
	}
	else {
		$USER['id']=0;
		$USER['keys']= array();
		$USER['admin']= array();
	}
	
	if (isset($_GET['debug'])) {
		var_dump($USER);
	}

?>