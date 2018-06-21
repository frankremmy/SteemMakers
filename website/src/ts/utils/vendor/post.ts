declare namespace steem
{
	export interface Post
	{
		author: string;
		body: string;
		cashout_time: string;
		created: string;
		permlink: string;
		title: string;
		url: string;
		max_accepted_payout: string;
		pending_payout_value: string;
		total_payout_value: string;
		curator_payout_value: string;
	}
}