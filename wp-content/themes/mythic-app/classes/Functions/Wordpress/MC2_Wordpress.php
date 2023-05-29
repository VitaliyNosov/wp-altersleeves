<?php

namespace Mythic\Functions\Wordpress;

/**
 * Class MC2_Wordpress
 *
 * @package Mythic\System
 */
class MC2_Wordpress {

    /**
     * MC2_Wordpress constructor.
     */
    public function __construct() {
        add_action( 'wp', [ $this, 'add_post_type_support'] );
        add_action( 'wp', [ $this, 'add_theme_support' ], 100 );
        add_action( 'wp', [ $this, 'remove_theme_support' ], 100 );
    }


    public function add_post_type_support() {
        add_post_type_support( 'product', 'author' );
    }
    
    public function add_theme_support() {
    }
    
    public function remove_theme_support() {
    }

}