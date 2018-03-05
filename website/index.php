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

		<script type="text/javascript" src="js/script.js?t=1234"></script> 
		<script type="text/javascript" src="js/image.js"></script>
		<script type="text/javascript" src="js/body.js"></script>
		<script src="https://steemit.github.io/sc2-angular/sc2.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
		<script type="text/javascript" src="js/sc2.min.js"></script>
		<script type="text/javascript" src="js/sc2.js"></script>
		<link href="css/bootstrap.min.css" rel="stylesheet"> 
		<link href="css/main.css" rel="stylesheet"> 
	</head>
	<body class="bg-secondary">
		<?php include("navbar.php"); ?>
		<div class="container">
			<?php
				require_once('./src/database.php'); 
				require_once('./src/paginator.php'); 
				$database = new Database();
				
				$query = "SELECT name, p.permlink FROM (SELECT * FROM approved_posts) p INNER JOIN users u ON p.author_id = u.id ORDER BY p.reviewed_on DESC";

				$limit = ( isset( $_GET['limit'] ) ) ? $_GET['limit'] : 5;
				$page = ( isset( $_GET['page'] ) ) ? $_GET['page'] : 1;
				$links = 3;

				$paginator = new Paginator( $database, $query );
				$results = $paginator->getData( $limit, $page );
				for ($i = 0; $i < count($results->data); $i++)
				{
					echo '<div class="row" id="article',$i,'">';
					echo '<div class="spinner" id="spinner',$i,'" style="float: none; margin: 0 auto;"></div>';
					echo '<script>';
					echo '  storyPreview(',$i,', \'',$results->data[$i]['name'],'\', \'',$results->data[$i]['permlink'],'\');';
					echo '</script>';
					echo '</div>';
				}
			?>
		</div>
		<div class="text-center">
			<div class="d-inline-block">
				<?php
					echo $paginator->createLinks( $links );
				?>
			</div>
		</div>
		<?php include("footer.php"); ?>
	</body>
</html>
