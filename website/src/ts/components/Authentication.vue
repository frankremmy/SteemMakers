<template>
	<div class="container article">
		<div class="row">
			<div style="width: 100%; position: relative;">
				<center>
					<p>
						You will be redirected automatically.<br/>
						If not <a href="index.php">click here</a>.
					</p>
				</center>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
	import Vue from "vue";

	export default Vue.extend({
		mounted: function ()
		{
			this.$store.commit('authenticated', 
				{
					username: this.$route.query.username,
					accessToken: this.$route.query.access_token
				});
			let expires_in = this.$route.query.expires_in;

			this.$router.push('home');
		}
	});
</script>

<style scoped>

</style>