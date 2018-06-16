<?php
	require_once('database.php'); 

	function IsAuthorizedReviewer()
	{
		if(IsAuthenticatedUser())
		{
			$database = new Database();
				
			$queryStatement = "SELECT enabled FROM reviewers INNER JOIN users ON reviewers.user_id=users.id WHERE name = :username";
			$queryInputParameters = array('username' => GetUsername());
			$queryResult = $database->select( $queryStatement, $queryInputParameters );

			if (count($queryResult) > 0)
			{
				if($queryResult[0]['enabled'] == "1")
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			return false;
		}
	}

	function IsAuthenticatedUser()
	{
		$headerStringValue = getallheaders();

		if (preg_match('/Basic\s+(.*)$/i', $headerStringValue['Authorization'], $auth))
		{
			list($username, $accessToken) = explode(':', base64_decode($auth[1]));
		}
		else
		{
			http_response_code(404);
			var_dump(http_response_code());
			return;
		}

		$json_url = "https://steemconnect.com/api/me?access_token=".$accessToken;
		$content = @file_get_contents($json_url);
		if($content !== FALSE)
		{
			$data = json_decode($content, TRUE);
			if($username = $data['user'])
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	function GetUsername()
	{
		$headerStringValue = getallheaders();

		if (preg_match('/Basic\s+(.*)$/i', $headerStringValue['Authorization'], $auth))
		{
			list($username, $accessToken) = explode(':', base64_decode($auth[1]));
			return $username;
		}
		else
		{
			return '';
		}
	}
?>