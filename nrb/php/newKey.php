<?php
	include_once("userSession.php");
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	if (isset($_GET["key"])) {
		$key = $_GET["key"];
	}
	else {
		$key = $_POST["key"];
	}
	
	$return['success']=false;
	
	$con=mysqli_connect("localhost","nrb","nrb2014","nrb",3306);
	$result = mysqli_query($con,"SELECT * FROM `sites`");

	while($row = mysqli_fetch_array($result)) {
		if ($row['key']==$key) {
			$stmt2=$con->prepare("INSERT INTO `keys` (userId,`key`) VALUES (?,?)");
			$stmt2->bind_param("is",$USER['id'],$key);
			$stmt2->execute();
			$stmt2->close();
			$return['success']=true;
			$return['redirect']="/nrb";
			$_SESSION['REFRESH_USER']=true;
		}
	}
	$con->close();
	echo json_encode($return);
?>