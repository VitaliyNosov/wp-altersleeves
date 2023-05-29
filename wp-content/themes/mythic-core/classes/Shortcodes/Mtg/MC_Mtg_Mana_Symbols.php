<?php

namespace Mythic_Core\Shortcodes\Mtg;

use Mythic_Core\Abstracts\MC_Shortcode;
use Mythic_Core\Display\MC_Render;

/**
 * Class MC_Mtg_Mana_Symbols
 *
 * @package Mythic_Core\Shortcodes\Mtg
 */
class MC_Mtg_Mana_Symbols extends MC_Shortcode {
    
    /**
     * @return string
     */
    public function getShortcode() : string {
        return 'mtg_mana_symbols';
    }
    
    /**
     * @param array  $args
     * @param string $content
     *
     * @return string
     */
    public function generate( $args = [], $content = '' ) : string {
        $symbols = [
            'forest'   => 'sg',
            'island'   => 'su',
            'mountain' => 'sr',
            'plains'   => 'sw',
            'swamp'    => 'sb',
        
        ];
        ob_start();
        foreach( $symbols as $symbol ) {
            MC_Render::component( 'mtg', 'mana-symbol', [ 'symbol' => $symbol ] );
        }
        
        return ob_get_clean() ?? '';
    }
    
}
