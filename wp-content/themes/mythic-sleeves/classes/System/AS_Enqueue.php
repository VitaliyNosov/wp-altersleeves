<?php

namespace Alter_Sleeves\System;

use MC_Mask_Cutter_Functions;
use MC_Url;

/**
 * Class MC_Enqueue
 *
 * @package Alter_Sleeves\System
 */
class AS_Enqueue {
    
    /**
     * MC_Enqueue constructor.
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'add' ], 1 );
    }
    
    public function add() {
        add_filter( 'mc_styles', [ AS_Styles::class, 'add' ], 10, 1 );
        add_filter( 'mc_scripts', [ AS_Scripts::class, 'add' ], 10, 1 );
    }
    
    public static function enqueueCoreJs() {
        // @Todo Clean up this ungodly mess
        
        if(
            MC_Url::contains( 'submit' ) || MC_Url::contains( 'cutter' ) ) {
            $js_dir = AS_URI_JS.'/cutter/';
            wp_register_script( 'as-cutter', AS_URI_JS.'/cutter/script.js', [], '1.0.0', true );
            $args = [
                'js_dir' => $js_dir,
            ];
            wp_localize_script( 'as-cutter', 'cutter', $args );
            $args = [
                'maskMaps' => MC_Mask_Cutter_Functions::data(),
            ];
            wp_localize_script( 'as-cutter', 'input', $args );
            wp_enqueue_script( 'as-cutter' );
        }
    }
    
}