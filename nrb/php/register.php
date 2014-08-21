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
	
	if (!preg_match('/^\w{4,8}$/',$user)) {
		$return['userError']=2;
		if (!preg_match('/^.{4,8}$/',$user)) {
			$return['userMessage'] = "Your user name must be between 4 and 8 characters.";
		}
		else {
			$return['userMessage'] = "Your user name must only contain alphanumeric characters and underscores.";
		}
	}
	else {
		$con=mysqli_connect("localhost","nrb","nrb2014","nrb",3306);
		$stmt=$con->prepare("SELECT id,name,passhash,salt FROM users WHERE name=?");
		$stmt->bind_param("s",$user);
		$stmt->execute();
		$stmt->bind_result($userr['id'],$userr['name'],$userr['passhash'],$userr['salt']);
		$stmt->fetch();
		$stmt->close();
		
		if (!isset($userr['id'])) {
			$return['userAvalible']=true;
			$salt = substr(uniqid(mt_rand(), false),0,5);
			$hash = md5($passhash.md5($salt));
			$stmt2=$con->prepare("INSERT INTO users (name,passhash,salt) VALUES (?,?,?)");
			$stmt2->bind_param("sss",$user,$hash,$salt);
			$return['success'] = $stmt2->execute();
			$return['redirect'] = "/nrb";
			$stmt2->close();
			
			$stmt=$con->prepare("SELECT id,name,passhash,salt FROM users WHERE name=?");
			$stmt->bind_param("s",$user);
			$stmt->execute();
			$stmt->bind_result($userr['id'],$userr['name'],$userr['passhash'],$userr['salt']);
			$stmt->fetch();
			$stmt->close();
			//$stmt2=$con->prepare("INSERT INTO keys (userId,key) VALUES (?,?)");
			//$stmt2->bind_param("is",$userr['id'],"test");
			//$stmt2->execute();
			//$stmt2->close();
			$_SESSION['USER_ID']=$userr['id'];
			$_SESSION['USER_AGENT']=$_SERVER['HTTP_USER_AGENT'];
		}
		
		else {
			$return['userAvalible']=false;
			$return['userError']=1;
			$return['userMessage']="Username <strong>".$user."</strong> is not available.";
		}
		$con->close();
	}
	echo json_encode($return);
?>