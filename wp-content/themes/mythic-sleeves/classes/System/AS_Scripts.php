<?php

namespace Alter_Sleeves\System;

use MC_Assets;
use MC_Mask_Cutter_Functions;
use MC_User_Functions;

/**
 * Class MC_Scripts
 *
 * @package Alter_Sleeves\System
 */
class AS_Scripts {
    
    /**
     * @param array $files
     *
     * @return array
     */
    public static function add( array $files ) : array {
        foreach( self::files() as $file ) $files[] = $file;
        return $files;
    }
    
    /**
     * @return array
     */
    public static function files() : array {
        $scripts = [
            'as-'.$handle = 'acceptance'          => [
                'handle'    => $handle,
                'condition' => MC_User_Functions::isMod(),
                'deps'      => 'as-webcam'
            ],
            'as-'.$handle = 'admin'               => [
                'handle'    => $handle,
                'condition' => MC_User_Functions::isMod()
            ],
            'as-'.$handle = 'app'                 => [
                'handle' => $handle,
            ],
            'as-'.$handle = 'cart'                => [
                'handle' => $handle,
                'deps'   => 'mc-cart'
            ],
            'as-'.$handle = 'creator'             => [
                'handle' => $handle
            ],
            'as-'.$handle = 'global'              => [
                'handle' => $handle
            ],
            'as-'.$handle = 'items'               => [
                'handle'    => $handle,
                'condition' => MC_User_Functions::isMod()
            ],
            'as-'.$handle = 'marketing'           => [
                'handle' => $handle
            ],
            'as-'.$handle = 'old'                 => [
                'handle' => $handle,
                'deps'   => [
                    'as-global',
                    'as-service-old',
                ],
            ],
            'as-'.$handle = 'search-autocomplete' => [
                'handle' => $handle,
            ],
            'as-'.$handle = 'service-old'         => [
                'handle' => $handle,
                'deps'   => [
                    'as-global',
                ],
            ],
            'as-'.$handle = 'submit'              => [
                'handle'    => $handle,
                'condition' => is_user_logged_in()
            ],
            'as-'.$handle = 'webcam'              => [
                'handle'    => $handle,
                'url'       => MC_Assets::getCssVendorUrl( 'webcam' ),
                'condition' => MC_User_Functions::isMod()
            ],
            'as-'.$handle = 'cutter'              => [
                'handle'              => $handle,
                'url'                 => MC_Assets::getJsModulesUrl( 'cutter/script' ),
                'condition'           => is_user_logged_in(),
                'localization_args'   => [
                    'js_dir'   => MC_URI_JS.'/modules/cutter/',
                    'maskMaps' => MC_Mask_Cutter_Functions::data()
                ],
                'localization_object' => 'input'
            ],
            'as-'.$handle = 'cutter-admin'        => [
                'handle'    => $handle,
                'url'       => MC_Assets::getJsModulesUrl( 'cutter/admin' ),
                'condition' => MC_User_Functions::isAdmin()
            ],
        ];
        foreach( $scripts as $key => $script ) {
            if( !empty( $script['prefix'] ) ) continue;
            $scripts[ $key ]['prefix'] = 'as-';
        }
        return $scripts;
    }
    
}