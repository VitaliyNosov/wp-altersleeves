<?php

namespace Mythic\Abstracts;

class MC2_Settings_Page {
    
    public static $settings_pages = [];
    
    /**
     * Uses Advanced Custom Fields
     */
    public function __construct() {
        if( !empty( static::$settings_pages ) && function_exists( 'acf_add_options_page' ) ) {
            foreach( static::$settings_pages as $settings_page ) {
                acf_add_options_page( $settings_page );
            }
        }
    }
    
}