<?php 

require_once('../../src/database.php'); 
require_once('paginator.php'); 


$request_method=$_SERVER["REQUEST_METHOD"];

switch($request_method)
{
	case 'GET':
		if(!empty($_GET["page"]))
		{
			$page=intval($_GET["page"]);
			get_articles($page);
		}
		else
		{
			// Invalid Request Method
			header("HTTP/1.0 405 Method Not Allowed");
		}
		break;
	default:
		// Invalid Request Method
		header("HTTP/1.0 405 Method Not Allowed");
		break;
}

function get_articles($page=0)
{
	global $connection;

	$database = new Database();
				
	$query = "SELECT name, p.permlink FROM (SELECT * FROM approved_posts) p INNER JOIN users u ON p.author_id = u.id ORDER BY p.reviewed_on DESC";

	$limit = ( isset( $_GET['limit'] ) ) ? $_GET['limit'] : 10;
	$page = ( isset( $_GET['page'] ) ) ? $_GET['page'] : 1;

	$paginator = new Paginator( $database, $query );

	if(is_numeric($limit) && is_numeric($page))
	{
		$results = $paginator->getData( (int)$limit, (int)$page );
	}
	else
	{
		header("HTTP/1.0 400 Bad request");
	}

	header('Content-Type: application/json');
	echo json_encode($results);
}

?>