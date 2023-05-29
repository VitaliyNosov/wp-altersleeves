<template>
	<div>
		<div class="lg-container">
			<div class="d-flex justify-content-center justify-content-md-between">
				<div class="hero-slider-title">{{ importedData.title }}</div>
				<div class="hero-slider-controls d-none d-md-block">
                    <span class="slider-control slide-prev">
                        <i class="icon-slider-arrow-left"></i>
                    </span>
					<span class="slider-control slide-next">
                        <i class="icon-slider-arrow-right"></i>
                    </span>
				</div>
			</div>
		</div>

		<div class="hero-slider">
			<div class="product-item hidden"></div>

			<div class="product-item" v-for="(product, index) in importedData.products" :key="index">
				<div class="row align-items-center">
					<div class="col-md text-left col-item-info">
						<div class="product-title">{{ product.title }}</div>
						<div class="price">{{ product.price.usd }}</div>
					</div>
					<div class="col-md-4">
						<div class="product-thumbnail">
							<img v-bind:src="product.image" alt="image">
						</div>
					</div>
					<div class="col-md col-item-actions">
						<div>
							<a href="#" class="button button-primary-gradient">Add to cart</a>
						</div>
						<div>
							<a href="#" class="button button-link">See details</a>
						</div>
					</div>
				</div>
			</div>

			<div class="product-item hidden"></div>
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
				let sliderContainer = $('.hero-slider');
				if(!sliderContainer.length) return;

				sliderContainer.each(function () {
					if($(this).hasClass('slick-initialized')) return;

					$(this).slick({
						slidesToShow: 3,
						arrows: true,
						dots: false,
						infinite: false,
						centerMode: false,
						variableWidth: true,
						appendArrows: $('.hero-slider-controls'),
						prevArrow: $('.slide-prev'),
						nextArrow: $('.slide-next'),
						speed: 500,
						initialSlide: 1,
						responsive: [
							{
								breakpoint: 1200,
								settings: {
									slidesToShow: 1,
									centerMode: true,
									centerPadding:'30px',
									variableWidth: false,
									initialSlide: 2
								}
							}
						]
					});
				});
			}
		},
		mounted(){
			this.initSlider()
		},
	}
</script>