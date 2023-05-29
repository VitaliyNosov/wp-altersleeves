<?php

namespace Mythic_Core\System;

/**
 * Class MC_Constants
 *
 * @package Mythic_Core\System
 */
class MC_Constants {
    
    /**
     * MC_Constants constructor.
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
        define( 'MC_VERSION', '1.0.0' );
        define( 'MC_TEXT_DOMAIN', get_template() );
        define( 'MC_SITE', get_site_url().'/' );
        define( 'MC_SITENAME', get_bloginfo( 'name' ) );
        define( 'MC_PREFIX', 'MC' );
        define( 'MC_PREFIX_LOWER', 'mc' );
        define( 'MC_PREFIX_CLASS', MC_PREFIX.'_' );
    }
    
    public function images() {
        /** PATHS **/
        define( 'MC_DIR_ICON_LOGO', MC_DIR_IMG.'/logos/mythic-gaming/icon/orange-gradient/200.png' );
        
        /** URIS **/
        define( 'MC_URI_ICON_LOGO', MC_URI_IMG.'/logos/mythic-gaming/icon/orange-gradient/200.png' );
    }
    
    public function newsletter() {
        define( 'NEWSLETTER_DISCLAIMER',
                'By submitting this form, you are consenting to our <a href="/privacy-policy" title="Privacy Policy" target="_blank">privacy policy</a>' );
        define( 'NEWSLETTER_CONFIRMATION',
                'Thank you for signing up to the '.get_bloginfo( 'name' ).' newsletter. We look forward to keeping you updated!' );
    }
    
    public function resources() {
        /** DIRS */
        define( 'MC_DIR_RESOURCES', ABSPATH.'resources/' );
        define( 'MC_DIR_RESOURCES_MF', MC_DIR_RESOURCES.'mythic-frames/' );
        define( 'MC_DIR_RESOURCES_MF_KS', MC_DIR_RESOURCES_MF.'kickstarter/' );
        
        /** URIS */
        define( 'MC_URI_RESOURCES', MC_SITE.'resources/' );
        define( 'MC_URI_RESOURCES_IMG', MC_URI_RESOURCES.'img/' );
        define( 'MC_URI_RESOURCES_KS', MC_URI_RESOURCES_IMG.'kickstarter/' );
        define( 'MC_URI_RESOURCES_IMG_PRODUCTS', MC_URI_RESOURCES_IMG.'products/' );
        define( 'MC_URI_RESOURCES_IMG_PRODUCTS_MF', MC_URI_RESOURCES_IMG_PRODUCTS.'mythic-frames/' );
        
        // BACK END - FILES
        define( 'DIR_PRINTS_PDF', ABSPATH.'files/prints/pdf' );
        define( 'DIR_PRINTS_PNG', ABSPATH.'files/prints/png' );
    }
    
    public function theme() {
        /** DIRS **/
        define( 'MC_DIR', get_template_directory() );
        define( 'MC_CHILD_DIR', get_stylesheet_directory() );
        define( 'MC_DIR_CLASSES', MC_DIR.'/classes' );
        define( 'MC_DIR_SRC', MC_DIR.'/src' );
        define( 'MC_DIR_CSS', MC_DIR_SRC.'/css' );
        define( 'MC_DIR_IMG', MC_DIR_SRC.'/img' );
        define( 'MC_DIR_FONTS', MC_DIR_SRC.'/fonts' );
        define( 'MC_DIR_JS', MC_DIR_SRC.'/js' );
        define( 'MC_DIR_LANG', MC_DIR_SRC.'/languages' );
        define( 'MC_DIR_TEMPLATE_PARTS', MC_DIR.'/template-parts' );
        
        /** URIS */
        define( 'MC_URI', get_template_directory_uri() );
        define( 'MC_URI_NODES', MC_URI.'/node_modules' );
        define( 'MC_URI_SRC', MC_URI.'/src' );
        define( 'MC_URI_CSS', MC_URI_SRC.'/css' );
        define( 'MC_URI_IMG', MC_URI_SRC.'/img' );
        define( 'MC_URI_JS', MC_URI_SRC.'/js' );
        define( 'MC_URI_LANG', MC_URI_SRC.'/languages' );
        define( 'MC_URI_TEMPLATE_PARTS', MC_URI_SRC.'/template-parts' );
        define( 'MC_URI_VENDOR', MC_URI.'/vendor' );
    }
    
    public static function vendor() {
        define( 'MC_WOO_ACTIVE', class_exists( 'WooCommerce' ) );
    }
    
}