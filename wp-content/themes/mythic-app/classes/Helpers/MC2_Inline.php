<?php

namespace Mythic\Helpers;

/**
 * Class MC2_Inline
 *
 * @package Mythic\Helpers
 */
class MC2_Inline {

    /**
     * @param bool $condition
     * @param bool $echo
     *
     * @return string
     */
    public static function displayNone( bool $condition = false, $echo = false ) : string {
        $output = !( empty( $condition ) ) ? !empty( $space_in_front ) ? ' ' : ''.'style="display:none;"' : '';
        if( !empty( $echo ) && !empty( $output ) ) echo $output;
        return $output;
    }

}