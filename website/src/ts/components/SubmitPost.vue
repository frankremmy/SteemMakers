<template>
	<div class="container article">
		<div class="row">
			<form id="addArticle" action="src/submitpost.php" style="width: 100%">
				<fieldset :disabled="isFormDisabled">
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
											<input class="form-check-input" type="checkbox" :value="keyword" v-model="selectedKeywords">
											{{keyword}}
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<button type="button" class="btn btn-primary" v-on:click="submit" :disabled="!isValid">
							<div v-if="isSubmitting" >
								<spinner size="15px" square-color="white" style="display: inline-block; margin-right: 5px; margin-bottom: -7px;"></spinner>
								<span style="display: inline-block;">Submitting</span>
							</div>
							<div v-else>
								Submit
							</div>
						</button>
					</div>
					<div class="form-group">
						<ul>
							<li v-for='(message, index) in submissionMessages' :key='index' v-bind:class="message.classes">{{ message.text }}</li>
						</ul>
					</div>
				</fieldset>
			</form>
			<div class="form-group">
				<button type="button" class="btn btn-primary" v-on:click="submitAnother" :hidden="!isFormDisabled">
					Submit another article
				</button>
			</div>
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
				isFormDisabled: false,
				isValidating: false,
				isValid: false,
				isSubmitting: false,
				validationMessages: [] as {text: string, classes :string}[],
				categories: [] as {name: string, visible: boolean, keywords :string[]}[],
				selectedCategory:'',
				selectedKeywords: [],
				submissionMessages: [] as {text: string, classes :string}[]
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
				this.OnInformationChanged();
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
			author: function(val)
			{
				this.OnInformationChanged();
			},
			permlink: function(val)
			{
				this.OnInformationChanged();
			},
			selectedCategory: function(val)
			{
				for(let i=0; i < this.categories.length; i++)
				{
					this.categories[i].visible = (this.categories[i].name == val);
				}

				this.selectedKeywords = [];
			}
		},
		methods:
		{
			validate()
			{
				this.isValidating = true;
				this.isValid = false;
				this.databaseCheckPassed = false;
				this.articleCheckPassed = false;

				// Clean up previous results
				while(this.validationMessages.length > 0)
				{
					this.validationMessages.pop();
				}
				this.article = null;

				// Verify if article not present in DB
				axios.post("api/v1/verifyarticle.php", "author=" + this.author + "&permlink=" + this.permlink).then(
					(response) =>
					{
						let result = JSON.parse(response.request.response);
						if(result.type == 'success')
						{
							this.validationMessages.push({text: 'New article, not present in the database.', classes: 'text-success'});
							this.databaseCheckPassed = true;
						}
						else
						{
							this.validationMessages.push({text: result.message, classes: 'text-danger'});
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
					if(this.articleCheckPassed && this.databaseCheckPassed)
					{
						this.isValid = true;
					}
					this.isValidating = false;
				}
			},
			submit()
			{
				this.isSubmitting = true;

				// Clean up previous messages
				while(this.submissionMessages.length > 0)
				{
					this.submissionMessages.pop();
				}

				if(this.selectedKeywords.length === 0)
				{
					this.submissionMessages.push({text: 'Select at least one keyword.', classes: 'text-danger'});
					return false;
				}

				axios.post("api/v1/submitpost.php", "author=" + this.author + "&permlink=" + this.permlink + "&category=" + this.selectedCategory + "&keywords=" + this.selectedKeywords).then(
					(response) =>
					{
						this.isSubmitting = false;
						this.submissionMessages.push({text: 'Article successfully committed.', classes: 'text-success'});
						this.isFormDisabled = true;
					}, (error) =>
					{
						this.isSubmitting = false;
						if (error.responseText !== '')
						{
							this.submissionMessages.push({text: 'An error occured while submitting to the database: ' + error.responseText, classes: 'text-danger'});
						}
						else
						{
							this.submissionMessages.push({text: 'Oops! An error occured and your entry was not added.', classes: 'text-danger'});
						}
					});

				return true;
			},
			submitAnother()
			{
				this.link = '';
				this.linkClasses = '';
				this.author = '';
				this.authorClasses = '';
				this.permlink = '';
				this.permlinkClasses = '';
				this.validationMessages = [];
				this.submissionMessages = [];
				this.selectedKeywords = [];
				this.article = null;
				this.isFormDisabled = false;
			},
			OnInformationChanged()
			{
				this.isValid = false;
				this.validationMessages = [];
				this.authorClasses = '';
				this.permlinkClasses = '';
				this.article = null;
			}
		}
	});
</script>


<style scoped>

</style>