import * as sanitize from 'sanitize-html';
import {GetLocalURLRegExp, GetAnyURLRegExp, GetAnyImageURLRegExp, GetAnyYouTubeURLRegExp, GetYouTubeIDRegExp} from './vendor/links';
import {proxyfyImageURL} from './vendor/image'

declare var Remarkable:any;

export class MarkdownContentParser
{
	public body: string = '';
	public previewBody: string = '';
	public previewImage: string|null = null;

	constructor()
	{
	}

	Parse(markdown: string)
	{
		this.tempPreviewImage = null;
		this.body = '';
		this.previewBody = '';
		this.previewImage = null;

		// Remove html comments to preventapi invalid result causing by for example only open tag and no close tag
		let temp = markdown.replace(/<!--([\s\S]+?)(-->|$)/g, '(html comment removed: $1)');

		// Remove starting whitespace
		temp = temp.replace(/^\s+</gm, '<');

		var remarkable = new Remarkable(
		{
			html: true,
			breaks: true,
			linkify: false, // linkify is done in prepareHTML
			typographer: false, // https://github.com/jonschlinkert/remarkable/issues/142#issuecomment-221546793
			quotes: '“”‘’',
		});

		temp = remarkable.render(temp);

		temp = this.prepareHTML(temp);

		if(this.tempPreviewImage)
		{
			this.previewImage = proxyfyImageURL(this.tempPreviewImage);
		}

		let storyPreview = sanitize(temp, this.storyPreviewOptions);
		this.previewBody = storyPreview.replace(/\r?\n|\r/g, ' ');


		let story = sanitize(temp, this.storyOptions);
		// For syntax highlighting with highlight.js
		story.replace('<code>', '<pre><code>');
		story.replace('</code>', '</code></pre>');

		this.body = story;
	}

	private prepareHTML(html: string) : string
	{
		var div = document.createElement('div');
		div.innerHTML = html.trim();
	
		this.Traverse(div);
		
		var imageElements = div.getElementsByTagName('img');
		for (var i = 0; i < imageElements.length; i++)
		{
			const url = imageElements[i].getAttribute('src');
			if (url && !GetLocalURLRegExp().test(url))
			{
				imageElements[i].setAttribute('src', proxyfyImageURL(url));
			}
		}
	
		return div.innerHTML;
	}
	
