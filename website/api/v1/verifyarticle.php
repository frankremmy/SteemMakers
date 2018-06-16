<?php
	require_once('../../src/utils.php');
	require_once('../../src/database.php');
	$database = new Database();

	$responseArray = array('type' => 'danger', 'message' => 'An error occured');

	if(IsAuthorizedReviewer())
	{
		if (!empty($_POST['author']) && !empty($_POST['permlink']))
		{
			try
			{
				$queryStatement = "SELECT * FROM approved_posts INNER JOIN users ON approved_posts.author_id=users.id WHERE name = :author AND permlink = :permlink";
				$queryInputParameters = array(
					'author' => $_POST['author'],
					'permlink' => $_POST['permlink']
				);
				$queryResult = $database->select($queryStatement, $queryInputParameters);

				if(count($queryResult) === 0)
				{
					$responseArray = array('type' => 'success', 'message' => 'Article not found in the database.');
				}
				else
				{
					$responseArray = array('type' => 'danger', 'message' => 'The article already exists in the database.');
				}
			}
			catch (\Exception $e)
			{
				$responseArray = array('type' => 'danger', 'message' => 'The query failed');
			}
		}
		else
		{
			$responseArray = array('type' => 'danger', 'message' => 'Input not valid to verify database');
		}
	}
	else
	{
		$responseArray = array('type' => 'danger', 'message' => 'User not authorized.');
	};

	$encoded = json_encode($responseArray);
	header('Content-Type: application/json');
	echo $encoded;
?>