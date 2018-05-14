<template>
	<div class="Site bg-secondary">
		<navbar></navbar>
		<div class="Site-content">
			<router-view></router-view>
		</div>

		<footercomp></footercomp>
	</div>
</template>

<script>
	import Navbar from './Components/Navbar'
	import Footercomp from './Components/Footercomp'
	export default {
		name: 'app',
		components: {Navbar, Footercomp},
	}
</script>

<style scoped>

</style>