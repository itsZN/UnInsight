<?php
	if (isset($_GET['logout']) and $_GET['logout']=="true") {
		include_once("php/logout.php");
	}
	
	include_once("php/userSession.php");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Uninsight</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/rpi2018.css" rel="stylesheet">
  </head>
  <body>
      <div class="header">
      <h1>UnInsight</h1>
          <ul class="nav nav-pills" role="tablist">
			<?php if ($USER['id']==0):?>
				<li id = "logIn" class="active"><a href="#">Log In</a></li>
				<li id = "reg" class="active"><a href="#">Register</a></li>
			<?php else:?>
				<li class="active" style="float: right;"><a href="?logout=true">Log Out</a></li>
				<li> Logged in as  <?php echo $USER['name']; ?> </li>
			<?php endif;?>
		  </ul>
      </div>

      <div class="container">
          <div id="popUp" style="display:none">
                <div class="form-group">
				
                  <label for="loginUser"></label>
				  <div class="alert alert-danger" role="alert" id="messageUserError" style="display: none"></div>
                  <input type="text" class="form-control" id="loginUser" placeholder="Username">
                </div>
                <div class="form-group">
                  <label for="loginPass"></label>
                  <input type="password" class="form-control" id="loginPass" placeholder="Password">
                </div>
                <center><button type="submit" class="btn btn-default" onclick="sendLogin()">Submit</button></center>
          </div>
		  <div id="popUp2" style="display:none">
                <div class="form-group">
				
                  <label for="regUser"></label>
				  <div class="alert alert-danger" role="alert" id="messageUserError2" style="display: none"></div>
                  <input type="text" class="form-control" id="regUser" placeholder="Username">
                </div>
                <div class="form-group">
                  <label for="regPass"></label>
                  <div class="alert alert-danger" role="alert" id="messagePassError1" style="display: none"></div>
                  <input type="password" class="form-control" id="regPass" placeholder="Password">
                </div>
				<div class="form-group">
                  <label for="regPass2"></label>
                  <div class="alert alert-danger" role="alert" id="messagePassError2" style="display: none"></div>
                  <input type="password" class="form-control" id="regPass2" placeholder="Retype Password">
                </div>
				<center><button type="submit" class="btn btn-default" onclick="sendReg()">Submit</button></center>
          </div>
		  <?php if ($USER['id']>0):?>
			<div id="keys">
					<div class="form-group">
						<label class="sr-only" for="siteKey">Site Key</label>
						<input type="text" class="form-control" id="siteKey" placeholder="Site Key">
					</div>
					<button type="submit" class="btn btn-default" onclick="sendKey()">Add</button>
				<?php  
					$con=mysqli_connect("localhost","nrb","nrb2014","nrb",3306);
					$result = mysqli_query($con,"SELECT * FROM `sites`");

					while($row = mysqli_fetch_array($result)): 
						if (isset($USER['keys'][$row['key']]) and $USER['keys'][$row['key']]):?>
						<h4><a href = <?php echo $row['href'].">".$row['name'];?></a></h4>
					<?php endif;
					endwhile;
					$con->close()?>
			</div>
		  <?php endif; ?>
      </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>

	<script src="js/md5.js"></script>
	
    <script>
    function sendLogin() {
		user = $("#loginUser")[0].value;
		pass = $("#loginPass")[0].value;
		passHash = calcMD5(pass);
		test = $.post("php/login.php",{"user":user, "passhash": passHash},
			function (data) {
				if (data.userError>0 && data.userMessage) {
					$("#messageUserError")[0].innerHTML = data.userMessage;
					$("#messageUserError").show();
				}
				else {
					$("#messageUserError").hide();
				}
				
				if (data.success && data.redirect) {
					document.location.href = data.redirect;
				}
			},"json"
		);
		console.log(test);
	}
	
	function sendKey() {
		key = $("#siteKey")[0].value;
		test = $.post("php/newKey.php",{"key":key},
			function (data) {				
				if (data.success && data.redirect) {
					document.location.href = data.redirect;
				}
			},"json"
		);
		console.log(test);
	}
	
	function sendReg() {
		user = $("#regUser")[0].value;
		pass = $("#regPass")[0].value;
		pass2 = $("#regPass2")[0].value;
		passHash = calcMD5(pass);
		if (pass==pass2) {
			$("#messagePassError2").hide();
			test = $.post("php/register.php",{"user":user, "passhash": passHash},
				function (data) {
					if (data.userError>0 && data.userMessage) {
						$("#messageUserError2")[0].innerHTML = data.userMessage;
						$("#messageUserError2").show();
					}
					else {
						$("#messageUserError2").hide();
					}
					if (data.passError>0 && data.passMessage) {
						$("#messagePassError")[0].innerHTML = data.passMessage;
						$("#messagePassError").show();
					}
					else {
						$("#messagePassError").hide();
					}
					
					if (data.success && data.redirect) {
						document.location.href = data.redirect;
					}
				},"json"
			);
			console.log(test);
		}
		else {
			$("#messagePassError2")[0].innerHTML = "Your passwords do not match";
			$("#messagePassError2").show();
		}
	}
	
	$(document).ready(function(){

      $("body").click(function(event){

        if ((!$(event.target).closest('#popUp').length) && (!$(event.target).closest('#logIn').length)){
          console.log("hi");
          if($('#popUp').is(":visible")){
            $('#popUp').hide();
          }
        }
		
		if ((!$(event.target).closest('#popUp2').length) && (!$(event.target).closest('#reg').length)){
          console.log("hi");
          if($('#popUp2').is(":visible")){
            $('#popUp2').hide();
          }
        }
        
      });

      $("#logIn").click(function(){
        $("#popUp").show();
      });
	  
	  $("#reg").click(function(){
        $("#popUp2").show();
      });

    });
	
	
    </script>
<span style="float: right;">
  <a href="https://github.com/itsZN/UnInsight">Source On GitHub </a><iframe style="padding:0px;" src="http://ghbtns.com/github-btn.html?user=itsZN&repo=UnInsight&type=fork"
  allowtransparency="true" frameborder="0" scrolling="0" width="62" height="20"></iframe>
  </span>
  </body>
</html>