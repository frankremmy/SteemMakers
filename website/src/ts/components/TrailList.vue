<template>
	<div>
		<center>
			<div v-if="trailers.length === 0" >
				<pre>Fetching data from steemauto</pre>
			</div>
			<div v-else>
				<table id="result" width="400px">
				<tr><td>Steem ID</td><td>&nbsp;&nbsp;&nbsp;</td><td>Weight</td><td>&nbsp;&nbsp;&nbsp;</td><td>Estimated Vote Value</td></tr>
				<tr v-for="(trailer, index) in trailers"  :key="index"><td><a :href="trailer.profileLink">{{ trailer.name }}</a></td><td>&nbsp;&nbsp;&nbsp;</td><td>{{ trailer.voteWeight | ToFixed(2)}}</td><td>&nbsp;&nbsp;&nbsp;</td><td>{{ trailer.voteValue | ToFixed(2)}} SBD</td></tr>
				</table>
			</div>
		</center>
	</div>
</template>

<script lang="ts">
	import axios from 'axios';
	import Vue from "vue";
	import {convertTrailers} from "../utils/utils";
	import {SteemAutoTrailer, Trailer} from "../Trailer";

	export default Vue.extend({
		data()
		{
			return {
				trailers: <Trailer[]>[],
				accounts: []
			}
		},
		created: async function()
		{
			var accounts:Array<string> = [];
			let weights:Array<number> = [];
			var name:string;

			let steemAutoTrailers: SteemAutoTrailer[] = [];

			await axios.get(`https://steemauto.com/api.php?i=1&user=steemmakers`)
			.then(response => {
				steemAutoTrailers = JSON.parse(response.request.response);
			});

			if(steemAutoTrailers.length !== 0)
			{
				this.trailers = await convertTrailers(steemAutoTrailers);

				this.trailers.sort(function(a, b) {
					if (b.voteValue < a.voteValue)
						return -1;
					else if (b.voteValue > a.voteValue)
						return 1;
					else
						return 0;
				});
			}
		},
		filters:
		{
			ToFixed: function (input: number, nofDigits: number)
			{
				return input.toFixed(nofDigits);
			}
		}
	});
</script>


<style scoped>

</style>
