<?php

namespace Mythic_Gaming\System;

use MC_Assets;
use MC_File_Locations;
use MC_Url;
use Mythic_Core\Functions\MC_Mythic_Frames_Functions;

/**
 * Class MG_Scripts
 *
 * @package Mythic_Core\System
 */
class MG_Scripts {
    
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
        return [
            'mg-'.$handle = 'app'          => [
                'handle' => $handle,
                'deps'   => [ 'mg-match-height' ]
            ],
            'mg-'.$handle = 'campaign'     => [
                'handle'              => $handle,
                'localization_object' => $handle,
                'localization_args'   => MC_Mythic_Frames_Functions::backerCreditData( MC_Mythic_Frames_Functions::backerId() ),
                'deps'                => [ 'jquery', 'mc-helpers' ]
            ],
            'mg-'.$handle = 'products'     => [
                'handle'    => $handle,
                'url'       => MC_Assets::getJsFunctionsUrl( $handle ),
                'deps'      => 'mc-products',
                'prefix'    => 'mg',
                'condition' => MC_Url::isProductPage()
            ],
            'mg-'.$handle = 'product-page' => [
                'handle'    => $handle,
                'url'       => MC_Assets::getJsSectionsUrl( $handle ),
                'deps'      => [ 'mg-products' ],
                'prefix'    => 'mg',
                'condition' => MC_Url::isProductPage()
            ],
            'mg-'.$handle = 'match-height' => [
                'handle' => $handle,
                'url'    => MC_File_Locations::matchHeightJsUrl(),
                'prefix' => 'mg'
            ],
        ];
    }
    
}
