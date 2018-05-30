<template>
	<b-navbar toggleable="md" type="dark" variant="primary" sticky>
		<div class="container"> 

		<b-navbar-brand href="#/Home" style="font-family: 'Rajdhani', sans-serif; font-size: 40px; line-height:40px; font-weight: 700; font-style: normal;">
			<img src="img/logo.png" width="40" height="40" style="margin:5px;">
			SteemMakers
		</b-navbar-brand>

		<b-navbar-toggle target="nav_collapse"></b-navbar-toggle>

		<b-collapse is-nav id="nav_collapse">
			<b-navbar-nav class="ml-auto navbar-items-bottom">
				<b-nav-item to="Blog">Blog</b-nav-item>
				<b-nav-item-dropdown text="Info" right>
					<b-dropdown-item to="about">About</b-dropdown-item>
					<b-dropdown-item to="faq">FAQ</b-dropdown-item>
					<b-dropdown-item to="delegation">Delegate to us</b-dropdown-item>
					<b-dropdown-item to="trail">Follow our trail</b-dropdown-item>
					<b-dropdown-item to="notices">Notices</b-dropdown-item>
				</b-nav-item-dropdown>
				<b-nav-item to="Contact">Contact</b-nav-item>
				<b-nav-item v-if="!isLoggedIn" @click='login()'>Login</b-nav-item>
				<b-nav-item-dropdown v-else :text="username" right >
					<b-dropdown-item @click='logout()'>Logout</b-dropdown-item>
				</b-nav-item-dropdown>
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
				this.$store.commit('logout');
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
	}
</style>