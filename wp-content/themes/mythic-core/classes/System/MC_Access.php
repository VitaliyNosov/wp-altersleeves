<?php

namespace Mythic_Core\System;

use MC_Vars;
use Mythic_Core\Settings\MC_Site_Settings;

/**
 * Class MC_Access
 *
 * @package Mythic_Core\System
 */
class MC_Access {
    
    /**
     * @return bool
     */
    public static function primarySite() : bool {
        if( !is_multisite() ) return true;
        if( get_current_blog_id() == 1 ) return true;
        return false;
    }
    
    /**
     * @return string
     */
    public static function liveUrl() : string {
        $settings = get_option( MC_Site_Settings::$setting_name );
        
        return $settings['live_url'] ?? '';
    }
    
    /**
     * @return bool
     */
    public static function live() : bool {
        $live_url = self::liveUrl();
        if( empty( $live_url ) ) return false;
        
        return MC_Vars::stringContains( $live_url, $_SERVER['HTTP_HOST'] );
    }
    
}