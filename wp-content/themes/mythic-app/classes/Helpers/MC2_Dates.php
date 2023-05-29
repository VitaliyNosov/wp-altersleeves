<?php

namespace Mythic\Helpers;

/**
 * Class MC2_Dates
 *
 * @package Mythic\Helpers
 */
class MC2_Dates {

    /**
     * @param string $period
     * @param null   $time
     *
     * @return int
     */
    public static function int( $period = 'day', $time = null ) : int {
        $time = $time ?? time();
        $time = is_string( $time ) && !is_numeric( $time ) ? strtotime( $time ) : $time;
        if( !self::isTimestamp( $time ) ) $time = time();
        switch( $period ) {
            case 'day' :
                $period = 'j';
                break;
            case 'month' :
                $period = 'n';
                break;
            case 'year' :
                $period = 'Y';
                break;
        }

        return date( $period, $time );
    }

    /**
     * @param $timestamp
     *
     * @return bool
     */
    public static function isTimestamp( $timestamp ) : bool {
        return ( (string) (int) $timestamp === $timestamp ) && ( $timestamp <= PHP_INT_MAX ) && ( $timestamp >= ~PHP_INT_MAX );
    }

    public static function today( string $format = 'Y-m-d' ) {
        return self::currentDate( $format );
    }

    /**
     * @return false|string
     */
    public static function currentDate( string $format = 'Y-m-d' ) {
        return date( $format, time() );
    }

    /**
     * @param string $format
     *
     * @return false|string
     */
    public static function yesterday( string $format = 'Y-m-d' ) : string {
        return date( $format, strtotime( "-1 days" ) );
    }

    /**
     * @param string $format
     *
     * @return false|string
     */
    public static function tomorrow( string $format = 'Y-m-d' ) : string {
        return date( $format, strtotime( "+1 days" ) );
    }

    /**
     * @return false|string
     */
    public static function currentSqlDatetime() {
        return date( 'Y-m-d H:i:s', time() );
    }

    /**
     * @return false|string
     */
    public static function sqlDatetime( $time = '' ) {
        $time = $time ?? time();
        return date( 'Y-m-d H:i:s', $time );
    }

    /**
     * @return false|string
     */
    public static function currentSqlDate() {
        return date( 'Y-m-d', time() );
    }

}