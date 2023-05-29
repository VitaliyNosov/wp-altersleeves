<?php

namespace Mythic_Core\System;

use Mythic_Core\Display\MC_Render;

/**
 * Class MC_Social
 *
 * @package Mythic_Core\System
 */
class MC_Social {
    
    /**
     * @param false $readable
     *
     * @return string[]
     */
    public static function available( $readable = false ) : array {
        $socials = [
            'artstation',
            'facebook',
            'instagram',
            'twitch',
            'twitter',
            'youtube',
        ];
        if( $readable ) {
            foreach( $socials as $key => $social ) $socials[ $key ] = ucfirst( $social );
        }
        
        return $socials;
    }
    
    /**
     * @param array $args
     */
    public static function render( $args = [] ) {
        $args = [ 'socials' => apply_filters( 'mc_social_icons_filter', [] ) ];
        MC_Render::component( 'social-icons', '', $args );
    }
    
}