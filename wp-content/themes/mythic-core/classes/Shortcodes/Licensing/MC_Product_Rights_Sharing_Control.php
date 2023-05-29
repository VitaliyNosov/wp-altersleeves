<?php

namespace Mythic_Core\Shortcodes\Licensing;

use Mythic_Core\Abstracts\MC_Shortcode;
use Mythic_Core\Display\MC_Template_Parts;

/**
 * Class MC_Product_Rights_Sharing_Control
 *
 * @package Mythic_Core\Shortcodes\ProductRightsSharing
 */
class MC_Product_Rights_Sharing_Control extends MC_Shortcode {
    
    public const SHORTCODE = 'mc_rights_sharing_control';
    
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
        return MC_Template_Parts::get( 'rights-sharing-control', 'rights-sharing-control' );
    }
    
}