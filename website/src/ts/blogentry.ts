import {MarkdownContentParser} from './utils/markdowncontentparser';

export class BlogEntry
{
	author: string = '';
	authorBlog: string = '';
	body: string = '';
	created: Date = new Date();
	isLoading: boolean = true;
	permlink: string = '';
	previewBody: string = '';
	previewImage: string | null = null;
	title: string = '';
	url: string = '';

	constructor(post: steem.Post)
	{
		let markdownContentParser = new MarkdownContentParser();
		markdownContentParser.Parse(post.body);

		this.author = post.author;
		this.authorBlog = "steemit.com/@" + post.author;
		this.body = markdownContentParser.body;
		this.permlink = post.permlink;
		this.previewBody = markdownContentParser.previewBody;
		this.previewImage = markdownContentParser.previewImage;
		this.created = new Date(post.created + '.000Z');
		this.title = post.title;
		this.url = post.url;
		this.isLoading = false;
	}
}