<template>
	<div class="join-bunner">
		<div class="content">
			<div v-html="importedData.html"></div>
			<a href="#" class="button button-primary-gradient">Join Us</a>
		</div>
	</div>
</template>

<script>
	export default {
		props: {
			importedData: Object
		}
	}
</script>