<?php

namespace Mythic\Functions\Marketing;

use Mythic\Functions\Display\MC2_Render;

class MC2_Social_Functions {

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
    public static function render( array $args = [] ) {
        $args = [ 'socials' => apply_filters( 'mc_social_icons_filter', [] ) ];
        MC2_Render::component( 'social-icons', '', $args );
    }
    
    /**
     * Returns the brand's social links and channel for front end icons
     * @return \string[][]
     */
    public static function brand_socials() : array {
        // @todo Sergey - maybe just make the channel the key? No need to number them?
        return [
            0 =>
                [
                    'link'    => 'https://www.facebook.com/MythicGamingCo/',
                    'channel' => 'facebook',
                ],
            1 =>
                [
                    'link'    => 'https://twitter.com/MythicGaming_Co',
                    'channel' => 'twitter',
                ],
            2 =>
                [
                    'link'    => 'https://www.instagram.com/mythicgaming_co/',
                    'channel' => 'instagram',
                ],
            3 =>
                [
                    'link'    => 'https://www.twitch.tv/MythicGamingTV',
                    'channel' => 'twitch',
                ],
        ];
    }
    
    
}