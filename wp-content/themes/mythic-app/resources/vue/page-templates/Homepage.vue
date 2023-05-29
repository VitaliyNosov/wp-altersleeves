<template>
	<div>
		<maHeader/>

		<main class="site-content home-page-content">

			<template v-if="top_banner">
				<BannerHome :imported-data="top_banner"/>
			</template>

			<template v-if="slider_products_big_single_and_half">
				<SliderProductsBigSingleAndHalf :imported-data="slider_products_big_single_and_half"/>
			</template>

			<template v-if="homepage_sorted_products">
				<HomepageSortedProducts :imported-data="homepage_sorted_products"/>
			</template>

			<template v-if="wc_featured_sliders">
				<WooCommerceProductsFeaturedSlider
					v-for="(wc_featured_slider, index) in wc_featured_sliders"
					:key="index"
					:imported-data="wc_featured_slider"
				/>
			</template>

			<template v-if="slider_articles">
				<SliderArticles :imported-data="slider_articles"/>
			</template>

			<template v-if="slider_creators">
				<SliderCreators :imported-data="slider_creators"/>
			</template>

			<template v-if="slider_products">
				<SliderProducts :imported-data="slider_products"/>
			</template>

			<template v-if="promotion_join_us">
				<PromotionJoinUs :imported-data="promotion_join_us"></PromotionJoinUs>
			</template>

		</main>

		<maFooter/>
	</div>
</template>

