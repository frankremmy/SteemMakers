<?php
	// setcookie() defines a cookie to be sent along with the rest of the HTTP headers. Like other headers, 
	// cookies must be sent before any output from your script (this is a protocol restriction). This requires 
	// that you place calls to this function prior to any output, including and tags as well as any whitespace.

	if(!empty($_GET["username"]) && !empty($_GET["access_token"]) && !empty($_GET["expires_in"]))
	{
		$expiresIn = $_GET["expires_in"];
		setcookie("username", $_GET["username"], time()+$expiresIn, "/");
		setcookie("access_token", $_GET["access_token"], time()+$expiresIn, "/");
		setcookie("expires_in", $expiresIn, time()+$expiresIn, "/");
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
		<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>

		<script src="https://cdn.jsdelivr.net/remarkable/1.7.1/remarkable.min.js"></script>

		<script type="text/javascript" src="js/script.js"></script> 
		<script type="text/javascript" src="js/image.js"></script>
		<script type="text/javascript" src="js/body.js"></script>
		<link href="css/bootstrap.min.css" rel="stylesheet"> 
		<link href="css/main.css" rel="stylesheet"> 
	</head>

	<body class="bg-secondary">
		<?php include("navbar.php"); ?>

		<div class="container" style="position:relative; width: 25%;">
			<div class="center-area">
				<div class="centered">
					<center>
					<p>
						You will be redirected automatically.<br/>
						If not <a href="index.php">click here</a>.
					</p>
					</center>
				</div>
			</div>
		</div>

		<script>
			setTimeout(function(){$(location).attr('href', 'index.php');}, 5000);
		</script>

		<?php include("footer.php"); ?>
	</body>
</html>
