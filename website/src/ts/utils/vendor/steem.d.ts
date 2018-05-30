// Ambient namespace as per: https://www.typescriptlang.org/docs/handbook/namespaces.html

declare namespace steem
{
	export interface API
	{
		setOptions(json: any): void;
		getContent(author: string, permlink: string, callback: (error: any, result: Post)=>void): void;
		getDiscussionsByBlog(query: any, callback: (error: any, result: Post[])=>void): void;
	}

	var api: API;
}