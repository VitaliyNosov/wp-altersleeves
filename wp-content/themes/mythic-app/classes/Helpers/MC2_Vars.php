<?php

namespace Mythic\Helpers;

/**
 * Class MC2_Vars
 *
 * @package Mythic\Helpers
 */
class MC2_Vars {

    /**
     * @param string $label
     * @param string $delimiter
     *
     * @return string
     */
    public static function readableToKey( $label = '', $delimiter = '_' ) : string {
        $key = trim( $label );
        $key = str_replace( ' ', $delimiter, $key );
        $key = sanitize_key( $key );

        return strtolower( $key );
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function alphanumericOnlyForSearch( $string = '' ) : string {
        return trim( strtolower( self::alphanumericOnly( $string ) ) );
    }

    /**
     * @param string $string
     * @param bool   $spaces
     *
     * @return string
     */
    public static function alphanumericOnly( $string = '', $spaces = true ) : string {
        $string = preg_replace( '/[^0-9a-zA-Z\s]/', '', $string );
        if( $spaces ) return $string;

        return str_replace( ' ', '', $string );
    }

    /**
     * @param int   $strength
     * @param false $caps_only
     *
     * @return string
     */
    public static function generate( $strength = 30, $caps_only = false ) : string {
        if( empty( $strength ) ) return false;

        $input         = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input_length  = strlen( $input );
        $random_string = '';
        for( $i = 0; $i < $strength; $i++ ) {
            $random_character = $input[ mt_rand( 0, $input_length - 1 ) ];
            $random_string    .= $random_character;
        }
        if( $caps_only ) return strtoupper( $random_string );

        return $random_string;
    }

    /**
     * @param string $string
     * @param false  $alphanumeric
     * @param bool   $spaces
     *
     * @return string
     */
    public static function parseableString( $string = '', $alphanumeric = false, $spaces = true ) : string {
        $string = trim( $string );
        $string = strtolower( $string );
        if( $alphanumeric ) $string = MC2_Vars::alphanumericOnly( $string, $spaces );

        return $string;
    }

    /**
     * @param array $args
     * @param array $params
     *
     * @return array
     */
    public static function stringSafe( $args = [], $params = [] ) {
        foreach( $params as $key => $param ) $args[ $key ] = $param;

        return $args;
    }

    /**
     * @param string $string
     *
     * @return array|false|string[]
     */
    public static function splitStringBySpace( $string = '' ) {
        return preg_split( "/\s+(?=\S*+$)/", $string );
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function stringCleanForUrl( $string = '' ) : string {
        if( empty( $string ) ) return '';

        return strtolower( $string );
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public static function isUnicode( string $string = '' ) : bool {
        return strlen( $string ) != strlen( utf8_decode( $string ) );
    }

    /**
     * @param string $string
     * @param string $case
     *
     * @return string
     */
    public static function getPeriodFromString( string $string = '', string $case = 'lowercase' ) : string {
        $period_number   = self::stringToInt( $string );
        $period_interval = self::getIntervalFromString( $string, $case );
        return $period_number.' '.$period_interval;
    }

    /**
     * @param string $string
     * @param string $case
     *
     * @return string
     */
    public static function getIntervalFromString( string $string = '', string $case = 'lowercase' ) : string {
        $string = strtolower( $string );
        switch( $string ) {
            case strpos( $string, 'day' ) !== false :
                $interval = 'day';
                break;
            case strpos( $string, 'week' ) !== false :
                $interval = 'week';
                break;
            case strpos( $string, 'month' ) !== false :
                $interval = 'month';
                break;
            case strpos( $string, 'year' ) !== false :
                $interval = 'year';
                break;
            default :
                return '';
        }
        switch( $case ) {
            case 'capitalise' :
            case 'capitalize' :
            case 'ucfirst' :
                return ucfirst( $interval );
            case 'upper' :
            case 'uppercase' :
            case 'uc' :
                return strtoupper( $interval );
            default :
                return strtolower( $interval );
        }
    }

    /**
     * @param string $string
     *
     * @return int
     */
    public static function stringToInt( $string = '' ) : int {
        $string = preg_replace( '/[^0-9]/', '', $string );
        if( empty( $string ) ) return 0;
        return (int) $string;
    }

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function stringContains( string $haystack = '', string $needle = '' ) : bool {
        return strpos( $haystack, $needle ) !== false;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function sanitize_with_underscores( string $string = '' ) : string {
        return str_replace( '-', '_', sanitize_title_with_dashes( $string ) );
    }

    /**
     * @param $data
     *
     * @return array
     */
    public static function prepare_format_array( $data ) {
        $format = [];
        foreach( $data as $data_single ) {
            if( is_int( $data_single ) ) {
                $format[] = '%d';
            } else if( is_float( $data_single ) ) {
                $format[] = '%f';
            } else {
                $format[] = '%s';
            }
        }

        return $format;
    }

    /**
     * @param $timestamp
     *
     * @return false|mixed
     */
    public static function is_timestamp($timestamp) {
        if( !is_numeric($timestamp) ) return false;
        if(strtotime(date('d-m-Y H:i:s',$timestamp)) === (int)$timestamp) {
            return $timestamp;
        } else return false;
    }

}