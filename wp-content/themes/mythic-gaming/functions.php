<?php

if( !function_exists( 'mythic_gaming' ) ) {
    function mythic_gaming() {
        require_once 'classes/Loader/MG_Global_Loader.php';

        new Mythic_Gaming\Loader\MG_Global_Loader();
    }

    add_action( 'after_setup_theme', 'mythic_gaming', 3 );
}

function custom_redirects() {
    if ( is_front_page() ) {
        wp_redirect( 'https://store.mythicgaming.com' );
        die;
    }
}
add_action( 'template_redirect', 'custom_redirects' );