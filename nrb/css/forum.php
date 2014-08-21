<?php
	include_once("php/userSession.php");
	
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	if (isset($_GET['sid']) and isset($_GET['fid'])){
		$con = mysqli_connect("localhost","nrb","nrb2014","nrb",3306);
		$stmt=$con->prepare("SELECT `key` FROM sites WHERE id=?");
		$stmt->bind_param("i",$_GET['sid']);
		$stmt->execute();
		$stmt->bind_result($siteKey);
		$stmt->fetch();
		$stmt->close();
		$result = mysqli_query($con,"SELECT * FROM `sites`");

		$frm = mysqli_fetch_array($result); 
		$con->close();
		if (isset($_GET['sid']) and isset($USER['keys'][$siteKey]) and $USER['keys'][$siteKey]){
		}
		else {
			echo "You don't have a site key for this site";
			exit();
			//header( 'Location: index' ) ;
		}
	}
	else {
		echo "Not enough data provided.";
		exit();
		//header( 'Location: index' ) ;
	}
	
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Forum</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/forum.css" rel="stylesheet">
  </head>

  <body>

    <div class="page-header">
      <h1>UnInsight <a href="<?php echo $frm['href']; ?>"><small>RPI</small></a></h1>
    </div>

    <div class="container">
      <div class="list-group">
		<?php
			$con = mysqli_connect("localhost","nrb","nrb2014","nrb",3306);
			$result = mysqli_query($con,"SELECT * FROM `forums` WHERE id=".$_GET['fid']);
			$row = mysqli_fetch_array($result);
		?>
        <a href="forum2.html" class="list-group-item active"><center><?php echo $row['name'];?></center></a>
		<?php 
			
			$result = mysqli_query($con,"SELECT * FROM `threads` WHERE forum=".$_GET['fid']);

			while($row = mysqli_fetch_array($result)): ?>
			<a href="<?php echo "post?sid=".$_GET['sid']."&fid=".$_GET['fid']."&tid=".$row['id']; ?>" class="list-group-item"><?php echo $row['subject'];?><span class="badge"><?php echo $row['posts']; ?> </span></a>
		<?php endwhile;
		$con->close();?>
      </div>
    <button id = "addThread" type="button" class="btn btn-primary btn-lg btn-block" data-toggle="modal" data-target="#newThread"><span class="glyphicon glyphicon-plus"></span></button>


    <!-- Modal -->
    <div class="modal fade" id="newThread" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content" style="padding:10px;">
          <div class="form-group">
            <label for="exampleInputEmail1">Enter Your Question:</label>
            <input type="text" class="form-control" id="question" placeholder="Question">
          </div>
          <div class="modal-footer">
            <button id="submitThread"type="button" class="btn btn-primary" onclick="addThread()">Submit</button>
          </div>
      </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
	function addThread() {
		subject = $("#question")[0].value;
		test = $.post("php/newThread.php?<?php echo "sid=".$_GET['sid']."&fid=".$_GET['fid']; ?>",{"subject":subject, "body": ""},
			function (data) {				
				if (data.success && data.redirect) {
					document.location.href = data.redirect;
				}
			},"json"
		);
		console.log(test);
	}
	
    /*$(document).ready(function(){
      $("#submitThread").click(function(){
        var listItem = '<a href="#" class="list-group-item"><span class="badge">0</span></a>';
        $(".list-group").append(listItem);
      });
    });*/
    </script>
  </body>
</html>
