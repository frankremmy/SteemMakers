<template>
	<div class="container article">
		<div class="row" v-for="(article, index) in articles" :key="index">
			<ArticlePreview v-bind:blogEntry="article"></ArticlePreview>
		</div>
	</div>
</template>

<script lang="ts">
	import Vue from "vue";
	import ArticlePreview from './ArticlePreview.vue'
	import {getBlogArticles, formatDate, createArticleAsync} from "../utils/utils";
	
	export default Vue.extend({
		components: {ArticlePreview},
		data: function ()
		{
			return {
				articles: <BlogEntry[]> []
			}
		},
		filters: 
		{
			formatDate: function (date: Date)
			{
				return formatDate(date);
			}
		},
		created: function ()
		{
			this.articles = new Array(10);
			this.LoadContent();
		},
		methods:
		{
			LoadContent()
			{
				fetch("http://localhost/api/v1/articles.php?page=1")
				.then(response => response.json())
				.then((data) =>
				{
					let index :number;
					for(index = 0; index < data.data.length; index++)
					{
						console.log(data.data[index]);
						createArticleAsync(data.data[index].name, data.data[index].permlink).then(function(this: any, index: number, article: BlogEntry)
						{
							Vue.set(this.articles, index, article);
						}.bind(this, index));
					}
				})
			}
		}
	});
</script>


<style scoped>

</style>