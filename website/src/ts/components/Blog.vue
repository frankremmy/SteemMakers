<template>
	<div class="container article">
		<template> 
			<div class="row" v-for="(article, index) in articles" :key="index">
				<label> {{ article.name }} </label>
			</div>
		</template>
	</div>
</template>

<script lang="ts">
	import Vue from "vue";
	import {GetBlogArticles} from "../utils/utils";
	
	export default Vue.extend({
		data: function ()
		{
			return {
				articles: [
					{name: 'article 1'},
					{name: 'article 2'}
				]
			}
		},
		created: function ()
		{
			this.LoadContent();
		},
		methods:
		{
			LoadContent()
			{
				GetBlogArticles("steemmakers", 20, (error, blogEntry) =>
				{
					// this.ArticleURL = blogEntry.url;
					// this.Author = blogEntry.author;
					// this.BodyHTML = blogEntry.body;
					// this.Title = blogEntry.title;
					
					// var options = {year: "numeric", month: "long", day: "numeric", hour: '2-digit', minute:'2-digit', hour12: false};
					// this.CreationDateTime = formatDate(blogEntry.created);

				});
			}
		}
	});

	function formatDate(date: Date) :string
	{
		var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];

		var day = date.getDate();
		var monthIndex = date.getMonth();
		var year = date.getFullYear();

		return day + ' ' + monthNames[monthIndex] + ' ' + year + ', ' + date.getHours() + ':' + date.getMinutes();
	}
</script>


<style scoped>

</style>