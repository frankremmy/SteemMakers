<?php 

require_once('../../src/php/database.php');  

$request_method=$_SERVER["REQUEST_METHOD"];

switch($request_method)
{
	case 'GET':
		get_keywords();
		break;
	default:
		// Invalid Request Method
		header("HTTP/1.0 405 Method Not Allowed");
		break;
}

class Category
{
	public $category;
	public $keywords = array();
}

function get_keywords()
{
	$database = new Database();

	$query = "SELECT k.name as keyword, c.name as category
	FROM keywords k
	JOIN keywords_categories kc ON kc.keywords_id = k.id
	JOIN categories c ON kc.categories_id = c.id";
	 
	$queryResult = $database->select( $query, []);

	$categories = array();
	foreach ($queryResult as $row)
	{
		$categoryFound = false;
		foreach($categories as $category)
		{
			if($category->category === $row['category'])
			{
				array_push($category->keywords, $row['keyword']);
				$categoryFound = true;
			}
		}
		if(!$categoryFound)
		{
			$newCategory = new Category();
			$newCategory->category = $row['category'];
			array_push($newCategory->keywords, $row['keyword']);
			array_push($categories, $newCategory);
		}
	}

	header('Content-Type: application/json');
	echo json_encode($categories);
}

?>