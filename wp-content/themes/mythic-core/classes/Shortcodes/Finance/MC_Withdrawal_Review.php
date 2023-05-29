<?php

namespace Mythic_Core\Shortcodes\Finance;

use Mythic_Core\Abstracts\MC_Shortcode;
use Mythic_Core\Display\MC_Template_Parts;
use Mythic_Core\Functions\MC_User_Functions;

class MC_Withdrawal_Review extends MC_Shortcode {
    
    public const SHORTCODE = 'mc_withdrawal_review';
    
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
        if( !MC_User_Functions::isAdmin() && $_GET['access'] != 'scott' ) return '';
        
        return MC_Template_Parts::get( 'finance', 'withdrawal-review' );
    }
    
}