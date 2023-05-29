require('jquery');
require('slick-carousel');
require('bootstrap');

require('./header/main');
require('./pages/single-product');
require('./pages/login');
require('./pages/search-results');
require('./pages/artist-profile');
require('./pages/home');
require('./pages/bureau');

import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter);

const routes = [];

const router = new VueRouter({
    mode: 'history',
    routes,
    linkExactActiveClass: "active",
});

const app = new Vue({
    router
}).$mount('#app');
