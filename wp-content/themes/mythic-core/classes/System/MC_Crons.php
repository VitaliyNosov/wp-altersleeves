<?php

namespace Mythic_Core\System;

use WP_Error;

/**
 * Class MC_Crons
 *
 * @package Mythic_Core\System
 */
class MC_Crons {
    
    public static function init() {
        new self();
    }
    
    /**
     * MC_Crons constructor
     */
    public function __construct() {
        $this->monthly();
        $this->weekly();
        $this->daily();
        $this->hourly();
    }
    
    public function hourly() {
        self::recurring( 'mc_transactions_import_remaining_orders', 1620209443 );
    }
    
    /**
     * Daily crons - GMT Firing order
     */
    public function daily() {
        MC_Crons::recurring( 'mc_send_withdrawals', '1579359600', 'daily' );
        // Fires Every day at 5am GMT
        
        self::recurring( 'mc_scryfall_card_import', 1616817600, 'daily' );
        
        //MC_Crons::recurring('mc_alterist_index', time(), 'daily' );
        //MC_Crons::recurring('mc_mf_invoices', time(), 'daily' );
    }
    
    /**
     * Weekly crons - GMT Firing order
     */
    public function weekly() {
        // Fires Every Saturday at 9am GMT
        /*
        self::recurring('mc_printings_scryfall_import', 1616832000, 'weekly', [
            'images' => 1,
            'fresh'  => 1,
            'printings' => 1
        ]);
        */
    }
    
    public static function monthly() {
    }
    
    /**
     * @param string $hook
     * @param string $time
     * @param string $recurrence
     * @param array  $args
     *
     * @return bool|WP_Error
     */
    public static function recurring( $hook = '', $time = '', $recurrence = 'hourly', $args = [] ) {
        if( empty( $hook ) ) return false;
        if( empty( $time ) ) $time = time();
        if( !wp_next_scheduled( $hook ) ) {
            if( !empty( $args ) ) $args = [ $args ];
            
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