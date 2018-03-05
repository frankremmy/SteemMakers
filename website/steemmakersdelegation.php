<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
		<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>

		<script src="https://cdn.jsdelivr.net/remarkable/1.7.1/remarkable.min.js"></script>

		<script type="text/javascript" src="js/script.js"></script> 
		<script type="text/javascript" src="js/image.js"></script>
		<script type="text/javascript" src="js/body.js"></script>
		<script type="text/javascript" src="js/delegation.js"></script>
		<link href="css/bootstrap.min.css" rel="stylesheet"> 
		<link href="css/main.css" rel="stylesheet">
  <script>
 
  $( initDelegation ); 
 
 
  </script>		
	</head>
	<body class="bg-secondary" >
		<?php include("navbar.php"); ?>

		<div class="container" style="width: 75%;">
			<h3 class="my-4">SteemMakers - Delegation</h3>
			<h4 class="my-4">Support us and yourself by delegating Steem Power</h4>
				<p>Help us grow and reward the maker community on the Steem Blockchain.</p>
				<p>The SteemMakers bot is curating good maker related content and needs your help to improve his power. We are a bunch of minnows and small fish. We should stick together and grow into a vast fish swarm. A powerful bot will help each of the fishes in the maker swarm.</p>

				<p>The steem blockchain provides for this purpose the possibility to delegate steem power.</p>


				<h4 class="my-4">Delegation is a Win-Win concept for everyone!</h4>
				<p>Outside of the Steem Blockchain when you want to support a good cause, you have to spend your money, and then it’s gone. You need to trust the receiver that he uses it for what he promised but if he doesn’t, you have no way to get it back.</p>

				<p>Steem Blockchain does change this. A delegation of Steem Power to @SteemMakers means you help the good cause as the SteemMakers bot will use the Power to curate Maker and DIY related content. And you will benefit from being able to read more of this type of posts or get higher votes for yourself if you write DIY posts.</p>
				
				<p>But you don’t lose the control. At any time you can cancel the delegation. It will take only seven days for the steem power to be available for you again. That gives all delegators the freedom to change their mind, and it will motivate the receiver of the delegation not to misuse the power that you provided.</p>

				<h4 class="my-4">How to Delegate to @SteemMakers?</h4>
				<p>Just fill out this form, and it will forward you to SteemConnect to execute a delegation! You will need your Active-key.</p>
				
			
				
				<form action="https://v2.steemconnect.com/sign/delegateVestingShares" id="person">
					Your Steem ID (without the @)<br /><input name="delegator" type="text"> <br />
					Steem Power to delegate<br /><input type="text" name="sp">
					<input name="delegatee" type="hidden" value="steemmakers">
					<input name="vesting_shares" type="hidden" value="0"><br />
					<input type="submit" value="Submit">
				</form>
				
				<h4 class="my-4">Who delegated to SteemMakers already?</h4>
				<table id="result" width="200px">
				<tr><td>Delegator</td><td>&nbsp;&nbsp;&nbsp;</td><td>SP</td></tr>
			    </table> 
				
				

		</div>
		<?php include("footer.php"); ?>
	</body>
</html>
