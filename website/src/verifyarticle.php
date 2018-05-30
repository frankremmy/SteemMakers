<?php
	require_once('utils.php');
	require_once('database.php');
	$database = new Database();

	if(IsAuthorizedReviewer())
	{
		$responseArray = array('type' => 'danger', 'message' => 'An error occured');

		if (!empty($_POST['author']) && !empty($_POST['permlink']))
		{
			try
			{
				$query = "SELECT * FROM approved_posts INNER JOIN users ON approved_posts.author_id=users.id WHERE name = '".$_POST['author']."' AND permlink = '".$_POST['permlink']."'";
				$queryResult = $database->select($query);

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
				$responseArray = array('type' => 'danger', 'message' => 'An error occured');
			}
		}
		else
		{
			$responseArray = array('type' => 'danger', 'message' => 'Input not valid to verify database');
		}

		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$encoded = json_encode($responseArray);

			header('Content-Type: application/json');

			echo $encoded;
		}
		else
		{
			echo $responseArray['message'];
		}
	};
?>