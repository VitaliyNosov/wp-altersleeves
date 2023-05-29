<template>
	<nav class="page-breadcrumbs" v-if="importedData.breadcrumbs">
		<template v-for="(breadcrumb, index) in importedData.breadcrumbs" :key="index">
			<template v-if="index == importedData.breadcrumbs.length - 1">{{ breadcrumb }}</template>
			<template v-else>
				<a v-bind:href="breadcrumb.url">{{ breadcrumb.title }}</a>
				<span class="devider">&gt;</span>
			</template>
		</template>
	</nav>
</template>

<script>
	export default {
		props: {
			importedData: Object
		}
	}
</script>