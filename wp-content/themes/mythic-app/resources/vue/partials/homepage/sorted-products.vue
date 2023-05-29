<template>
	<div class="sorted-products">
		<div class="lg-container">
			<div class="row">
				<div class="col-xl-8">
					<div class="row">
						<template v-if="importedData.bestsellers.products.length">
							<ProductsFourAsSquare :imported-data="importedData.bestsellers"></ProductsFourAsSquare>
						</template>
						<template v-if="importedData.sales.products.length">
							<ProductsFourAsSquare :imported-data="importedData.sales"></ProductsFourAsSquare>
						</template>
					</div>
					<div class="row d-none d-md-flex">
						<template v-if="importedData.categoryOne.products.length">
							<ProductsFourAsSquare :imported-data="importedData.categoryOne"></ProductsFourAsSquare>
						</template>
						<template v-if="importedData.categoryTwo.products.length">
							<ProductsFourAsSquare :imported-data="importedData.categoryTwo"></ProductsFourAsSquare>
						</template>
					</div>
				</div>
				<div class="col-xl-4">
					<div class="row h-100">
						<template v-if="importedData.bannerUnlocked.html.length">
							<BannerUnlockedData :imported-data="importedData.bannerUnlocked"></BannerUnlockedData>
						</template>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import ProductsFourAsSquare from '../woocommerce/products/four-products-as-square'
	import BannerUnlockedData from '../promotions/banner-unlocked.vue'

	export default {
		props: {
			importedData: Object
		},
		components: {
			ProductsFourAsSquare,
			BannerUnlockedData
		}
	}
</script>