<template>
	<div class="sort-container d-flex align-items-center">
		<span>Sort by</span>
		<select class="form-control sort-by-control" id="results_sort" v-if="importedData.nav_sort">
			<option v-for="(nav_sort_single, index) in importedData.nav_sort" :key="index" v-bind:value="nav_sort_single.value">
				{{ nav_sort_single.title }}
			</option>
		</select>
	</div>
</template>

<script>
	export default {
		props: {
			importedData: Object
		}
	}
</script>