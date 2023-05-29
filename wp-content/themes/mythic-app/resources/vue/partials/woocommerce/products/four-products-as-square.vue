<template>
	<div class="col-md-6">
		<div class="products-wrapper">
			<div class="section-title">{{ importedData.title }}</div>
			<div class="row">
				<div class="col-6" v-for="(product, index) in importedData.products" :key="index">
					<div class="product-item">
						<div class="product-thumbnail">
							<img v-bind:src="product.image" alt="product">
						</div>
						<div class="product-title">{{ product.title }}</div>
					</div>
				</div>
				<div class="col-12">
					<a v-bind:href="importedData.see_more_link.url" class="button button-link">{{ importedData.see_more_link.title }}</a>
				</div>
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