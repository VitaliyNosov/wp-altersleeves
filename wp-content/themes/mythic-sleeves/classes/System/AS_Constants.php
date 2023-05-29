<?php

namespace Alter_Sleeves\System;

/**
 * Class AS_Constants
 *
 * @package Alter_Sleeves\System
 */
class AS_Constants {
    
    /**
     * AS_Constants constructor.
     */
    public function __construct() {
        $this->theme();
        $this->images();
    }
    
    public function images() {
        /** URIS **/
        define( 'AS_URI_ICON_LOGO', AS_URI_IMG.'/logo/dark.png' );
    }
    
    public function theme() {
        /** DIRECTORIES **/
        define( 'AS_DIR', get_stylesheet_directory() );
        
        /** URIS */
        define( 'AS_URI', get_stylesheet_directory_uri() );
        define( 'AS_URI_SRC', AS_URI.'/src' );
        define( 'AS_URI_CSS', AS_URI_SRC.'/css' );
        define( 'AS_URI_IMG', AS_URI_SRC.'/img' );
        define( 'AS_URI_JS', AS_URI_SRC.'/js' );
        
        /** EMAILS */
        define( 'EMAIL_DOMAIN', '@altersleeves.com' );
        define( 'EMAIL_CHAD', 'chad'.EMAIL_DOMAIN );
        define( 'EMAIL_JAMES', 'james'.EMAIL_DOMAIN );
        define( 'EMAIL_TARGA', 'Nighttarga@gmail.com' );
        define( 'EMAIL_SUPPORT', 'support'.EMAIL_DOMAIN );
        
        /** DIRS, FILES AND FOLDERS  */
        define( 'DIR_THEME', get_stylesheet_directory() );
        define( 'DIR_THEME_CSS', DIR_THEME.'/css' );
        define( 'DIR_THEME_FONTS', DIR_THEME.'/fonts' );
        define( 'DIR_THEME_IMAGES', DIR_THEME.'/src/img' );
        define( 'DIR_THEME_JS', DIR_THEME.'/js' );
        define( 'DIR_THEME_TEMPLATE_PARTS', DIR_THEME.'/template-parts' );
        // TEMPLATE PARTS -- BROWSING
        define( 'TP_ITEMS_ALTER_A', DIR_THEME_TEMPLATE_PARTS.'/items/alter/no-card-set.php' );
        define( 'TP_ITEMS_ALTER_C', DIR_THEME_TEMPLATE_PARTS.'/items/alter/grid-frame.php' );
        define( 'TP_ITEMS_ALTER_D', DIR_THEME_TEMPLATE_PARTS.'/items/alter/by.php' );
        
        // TEMPLATE PARTS -- SCRIPTS
        define( 'TP_SCRIPTS', DIR_THEME_TEMPLATE_PARTS.'/scripts/' );
        define( 'TP_REDIRECT', TP_SCRIPTS.'redirect.php' );
    }
    
}