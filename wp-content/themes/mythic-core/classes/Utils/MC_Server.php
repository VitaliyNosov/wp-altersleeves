<?php

namespace Mythic_Core\Utils;

/**
 * Class MC_Server
 *
 * @package Mythic_Core\Utils
 */
class MC_Server {
    
    /**
     * Returns $_SERVER['request_uri'] with option to clean out the query string
     *
     * @param bool $clean
     *
     * @return string
     */
    public static function requestUri( $clean = true ) : string {
        $response = $_SERVER['REQUEST_URI'] ?? '';
        if( substr( $response, 0, 1 ) === '/' ) $response = substr( $response, 1 );
        if( empty( $clean ) ) return $response;
        
        return preg_replace( '/\?.*/', '', $response );
    }
    
    /**
     * @return string
     */
    public static function primaryPath() : string {
        $url = self::requestUri();
        $url = parse_url( $url );
        if( empty( $url['path'] ) ) return '';
        $path  = $url['path'];
        $paths = explode( '/', $path );
        if( !is_array( $paths ) ) return '';
        
        return $paths[0];
    }
    
}