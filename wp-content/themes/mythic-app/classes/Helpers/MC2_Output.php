<?php

namespace Mythic\Helpers;

class MC2_Output {
    
    /**
     * @param string $s
     * @param array  $v
     * @param string $td
     *
     * @return string
     */
    public static function sprintf( string $s = '', array $v = [], string $td = MC2_TEXT_DOMAIN ) : string {
        if( empty( $v ) ) return $s;
        return vsprintf( __( $s, $td ), $v );
    }
    
}