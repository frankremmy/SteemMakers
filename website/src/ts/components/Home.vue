<template>
	<div class="container article">
		<template> 
			<div class="row" v-for="(article, index) in articles" :key="index">
				<div class="imageframe col-md-3">
					<div class="blog-image">
						<img :src="article.previewImage" style="border-radius: 5px;">
					</div>
				</div>
				<div class="col-md-9">
					<h5 class="font-weight-bold" style="margin-top:5px;"><router-link :to="{ name: 'Article', params: {author: article.author, permlink: article.permlink } }">{{article.title}}</router-link></h5>
					<div class="multiline-ellipsis">
						<p>{{article.previewBody}}</p>
					</div>
					<span class="metadata"><i>by <a :href="article.previewBody">{{article.author}}</a> on {{article.created | formatDate}}</i></span>
				</div>
			</div>
		</template>
	</div>
</template>

<script lang="ts">
	import Vue from "vue";
	import {getBlogArticles, formatDate} from "../utils/utils";
	
	export default Vue.extend({
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
.imageframe
{
	display: flex;
	align-items: center;
	padding: 0px;
}

.metadata
{
	margin-top: 10px;
	display: block;
}

.blog-image
{
	height:130px;
	text-align: center;
	line-height: 130px;
}

.blog-image img
{
	max-width:100%; max-height:100%; display: inline-block; margin: auto;
}

.multiline-ellipsis
{
	display: block;
	display: -webkit-box;
	max-width: 100%;
	height: 72px;
	line-height: 1.2;
	margin: 0 auto;
	font-size: 15px;
	-webkit-line-clamp: 4;
	-webkit-box-orient: vertical;
	overflow: hidden;
	text-overflow: ellipsis;
}
</style>