<template>
	<div class="page-navigation">
		<div class="lg-container d-flex justify-content-between">
			<template v-if="importedData.breadcrumbs">
				<Breadcrumbs :imported-data="importedData.breadcrumbs"/>
			</template>
			<template v-if="importedData.nav_tags">
				<NavTags :imported-data="importedData.nav_tags"/>
			</template>
			<template v-if="importedData.nav_sort">
				<NavSort :imported-data="importedData.nav_sort"/>
			</template>
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