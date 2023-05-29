<?php

namespace Mythic_Core\System;

use Mythic_Core\Utils\MC_Vars;

/**
 * Class MC_Nav
 *
 * @package Mythic_Core\System
 */
class MC_Nav {
    
    /**
     * MC_Nav constructor.
     */
    public function __construct() {
        add_action( 'init', [ $this, 'registerNavs' ] );
    }
    
    public function registerNavs() {
        register_nav_menu( 'dashboard', 'Dashboard Navigation' );
        register_nav_menu( 'header', 'Header Navigation' );
        register_nav_menu( 'sidebar', 'Sidebar Navigation' );
        register_nav_menu( 'user', 'User Navigation' );
    }
    
    /**
     * @param array $params
     */
    public static function displayNav( $params = [] ) {
        if( empty( $params['theme_location'] ) ) return;
        $location = $params['theme_location'];
        if( empty( $params['menu_id'] ) ) $params['menu_id'] = $location.'-nav';
        $args = [
            'menu_class'  => 'nav',
            'fallback_cb' => false,
        ];
        $args = MC_Vars::stringSafe( $args, $params );
        wp_nav_menu( $args );
    }
    
}