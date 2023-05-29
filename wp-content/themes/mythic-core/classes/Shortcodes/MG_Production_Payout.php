<?php

namespace Mythic_Core\Shortcodes;

use Mythic_Core\Abstracts\MC_Shortcode;
use Mythic_Core\Display\MC_Render;
use Mythic_Core\Functions\MC_User_Functions;

/**
 * Class MC_Backers_Remaining_Credits
 *
 * @package Mythic_Core\Shortcodes
 */
class MG_Production_Payout extends MC_Shortcode {
    
    /**
     * @return string
     */
    public function getShortcode() : string {
        return 'mg_production_payout';
    }
    
    /**
     * @param array  $args
     * @param string $content
     *
     * @return string
     */
    public function generate( $args = [], $content = '' ) : string {
        if( !MC_User_Functions::isAdmin() ) return '';
        ob_start();
        MC_Render::templatePart( 'campaign/mythic-frames/reports/production-payout' );
        $output = ob_get_clean();
        
        return $output ?? '';
    }
    
}