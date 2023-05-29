<?php

namespace Mythic_Core\Shortcodes\Store;

use Mythic_Core\Abstracts\MC_Shortcode;
use Mythic_Core\Display\MC_Render;

class MC_Example_Alter extends MC_Shortcode {
    
    public const SHORTCODE = 'example_alters';
    
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
        ob_start();
        MC_Render::templatePart( 'demo', 'alter' );
        return ob_get_clean();
    }
    
}