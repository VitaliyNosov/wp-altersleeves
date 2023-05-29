import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter);

Vue.component('maHeader', require('./partials/global/Header.vue').default);
Vue.component('maFooter', require('./partials/global/Footer.vue').default);
Vue.component('BannerHome', require('./partials/promotions/banner-home.vue').default);
Vue.component('SliderProductsBigSingleAndHalf', require('./partials/sliders/products-big-single-and-half.vue').default);
Vue.component('HomepageSortedProducts', require('./partials/homepage/sorted-products.vue').default);
Vue.component('WooCommerceProductsFeaturedSlider', require('./partials/woocommerce/products/featured-with-logo-text-and-slider.vue').default);
Vue.component('SliderArticles', require('./partials/sliders/articles.vue').default);
Vue.component('SliderCreators', require('./partials/sliders/creators.vue').default);
Vue.component('SliderProducts', require('./partials/sliders/products.vue').default);
Vue.component('PromotionJoinUs', require('./partials/promotions/promotion-join-us.vue').default);
Vue.component('NavContainer', require('./partials/page-nav/nav-container.vue').default);
Vue.component('Breadcrumbs', require('./partials/page-nav/breadcrumbs.vue').default);
Vue.component('NavSort', require('./partials/page-nav/nav-sort.vue').default);
Vue.component('NavTags', require('./partials/page-nav/nav-tags.vue').default);
Vue.component('SideMenu', require('./partials/sidebar/side-menu.vue').default);

import HomepageView from './page-templates/Homepage.vue';
import LoginView from './page-templates/Login.vue';
import PageView from './page-templates/Page.vue';

const routes = [
	{
		path: '/',
		name: 'HomepageView',
		component: HomepageView
	},
	{
		path: '/login',
		name: 'LoginView',
		component: LoginView
	},
	{
		path: '/page',
		name: 'PageView',
		component: PageView
	},
];

const router = new VueRouter({
	mode: 'history',
	routes,
	linkExactActiveClass: "active",
});

const app = new Vue({
	router
}).$mount('#app');
