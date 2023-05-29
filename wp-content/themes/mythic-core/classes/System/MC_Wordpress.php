<?php

namespace Mythic_Core\System;

/**
 * Class MC_Wordpress
 *
 * @package Mythic_Core\System
 */
class MC_Wordpress {
    
    /**
     * MC_Wordpress constructor.
     */
    public function __construct() {
        add_action( 'wp', [ $this, 'add_post_type_support' ] );
        add_action( 'wp', [ $this, 'remove_theme_support' ], 100 );
    }
    
    public function add_post_type_support() {
        add_post_type_support( 'product', 'author' );
    }
    
    public function remove_theme_support() {
        remove_theme_support( 'wc-product-gallery-zoom' );
    }
    
}