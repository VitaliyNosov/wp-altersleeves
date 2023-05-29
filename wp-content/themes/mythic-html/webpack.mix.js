const mix = require('laravel-mix');
require('laravel-mix-icomoon');

mix.sass('resources/scss/all.scss', 'assets/css/all.css')
	.js('resources/js/all.js', 'assets/js/all.js')
	.sourceMaps()
	.webpackConfig({
		devtool: 'source-map'
	})
	.options({
		processCssUrls: false,
		terser: {
			extractComments: false,
		}
	})
	.autoload({
		jquery: ['$', 'window.jQuery']
	})
	.icomoon({
		inputPath: 'resources/icons',
		publicPath: 'assets',
		output: 'fonts/mg-icons',
		cssFile: 'resources/scss/_mg-icons.scss',
		reload: true,
		debug: false
	});
