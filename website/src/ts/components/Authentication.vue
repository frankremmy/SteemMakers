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
			fetch("./api/v1/profiles.php?usernames=" + this.$route.query.username)
				.then(response => response.json())
				.then((data) =>
				{
					let isReviewer :boolean = false;
					if(data.length > 0 && data[0].reviewer == 1)
					{
						isReviewer = true;
					}
					this.$store.commit('authenticated', 
						{
							username: this.$route.query.username,
							accessToken: this.$route.query.access_token,
							reviewer: isReviewer
						});
					this.$router.push('home');
				})

		}
	});
</script>

<style scoped>

</style>