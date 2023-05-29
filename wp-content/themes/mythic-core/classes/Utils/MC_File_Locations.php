<?php

namespace Mythic_Core\Utils;

/**
 * Class MC_File_Locations
 *
 * @package Mythic_Core\Utils
 */
class MC_File_Locations {
    
    /**
     * @return string
     */
    public static function fontAwesomeUrl() : string {
        return MC_URI.'/vendor/components/font-awesome/css/all.css';
    }
    
    /**
     * @return string
     */
    public static function hoverCssUrl() : string {
        return MC_URI.'/node_modules/hover.css/css/hover.css';
    }
    
    /**
     * @return string
     */
    public static function bootstrapCssUrl() : string {
        return MC_URI_NODES.'/bootstrap/dist/css/bootstrap.min.css';
    }
    
    /**
     * @return string
     */
    public static function bootstrapJsUrl() : string {
        return MC_URI_NODES.'/bootstrap/dist/js/bootstrap.min.js';
    }
    
    /**
     * @return string
     */
    public static function matchHeightJsUrl() : string {
        return MC_URI_NODES.'/jquery-match-height/dist/jquery.matchHeight-min.js';
    }
    
    /**
     * @return string
     */
    public static function select2JsUrl() : string {
        return MC_URI_NODES.'/select2/dist/js/select2.min.js';
    }
    
    /**
     * @return string
     */
    public static function select2CssUrl() : string {
        return MC_URI_NODES.'/select2/dist/css/select2.min.css';
    }
    
    /**
     * @return string
     */
    public static function videoCssUrl() : string {
        return MC_URI_NODES.'/video.js/dist/video-js.min.css';
    }
    
    /**
     * @return string
     */
    public static function videoJsUrl() : string {
        return MC_URI_NODES.'/video.js/dist/video.min.js';
    }
    
    /**
     * @return string
     */
    public static function flexSearchJsUrl() : string {
        return MC_URI_NODES.'/flexsearch/dist/flexsearch.min.js';
    }
    
}