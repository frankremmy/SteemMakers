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
	import {getBlogArticles, formatDate} from "../utils/utils";
	
	export default Vue.extend({
		components: {ArticlePreview},
		data: function ()
		{
			return {
				articles: <BlogEntry[]> []
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
				getBlogArticles("steemmakers", 20, (error, blogEntries) =>
				{
					this.articles = blogEntries;
				});
			}
		}
	});
</script>


<style scoped>

</style>