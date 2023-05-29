<?php

namespace Mythic\Helpers;

class MC2_Arrays {
    
    /**
     * @param array $keys
     * @param array $arr
     *
     * @return bool
     */
    public static function keys_exists( array $keys, array $arr ) : bool {
        return !array_diff_key( array_flip( $keys ), $arr );
    }
    
}