<?php

namespace Mythic\Helpers;

use WP_Error;

/**
 * Class MC2_Crons
 *
 * @package Mythic\System
 */
class MC2_Events {

    /**
     * @param string $hook
     * @param string $time
     * @param string $recurrence
     * @param array  $args
     *
     * @return bool|WP_Error
     */
    public static function recurring( $hook = '', $time = '', $recurrence = 'hourly', $args = null ) {
        if( empty( $hook ) ) return false;
        if( empty( $time ) ) $time = time();
        if( !wp_next_scheduled( $hook ) ) {
            if( is_null( $args ) ) return wp_schedule_event( $time, $recurrence, $hook );
            if( !empty( $args ) || !is_array( $args ) ) $args = [ $args ];
            return wp_schedule_event( $time, $recurrence, $hook, $args );
        }

        return false;
    }

    /**
     * @param string $hook
     * @param array  $args
     * @param string $time
     *
     * @return bool
     */
    public static function single( $hook = '', $args = [], $time = '' ) : bool {
        if( empty( $hook ) ) return false;
        if( empty( $time ) ) $time = time();
        if( !empty( $args ) ) $args = [ $args ];

        return wp_schedule_single_event( $time, $hook, $args );
    }

    /**
     * @param string $hook
     * @param array  $args
     *
     * @return false|int
     */
    public static function remove( $hook = '', $args = [] ) {
        $cleared = wp_clear_scheduled_hook( $hook, [ $args ] );
        if( empty( $cleared ) ) return 0;

        return $cleared;
    }

}