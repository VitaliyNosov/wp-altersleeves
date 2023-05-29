<?php

namespace Mythic_Core\Shortcodes\User;

use MC_Render;
use Mythic_Core\Abstracts\MC_Shortcode;

/**
 * Class MC_Alter_Favoriter
 *
 * @package Mythic_Core\Shortcodes\Creator
 */
class MC_Alter_Favoriter extends MC_Shortcode {
    
    const SHORTCODE = 'mc_alter_favoriter';
    
    /**
     * Returns the shortcode slug
     *
     * @return string
     */
    public function getShortcode() : string {
        return strtolower( self::SHORTCODE );
    }
    
    /**
     * Generates the shortcode output
     *
     * @param array  $args
     * @param string $content
     *
     * @return string
     */
    public function generate( $args = [], $content = '' ) : string {
        if( !is_user_logged_in() ) return '';
        
        ob_start();
        MC_Render::templatePart( 'users/content-creator/favoriter' );
        
        return ob_get_clean();
    }
    
}