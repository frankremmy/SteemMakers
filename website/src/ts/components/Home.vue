<template>
	<div class="container article">
		<div class="row" v-for="(article, index) in articles" :key="index">
			<ArticlePreview v-bind:blogEntry="article"></ArticlePreview>
		</div>
		<b-pagination-nav :use-router="true" :link-gen="generateLink" align="center" :number-of-pages="nofPages" v-model="pageIndex" />
	</div>
</template>

<script lang="ts">
	import Vue from "vue";
	import ArticlePreview from './ArticlePreview.vue';
	import {getBlogArticles, formatDate, createArticleAsync} from "../utils/utils";
	import {BlogEntry} from '../blogentry';
	
	export default Vue.extend({
		components: {ArticlePreview},
		props:
		{
			page:
			{
				type: String,
				required: false,
				default: '1',
			}
		},
		data: function ()
		{
			return {
				articles: <(null|BlogEntry)[]> [],
				nofPages: 1
			}
		},
		filters: 
		{
			formatDate: function (date: Date)
			{
				return formatDate(date);
			}
		},
		computed:
		{
			pageIndex() :number
			{
				return parseInt(this.page, 10);
			}
		},
		watch:
		{
			pageIndex()
			{
				this.loadContent();
			},
		},
		mounted()
		{
			this.loadContent();
		},
		methods:
		{
			loadContent()
			{
				fetch("./api/v1/articles.php?page=" + this.pageIndex)
				.then(response => response.json())
				.then((data) =>
				{
					this.nofPages = Math.ceil(data.nofItems/10);
					this.articles.splice(0);
					this.articles.splice(data.data.length);
					let index :number;
					for(index = 0; index < data.data.length; index++)
					{
						this.articles[index] = null;
						createArticleAsync(data.data[index].name, data.data[index].permlink).then(function(this: any, index: number, article: steem.Post)
						{
								Vue.set(this.articles, index, new BlogEntry(article));
							// for testing async
							// setTimeout(function(this: any){
							// 	Vue.set(this.articles, index, new BlogEntry(article));}.bind(this), Math.random()*5000);
						}.bind(this, index));
					}
				})
			},
			generateLink(page :number)
			{
				return { name:'Home', params: { page: page.toString() } };
			},
		}
	});
</script>


<style scoped>

</style>