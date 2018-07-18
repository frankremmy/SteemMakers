<template>
	<div class="container-fluid">
		<div class="row">
			<template v-if="blogEntry">
				<div class="imageframe col-md-3">
					<div class="blog-image">
						<img :src="blogEntry.previewImage" style="border-radius: 5px;">
					</div>
				</div>
				<div class="col-md-9">
					<h5 class="font-weight-bold" style="margin-top:5px;"><router-link :to="{ name: 'Article', params: {author: blogEntry.author, permlink: blogEntry.permlink } }">{{blogEntry.title}}</router-link></h5>
					<div class="multiline-ellipsis">
						<p>{{blogEntry.previewBody}}</p>
					</div>
					<p class="metadata"><i>by <a :href="AuthorBlogLink">{{blogEntry.author}}</a> on {{blogEntry.created | formatDate}}</i><span style="float:right;">${{blogEntry.payout | limitDecimals}}</span></p>
				</div>
			</template>
			<template v-else>
				<div style="margin: auto;">
					<spinner style="margin: 10px;"></spinner>
				</div>
			</template>
		</div>
	</div>
</template>

<script lang="ts">
	import Vue from "vue";
	import VueRouter from 'vue-router';
	import Spinner from './spinner.vue';
	import {formatDate} from "../utils/utils";
	import { BlogEntry } from "../blogentry";

	export default Vue.extend({
		props:[ 'blogEntry'],
		components: { Spinner },
		computed:
		{
			AuthorBlogLink() :string
			{
				return 'https://www.steemit.com/@' + this.blogEntry.author;
			},
		},
		filters: 
		{
			formatDate: function (date: Date)
			{
				return formatDate(date);
			},
			limitDecimals: function (value :number)
			{
				return value.toFixed(2);
			}
		}
	});
</script>

<style scoped>
.container-fluid
{
	padding: 0px;
}

.row
{
	margin: 0px;
	padding: 0px;
	border: 0px;
}

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
	text-align: left;
}

.blog-image
{
	height:130px;
	text-align: center;
	line-height: 130px;
	margin: auto;
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