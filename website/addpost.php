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
		<?php include("./src/globalhead.php"); ?>

		<script src="https://cdn.jsdelivr.net/remarkable/1.7.1/remarkable.min.js"></script>
		<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>
		<script type="text/javascript" src="js/steem.js?filever=<?php echo filesize('./js/steem.js')?>"></script>

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	</head>
	<body class="bg-secondary">
		<?php include("navbar.php"); ?>
		
		<div class="container" style="width: 75%;">
			<form id="addArticle" action="src/submitpost.php">
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
					<input type="text" class="form-control" placeholder="Permlink" id="permlinkBox" name="permlink">
				</div>
				<div class="form-group">
					<label class="control-label">Discoverer</label>
					<div class="input-group">
						<div class="input-group-addon">
							<span class="input-group-text">@</span>
						</div>
						<input type="text" class="form-control" placeholder="Discoverer" id="discovererBox" name="discoverer">
					</div>
					<small class="form-text text-muted">If the link was proposed to you, for example on discord in 'request-review', please use the original finder's id if it matches to a steem id.</small>
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

				<button type="submit" class="btn btn-primary" id="SubmitButton">Submit</button>
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
				var isInformationValid = false;

				$('#ParseLinkButton').on('click', function (e)
				{
					var regex = new RegExp(".*\/@(.*)\/(.*)$");
					var matched = regex.exec($('#linkBox').val());

					if(matched && matched.length === 3)
					{
						$('#authorBox').val(matched[1]);
						$('#permlinkBox').val(matched[2]);
						$('#linkBox').addClass('is-valid').removeClass('is-invalid');
					}
					else
					{
						$('#linkBox').addClass('is-invalid').removeClass('is-valid');
					}
				});

				function OnInformationChanged()
				{
					isInformationValid = false;
					$('#validation-messages').empty();
					$('#submit-messages').empty();
				}

				$('#authorBox').on('change keyup paste', function ()
				{
					$('#authorBox').removeClass('is-valid').removeClass('is-invalid');
					OnInformationChanged();
				});
				
				$('#permlinkBox').on('change keyup paste', function ()
				{
					$('#permlinkBox').removeClass('is-valid').removeClass('is-invalid');
					OnInformationChanged();
				});

				$('#discovererBox').on('change keyup paste', function ()
				{
					$('#discovererBox').removeClass('is-valid').removeClass('is-invalid');
					OnInformationChanged();
				});

				$("#categoryselector :input").change(function()
				{
					$('#submit-messages').empty();
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

				$("#keywordCheckBoxes input[type=checkbox]").change(function()
				{
					$('#submit-messages').empty();
				});

				$('#ValidateButton').on('click', function (e)
				{
					databaseCheckCompleted = false;
					articleCheckCompleted = false;
					discovererCheckCompleted = false;
					databaseCheckPassed = false;
					articleCheckPassed = false;
					discovererCheckPassed = false;

					$('#ValidateButton').html("<i class='fa fa-spinner fa-spin'></i> Validating");

					$('#validation-messages').empty();
					$('#article1').empty();

					// Verify if article not present in DB
					$.ajax(
					{
						type: 'POST',
						url: 'src/verifyarticle.php',
						data: { author: $('#authorBox').val(), permlink: $('#permlinkBox').val() }
					})
					.done(function(response)
					{
						if(response.type === 'success')
						{
							$('#validation-messages').append('<li class="text-success">New article, not present in the database.</li>');
							databaseCheckPassed = true;
						}
						else
						{
							$('#validation-messages').append('<li class="text-danger">' + response.message + '</li>');
						}
						databaseCheckCompleted = true;
						validationComplete();
					})
					.fail(function(data)
					{
						if (data.responseText !== '')
						{
							$('#validation-messages').append('<li class="text-danger">An error occured while checking the database' + data.responseText + '</li>');
						}
						else
						{
							$('validation-messages').append('<li class="text-danger">An error occured, the system couldn\'t check if your entry already exists.</li>');
						}
						databaseCheckCompleted = true;
						validationComplete();
					});

					// Verify if article exists on the blockchain
					var result = storyPreview(1, $('#authorBox').val(), $('#permlinkBox').val(), function(post)
					{
						if(post !== null)
						{
							$('#authorBox').removeClass('is-invalid').addClass('is-valid');
							$('#permlinkBox').removeClass('is-invalid').addClass('is-valid');
							$('#validation-messages').append('<li class="text-success">Article found on the blockchain</li>');

							var timeDiff = new Date(post.cashout_time) - Date.now();
							var diffDays = timeDiff / (1000 * 3600 * 24); 

							if(diffDays > 1)
							{
								articleCheckPassed = true;
								$('#validation-messages').append('<li class="text-success">Article is less than 6 days old</li>');
							}
							else
							{
								$('#validation-messages').append($('<li class="text-danger">Article is more than 6 days old</li>'));
							}
						}
						else
						{
							$('#authorBox').removeClass('is-valid').addClass('is-invalid');
							$('#permlinkBox').removeClass('is-valid').addClass('is-invalid');
							$('#validation-messages').append($('<li class="text-danger">Article (combination author/permlink) not found on the blockchain</li>'));
						}
						articleCheckCompleted = true;
						validationComplete();
					});

					// Verify if the discoverer is valid
					if($('#discovererBox').val())
					{
						steem.api.getAccounts([$('#discovererBox').val()], function(err, result)
						{
							if(!err & result.length === 1)
							{
								$('#discovererBox').removeClass('is-invalid').addClass('is-valid');
								$('#validation-messages').append('<li class="text-success">Discoverer found on the blockchain</li>');
								discovererCheckPassed = true;
							}
							else
							{
								$('#discovererBox').removeClass('is-valid').addClass('is-invalid');
								$('#validation-messages').append('<li class="text-danger">Discoverer not found on the blockchain</li>');
								validDiscoverer = false;
							}
							discovererCheckCompleted = true;
							validationComplete();
						});
					}
					else
					{
						$('#validation-messages').append('<li class="text-success">Your account will be used as discoverer</li>');
						$('#discovererBox').removeClass('is-invalid').addClass('is-valid');
						discovererCheckPassed = true;
						discovererCheckCompleted = true;
						validationComplete();
					}

					function validationComplete()
					{
						if (databaseCheckCompleted && articleCheckCompleted && discovererCheckCompleted)
						{
							isInformationValid = databaseCheckPassed && articleCheckPassed && discovererCheckPassed;
							$('#ValidateButton').html("Validate");
						}
					}
				});

				$('#addArticle').submit(function(event)
				{
					event.preventDefault();

					$('#submit-messages').empty();

					if(!isInformationValid)
					{
						$('#submit-messages').append('<li class="text-danger">Validate the information first.</li>');
						return false;
					}

					if($('#keywordCheckBoxes input:checked').length === 0)
					{
						$('#submit-messages').append('<li class="text-danger">Select at least one keyword</li>');
						return false;
					}

					$('#SubmitButton').html("<i class='fa fa-spinner fa-spin'></i> Submitting");

					var formData = $('#addArticle').serialize();

					$.ajax(
					{
						type: 'POST',
						url: $('#addArticle').attr('action'),
						data: formData
					})
					.done(function(response)
					{
						isInformationValid = false;
						$('#submit-messages').append('<li class="text-success">Article successfully committed</li>');
						$('#SubmitButton').html("Submit");

						$('#linkBox').val('');
						$('#linkBox').removeClass('is-valid');
						$('#authorBox').val('');
						$('#authorBox').removeClass('is-valid');
						$('#permlinkBox').val('');
						$('#permlinkBox').removeClass('is-valid');
						$('#discovererBox').val('');
						$('#discovererBox').removeClass('is-valid');
						$('#validation-messages').empty();
						$('#keywordCheckBoxes').find('input:checkbox').prop('checked', false);
						$('#article1').empty();
					})
					.fail(function(data)
					{
						$('#SubmitButton').html("Submit");
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