<script>
	export default {
		data: function () {
			return {
				top_banner: {
					html: "LOREM IPSUM",
					background: "#B72906",
					padding: "3rem"
				},
				slider_products_big_single_and_half: {
					title: 'NEW MYTHIC STAFF',
					products: [
						{
							id: 1,
							title: 'Non-CREATURE FRAMES PACK',
							price: {
								usd: '$9',
								eur: 8
							},
							image: ''
						},
						{
							id: 2,
							title: 'Non-CREATURE FRAMES PACK2',
							price: {
								usd: '$9',
								eur: 8
							},
							image: ''
						},
						{
							id: 3,
							title: 'Non-CREATURE FRAMES PACK3',
							price: {
								usd: '$9',
								eur: 8
							},
							image: ''
						}
					]
				},
				homepage_sorted_products: {
					bannerUnlocked: {
						html: "<p>New set</p><h3>Unlocked</h3>",
						background: ""
					},
					bestsellers: {
						title: "BESTSELLERS",
						see_more_link: {
							title: "SEE MORE",
							url: "/"
						},
						products: [
							{
								id: 1,
								title: 'Non-CREATURE FRAMES PACK',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							},
							{
								id: 2,
								title: 'Non-CREATURE FRAMES PACK2',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							},
							{
								id: 3,
								title: 'Non-CREATURE FRAMES PACK3',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							},
							{
								id: 4,
								title: 'Non-CREATURE FRAMES PACK4',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							}
						]
					},
					sales: {
						title: "SALES",
						see_more_link: {
							title: "SEE MORE",
							url: "/"
						},
						products: [
							{
								id: 1,
								title: 'Non-CREATURE FRAMES PACK',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							},
							{
								id: 2,
								title: 'Non-CREATURE FRAMES PACK2',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							},
							{
								id: 3,
								title: 'Non-CREATURE FRAMES PACK3',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							},
							{
								id: 4,
								title: 'Non-CREATURE FRAMES PACK4',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							}
						]
					},
					categoryOne: {
						title: "Category",
						see_more_link: {
							title: "SEE MORE",
							url: "/"
						},
						products: [
							{
								id: 1,
								title: 'Non-CREATURE FRAMES PACK',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							},
							{
								id: 2,
								title: 'Non-CREATURE FRAMES PACK2',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							},
							{
								id: 3,
								title: 'Non-CREATURE FRAMES PACK3',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							},
							{
								id: 4,
								title: 'Non-CREATURE FRAMES PACK4',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							}
						]
					},
					categoryTwo: {
						title: "Category",
						see_more_link: {
							title: "SEE MORE",
							url: "/"
						},
						products: [
							{
								id: 1,
								title: 'Non-CREATURE FRAMES PACK',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							},
							{
								id: 2,
								title: 'Non-CREATURE FRAMES PACK2',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							},
							{
								id: 3,
								title: 'Non-CREATURE FRAMES PACK3',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							},
							{
								id: 4,
								title: 'Non-CREATURE FRAMES PACK4',
								price: {
									usd: '$9',
									eur: 8
								},
								image: ''
							}
						]
					}
				},
				wc_featured_sliders: [
					{
						logo: '',
						title: 'ALTER SLEEVES',
						link: {
							title: 'ALTER SLEEVES',
							url: '#'
						},
						description: "<p>Aliquam quis ante porta, faucibus metus eu, auctor massa. Aenean id convallis neque. Donec sit amet auctor urna. Sed id mi quis arcu posuere rutrum sit amet ac dolor.                                 Curabitur dignissim dictum feugiat. Phasellus placerat lectus lacus, ac tempus mi sagittis sit amet. Duis a porttitor lectus, a vestibulum metus.</p>"+
							"<p>Donec egestas eget nisl at consectetur. Sed efficitur fringilla lorem, eu ornare nisi lobortis non. Proin luctus, quam elementum facilisis convallis, diam odio pellentesque felis, vel dapibus magna massa elementum magna. Donec lacinia nibh at feugiat pulvinar. Nullam a est tincidunt, vehicula ipsum at, vestibulum tortor. In diam elit, porta eget ex a, pretium pretium nibh. Suspendisse aliquet consectetur leo a hendrerit.</p>",
						see_more_link: {
							title: "SEE MORE ALTER SLEEVES PRODUCTS",
							url: '#'
						},
						products: [
							{
								id: 1,
								title: 'Non-CREATURE FRAMES PACK',
								price: {
									usd: '$9',
									eur: 8
								},
								url: '#',
								image: ''
							},
							{
								id: 2,
								title: 'Non-CREATURE FRAMES PACK2',
								price: {
									usd: '$9',
									eur: 8
								},
								url: '#',
								image: ''
							},
							{
								id: 3,
								title: 'Non-CREATURE FRAMES PACK3',
								price: {
									usd: '$9',
									eur: 8
								},
								url: '#',
								image: ''
							},
							{
								id: 4,
								title: 'Non-CREATURE FRAMES PACK4',
								price: {
									usd: '$9',
									eur: 8
								},
								url: '#',
								image: ''
							},
							{
								id: 5,
								title: 'Non-CREATURE FRAMES PACK5',
								price: {
									usd: '$9',
									eur: 8
								},
								url: '#',
								image: ''
							}
						]
					},
					{
						logo: '',
						title: 'MYTHIC FRAMES',
						link: {
							title: 'MYTHIC FRAMES',
							url: '#'
						},
						description: "<p>Aliquam quis ante porta, faucibus metus eu, auctor massa. Aenean id convallis neque. Donec sit amet auctor urna. Sed id mi quis arcu posuere rutrum sit amet ac dolor.                                 Curabitur dignissim dictum feugiat. Phasellus placerat lectus lacus, ac tempus mi sagittis sit amet. Duis a porttitor lectus, a vestibulum metus.</p>"+
							"<p>Donec egestas eget nisl at consectetur. Sed efficitur fringilla lorem, eu ornare nisi lobortis non. Proin luctus, quam elementum facilisis convallis, diam odio pellentesque felis, vel dapibus magna massa elementum magna. Donec lacinia nibh at feugiat pulvinar. Nullam a est tincidunt, vehicula ipsum at, vestibulum tortor. In diam elit, porta eget ex a, pretium pretium nibh. Suspendisse aliquet consectetur leo a hendrerit.</p>",
						see_more_link: {
							title: "SEE MORE MYTHIC FRAMES PRODUCTS",
							url: '#'
						},
						products: [
							{
								id: 1,
								title: 'Non-CREATURE FRAMES PACK',
								price: {
									usd: '$9',
									eur: 8
								},
								url: '#',
								image: ''
							},
							{
								id: 2,
								title: 'Non-CREATURE FRAMES PACK2',
								price: {
									usd: '$9',
									eur: 8
								},
								url: '#',
								image: ''
							},
							{
								id: 3,
								title: 'Non-CREATURE FRAMES PACK3',
								price: {
									usd: '$9',
									eur: 8
								},
								url: '#',
								image: ''
							},
							{
								id: 4,
								title: 'Non-CREATURE FRAMES PACK4',
								price: {
									usd: '$9',
									eur: 8
								},
								url: '#',
								image: ''
							},
							{
								id: 5,
								title: 'Non-CREATURE FRAMES PACK5',
								price: {
									usd: '$9',
									eur: 8
								},
								url: '#',
								image: ''
							}
						]
					}
				],
				slider_articles: {
					title: "NEWEST ARTICLES",
					see_more_link: {
						title: 'More articles',
						url: '#'
					},
					posts: [
						{
							title: 'Article title',
							image: '',
							read_more_link: {
								title: 'READ ARTICLE',
								url: '#'
							}
						},
						{
							title: 'Article title2',
							image: '',
							read_more_link: {
								title: 'READ ARTICLE',
								url: '#'
							}
						},
						{
							title: 'Article title3',
							image: '',
							read_more_link: {
								title: 'READ ARTICLE',
								url: '#'
							}
						}
					]
				},
				slider_creators: {
					title: "RECENTLY JOINED CrEators",
					see_more_link: {
						title: 'FIND MORE CREATORS',
						url: '#'
					},
					posts: [
						{
							title: 'Creators name/NICK',
							image: '',
							profile_link: {
								title: 'store.Artist.com',
								url: '#'
							},
							read_more_link: {
								title: 'View profile',
								url: '#'
							}
						},
						{
							title: 'Creators name/NICK',
							image: '',
							profile_link: {
								title: 'store.Artist.com',
								url: '#'
							},
							read_more_link: {
								title: 'View profile',
								url: '#'
							}
						},
						{
							title: 'Creators name/NICK',
							image: '',
							profile_link: {
								title: 'store.Artist.com',
								url: '#'
							},
							read_more_link: {
								title: 'View profile',
								url: '#'
							}
						},
						{
							title: 'Creators name/NICK',
							image: '',
							profile_link: {
								title: 'store.Artist.com',
								url: '#'
							},
							read_more_link: {
								title: 'View profile',
								url: '#'
							}
						},
					]
				},
				slider_products: {
					title: "Inspired by your browsing history",
					see_more_link: {
						title: 'SEE MORE',
						url: '#'
					},
					products: [
						{
							id: 1,
							title: 'Non-CREATURE FRAMES PACK',
							price: {
								usd: '$9',
								eur: 8
							},
							url: '#',
							image: ''
						},
						{
							id: 2,
							title: 'Non-CREATURE FRAMES PACK2',
							price: {
								usd: '$9',
								eur: 8
							},
							url: '#',
							image: ''
						},
						{
							id: 3,
							title: 'Non-CREATURE FRAMES PACK3',
							price: {
								usd: '$9',
								eur: 8
							},
							url: '#',
							image: ''
						},
						{
							id: 4,
							title: 'Non-CREATURE FRAMES PACK4',
							price: {
								usd: '$9',
								eur: 8
							},
							url: '#',
							image: ''
						},
						{
							id: 5,
							title: 'Non-CREATURE FRAMES PACK5',
							price: {
								usd: '$9',
								eur: 8
							},
							url: '#',
							image: ''
						}
					]
				},
				promotion_join_us: {
					html: "<h3>WOULD LIKE TO GET PERSONALIZED RECOMMENDATIONS?</h3>"
				}
			}
		}
	}
</script>
