<?php

namespace Mythic\Functions\Wordpress;

class MC2_Event_Functions {
    
    /**
     * @param string $hook
     * @param string $time
     * @param string $recurrence
     * @param array  $args
     *
     * @return bool
     */
    public static function recurring( $hook = '', $time = '', $recurrence = 'hourly', $args = [] ) {
        if( empty( $hook ) ) return false;
        if( !wp_next_scheduled( $hook ) ) {
            if( !empty( $args ) ) $args = [ $args ];
            
            return wp_schedule_event( $time ?? time(), $recurrence, $hook, $args );
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
        if( !empty( $args ) ) $args = [ $args ];
        
        return wp_schedule_single_event( $time ?? time(), $hook, $args );
    }
    
    /**
     * @param string $hook
     * @param array  $args
     *
     * @return int
     */
    public static function remove( $hook = '', $args = [] ) {
        $cleared = wp_clear_scheduled_hook( $hook, [ $args ] );
        return empty( $cleared ) ? 0 : $cleared;
    }
    
}