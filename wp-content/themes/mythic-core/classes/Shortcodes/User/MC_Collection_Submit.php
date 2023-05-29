<?php

namespace Mythic_Core\Shortcodes\User;

use MC_Render;
use Mythic_Core\Abstracts\MC_Shortcode;

/**
 * Class MC_Alter_Submit
 *
 * @package Mythic_Core\Shortcodes\Creator
 */
class MC_Collection_Submit extends MC_Shortcode {
    
    // @todo update to manage_collection
    const SHORTCODE = 'mc_submit_collection';
    
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
        MC_Render::templatePart( 'creator/collection/collection-submit' );
        
        return ob_get_clean();
    }
    
}