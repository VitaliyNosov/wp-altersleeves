<?php
add_action('wp_enqueue_scripts', function () {
	wp_enqueue_script('ma-vue', get_stylesheet_directory_uri() . '/assets/js/app.js', [], 0, 1);
	wp_enqueue_script('ma-js', get_stylesheet_directory_uri() . '/assets/js/all.js', [], 0, 1);
	wp_enqueue_style('ma-css', get_stylesheet_directory_uri() . '/assets/css/all.css');
});

add_action( 'after_setup_theme', 'mythic_init', 1 );

if( !function_exists('mythic_init') ) {
	function mythic_init() {
		require 'vendor/autoload.php';
		MC2_init_classes();
	}
}

function MC2_autoloader($class , $dir = TEMPLATE_CLASSES_DIR, $namespace = MC2_NAMESPACE) {
	if (strpos($class, $namespace) !== 0) return;
	$class = str_replace($namespace, '', $class);
	$class = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
	$path = $dir.$class;
	if (file_exists($path)) require_once($path);
}
spl_autoload_register('MC2_autoloader');

function MC2_init_classes( $dir = TEMPLATE_FUNCTIONS_DIR, $namespace = MC2_NAMESPACE.'/Functions' ) {
	$sub_dirs = [ '/*', '/*/*', '/*/*/*', '/*/*/*/*' ];
	foreach( $sub_dirs as $sub_dir ) {
		foreach( glob( $dir.$sub_dir.'.php' ) as $filename ) {
			if( strpos($filename, '_Transaction_') !== false ) continue;
			$class     = $namespace.str_replace( $dir, '', $filename );
			$class     = str_replace( '//', '/', $class );
			$class = str_replace( '/', '\\', $class );
			$class     = str_replace( '.php', '', $class );
			if( !file_exists($filename) ) continue;
			require_once $filename;
			new $class();
		}
	}
}

/** Constants **/
define( 'SITE_URL', get_site_url() );
define( 'TEMPLATE_DIR', get_template_directory() );
define( 'TEMPLATE_CLASSES_DIR', TEMPLATE_DIR.'/classes' );
define( 'TEMPLATE_FUNCTIONS_DIR', TEMPLATE_CLASSES_DIR.'/Functions' );
define( 'TEMPLATE_URI', get_template_directory_uri() );
define( 'STYLESHEET_DIR', get_stylesheet_directory() );
define( 'STYLESHEET_URI', get_stylesheet_directory_uri() );
define( 'WOO_ACTIVE', class_exists( 'WooCommerce' ) );

define( 'MC2_NAMESPACE', 'Mythic' );
define( 'MC2_TEXT_DOMAIN', get_template() );

