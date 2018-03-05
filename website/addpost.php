<?php 
	require_once('./src/utils.php');
	if(!IsAuthorizedReviewer())
	{
		header('Location: /index.php');
		die();
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
		<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>

		<script src="https://cdn.jsdelivr.net/remarkable/1.7.1/remarkable.min.js"></script>

		<script type="text/javascript" src="js/script.js"></script> 
		<script type="text/javascript" src="js/image.js"></script>
		<script type="text/javascript" src="js/body.js"></script>
		<script type="text/javascript" src="js/sc2.min.js"></script>
		<script type="text/javascript" src="js/sc2.js"></script>
		<link href="css/bootstrap.min.css" rel="stylesheet"> 
		<link href="css/main.css" rel="stylesheet">
		<script type="text/javascript">

		</script>
	</head>
	<body class="bg-secondary">
		<?php include("navbar.php"); ?>
		
		<div class="container" style="width: 75%;">
			<form id="addArticle" action="submitpost.php">
				<div class="form-group">
					<label class="control-label">Article link</label>
					<div class="input-group">
						<input type="text" class="form-control" placeholder="Paste your link here" id="linkBox">
						<span class="input-group-btn">
							<button class="btn btn-primary" type="button" id="ParseLinkButton">Parse link</button>
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label">Author</label>
					<input type="text" class="form-control" placeholder="Author" id="authorBox" name="author">
				</div>
				<div class="form-group">
					<label class="control-label">Permlink</label>
					<input type="text" class="form-control" placeholder="Permlink" id="permLinkBox" name="permlink">
				</div>
				<div class="form-group">
					<label class="control-label">Discoverer</label>
					<div class="input-group">
						<div class="input-group-addon">
							<span class="input-group-text">@</span>
						</div>
						<input type="text" class="form-control" placeholder="Discoverer" id="discovererBox" name="discoverer">
					</div>
				</div>
				<div class="form-group">
					<button type="button" class="btn btn-primary" id="ValidateButton">Validate</button>
				</div>
				<div class="form-group">
					<ul id="validation-messages"></ul>
					<div id="article1">
					</div>
				</div>
				<div class="form-group" id="categoryselector">
					<label class="control-label">Category</label><br>
					<label class="radio-inline"><input type="radio" name="category" value="makers" id="MakersRadio" checked>Makers</label>
					<label class="radio-inline"><input type="radio" name="category" value="diy" id="DIYRadio">DIY</label>
				</div>
				<div id="keywordCheckBoxes">
					<label class="control-label">Keywords</label>
				<?php
					require_once('./src/database.php');

					$database = new Database();
						
					$query = "SELECT name FROM keywords_categories INNER JOIN keywords ON keywords_categories.keywords_id=keywords.id WHERE categories_id=1";
					$queryResult = $database->select($query);
					echo '<div class="form-group" id="makerscheckboxes">';
					for ($i = 0; $i < count($queryResult); $i++)
					{
						echo
						'<div class="form-check">
							<label class="form-check-label" for="defaultCheck1"><input class="form-check-input" type="checkbox" name="keywords[]" value="',$queryResult[$i]['name'],'" id="defaultCheck1">',$queryResult[$i]['name'],'</label>
						</div>';
					}
					echo '</div>';
					$query = "SELECT name FROM keywords_categories INNER JOIN keywords ON keywords_categories.keywords_id=keywords.id WHERE categories_id=2";
					$queryResult = $database->select($query);
					echo '<div class="form-group" id="diycheckboxes" style="display: none">';
					for ($i = 0; $i < count($queryResult); $i++)
					{
						echo
						'<div class="form-check">
							<label class="form-check-label" for="defaultCheck1"><input class="form-check-input" type="checkbox" name="keywords[]" value="" id="defaultCheck1">',$queryResult[$i]['name'],'</label>
						</div>';
					}
					echo '</div>';

				?>
				</div>

				<button type="submit" class="btn btn-primary">Submit</button>
				<div class="form-group">
					<br>
					<ul id="submit-messages"></ul>
				</div>
			</form>
		</div>

		<?php include("footer.php"); ?>


		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js" ></script>

		<script>
			$(function ()
			{
				var validAuthorAndPermlink = false;
				var validDiscoverer = false;
				$('#ParseLinkButton').on('click', function (e)
				{
					var regex = new RegExp("(?<=@)(.*)(\/)(.*)");
					var matched = regex.exec($('#linkBox').val());

					if(matched && matched.length === 4)
					{
						$('#authorBox').val(matched[1]);
						$('#permLinkBox').val(matched[3]);
						$('#linkBox').addClass('is-valid').removeClass('is-invalid');
					}
					else
					{
						$('#linkBox').addClass('is-invalid').removeClass('is-valid');
					}
				});

				$('#authorBox').on('change keyup paste', function ()
				{
					$('#authorBox').removeClass('is-valid');
					validAuthorAndPermlink = false;
				});
				
				$('#permLinkBox').on('change keyup paste', function ()
				{
					$('#permLinkBox').removeClass('is-valid');
					validAuthorAndPermlink = false;
				});

				$('#ValidateButton').on('click', function (e)
				{
					$('#validation-messages').empty();
					$('#article1').empty();
					$('#article1').append($('<div class="spinner" id="spinner1" style="float: none; margin: 0 auto;"></div>'));

					var result = storyPreview(1, $('#authorBox').val(), $('#permLinkBox').val(), function(success)
					{
						if(success)
						{
							$('#authorBox').removeClass('is-invalid').addClass('is-valid');
							$('#permLinkBox').removeClass('is-invalid').addClass('is-valid');
							$('#validation-messages').append('<li class="text-success">Article found on the blockchain</li>');
							validAuthorAndPermlink = true;
						}
						else
						{
							$('#authorBox').removeClass('is-valid').addClass('is-invalid');
							$('#permLinkBox').removeClass('is-valid').addClass('is-invalid');
							$('#article1 ').append($('<li class="text-danger">Article not found on the blockchain</li>'));
							validAuthorAndPermlink = false;
						}

						if($('#discovererBox').val())
						{
							steem.api.getAccounts([$('#discovererBox').val()], function(err, result)
							{
								if(!err & result.length === 1)
								{
									$('#discovererBox').removeClass('is-invalid').addClass('is-valid');
									$('#validation-messages').append('<li class="text-success">Discoverer found on the blockchain</li>');
									validDiscoverer = true;
								}
								else
								{
									validDiscoverer = false;
									$('#discovererBox').removeClass('is-valid').addClass('is-invalid');
									$('#validation-messages').append('<li class="text-danger">Discoverer not found on the blockchain</li>');
								}
							});
						}
						else
						{
							$('#validation-messages').append('<li class="text-success">Your account will be used as discoverer</li>');
							$('#discovererBox').removeClass('is-invalid').addClass('is-valid');
							validDiscoverer = true;
						}
					});
				});

				$("#categoryselector :input").change(function()
				{
					$('#keywordCheckBoxes').find('input:checkbox').prop('checked', false);
					if($("#DIYRadio").is(':checked'))
					{
						$('#diycheckboxes').show();
						$('#makerscheckboxes').hide();
					}
					else
					{
						$('#diycheckboxes').hide();
						$('#makerscheckboxes').show();
					}
					return false;
				});

				$('#addArticle').submit(function(event)
				{
					event.preventDefault();

					$('#submit-messages').empty();

					var inputValid = true;
					if(!validAuthorAndPermlink)
					{
						$('#submit-messages').append('<li class="text-danger">Combination author and permlink not valid</li>');
						inputValid = false;
					}

					if(!validDiscoverer)
					{
						$('#submit-messages').append('<li class="text-danger">Discoverer is not valid</li>');
						inputValid = false;
					}

					if($('#keywordCheckBoxes input:checked').length === 0)
					{
						$('#submit-messages').append('<li class="text-danger">Select at least one keyword</li>');
						inputValid = false;
					}

					if(!inputValid)
					{
						return false;
					}

					var formData = $('#addArticle').serialize();

					$.ajax(
					{
						type: 'POST',
						url: $('#addArticle').attr('action'),
						data: formData
					})
					.done(function(response)
					{
						$('#submit-messages').append('<li class="text-success">Article successfully committed</li>');

						validAuthorAndPermlink = false;
						$('#linkBox').removeClass('is-valid')
						$('#authorBox').removeClass('is-valid');
						$('#permLinkBox').removeClass('is-valid');
						$('#article1').empty();
						$('#linkBox').val('');
						$('#authorBox').val('');
						$('#permLinkBox').val('');
						$('#keywordCheckBoxes').find('input:checkbox').prop('checked', false);
					})
					.fail(function(data)
					{
						if (data.responseText !== '')
						{
							$('#submit-messages').append('<li class="text-success">',data.responseText,'</li>');
						}
						else
						{
							$('#submit-messages').append('<li class="text-success">Oops! An error occured and your entry was not added.</li>');
						}
					});
				});
			});
		</script>

	</body>
</html>
