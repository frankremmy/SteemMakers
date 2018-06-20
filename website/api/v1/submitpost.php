<?php
	require_once('../../src/php/utils.php');
	require_once('../../src/php/database.php');
	$database = new Database();

	$okMessage = 'Blog post submitted, thank you!';
	$errorMessage = 'Oops, something went wrong.';

	if(IsAuthorizedReviewer())
	{
		if (!empty($_POST['author']) &&
			!empty($_POST['permlink']) &&
			!empty($_POST['category']) &&
			!empty($_POST['keywords']))
		{
			try
			{
				$queryStatement = "CALL steemmak_steemmakers.AddApprovedPost(:author, :permlink, :category, :discoverer, :user, :keywords)";
				$queryInputParameters = array(
					'author' => $_POST['author'],
					'permlink' => $_POST['permlink'],
					'category' => $_POST['category'],
					'discoverer' => GetUsername(),
					'user' => GetUsername(),
					'keywords' => $_POST['keywords'],
				);
				$queryResult = $database->select($queryStatement, $queryInputParameters);

				if(count($queryResult) === 0)
				{
					$responseArray = array('type' => 'success', 'message' => $okMessage);
				}
				else
				{
					$responseArray = array('type' => 'danger', 'message' => $errorMessage);
				}
			}
			catch (Exception $e)
			{
				$responseArray = array('type' => 'danger', 'message' => $errorMessage);
			}

			$encoded = json_encode($responseArray);
			header('Content-Type: application/json');
			echo $encoded;
		}
	};
?>