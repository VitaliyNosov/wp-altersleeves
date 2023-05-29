<?php

namespace Mythic_Core\Display;

use Windwalker\Dom\DomElement;

/**
 * Class MC_Loading
 *
 * @package Mythic_Core\Display
 */
class MC_Display_Load {
    
    public static function spinner() {
        echo new DomElement( 'div', '', [ 'class' => 'load-icon spinner' ] );
    }
    
}