<?php

namespace Mythic_Gaming\System;

/**
 * Class MG_Styles
 *
 * @package Mythic_Core\System
 */
class MG_Styles {

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
            'mg-campaign' => [
                'handle' => 'campaign'
            ]
        ];
    }

}