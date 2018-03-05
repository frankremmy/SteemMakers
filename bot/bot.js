// REQUIREMENTS: 
// nodeJS: https://nodejs.org
// steem-js: npm install steem --save
// mysql: npm install mysql

var mysql = require('mysql');
var steem = require('steem');
var fs = require('fs');

steem.api.setOptions({ url: 'https://api.steemit.com' });

var config;
var mysqlConnection;

function createCommentPermlink(parentAuthor, parentPermlink)
{
	var permlink;
	// comments: re-parentauthor-parentpermlink-time
	const timeStr = new Date().toISOString().replace(/[^a-zA-Z0-9]+/g, '');
	parentPermlink = parentPermlink.replace(/(-\d{8}t\d{9}z)/g, '');
	permlink = `re-${parentAuthor}-${parentPermlink}-${timeStr}`;

	if (permlink.length > 255)
	{
	// STEEMIT_MAX_PERMLINK_LENGTH
	permlink = permlink.substring(permlink.length - 255, permlink.length);
	}

	// only letters numbers and dashes shall survive
	permlink = permlink.toLowerCase().replace(/[^a-z0-9-]+/g, '');
	return permlink;
}

function main()
{
	config = JSON.parse(fs.readFileSync('./config/config.json', 'utf8'));

	mysqlConnection = mysql.createConnection({
		host: config.host,
		user: config.user,
		password: config.password,
		database: config.database
	});

	mysqlConnection.connect(function(error)
	{
		if (error)
		{
			console.log("Failed to connect to database");
			throw error;
		}
		else
		{
			console.log("Connected to database!");

			mysqlConnection.query("SELECT approved_posts.id, name AS author_name, permlink FROM approved_posts JOIN users ON approved_posts.author_id = users.id WHERE (approved_posts.commented_on IS NULL AND approved_posts.commented_on IS NULL)", function (error, rows, fields)
			{
			if (error)
				{
					console.log("Failed to query approved posts");
					throw error;
				}
				else
				{
					var currentEntry = 0;
					var totalEntries = rows.length;

					console.log(rows.length + " entries need voting and commenting");
					if(totalEntries == 0)
					{
						return;
					}

					var looper = function()
					{

						var currentId = rows[currentEntry].id;
						var currentAuthor = rows[currentEntry].author_name;
						var currentPermlink = rows[currentEntry].permlink;
						console.log("Entry " + currentEntry + ": id=" + currentId + ", author name=" + currentAuthor + ", permlink=" + currentPermlink);
 
						var wif = steem.auth.toWif(config.votingAccount, config.votingAccountPW, 'posting');

						steem.broadcast.vote(wif, config.votingAccount, currentAuthor, currentPermlink, 10000, function(err, result)
						{
							// Vote callback
							if(!err && result)
							{
								console.log("Successfully voted on id " + currentId);
					
								var query = 'UPDATE approved_posts SET voted_on = NOW() WHERE id=?';
											
								mysqlConnection.query(query,[currentId], function (error, result, rows1, fields)
								{
									if(!err && result)
									{
										console.log("Added voting time to database for id " + currentId); 
									}
									else
									{
										console.log("Failed to add voting time to database for id " + currentId); 
									}
								});

								var commentPermlink = createCommentPermlink(currentAuthor, currentPermlink);
								body = "Congratulations! This post has been upvoted by SteemMakers. We are a community based project that aims to support makers and DIYers on the blockchain in every way possible. Find out more about us on our website: [www.steemmakers.com](www.steemmakers.com). <br/><br/>If you like our work, please consider upvoting this comment to support the growth of our community. Thank you.";

								steem.broadcast.comment(wif,  currentAuthor, currentPermlink, config.votingAccount, commentPermlink, "", body, "", function(err, result)
								{
									// Comment callback
									if(!err && result)
									{
										console.log("Successful commented on id " + currentId);

										var query = 'UPDATE approved_posts SET commented_on = NOW() WHERE id=?';

										mysqlConnection.query(query,[currentId], function (error, result, rows2, fields)
										{
											if(!err && result)
											{
												console.log("Added commenting time to database for id " + currentId); 
											}
											else
											{
												console.log("Failed to add commenting time to database for id " + currentId); 
											}
										});

										currentEntry++;
										if (currentEntry == totalEntries)
										{
											console.log("No more entries.");
											return;
										}
				
										setTimeout(looper, 30000);
									}
									else
									{
										console.log("Failed to comment on id " + currentId + ": " + err);
									}
								});
							}
							else
							{
								console.log("Failed to vote on id " + currentId + ": " + err);
							}
						});
					};

					looper();
				}
			});
		}
	});
}

main();