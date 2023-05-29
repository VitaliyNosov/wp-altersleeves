<?php

namespace Mythic_Gaming\System;

/**
 * Class MG_Constants
 *
 * @package Mythic_Gaming\Globals
 */
class MG_Constants {

    /**
     * MG_Constants constructor.
     */
    public function __construct() {
            $this->theme();
    }


    public function theme() {
        /** URIS */
        define( 'MG_URI', get_stylesheet_directory_uri() );
        define( 'MG_URI_SRC', MG_URI.'/src' );
        define( 'MG_URI_IMG', MG_URI_SRC.'/img' );
    }

}
