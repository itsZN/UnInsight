<?php
	include_once("userSession.php");
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	if (isset($_GET["subject"])) {
		$subject = $_GET["subject"];
		$body = $_GET["body"];
	}
	else {
		$subject = $_POST["subject"];
		$body = $_POST["body"];
	}
	
	$return['success']=false;
	
	$siteId = $_GET["sid"];
	$forumId = $_GET["fid"];
	
	$con=mysqli_connect("localhost","nrb","nrb2014","nrb",3306);
	$stmt=$con->prepare("SELECT `key` FROM sites WHERE id=?");
	$stmt->bind_param("i",$siteId);
	$stmt->execute();
	$stmt->bind_result($siteKey);
	$stmt->fetch();
	$stmt->close();
	if (isset($USER['keys'][$siteKey]) and $USER['keys'][$siteKey] and trim($subject)!="") {
		$subject = htmlentities($subject,ENT_QUOTES | ENT_IGNORE, "UTF-8");
		$body = htmlentities($body,ENT_QUOTES | ENT_IGNORE, "UTF-8");
		$body = str_replace("\n","<br>",$body);
		
		$stmt2=$con->prepare("INSERT INTO threads (forum,username,subject,posts,lastPost,postTime,body) VALUES (?,?,?,0,now(),now(),?)");
		$stmt2->bind_param("isss",$forumId,$USER['name'],$subject,$body);
		$return['success'] = $stmt2->execute();
		$stmt2->close();
		$return['redirect'] = "forum?sid=".$siteId."&fid=".$forumId;
	}
	$con->close();
	
	
	
	
	

	echo json_encode($return);
?>