<?php
	require_once('./src/utils.php');
	require_once('./src/database.php');
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
				if($_POST['discoverer'])
				{
					$discoverer = $_POST['discoverer'];
				}
				else
				{
					$discoverer = $_COOKIE["username"];
				}

				$query = "CALL steemmak_steemmakers.AddApprovedPost('".$_POST['author']."', '".$_POST['permlink']."', '".$_POST['category']."', '".$discoverer."', '".$_COOKIE["username"]."', '".implode(",", $_POST['keywords'])."')";
				$queryResult = $database->select($query);

				if(count($queryResult) === 0)
				{
					$responseArray = array('type' => 'success', 'message' => $okMessage);
				}
				else
				{
					$responseArray = array('type' => 'danger', 'message' => $errorMessage);
				}
			}
			catch (\Exception $e)
			{
				$responseArray = array('type' => 'danger', 'message' => $errorMessage);
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
		}
	};
?>