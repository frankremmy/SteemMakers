sc2.init(
{
	app: 'steemmakers.app',
	callbackURL: 'http://localhost/loggedin.php',
	//callbackURL: 'https://www.steemmakers.com/loggedin.php',
	scope: ['login'],
});

function SetProfileInfo()
{
	if ($.cookie("access_token") != null)
	{
		$("#login").hide();
		$("#logout").show();
		sc2.setAccessToken($.cookie("access_token"));
		sc2.me(function (err, result)
		{
			if (!err)
			{
				if(result.account.json_metadata)
				{
					var profileImage = JSON.parse(result.account.json_metadata)['profile']['profile_image'];

					if(profileImage)
					{
						$("#accountPreview").append(`<img src="`+ profileImage + `" height="40" width="40" style="margin-right: 10px; border-radius: 5px;">` + result.account.name);
					}
					else
					{
						$("#accountPreview").append(result.account.name);
					}

					$("#accountPreview").show();
				}
			}
			else
			{
				var loginUrl = sc2.getLoginURL();
				$("#login").attr("href", loginUrl);
				$("#login").show();
				$("#logout").hide();
			}
		});
	}
	else
	{
		var loginUrl = sc2.getLoginURL();
		$("#login").attr("href", loginUrl);
		$("#login").show();
		$("#logout").hide();
	}
}

function Logout()
{
	sc2.revokeToken(function (err, result)
	{
		$.removeCookie("access_token", { path: '/' });
		$.removeCookie("username", { path: '/' });
		$.removeCookie("expires_in", { path: '/' });

		location.reload();
	});
}