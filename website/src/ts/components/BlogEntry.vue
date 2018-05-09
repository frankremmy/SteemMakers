<template>
	<div>
		<div class="post-title">
			<h1>{{Title}}</h1>
		</div>
		<div class="post-meta">
			<span><i>by <a :href="AuthorBlogLink">{{Author}}</a> on {{CreationDateTime}}</i></span>
			<a :href="SteemitArticleLink" target="_blank" style="float: right;"><img class="media-button" src="img/steemit.png"></a>
			<a :href="BusyArticleLink" target="_blank" style="float: right;"><img class="media-button" src="img/busy.png"></a>
		</div>
		<div v-highlightjs v-html="BodyHTML"></div>
	</div>
</template>

<script lang="ts">
	import Vue from "vue";
	import VueRouter from 'vue-router';
	declare var hljs: any;

	Vue.use(VueRouter);
	import {createPostHtml, formatDate} from "../utils/utils";

	var router = new VueRouter({
		mode: 'history',
		routes: []
	});

	export default Vue.extend({
		router,
		data: function ()
		{
			return {
				ArticleURL: '',
				Author: '',
				CreationDateTime: '',
				BodyHTML: '<p>Loading...<p>',
				Title: '',
			}
		},
		directives:
		{
			highlightjs:
			{
				componentUpdated: function (el, binding)
				{
					let targets = el.querySelectorAll('code');
					let i;
					for (i = 0; i < targets.length; ++i)
					{
						hljs.highlightBlock(targets[i]);
					}
				}
			}
		},
		computed:
		{
			AuthorBlogLink() :string
			{
				return 'https://www.steemit.com/@' + this.Author;
			},
			SteemitArticleLink() :string
			{
				return 'https://steemit.com' + this.ArticleURL;
			},
			BusyArticleLink() :string
			{
				return 'https://busy.org' + this.ArticleURL;
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
				createPostHtml(this.$route.query.author, this.$route.query.permlink, (error, blogEntry) =>
				{
					this.ArticleURL = blogEntry.url;
					this.Author = blogEntry.author;
					this.BodyHTML = blogEntry.body;
					this.Title = blogEntry.title;
					
					var options = {year: "numeric", month: "long", day: "numeric", hour: '2-digit', minute:'2-digit', hour12: false};
					this.CreationDateTime = formatDate(blogEntry.created);

				});
			}
		}
	});
</script>

<style scoped>
.post-title
{
	padding:20px 10px;
}

.post-meta
{
	border-top: 1px solid #bdbdbd;
	border-bottom: 1px solid #bdbdbd;
	display: block;
	font-size: 13px;
	font-weight: 400;
	line-height: 21px;
	padding: 10px;
	margin-bottom: 10px;
}
</style>