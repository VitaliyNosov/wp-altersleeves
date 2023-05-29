<?php

namespace Mythic_Core\Utils;

/**
 * Class MC_Content
 *
 * @package Mythic_Core\Utils
 */
class MC_Content {
    
    /**
     * @param string $content
     *
     * @return false|string
     */
    public static function firstSentenceFromText( $content = '' ) {
        $content = strip_tags( $content );
        $pos     = strpos( $content, '.' );
        $string  = substr( $content, 0, $pos + 1 );
        if( empty( $string ) ) return '';
        if( $pos !== false ) return $content;
        
        return $content;
    }
    
}