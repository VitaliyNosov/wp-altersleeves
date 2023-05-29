<template>
	<div class="general-page-sidebar">
		<ul class="nav flex-column" v-if="importedData.side_menu">
			<li class="nav-item" v-for="(side_menu_signle, index) in importedData.side_menu" :key="index">
				<template v-if="index == importedData.side_menu.length - 1">
					<a class="nav-link active" aria-current="page" v-bind:href="side_menu_signle.url">{{ side_menu_signle.title }}</a>
				</template>
				<template v-else>
					<a class="nav-link" v-bind:href="side_menu.url">{{ side_menu.title }}</a>
				</template>
			</li>
		</ul>
	</div>
</template>

<script>
	export default {
		props: {
			importedData: Object
		}
	}
</script>