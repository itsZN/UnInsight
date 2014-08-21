

<?php
	include_once("userSession.php");
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	if (isset($_GET["body"])) {
		$body = $_GET["body"];
	}
	else {
		$body = $_POST["body"];
	}
	
	$return['success']=false;
	
	$siteId = $_GET["sid"];
	$forumId = $_GET["fid"];
	$threadId = $_GET["tid"];
	
	$con=mysqli_connect("localhost","nrb","nrb2014","nrb",3306);
	$stmt=$con->prepare("SELECT `key` FROM sites WHERE id=?");
	$stmt->bind_param("i",$siteId);
	$stmt->execute();
	$stmt->bind_result($siteKey);
	$stmt->fetch();
	$stmt->close();
	if (isset($USER['keys'][$siteKey]) and $USER['keys'][$siteKey] and trim($body)!="") {
		$body = htmlentities($body,ENT_QUOTES | ENT_IGNORE, "UTF-8");
		$body = str_replace("\n","<br>",$body);
		
		$stmt2=$con->prepare("INSERT INTO posts (thread,username,postTime,body) VALUES (?,?,now(),?)");
		$stmt2->bind_param("iss",$threadId,$USER['name'],$body);
		$return['success'] = $stmt2->execute();
		$stmt2->close();
		$return['redirect'] = "post?sid=".$siteId."&fid=".$forumId."&tid=".$threadId;
		
		$stmt3=$con->prepare("UPDATE threads SET posts=posts+1, lastPost=now() WHERE id=?");
		$stmt3->bind_param("i",$threadId);
		$stmt3->execute();
		$stmt3->close();		
	}
	$con->close();
	
	
	
	
	

	echo json_encode($return);
?>