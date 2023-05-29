const mix = require('laravel-mix');

mix.sass('resources/scss/all.scss', 'assets/css/all.css')
	.js('resources/js/all.js', 'assets/js/all.js')
	.autoload({jquery: ['$', 'window.jQuery']})
	.sourceMaps()
	.webpackConfig({
		devtool: 'source-map'
	})
	.options({
		processCssUrls: false,
		terser: {
			extractComments: false,
		}
	});

mix.js('resources/vue/app.js', 'assets/js/app.js').vue();