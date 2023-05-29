<?php

namespace Mythic_Core\Loader;

/**
 * Class MC_Vendor_Loader
 *
 * @package Mythic_Core\Loader
 */
class MC_Vendor_Loader {
    
    /**
     * MC_Vendor_Loader constructor.
     */
    public function __construct() {
        $vendor_dir      = '/vendor';
        $vendor_autoload = '/vendor/autoload.php';
        $themes          = [ get_template_directory(), get_stylesheet_directory() ];
        $message         = 'Vendor files not found. Please contact '.get_bloginfo( 'admin_email' );
        foreach( $themes as $theme ) {
            if( !file_exists( $theme.$vendor_dir ) ) continue;
            if( !file_exists( $autoload = $theme.$vendor_autoload ) ) die( $message );
            require_once $autoload;
        }
    }
    
}