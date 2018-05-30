steem.api.setOptions({ url: 'https://api.steemit.com' });

function fillBlogEntries(username)
{
	steem.api.getDiscussionsByBlog({tag: username, limit: 20}, function(err, posts) 
	{
		var blogContainer = $('#blog');
		for (var i = 0; i < posts.length; i++) 
		{
			blogContainer.append('<div class="row">' + generatePreviewHtml(posts[i]) + '</div>');
		}
	});
}

const IMG_PROXY = 'https://steemitimages.com/0x0/';
const IMG_PROXY_PREVIEW = 'https://steemitimages.com/600x800/';

function getProxyImageURL(url, type)
{
	if (type === 'preview')
	{
		return `${IMG_PROXY_PREVIEW}${url}`;
	}

	return `${IMG_PROXY}${url}`;
};

function generatePreviewImageURL(post)
{
	const jsonMetadata = JSON.parse(post.json_metadata);
	let imagePath = '';
	bodyText = '';

	if (jsonMetadata.image && jsonMetadata.image[0])
	{
		imagePath = getProxyImageURL(jsonMetadata.image[0], 'preview');
	}

	return imagePath;
}

function generatePreviewText(post)
{
	bodyText = '';
	bodyText = post.body.replace(/(!\[.*?\]\()(.+?)(\))/g, '');
	bodyText= bodyText.replace(/<\/?[^>]+(>|$)/g, '');
	bodyText= bodyText.replace(/\[([^\]]+)\][^\)]+\)/g, '$1');

	return bodyText;
}

function generatePreviewHtml(post)
{
	previewHtml = 
		`<div class="blog-image col-md-2">
		<img src="`+ generatePreviewImageURL(post) + `">
		</div>
		<div class="col-md-10">
		<h5 class="font-weight-bold" style="margin-top:5px;"><a href="post.php?author=` + post.author + `&permlink=` + post.permlink + `">` + post.title + `</a></h5>
		<div class="multiline-ellipsis">
			<p>` + generatePreviewText(post) + `</p>
		</div>
		</div>`;

		return previewHtml;
}

function storyPreview ( article, author, permlink, callback)
{
	steem.api.getContent(author, permlink, function(err, post)
	{
		if(!err && post.body !== "")
		{
			if(("#spinner" + article).length)
			{
				$("#spinner" + article).hide();
			}
			
			$("#article" + article).append(generatePreviewHtml(post));

			typeof callback === 'function' && callback(post);
		}
		else
		{
			if(("#spinner" + article).length)
			{
				$("#spinner" + article).hide();
			}
			typeof callback === 'function' && callback(null);
		}
	});
}

function createPostHtml (author, permlink)
{
	steem.api.getContent(author, permlink, function(err, post)
	{
		if(!err && post.body !== "")
		{
			// Remove html comments to prevent invalid result causing by for example only open tag and no close tag
			var parsedBody = post.body.replace(/<!--([\s\S]+?)(-->|$)/g, '(html comment removed: $1)');

			// Don't understand why
			parsedBody = parsedBody.replace(/^\s+</gm, '<');

			remarkable = new Remarkable(
			{
				html: true, // remarkable renders first then sanitize runs...
				breaks: true,
				linkify: false, // linkify is done locally
				typographer: false, // https://github.com/jonschlinkert/remarkable/issues/142#issuecomment-221546793
				quotes: '����',
			});

			parsedBody = remarkable.render(parsedBody);

			const htmlReadyOptions = { mutate: true, resolveIframe: true };
			if (parsedBody)
			{
				renderedText = htmlReady(parsedBody);
			}

			parsedBody = htmlReady(parsedBody, htmlReadyOptions);
			parsedBody = parsedBody.replace(dtubeImageRegex, '');
			parsedBody = sanitizeHtml(parsedBody, sanitizeConfig({}));
			
			parsedBody = "";
		}
	});
}










