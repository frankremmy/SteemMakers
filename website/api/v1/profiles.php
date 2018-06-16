<?php 

require_once('../../src/php/database.php');  


$request_method=$_SERVER["REQUEST_METHOD"];

switch($request_method)
{
	case 'GET':
		if(!empty($_GET["usernames"]))
		{
			$usernamesArray = explode(',', $_GET["usernames"]);
			get_profiles($usernamesArray);
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

function get_profiles($usernames)
{
	$database = new Database();

	$placeholders = rtrim(str_repeat('?, ', count($usernames)), ', ') ;

	$query = "SELECT u.id, u.name, r.enabled as reviewer
	FROM users u
	INNER JOIN reviewers r
		ON u.id=r.user_id
	 WHERE u.name IN ($placeholders)";
	 
	$results = $database->select( $query, $usernames);

	header('Content-Type: application/json');
	echo json_encode($results);
}

?>