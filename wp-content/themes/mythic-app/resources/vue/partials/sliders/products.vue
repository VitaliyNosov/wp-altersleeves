<template>
	<div class="page-section">
		<div class="container-right">
			<div class="d-md-flex align-items-center justify-content-md-between text-center">
				<h2 class="page-section-title">{{ importedData.title }}</h2>
			</div>

			<div class="section-slider">
				<div class="product-card" v-for="(product, index) in importedData.products" :key="index">
					<div class="product-title">{{ product.title }}</div>
					<div class="product-thumbnail">
						<img v-bind:src="product.image" alt="product">
					</div>
					<div class="product-actions">
						<a v-bind:href="product.url" class="button-link">Details</a>
						<div class="add-to-cart">
							<div class="price">{{ product.price.usd }}</div>
							<a href="#" class="button-icon-primary-gradient">
								<i class="icon-cart"></i>
							</a>
						</div>
					</div>
				</div>
			</div>

			<div>
				<a v-bind:href="importedData.see_more_link.url" class="button button-link btn-see-more">{{ importedData.see_more_link.title }}</a>
			</div>
		</div>
	</div>
</template>

<script>
	import 'slick-carousel';

	export default {
		props: {
			importedData: Object
		},
		methods:{
			initSlider: function() {
				let sliderContainer = $('.section-slider');
				if(!sliderContainer.length) return;

				sliderContainer.each(function () {
					if($(this).hasClass('slick-initialized')) return;

					$(this).slick({
						slidesToShow: 4,
						arrows: false,
						dots: false,
						infinite: false,
						centerMode: false,
						responsive: [
							{
								breakpoint: 1200,
								settings: {
									fade: false,
									slidesToShow: 2.1
								}
							},
							{
								breakpoint: 768,
								settings: {
									fade: false,
									slidesToShow: 1.2
								}
							}
						]
					});
				});
			}
		},
		mounted(){
			this.initSlider()
		}
	}
</script>