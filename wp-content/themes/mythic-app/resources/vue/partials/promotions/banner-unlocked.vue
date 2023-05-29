<template>
	<div class="col-12 h-100">
		<div class="unlock-banner" v-html="importedData.html">
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