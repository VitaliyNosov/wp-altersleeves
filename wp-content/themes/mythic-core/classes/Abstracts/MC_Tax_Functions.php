<?php

namespace Mythic_Core\Abstracts;

use WP_Error;
use WP_Term;

/**
 * Class MC_Post_Type_Functions
 *
 * @package Mythic_Core\Abstracts
 */
abstract class MC_Tax_Functions {
    
    public static $tax = '';
    
    /**
     * @param array $params
     *
     * @return int|WP_Error|WP_Term[]
     */
    public static function query( $params = [] ) {
        $args = [
            'number'     => 0,
            'hide_empty' => false,
            'taxonomy'   => static::$tax,
        ];
        foreach( $params as $key => $param ) $args[ $key ] = $param;
        
        return get_terms( $args );
    }
    
}