	private Traverse(node: Node)
	{	
		if(node instanceof Element)
		{
			var element = node as Element;
			var tag = element.tagName ? element.tagName.toLowerCase() : null;
			switch (tag)
			{
				case 'img':
					{
						let url = element.getAttribute('src');
						if (url)
						{
							if(!this.tempPreviewImage)
							{
								this.tempPreviewImage = url;
							}
							if (/^\/\//.test(url))
							{
								// Change relative protocol imgs to https
								url = 'https:' + url;
								element.setAttribute('src', url);
							}
						}
					}
					break;
				case 'iframe':
					{
						let url = element.getAttribute('src');
	
						if(element.parentElement)
						{
							let tag = element.parentElement.tagName ? element.parentElement.tagName.toLowerCase() : element.parentElement.tagName;
						
							if (tag === 'div' && element.parentElement.getAttribute('class') === 'videoWrapper')
							{
								return;
							}
							else
							{
								var html = (new XMLSerializer()).serializeToString(element);

								let videoID = html.match(GetYouTubeIDRegExp());
								if(videoID)
								{
									if(!this.tempPreviewImage)
									{
										this.tempPreviewImage = 'https://img.youtube.com/vi/' + videoID[0] + '/0.jpg';
									}
								}
								let doc = this.domParser.parseFromString(`<div class="videoWrapper">${html}</div>`, "text/html");
								element.parentElement.replaceChild(doc.body.childNodes[0], element);
							}
						}
					}
					break;
				case 'a':
					{
						let url = element.getAttribute('href');
						if (url)
						{
							// If this link is not http or https -- add https.
							if (!/(https?:)?\/\//.test(url))
							{
								element.setAttribute('href', 'https://' + url);
							}
						}
					}
					break;
			}
		}
		else if (node.nodeName === '#text' && node.parentNode && node.nodeValue)
		{
			let interpretedContent = node.nodeValue;
			interpretedContent = interpretedContent.replace(GetAnyImageURLRegExp(), link =>
			{
				if(!this.tempPreviewImage)
				{
					this.tempPreviewImage = link;
				}
				return `<img src="${link}" />`;
			});
	
			interpretedContent = interpretedContent.replace(GetAnyURLRegExp(), link =>
			{
				let videoID = link.match(/(?:(?:youtube.com\/watch\?v=)|(?:youtu.be\/)|(?:youtube.com\/embed\/))([A-Za-z0-9\_\-]+)/i);
				if(videoID && videoID.length >= 2)
				{
					if(!this.tempPreviewImage)
					{
						this.tempPreviewImage = 'https://img.youtube.com/vi/' + videoID[1] + '/0.jpg';
					}
					return `<div class="videoWrapper"><iframe class="videoWrapper" src="https://www.youtube.com/embed/${videoID[1]}"></iframe></div>`;
				}
				else
				{
					return link;
				}
			});
	
			if(interpretedContent !== node.nodeValue)
			{
				try
				{
					let doc = this.domParser.parseFromString(`<span>${interpretedContent}</span>`, "text/html");
					node.parentNode.replaceChild(doc.body.childNodes[0], node);
				}
				catch(err)
				{
					console.log(err);
				}
			}
		}
	
		if(node.firstChild)
		{
			for(var i in node.childNodes)
			{
				this.Traverse(node.childNodes[i]);
			}
		}
	}

	private tempPreviewImage :string|null = null;
	private readonly domParser = new DOMParser();
	private readonly storyOptions: sanitize.IOptions = 
	{
		allowedTags: [
			'iframe', 'div',
			'a', 'p', 'b', 'i', 'q', 'br', 'ul', 'li', 'ol', 'img', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr',
			'blockquote', 'pre', 'code', 'em', 'strong', 'center', 'table', 'thead', 'tbody', 'tr', 'th', 'td',
			'strike', 'sup', 'sub'],
		allowedAttributes:
		{
			a: ['href', 'rel', 'title'],
			img: ['src', 'alt'],
			div: ['class'],
			iframe: [
				'src',
				'width',
				'height',
				'frameborder',
				'allowfullscreen',
				'webkitallowfullscreen',
				'mozallowfullscreen',
			],
		},
		transformTags: {
			a: (tagName: string, attribs: sanitize.Attributes) => 
			{
				let title = '';
				let href = attribs.href;
				if (!href)
				{
					href = '#';
				}
				href = href.trim();
				if (!href.match(/^https:\/\/steemmakers.com/))
				{
					title = 'This link will take you away from steemmakers.com';
				}
				return {
					tagName: 'a',
					attribs: 
					{
						href: href,
						rel: 'noopener',
						title: title
					},
				};
			},
			div: (tagName: string, attribs: sanitize.Attributes) =>
			{
				return {
					tagName: 'div',
					attribs: 
					{
						class: attribs.class.indexOf('videoWrapper') !== -1 ? 'videoWrapper' : '',
					},
				};
			},
			iframe: (tagName: string, attribs: sanitize.Attributes) =>
			{
				let sourceAttribute = attribs.src;
	
				if(sourceAttribute)
				{
					let matches = sourceAttribute.match(/^(https?:)?\/\/www.youtube.com\/embed\/.*/i);
					if(matches)
					{
						sourceAttribute.replace(/\?.+$/, ''); // strip query string (autoplay, controls, showinfo, etc)
					}
	
					return {
						tagName: 'iframe',
						attribs:
						{
							frameborder: '0',
							allowfullscreen: 'allowfullscreen',
							src: sourceAttribute,
							width: '480',
							height: '270',
						},
					};
				}
	
				return { tagName: 'p', text: `(Unsupported iframe element)`, attribs };
			},
			img: (tagName: string, attribs: sanitize.Attributes) => 
			{
				if (!/^(https?:)?\/\//i.test(attribs.src))
				{
					return {
						tagName: 'img',
						attribs: 
						{
							src: '',
							alt: 'suspicious image',
						}
					};
				}
	
				// replace http:// with // to force https when needed
				let src = attribs.src.replace(/^http:\/\//i, '//');
				let alt = attribs.alt;
				return {
					tagName: 'img',
					attribs: 
					{
						src: src,
						alt: attribs.alt ? attribs.alt : '',
					}
				};
			},
		},
	};
	
	private readonly storyPreviewOptions: sanitize.IOptions = 
	{
		allowedTags: []
	};
}