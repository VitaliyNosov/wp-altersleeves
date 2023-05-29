<template>
	<div class="lg-container">
		<div class="home-banner"
			 v-bind:style="{ background: importedData.background, padding: importedData.padding }">
			<div class="banner-text" v-html="importedData.html">
			</div>
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