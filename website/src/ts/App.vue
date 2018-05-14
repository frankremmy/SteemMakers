<template>
	<div id="app">
		<navbar></navbar>
		<section class="container-fluid flex-grow">
			<router-view></router-view>
		</section>
	</div>
</template>

<script>
	import Navbar from './Components/Navbar'
	export default {
		name: 'app',
		components: {Navbar},
	}
</script>

<style scoped>
</style>