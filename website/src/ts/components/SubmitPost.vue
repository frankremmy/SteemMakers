<template>
	<div class="container article">
		<div class="row">
			<form id="addArticle" action="src/submitpost.php" style="width: 100%">
				<div class="form-group">
					<label class="control-label">Article link</label>
					<input	type="text" 
							placeholder="Paste your link here"
							class="form-control input-lg"
							v-model="link"
							v-bind:class="linkClasses">
				</div>
				<div class="form-group">
					<label class="control-label">Author</label>
					<input	type="text"
							placeholder="Author"
							class="form-control input-lg"
							v-model="author"
							v-bind:class="authorClasses">
				</div>
				<div class="form-group">
					<label class="control-label">Permlink</label>
					<input	type="text" 
							placeholder="Permlink"
							class="form-control input-lg"
							v-model="permlink"
							v-bind:class="permlinkClasses">
				</div>
				<div class="form-group">
					<button type="button" class="btn btn-primary" v-on:click="validate">
						<div v-if="isValidating" >
							<spinner size="15px" square-color="white" style="display: inline-block; margin-right: 5px; margin-bottom: -7px;"></spinner>
							<span style="display: inline-block;">Validating</span>
						</div>
						<div v-else>
							Validate
						</div>
					</button>
				</div>
				<div class="form-group">
					<ul id="validation-messages">
						<li v-for='(message, index) in validationMessages' :key='index' v-bind:class="message.classes">{{ message.text }}</li>
					</ul>
					<div class="row">
						<ArticlePreview v-if="article !== null" v-bind:blogEntry="article"></ArticlePreview>
					</div>
				</div>
				<div class="form-group" id="categoryselector">
					<label class="control-label">Category</label><br>
					<div v-for="(category, index) in categories" :key="index" style="display:inline;">
						<label class="radio-inline" style="margin-left: 10px"><input type="radio" name="category" :value="category.name" v-model="selectedCategory">{{category.name}}</label>
					</div>
				</div>
				<div>
					<label class="control-label">Keywords</label>
					<div v-for="(category, index) in categories" :key="index" ref="checkboxGroups">
						<div class="form-group" :id="category.name" :hidden="!category.visible">
							<div class="form-check">
								<div v-for="(keyword, index) in category.keywords" :key="index">
									<label class="form-check-label">
										<input class="form-check-input" type="checkbox" :value="keyword" v-model="checkedKeywords">
										{{keyword}}
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<button type="submit" class="btn btn-primary" id="SubmitButton">Submit</button>
				<div class="form-group">
					<br>
					<ul id="submit-messages"></ul>
				</div>

				<div class="form-group">
					<button type="button" class="btn btn-primary" v-on:click="submit">
						<div v-if="isSubmitting" >
							<spinner size="15px" square-color="white" style="display: inline-block; margin-right: 5px; margin-bottom: -7px;"></spinner>
							<span style="display: inline-block;">Submitting</span>
						</div>
						<div v-else>
							Submit
						</div>
					</button>
				</div>
			</form>
		</div>
	</div>
</template>

