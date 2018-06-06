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
				<!--
				<div class="form-group" id="categoryselector">
					<label class="control-label">Category</label><br>
					<label class="radio-inline"><input type="radio" name="category" value="makers" id="MakersRadio" checked>Makers</label>
					<label class="radio-inline"><input type="radio" name="category" value="diy" id="DIYRadio">DIY</label>
				</div>
				<div id="keywordCheckBoxes">
					<label class="control-label">Keywords</label>
				<?php
					require_once('./src/database.php');

					$database = new Database();
						
					$query = "SELECT name FROM keywords_categories INNER JOIN keywords ON keywords_categories.keywords_id=keywords.id WHERE categories_id=1";
					$queryResult = $database->select($query);
					echo '<div class="form-group" id="makerscheckboxes">';
					for ($i = 0; $i < count($queryResult); $i++)
					{
						echo
						'<div class="form-check">
							<label class="form-check-label" for="defaultCheck1"><input class="form-check-input" type="checkbox" name="keywords[]" value="',$queryResult[$i]['name'],'" id="defaultCheck1">',$queryResult[$i]['name'],'</label>
						</div>';
					}
					echo '</div>';
					$query = "SELECT name FROM keywords_categories INNER JOIN keywords ON keywords_categories.keywords_id=keywords.id WHERE categories_id=2";
					$queryResult = $database->select($query);
					echo '<div class="form-group" id="diycheckboxes" style="display: none">';
					for ($i = 0; $i < count($queryResult); $i++)
					{
						echo
						'<div class="form-check">
							<label class="form-check-label" for="defaultCheck1"><input class="form-check-input" type="checkbox" name="keywords[]" value="" id="defaultCheck1">',$queryResult[$i]['name'],'</label>
						</div>';
					}
					echo '</div>';

				?>
				</div>
				-->

				<button type="submit" class="btn btn-primary" id="SubmitButton">Submit</button>
				<div class="form-group">
					<br>
					<ul id="submit-messages"></ul>
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
				validationMessages: [] as {text: string, classes :string}[],
			}
		},
		watch:
		{
			link: function (val)
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

				// var result = storyPreview(1, $('#authorBox').val(), $('#permlinkBox').val(), function(post)
				// {
				// 	if(post !== null)
				// 	{
				// 		$('#authorBox').removeClass('is-invalid').addClass('is-valid');
				// 		$('#permlinkBox').removeClass('is-invalid').addClass('is-valid');
				// 		$('#validation-messages').append('<li class="text-success">Article found on the blockchain</li>');

				// 		var timeDiff = new Date(post.cashout_time) - Date.now();
				// 		var diffDays = timeDiff / (1000 * 3600 * 24); 

				// 		if(diffDays > 1)
				// 		{
				// 			articleCheckPassed = true;
				// 			$('#validation-messages').append('<li class="text-success">Article is less than 6 days old</li>');
				// 		}
				// 		else
				// 		{
				// 			$('#validation-messages').append($('<li class="text-danger">Article is more than 6 days old</li>'));
				// 		}
				// 	}
				// 	else
				// 	{
				// 		$('#authorBox').removeClass('is-valid').addClass('is-invalid');
				// 		$('#permlinkBox').removeClass('is-valid').addClass('is-invalid');
				// 		$('#validation-messages').append($('<li class="text-danger">Article (combination author/permlink) not found on the blockchain</li>'));
				// 	}
				// 	articleCheckCompleted = true;
				// 	validationComplete();
				// });
			},
			validationComplete()
			{
				if (this.databaseCheckCompleted && this.articleCheckCompleted)
				{
					this.isValidating = false;
				}
			}
		}
	});
</script>


<style scoped>

</style>