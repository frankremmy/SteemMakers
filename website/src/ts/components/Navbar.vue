<template>
	<b-navbar toggleable="md" type="dark" variant="primary" sticky>
		<div class="container"> 

		<b-navbar-brand href="#/Home">
			<img src="img/navbar-logo.png">
		</b-navbar-brand>

		<b-navbar-toggle target="nav_collapse"></b-navbar-toggle>

		<b-collapse is-nav id="nav_collapse">
			<b-navbar-nav class="ml-auto navbar-items-bottom">
				<b-nav-item to="/Blog">Blog</b-nav-item>
				<b-nav-item-dropdown text="Info" right>
					<b-dropdown-item to="/About">About</b-dropdown-item>
					<b-dropdown-item to="/Aaq">FAQ</b-dropdown-item>
					<b-dropdown-item to="/Delegation">Delegate to us</b-dropdown-item>
					<b-dropdown-item to="/Trail">Follow our trail</b-dropdown-item>
					<b-dropdown-item to="/Notices">Notices</b-dropdown-item>
				</b-nav-item-dropdown>
				<b-nav-item to="/Contact">Contact</b-nav-item>
				<b-nav-item v-if="!isLoggedIn" @click='login()'>Login</b-nav-item>
				<b-nav-item-dropdown v-if="isLoggedIn" :text="username" right >
					<b-dropdown-item v-if="isReviewer" to="/SubmitPost">Submit post</b-dropdown-item>
					<b-dropdown-item @click='logout()'>Logout</b-dropdown-item>
				</b-nav-item-dropdown>
				<b-nav-item v-if="isLoggedIn" class="nav-item d-none d-md-block"><img :src="profileImage" height="40" width="40" style="margin-right: 10px; border-radius: 5px;"></b-nav-item>
			</b-navbar-nav>
		</b-collapse>
		</div>
	</b-navbar>
</template>

<script lang="ts">
	import Vue from "vue";
	
	export default Vue.extend({
		data: function ()
		{
			return {
				
			}
		},
		computed:
		{
			isLoggedIn() :boolean { return this.$store.state.isLoggedIn; },
			isReviewer() :boolean { return this.$store.state.isReviewer; },
			profileImage() :boolean { return this.$store.state.profileImage; },
			username() :string { return this.$store.state.username; }
		},
		methods:
		{
			login()
			{
				this.$store.dispatch('login');
			},
			logout()
			{
				this.$store.dispatch('logout');
			}
		}
	});
</script>

<style scoped>
	.navbar
	{
		padding:0rem 0.75rem;
	}

	.navbar-brand
	{
		padding: 0;
		margin:0;
		background-color: black;
	}

@media (min-width: 768px)
{
	/* 	Copy of .align-items-end
		To be able to apply on large screens and not apply on small screens (causes right align in hamburger) */
	 .navbar-items-bottom
	{
		-webkit-box-align:end !important;
		-ms-flex-align:end !important;
		align-items:flex-end !important;
	} 
	
	.navbar-collapse > ul 
	{
		height: 56px;
		
	}
}
</style>