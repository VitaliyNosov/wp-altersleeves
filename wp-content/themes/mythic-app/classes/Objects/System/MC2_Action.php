<?php

namespace Mythic\Objects\System;

use Mythic\Abstracts\MC2_Abstract;
use Mythic\Helpers\MC2_Events;
use Mythic\Helpers\MC2_Vars;

class MC2_Action extends MC2_Abstract {

    public $args = [];
    public $hook;
    public $function;
    public $recurrence;
    public $time;
    const recurrences = [ 'daily', 'hourly', 'weekly', 'monthly' ];

    public function __construct( $params ) {
        if( empty( $params ) || count( $params ) < 4 ) return;
        $hook     = $params[0] ?? '';
        $function = $params[1] ?? '';
        if( empty( $hook ) || empty( $function ) ) return;
        $this->set_hook($hook);
        $this->set_function($function);
        $this->set_args($args ?? []);

        add_action( $hook, $function, $params[2], count( $args ?? [] ) );
        add_action( $hook.'_trigger', [ $this, 'trigger' ], $params[2], $params[3] );
        $i = 0;
        foreach( $params as $param ) {
            $i++;
            if( $i < 5 ) continue;
            $param = is_string( $param ) ? strtolower( $param ) : $param;
            if( in_array( $param, self::recurrences ) ) {
                $recurrence = $param;
            } else if( !empty( MC2_Vars::is_timestamp( $param ) ) ) {
                $time = $param;
            } else if( is_string( $param ) ) {
                if( !empty( strtotime( $param ) ) ) {
                    $time = $param;
                }
            }
            if( !empty( $time ) && !empty( $recurrence ) ) break;
        }
        if( empty( $time ) && empty( $recurrence ) ) return;
        $this->set_time( $time ?? time() );
        $this->set_recurrence( $recurrence ?? 'daily' );
        $this->schedule();
    }

    /**
     * @return array
     */
    public function get_args() : array {
        return $this->args;
    }

    /**
     * @param array $args
     */
    public function set_args( array $args ) {
        $this->args = $args;
    }

    /**
     * @return mixed|string
     */
    public function get_hook() {
        return $this->hook;
    }

    /**
     * @param mixed|string $hook
     */
    public function set_hook( $hook ) {
        $this->hook = $hook;
    }

    /**
     * @return mixed|string
     */
    public function get_function() {
        return $this->function;
    }

    /**
     * @param mixed|string $function
     */
    public function set_function( $function ) {
        $this->function = $function;
    }

    /**
     * @return mixed|string
     */
    public function get_recurrence() {
        return $this->recurrence;
    }

    /**
     * @param mixed|string $recurrence
     */
    public function set_recurrence( $recurrence ) {
        $this->recurrence = $recurrence;
    }

    /**
     * @return int
     */
    public function get_time() : int {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function set_time( int $time ) {
        $this->time = $time;
    }

    public function schedule() {
        MC2_Event_Functions::recurring( $this->get_hook(), $this->get_time(), $this->get_recurrence(), $this->get_args() );
    }

    public function trigger() {
        MC2_Event_Functions::single( $this->get_hook(), $this->get_args(), time() );
    }

}