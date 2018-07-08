// Ambient namespace as per: https://www.typescriptlang.org/docs/handbook/namespaces.html

declare namespace steem
{
	export interface API
	{
		setOptions(json: any): void;
		getContent(author: string, permlink: string, callback: (error: any, result: Post)=>void): void;
		getDiscussionsByBlog(query: any, callback: (error: any, result: Post[])=>void): void;
		getAccounts(author: Array<string>,callback: (error: any, result: any)=>void): void;
		getConfig(callback: (error: any, result: any)=>void): void;
		getDynamicGlobalProperties(callback: (error: any, result: any)=>void): void;
		getChainProperties(callback: (error: any, result: any)=>void): void;
		getCurrentMedianHistoryPrice(callback: (error: any, result: any)=>void): void;
		getRewardFund(r: string, callback: (error: any, result: any)=>void): void;
	}

	export interface API
	{
		vestToSteem(vestingShares: number, totalVestingShares: number, totalVestingFundSteem: number);
	}

	var api: API;
	var formatter: FORMATTER

}
