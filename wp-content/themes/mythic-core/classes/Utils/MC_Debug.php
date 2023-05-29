<?php

namespace Mythic_Core\Utils;

/**
 * Class MC_Debug
 *
 * @package Mythic_Core\Utils
 */
class MC_Debug {
    
    /**
     * @param      $data
     * @param bool $exit
     */
    public static function print( $data ) {
        print( "<pre>".print_r( $data, true )."</pre>" );
    }
    
}