<?php

namespace Mythic_Core\Utils;

/**
 * Class MC_System
 *
 * @package Mythic_Core\Utils
 */
class MC_System {
    
    /**
     * @param string $dir
     * @param        $require_once
     */
    public static function requireDir( $dir = '', $require_once = true ) {
        if( empty( $dir ) ) return;
        if( !file_exists( $dir ) && !file_exists( ABSPATH.$dir ) ) return;
        if( !file_exists( $dir ) ) $dir = ABSPATH.$dir;
        // Require classes and functions
        $sub_dirs = [ '/*', '/*/*', '/*/*/*', '/*/*/*/*' ];
        foreach( $sub_dirs as $sub_dir ) {
            foreach( glob( $dir.$sub_dir.'.php' ) as $filename ) {
                if( $require_once ) {
                    require_once $filename;
                } else {
                    require $filename;
                }
            }
        }
    }
    
    /**
     * @param string $url
     */
    public static function redirect( $url = '' ) {
        if( empty( $url ) || !MC_Url::is( $url ) ) return;
        wp_redirect( $url );
        die();
    }
    
}