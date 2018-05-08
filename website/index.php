<!DOCTYPE html>
<html lang="en">
	<head>
		<?php include("./src/globalhead.php"); ?>

		<script src="https://cdn.jsdelivr.net/remarkable/1.7.1/remarkable.min.js"></script>
		<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>
		<script type="text/javascript" src="js/steem.js?filever=<?php echo filesize('./js/steem.js')?>"></script>
	</head>
	
	<body class="bg-secondary d-flex flex-column">
		<section class="container-fluid flex-grow" style="padding: 0px">
			<app></app>
		</section>
		<footer class="bg-primary">
			<div class="container-fluid py-3">
			<center>
					<span>
						Copyright &copy; SteemMakers <?php echo date("Y"); ?> - A community driven project founded by <a href="http://www.steemit.com/@jefpatat" class="text-white" style="text-decoration: underline;">@jefpatat</a>.
						Our gratitude goes to <a href="#/courtesy" class="text-white" style="text-decoration: underline;">all these nice people</a>.
					</span>
				</center>
			</div>
		</footer>
	</body>
	
	<script src="./dist/build.js?filever=<?php echo filesize('./dist/build.js')?>""></script>
</html>
