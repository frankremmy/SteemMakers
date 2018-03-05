<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
		<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>
		<script src="https://steemit.github.io/sc2-angular/sc2.min.js"></script>
		<script type="text/javascript" src="/test/test.js"></script>
	</head>

	<body>
		<div>
			<img src"" id='profileImage' height="40" width="40">
			<p id='accountName'/>
		</div>
		<div>
			<a href="" id='login'>Login</a>
			<a href="test/test.php" id='logout' onclick="Logout();return false;">Logout</a>
			<?php
				require_once('utils.php');

				if(IsAuthorizedReviewer())
				{
					echo '<p>Extra functionality</p>';
				}
				else
				{
					echo '<p>Limited functionality</p>';
				}
			?>
		</div>
		<script>SetProfileInfo();</script>

	</body>

</html>