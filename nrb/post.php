<?php
include_once("php/userSession.php");
	
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	if (isset($_GET['sid']) and isset($_GET['fid']) and isset($_GET['tid'])){
		$con = mysqli_connect("localhost","nrb","nrb2014","nrb",3306);
		$stmt=$con->prepare("SELECT `key` FROM sites WHERE id=?");
		$stmt->bind_param("i",$_GET['sid']);
		$stmt->execute();
		$stmt->bind_result($siteKey);
		$stmt->fetch();
		$stmt->close();
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
    <link href="forum.css" rel="stylesheet">
  </head>

  <body>
  	<div class="page-header">
      <h1>UnInsight <a href="/nrb/forum?<?php echo "sid=".$_GET['sid']."&fid=".$_GET['fid']; ?>"><small>RPI</small></a></h1>
    </div>
	<?php //
			$con = mysqli_connect("localhost","nrb","nrb2014","nrb",3306);
			$result = mysqli_query($con,"SELECT * FROM `threads` WHERE id=".$_GET['tid']);
			$row = mysqli_fetch_array($result);
	?>
    <div class="container">
    	<div class="description">
    		<h3><?php echo $row['subject'];?></h3>
    		<h6><?php echo $row['username'];?></h6>
    		<p></p>
    	</div>
    	<ul class="list-group disabled">
		<?php
		$result = mysqli_query($con,"SELECT * FROM `posts` WHERE thread=".$_GET['tid']." ORDER BY postTime ASC");
			
		while($row = mysqli_fetch_array($result)): 
			?>
		  <li class="list-group-item"><?php echo $row['username'].": ".$row['body']; ?></li>
		<?php endwhile;
		$con->close();?>
		</ul>
		<button id = "comment" type="button" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-plus"></span></button>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
	function addPost() {
		body = $("#postBody")[0].value;
		test = $.post("php/newPost.php?<?php echo "sid=".$_GET['sid']."&fid=".$_GET['fid']."&tid=".$_GET['tid']; ?>",{"subject":"", "body": body},
			function (data) {				
				if (data.success && data.redirect) {
					document.location.href = data.redirect;
				}
			},"json"
		);
		console.log(test);
	}
    $(document).ready(function(){
      $("#comment").click(function(){
        var listItem = '<li class="list-group-item"><input type="text" id="postBody" class="form-control" placeholder="Text input"><br><button type="submit" class="btn btn-primary" onclick="addPost()">Submit</button></li>';
        $(".list-group").append(listItem);
      });
    });
    
    </script>
  </body>
</html>