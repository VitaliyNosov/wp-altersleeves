<?php

namespace Mythic\Helpers;

class MC2_Data {
    
    /**
     * @param string $url
     * @param array  $data
     *
     * @return array
     */
    public static function post( string $url = '', array $data = [] ) : array {
        if( empty( $url ) ) return [];
        $postdata = json_encode( $data );
        $ch       = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postdata );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' ] );
        $result = curl_exec( $ch );
        curl_close( $ch );
        if( empty( $result ) || !is_string( $result ) ) return [];
        $result = json_decode( $result, ARRAY_A );
        if( empty( $result ) ) return [];
        return $result;
    }
    
}