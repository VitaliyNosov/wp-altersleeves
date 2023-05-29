<?php

namespace Mythic_Core\Shortcodes;

use Mythic_Core\Abstracts\MC_Shortcode;
use Mythic_Core\Display\MC_Render;

/**
 * Class MC_Backers_Remaining_Credits
 *
 * @package Mythic_Core\Shortcodes
 */
class MG_Backers_Remaining_Credits extends MC_Shortcode {
    
    /**
     * @return string
     */
    public function getShortcode() : string {
        return 'mg_remaining_backers';
    }
    
    /**
     * @param array  $args
     * @param string $content
     *
     * @return string
     */
    public function generate( $args = [], $content = '' ) : string {
        ob_start();
        MC_Render::templatePart( 'campaign/mythic-frames/reports/users-remaining-credits' );
        $output = ob_get_clean();
        
        return $output ?? '';
    }
    
}