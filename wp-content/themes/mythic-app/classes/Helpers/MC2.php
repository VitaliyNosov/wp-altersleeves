<?php

namespace Mythic\Helpers;

class MC2 {
    
    /**
     * @param array $keys
     * @param array $arr
     *
     * @return bool
     */
    public static function array_keys_exists( array $keys, array $arr ) : bool {
        return MC2_Arrays::keys_exists( $keys, $arr );
    }
    
    /**
     * @param string $url
     * @param array  $data
     *
     * @return array
     */
    public static function post_data( string $url = '', array $data = [] ) : array {
        return MC2_Data::post( $url, $data );
    }
    
    /**
     * @param string $s
     * @param array  $v
     * @param string $td
     *
     * @return string
     */
    public static function sprintf( string $s = '', array $v = [], string $td = MC2_TEXT_DOMAIN ) : string {
        return MC2_Output::sprintf( $s, $v, $td );
    }
    
}