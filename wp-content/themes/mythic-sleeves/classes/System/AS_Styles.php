<?php

namespace Alter_Sleeves\System;

use Mythic_Core\Utils\MC_Assets;
use Mythic_Core\Utils\MC_Url;

/**
 * Class MC_Styles
 *
 * @package Alter_Sleeves\System
 */
class AS_Styles {
    
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
            'as-'.$handle = 'creator'             => [
                'handle' => $handle
            ],
            'as-'.$handle = 'cutter'              => [
                'handle'    => $handle,
                'url'       => MC_Assets::getCssComponentsUrl( 'cutter' ),
                'condition' => MC_Url::contains( 'submit' ) || MC_Url::contains( 'cutter' )
            ],
            'as-'.$handle = 'front'               => [
                'handle' => $handle
            ],
            'as-'.$handle = 'search'              => [
                'handle' => $handle
            ],
            'as-'.$handle = 'search-autocomplete' => [
                'handle' => $handle
            ],
            'as-'.$handle = 'checkout-account-create' => [
                'handle' => $handle
            ],
            'as-'.$handle = 'submit'              => [
                'handle'    => $handle,
                'condition' => MC_Url::contains( 'submit' ) || MC_Url::contains( 'cutter' )
            ],
        
        ];
    }
    
}
