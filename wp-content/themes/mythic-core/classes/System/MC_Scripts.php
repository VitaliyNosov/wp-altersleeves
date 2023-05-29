<?php

namespace Mythic_Core\System;

use MC_Assets;
use MC_File_Locations;
use MC_Url;
use MC_User_Functions;

/**
 * Class MC_Scripts
 *
 * @package Mythic_Core\System
 */
class MC_Scripts {
    
    /**
     * @return array
     */
    public static function deregisters() : array {
        $scripts = [ 'comment-reply', 'jquery' ];
        return apply_filters( 'mc_deregister_scripts', $scripts );
    }
    
    /**
     * @return array
     */
    public static function files() : array {
        return [
            'mc-'.$handle = 'affiliation' => [
                'handle'    => $handle,
                'url'       => MC_Assets::getJsModulesUrl( $handle ),
                'deps'      => 'mc-helpers',
                'condition' => apply_filters( 'mc_affiliation_enqueue', true ) // @todo update to false and add correct filters
            ],
            'mc-'.$handle = 'search'      => [
                'handle' => $handle,
                'url'    => MC_Assets::getJsFunctionsUrl( 'search' )
            ],
            'mc-'.$handle = 'admin'       => [
                'handle' => $handle,
                'admin'  => true
            ],
            'mc-'.$handle = 'bootstrap'   => [
                'handle' => $handle,
                'url'    => MC_File_Locations::bootstrapJsUrl()
            ],
            'mc-'.$handle = 'flexsearch'  => [
                'handle' => $handle,
                'url'    => MC_File_Locations::flexSearchJsUrl()
            ],
            'mc-'.$handle = 'cart'        => [
                'handle' => $handle,
                'url'    => MC_Assets::getJsFunctionsUrl( 'cart' )
            ],
            'mc-'.$handle = 'helpers'     => [
                'handle' => $handle
            ],
            'mc-'.$handle = 'init'        => [
                'handle'              => $handle,
                'deps'                => 'mc-helpers',
                'localization_args'   => MC_User_Functions::localizeArgs(),
                'localization_object' => 'vars'
            ],
            'mc-'.$handle = 'licensing'   => [
                'handle' => $handle,
                'url'    => MC_Assets::getJsModulesUrl( $handle )
            ],
            'mc-'.$handle = 'nav'         => [
                'handle' => $handle,
                'deps'   => 'mc-init'
            ],
            'mc-'.$handle = 'products'    => [
                'handle'    => $handle,
                'url'       => MC_Assets::getJsFunctionsUrl( $handle, true ),
                'condition' => MC_Url::isProductPage()
            ],
            'mc-'.$handle = 'promo-mailing'   => [
	            'handle' => $handle,
	            'url'    => MC_Assets::getJsModulesUrl( $handle )
            ],
            'mc-'.$handle = 'charity'   => [
                'handle' => $handle,
                'url'    => MC_Assets::getJsModulesUrl( $handle )
            ],
        ];
    }
    
    /**
     * @return array
     */
    public static function adminFiles() : array {
        return [
            'mc-'.$handle = 'cli' => [
                'handle' => $handle,
                'url'    => MC_Assets::getJsAdminUrl( 'cli' )
            ],
        ];
    }
    
    public static function jquery() {
        wp_deregister_script( 'jquery' );
        wp_enqueue_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', [], null, true );
    }
    
    /**
     * @return array
     */
    public static function inlines() : array {
        return apply_filters( 'mc-inline_scripts', [] );
    }
    
}