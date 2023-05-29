<?php

namespace Mythic\Helpers;

/**
 * Class MC2_Debug
 *
 * @package Mythic\Helpers
 */
class MC2_Debug {

    /**
     * @param      $data
     * @param bool $exit
     */
    public static function print( $data) {
        print( "<pre>".print_r( $data, true )."</pre>" );
    }

}