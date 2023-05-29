<?php

namespace Mythic_Retail\System;

/**
 * Class MR_Constants
 *
 * @package Mythic_Retail\Globals
 */
class MR_Constants {

    /**
     * MR_Constants constructor.
     */
    public function __construct() {
            $this->core();
            $this->theme();
    }

    public function core() {
        /** INFO */
        define( 'MR_VERSION', '1.0.0' );
        define( 'MR_TEXT_DOMAIN', get_stylesheet() );
    }

    public function theme() {
        /** PATHS **/
        define( 'MR_DIR', get_stylesheet_directory() );
        define( 'MR_DIR_CLASSES', MR_DIR.'/classes' );
        define( 'MR_DIR_SRC', MR_DIR.'/src' );
        define( 'MR_DIR_CSS', MR_DIR_SRC.'/css' );
        define( 'MR_DIR_IMG', MR_DIR_SRC.'/img' );
        define( 'MR_DIR_JS', MR_DIR_SRC.'/js' );
        define( 'MR_DIR_LANG', MR_DIR_SRC.'/languages' );
        define( 'MR_DIR_TEMPLATE_PARTS', MR_DIR.'/template-parts' );

        /** URIS */
        define( 'MR_URI', get_stylesheet_directory_uri() );
        define( 'MR_URI_SRC', MR_URI.'/src' );
        define( 'MR_URI_CSS', MR_URI_SRC.'/css' );
        define( 'MR_URI_IMG', MR_URI_SRC.'/img' );
        define( 'MR_URI_JS', MR_URI_SRC.'/js' );
        define( 'MR_URI_LANG', MR_URI_SRC.'/languages' );
        define( 'MR_URI_TEMPLATE_PARTS', MR_URI_SRC.'/template-parts' );
    }

}
