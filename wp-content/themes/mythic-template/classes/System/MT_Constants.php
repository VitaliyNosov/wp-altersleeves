<?php

namespace Mythic_Template\System;

/**
 * Class MT_Constants
 *
 * @package Mythic_Template\System
 */
class MT_Constants {

    /**
     * MT_Constants constructor.
     */
    public function __construct() {
        $this->core();
        $this->newsletter();
        $this->resources();
        $this->theme();
        $this->images();
        $this->vendor();
    }

    public function core() {
        define( 'MT_VERSION', '1.0.0' );
        define( 'MT_TEXT_DOMAIN', get_template() );
        define( 'MT_SITE', get_site_url().'/' );
        define( 'MT_SITENAME', get_bloginfo( 'name' ) );
        define( 'MT_PREFIX', 'MC' );
        define( 'MT_PREFIX_LOWER', 'mc' );
        define( 'MT_PREFIX_CLASS', MT_PREFIX.'_' );
        define( 'MT_HANDLE', MT_PREFIX_LOWER.'-' );
        define( 'MT_HOOK', MT_PREFIX_LOWER.'_' );
    }

    public function images() {
        /** PATHS **/
        define( 'MT_DIR_ICON_LOGO', MT_DIR_IMG.'/logos/mythic-gaming/icon/orange-gradient/200.png' );

        /** URIS **/
        define( 'MT_URI_ICON_LOGO', MT_URI_IMG.'/logos/mythic-gaming/icon/orange-gradient/200.png' );
    }

    public function newsletter() {
        define( 'NEWSLETTER_DISCLAIMER',
                'By submitting this form, you are consenting to our <a href="/privacy-policy" title="Privacy Policy" target="_blank">privacy policy</a>' );
        define( 'NEWSLETTER_CONFIRMATION',
                'Thank you for signing up to the '.get_bloginfo( 'name' ).' newsletter. We look forward to keeping you updated!' );
    }

    public function resources() {
        /** DIRS */
        define( 'MT_DIR_RESOURCES', ABSPATH.'resources/' );
        define( 'MT_DIR_ACF_JSON', ABSPATH.'/files/mt_acf_json/' );
        define( 'MT_DIR_RESOURCES_MF', MT_DIR_RESOURCES.'mythic-frames/' );
        define( 'MT_DIR_RESOURCES_MF_KS', MT_DIR_RESOURCES_MF.'kickstarter/' );

        /** URIS */
        define( 'MT_URI_RESOURCES', MT_SITE.'resources/' );
        define( 'MT_URI_RESOURCES_IMG', MT_URI_RESOURCES.'img/'  );
        define( 'MT_URI_RESOURCES_KS', MT_URI_RESOURCES_IMG.'kickstarter/' );
        define( 'MT_URI_RESOURCES_IMG_PRODUCTS', MT_URI_RESOURCES_IMG.'products/'  );
        define( 'MT_URI_RESOURCES_IMG_PRODUCTS_MF', MT_URI_RESOURCES_IMG_PRODUCTS.'mythic-frames/'  );

    }

    public function theme() {
        /** DIRS **/
        define( 'MT_DIR', get_template_directory() );
        define( 'MT_CHILD_DIR', get_stylesheet_directory() );
        define( 'MT_DIR_CLASSES', MT_DIR.'/classes' );
        define( 'MT_DIR_SRC', MT_DIR.'/src' );
        define( 'MT_DIR_CSS', MT_DIR_SRC.'/css' );
        define( 'MT_DIR_IMG', MT_DIR_SRC.'/img' );
        define( 'MT_DIR_FONTS', MT_DIR_SRC.'/fonts' );
        define( 'MT_DIR_JS', MT_DIR_SRC.'/js' );
        define( 'MT_DIR_LANG', MT_DIR_SRC.'/languages' );
        define( 'MT_DIR_TEMPLATE_PARTS', MT_DIR.'/template-parts' );

        /** URIS */
        define( 'MT_URI', get_template_directory_uri() );
        define( 'MT_URI_NODES', MT_URI.'/node_modules' );
        define( 'MT_URI_SRC', MT_URI.'/src' );
        define( 'MT_URI_CSS', MT_URI_SRC.'/css' );
        define( 'MT_URI_IMG', MT_URI_SRC.'/img' );
        define( 'MT_URI_JS', MT_URI_SRC.'/js' );
        define( 'MT_URI_LANG', MT_URI_SRC.'/languages' );
        define( 'MT_URI_TEMPLATE_PARTS', MT_URI_SRC.'/template-parts' );
        define( 'MT_URI_VENDOR', MT_URI.'/vendor' );
    }

    public static function vendor() {
        define( 'MT_WOO_ACTIVE', class_exists( 'WooCommerce' ) );
    }

}
