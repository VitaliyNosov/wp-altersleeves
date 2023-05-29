<?php

namespace Mythic_Gaming\Display;

use MC_Render;
use Mythic_Core\Display\MC_Template_Parts;

/**
 * Class MG_Render
 *
 * @package Mythic_Gaming\Display
 */
class MG_Render extends MC_Render {

    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     */
    public static function campaign( $slug = '', $name = '', $args = [] ) {
        echo MC_Template_Parts::get( 'campaign/'.$slug, $name, $args );
    }

}