<script lang="ts">
	import Vue from "vue";
	import axios from 'axios';
	import ArticlePreview from './ArticlePreview.vue';
	import {BlogEntry} from '../blogentry';
	import Spinner from './spinner.vue';
	import {createArticleAsync} from "../utils/utils";

	export default Vue.extend({
		components: { ArticlePreview, Spinner },
		data ()
		{
			return {
				article: null,
				author:'',
				authorClasses:'',
				articleCheckCompleted: false,
				articleCheckPassed: false,
				databaseCheckCompleted: false,
				databaseCheckPassed: false,
				link: '',
				linkClasses: '', 
				permlink: '',
				permlinkClasses: '',
				isAuthorValid: false,
				isValidating: false,
				isSubmitting: false,
				validationMessages: [] as {text: string, classes :string}[],
				categories: [] as {name: string, visible: boolean, keywords :string[]}[],
				selectedCategory:'',
				checkedKeywords: [],
			}
		},
		created: function ()
		{
			fetch("./api/v1/keywords.php")
				.then(response => response.json())
				.then((data) =>
				{
					for(let i = 0; i < data.length; i++)
					{
						this.categories.push({name: data[i].category, visible: false, keywords: data[i].keywords})
					}
					this.categories[0].visible = true;
					this.selectedCategory = this.categories[0].name;
				});
		},
		watch:
		{
			link: function(val)
			{
				if(val === '')
				{
					this.linkClasses = '';
				}
				else
				{
					var regex = new RegExp(".*\/@(.*)\/(.*)$");
					var matched = regex.exec(this.link);

					if(matched && matched.length === 3)
					{
						this.author = matched[1];
						this.permlink = matched[2];
						this.linkClasses = "is-valid";
					}
					else
					{
						this.linkClasses = "is-invalid";
					}
				}
			},
			selectedCategory: function(val)
			{
				for(let i=0; i < this.categories.length; i++)
				{
					this.categories[i].visible = (this.categories[i].name == val);
				}

				this.checkedKeywords = [];
			}
		},
		methods:
		{
			validate()
			{
				this.isValidating = true;

				while(this.validationMessages.length > 0)
				{
					this.validationMessages.pop();
				}
				// $('#article1').empty();

				// Verify if article not present in DB
				axios.get("src/verifyarticle.php", { params: {author: this.author, permlink: this.permlink}}).then(
					(response) =>
					{
						if(response.request.responseType === 'success')
						{
							this.validationMessages.push({text: 'New article, not present in the database.', classes: 'text-success'});
							this.databaseCheckPassed = true;
						}
						else
						{
							this.validationMessages.push({text: response.request.responseText, classes: 'text-danger'});
						}
						this.databaseCheckCompleted = true;
						this.validationComplete();
					}, (error) =>
					{
						if (error.responseText !== '')
						{
							this.validationMessages.push({text: 'An error occured while checking the database: ' + error.responseText, classes: 'text-danger'});
						}
						else
						{
							this.validationMessages.push({text: 'An error occured, the system couldn\'t check if your entry already exists.', classes: 'text-danger'});
						}
						this.databaseCheckCompleted = true;
						this.validationComplete();
					});


				// Verify if article exists on the blockchain
				createArticleAsync(this.author, this.permlink)
				.then(function(this: any, article: steem.Post)
				{
					this.authorClasses = 'is-valid';
					this.permlinkClasses = 'is-valid';
					this.validationMessages.push({text: 'Article found on the blockchain.', classes: 'text-success'});


					var timeDiff = new Date(article.cashout_time).getTime() - Date.now();
					var diffDays = timeDiff / (1000 * 3600 * 24); 
					if(diffDays > 1)
					{
						this.articleCheckPassed = true;
						this.validationMessages.push({text: 'Article is less than 6 days old.', classes: 'text-success'});
					}
					else
					{
						this.validationMessages.push({text: 'Article is more than 6 days old.', classes: 'text-danger'});
					}

					this.article = new BlogEntry(article);
					this.articleCheckCompleted = true;
					this.validationComplete();
				}.bind(this))
				.catch(function(this :any, error :any)
				{
					this.authorClasses = 'is-invalid';
					this.permlinkClasses = 'is-invalid';
					this.validationMessages.push({text: 'Article (combination author/permlink) not found on the blockchain.', classes: 'text-danger'});
					
					this.articleCheckCompleted = true;
				 	this.validationComplete();
				}.bind(this));
			},
			validationComplete()
			{
				if (this.databaseCheckCompleted && this.articleCheckCompleted)
				{
					this.isValidating = false;
				}
			},
			submit()
			{
				var auth = 'Basic ' + new Buffer('username' + ':' + 'password').toString('base64');
				axios.defaults.headers.common['Authorization'] = auth;

				axios({url: 'src/submitpost.php', data: 'testdata', method: 'POST' })
            .then(resp => {
                debugger;
            })
            .catch(err => {
                debugger;
            })
			}
		}
	});
</script>


<style scoped>

</style>