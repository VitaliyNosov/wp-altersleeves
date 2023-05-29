<template>
	<div class="product-tags" v-if="importedData.nav_tags">
		<a v-bind:href="nav_tags_single.href" class="button button-dark button-sm"
		   v-for="(nav_tags_single, index) in importedData.nav_tags" :key="index">
			{{ nav_tags_single }}
		</a>
	</div>
</template>

<script>
	export default {
		props: {
			importedData: Object
		}
	}
</script>