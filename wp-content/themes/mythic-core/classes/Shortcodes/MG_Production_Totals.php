<?php

namespace Mythic_Core\Shortcodes;

use MC_User_Functions;
use Mythic_Core\Abstracts\MC_Shortcode;
use Mythic_Core\Display\MC_Render;

/**
 * Class MC_Backers_Remaining_Credits
 *
 * @package Mythic_Core\Shortcodes
 */
class MG_Production_Totals extends MC_Shortcode {
    
    /**
     * @return string
     */
    public function getShortcode() : string {
        return 'mg_production_totals';
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
        MC_Render::templatePart( 'campaign/mythic-frames/reports/production-totals' );
        $output = ob_get_clean();
        
        return $output ?? '';
    }
    
}