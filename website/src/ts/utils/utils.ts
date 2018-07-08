import {BlogEntry} from '../blogentry';

steem.api.setOptions({ url: 'https://api.steemit.com' });


export function createPostHtml (author: string, permlink: string, callback: (error :string|null, result :BlogEntry) => void) : void
{
	steem.api.getContent(author, permlink, function(err, post)
	{
		let result = {} as BlogEntry;

		if(!err && post.body !== "")
		{
			let blogEntry = new BlogEntry(post);

			callback(null, blogEntry);
		}
		else
		{
			callback(err, result);
		}
	});
}

export function createArticleAsync (author: string, permlink: string) : Promise<steem.Post>
{
	return new Promise((resolve,reject) =>
	{
		steem.api.getContent(author, permlink, function(err, post)
		{
			if(!err && post.body !== "")
			{
				resolve(post);
			}
			else
			{
				reject(err);
			}
		});
	});
}

export function getBlogArticles (author: string, limit: number, callback: (error :string|null, result :Array<BlogEntry>) => void) : void
{
	steem.api.getDiscussionsByBlog({tag: 'steemmakers', limit: 20}, function(err, posts)
	{
		let result: BlogEntry[] = Array();
		if(!err)
		{
			for (var i = 0; i < posts.length; i++)
			{
				let newEntry = new BlogEntry(posts[i]);
				result.push(newEntry);
			}
			callback(null, result);
		}
		else
		{
			callback(err, result);
		}
	});
}

export function formatDate(date: Date) :string
{
	var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];

	var day = date.getDate();
	var monthIndex = date.getMonth();
	var year = date.getFullYear();

	return day + ' ' + monthNames[monthIndex] + ' ' + year + ', ' + date.getHours() + ':' + date.getMinutes();
}

export async function getVoteValue (authors: Array<string>, weights: Array<string>): Promise<Array<any>>
{
	// this will be the result array
	var result:Array<any> = [];

	// here get all the blockchain data
	var globalData:any;
	var data: any;
	var sbd_base: any;
	var accounts: any;

	// async calls to steem apis
	let globalDataPromise = new Promise((resolve, reject) => {
		steem.api.getDynamicGlobalProperties(function(err, result) {
			if (err) reject(err);
			resolve(result);
		});
	});

	let dataPromise = new Promise((resolve, reject) => {
		steem.api.getRewardFund('post',function(err, result) {
			if (err) reject(err);
			resolve(result);
		});
	});

	let sbd_basePromise = new Promise((resolve, reject) => {
		steem.api.getCurrentMedianHistoryPrice(function(err, result) {
			if (err) reject(err);
			resolve(result);
		});
	});

	let accountsPromise = new Promise((resolve, reject) => {
		steem.api.getAccounts(authors, function(err, result) {
			if (err) reject(err);
			resolve(result);
		});
	});

	// wait until all data is loaded
	// we load them in paralell so it should be quite fast
	globalData = await globalDataPromise;
	data = await dataPromise;
	sbd_base = await sbd_basePromise;
	accounts = await accountsPromise;

	//exchange rate
	var o = parseFloat(sbd_base.base.replace(' SBD', '')) /
	parseFloat(sbd_base.quote.replace(' STEEM', ''));

	// global available fund
	var a = globalData.total_vesting_fund_steem.replace(' STEEM', '') /
	globalData.total_vesting_shares.replace(' VESTS', '');

	// main loop across all accounts
	for(var i=0;i < accounts.length; i++ ){
		// calculate 100% votes
		result[i] = {};
		result[i].follower = authors[i];
		result[i].weight = weights[i];

		var vestingSharesParts = accounts[i].vesting_shares.split(' ');
		var receivedSharesParts = accounts[i].received_vesting_shares.split(' ');
		var delegatedSharesParts = accounts[i].delegated_vesting_shares.split(' ');

		var vests = (
			parseFloat(vestingSharesParts) +
			parseFloat(receivedSharesParts)
		) - (parseFloat(delegatedSharesParts));

		// maybe show the vests in result
		result[i].vests = vests;

		var	sp = steem.formatter.vestToSteem(
			vests,
			parseFloat(globalData.total_vesting_shares),
			parseFloat(globalData.total_vesting_fund_steem)
		);

		// maybe show vp in result
		result[i].sp = sp;

		var vp = Math.trunc( parseInt(weights[i])) / 100;
		var m  = Math.trunc(100 * vp * (100 * 100) / 10000);

		m = Math.trunc((m + 49) / 50);
		var ii:number = parseFloat(data.reward_balance.replace(' STEEM', '')) /
		parseFloat(data.recent_claims);
		var r = sp / a;
		var vote = Math.trunc(r * m * 100) * ii * o;

		// weight the 100% vote with the weight from input
		vote = vote * (parseFloat(weights[i])/100);
		vote = vote * 100;

		// show vote value in result
		result[i].valueunfixed = vote; // this is for sorting
		result[i].value = vote.toFixed(2);
	}

	result.sort(function(a, b) {
		if (b.valueunfixed < a.valueunfixed)
			return -1;
		else if (b.valueunfixed > a.valueunfixed)
			return 1;
		else
			return 0;
		});
	return result;

}
