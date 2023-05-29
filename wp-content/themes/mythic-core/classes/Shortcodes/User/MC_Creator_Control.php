<?php

namespace Mythic_Core\Shortcodes\User;

use Mythic_Core\Abstracts\MC_Shortcode;
use Mythic_Core\Display\MC_Template_Parts;
use Mythic_Core\Functions\MC_User_Functions;
use Mythic_Core\Users\MC_Affiliates;

/**
 * Class MC_Affiliates_Control
 *
 * @package Mythic_Core\Shortcodes\Affiliates
 */
class MC_Creator_Control extends MC_Shortcode {
    
    public const SHORTCODE = 'mc_affiliates_control';
    
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
        if( !MC_User_Functions::isAdmin() && !MC_Affiliates::is() ) return '';
        
        return MC_Template_Parts::get( 'affiliates', 'as-affiliates-control' );
    }
    
}
