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
	payout: number = 0;

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

		// Calculate payout
		this.payout = parseFloat(post.pending_payout_value) + parseFloat(post.total_payout_value) + parseFloat(post.curator_payout_value);
		if (this.payout < 0.0) this.payout = 0.0;
		let maxPayout = parseFloat(post.max_accepted_payout);
		if (this.payout > maxPayout) this.payout = maxPayout;
	}

	private parsePayoutAmount(amount :string)
	{
		return parseFloat(String(amount).replace(/\s[A-Z]*$/, ''));
	}
}