import {BlogEntry} from '../blogentry';
import {SteemAutoTrailer, Trailer} from '../Trailer'

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

export async function convertTrailers (steemAutoTrailers: Array<SteemAutoTrailer>): Promise<Trailer[]>
{
	// this will be the result array
	let result:Array<Trailer> = [];
	let authors: string[] = [];

	// here get all the blockchain data
	let globalData:any;
	let data: any;
	let sbd_base: any;
	let accounts: any;

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

	steemAutoTrailers.forEach(function(trailer) {
		authors.push(trailer.follower);
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
	for(var i=0;i < accounts.length; i++ )
	{
		let trailer: Trailer = new Trailer;
		trailer.name = steemAutoTrailers[i].follower;
		trailer.voteWeight = steemAutoTrailers[i].weight / 100;
		trailer.profileLink = 'https://www.steemit.com/@' + steemAutoTrailers[i].follower;
		
		// calculate 100% votes

		var vestingSharesParts = accounts[i].vesting_shares.split(' ');
		var receivedSharesParts = accounts[i].received_vesting_shares.split(' ');
		var delegatedSharesParts = accounts[i].delegated_vesting_shares.split(' ');

		var vests = (
			parseFloat(vestingSharesParts) +
			parseFloat(receivedSharesParts)
		) - (parseFloat(delegatedSharesParts));

		var	sp = steem.formatter.vestToSteem(
			vests,
			parseFloat(globalData.total_vesting_shares),
			parseFloat(globalData.total_vesting_fund_steem)
		);

		let now = new Date();
		let lastVoteTime = new Date(accounts[i].last_vote_time + "Z");
		var secondsago = (now.valueOf() - lastVoteTime.valueOf()) / 1000;

		// 5 days required to get to 100% VP: 60*60*24*5 = 432000
		var vpow = accounts[i].voting_power + (10000 * secondsago / 432000);
		vpow = Math.min(vpow , 10000);

		var vp = vpow / 100;
		var m  = Math.trunc(100 * vp * (100 * 100) / 10000);

		m = Math.trunc((m + 49) / 50);
		var ii:number = parseFloat(data.reward_balance.replace(' STEEM', '')) /
		parseFloat(data.recent_claims);
		var r = sp / a;
		var vote = Math.trunc(r * m * 100) * ii * o;

		// weight the 100% vote with the weight from input
		vote = vote * (steemAutoTrailers[i].weight/100);
		// vote = vote * 100;

		trailer.voteValue = vote / 100; 

		result.push(trailer);
	}

	return result;
}
