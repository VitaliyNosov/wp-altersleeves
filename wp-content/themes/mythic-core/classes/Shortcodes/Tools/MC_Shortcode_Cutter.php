<?php

namespace Mythic_Core\Shortcodes\Tools;

use Mythic_Core\Abstracts\MC_Shortcode;

/**
 * Class Cutter
 *
 * @package Mythic_Core\Shortcodes\Tools\Cutter
 */
class MC_Shortcode_Cutter extends MC_Shortcode {
    
    const SHORTCODE = 'mc_cutter';
    
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
        $printing_id = $atts['printing_id'] ?? 0;
        
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/tools/cutter/cutter.php' );
        
        return ob_get_clean();
    }
    
}