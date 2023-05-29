<?php

namespace Mythic_Core\Shortcodes\Store;

use MC_Woo_Order_Functions;
use Mythic_Core\Abstracts\MC_Shortcode;

class MC_Order_Notes extends MC_Shortcode {
    
    public const SHORTCODE = 'mc_order_notes';
    
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
        $notes = MC_Woo_Order_Functions::getNotes();
        ob_start();
        echo '<ul>';
        foreach( $notes as $note ) echo '<li>'.$note.'</li>';
        echo '</ul>';
        return ob_get_clean();
    }
    
}