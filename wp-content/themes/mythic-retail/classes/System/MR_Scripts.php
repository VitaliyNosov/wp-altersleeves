<?php

namespace Mythic_Retail\System;

/**
 * Class MR_Scripts
 *
 * @package Mythic_Core\System
 */
class MR_Scripts {

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
        return [            'mr-'.$handle = 'app' => [
            'handle' => $handle,
        ]];
    }

}
