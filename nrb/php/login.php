<?php
	session_start();
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	
	if (isset($_GET["user"])) {
		$user = $_GET["user"];
		$passhash = $_GET["passhash"];
	}
	else {
		$user = $_POST["user"];
		$passhash = $_POST["passhash"];
	}
	
	$return['userError']=0;
	$return['passError']=0;
	$return['success']=false;
	
	$con=mysqli_connect("localhost","nrb","nrb2014","nrb",3306);
	$stmt=$con->prepare("SELECT id,name,passhash,salt FROM users WHERE name=?");
	$stmt->bind_param("s",$user);
	$stmt->execute();
	$stmt->bind_result($userr['id'],$userr['name'],$userr['passhash'],$userr['salt']);
	$stmt->fetch();
	$stmt->close();
		
	if (isset($userr['id'])) {
		$hash = md5($passhash.md5($userr['salt']));
		if ($hash == $userr['passhash']) {
			$_SESSION['USER_ID']=$userr['id'];
			$_SESSION['USER_AGENT']=$_SERVER['HTTP_USER_AGENT'];
			$return['success']=true;
			$return['redirect']="/nrb";
		}
		else {
			$return['userError']=1;
			$return['passError']=1;
			$return['userMessage']="<strong>Login credentials invalid.</strong>";
		}
	}
	else {
		$return['userError']=1;
		$return['passError']=1;
		$return['userMessage']="<strong>Login credentials invalid.</strong>";
	}
	$con->close();
	
	echo json_encode($return);
?>