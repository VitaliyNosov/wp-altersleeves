<?php

namespace Mythic\Helpers;

class MC2_DB {
    
    /**
     * @return string
     */
    public static function wp_prefix() : string {
        global $wpdb;
        return $wpdb->prefix;
    }
    
    /**
     * @return string
     */
    public static function prefix() : string {
        return self::wp_prefix().'_mc_';
    }
    
    /**
     * @param string $table_name
     *
     * @return string
     */
    public static function prefix_table_name( string $table_name = '' ) : string {
        return !empty($table_name) ? self::prefix().$table_name : '';
    }
    
    /**
     * @param        $table_name
     * @param        $where
     * @param string $select_column
     *
     * @return string|void
     */
    public static function prepare_get_query( $table_name, $where, $select_column = '*' ) {
        global $wpdb;
        $variables    = [];
        $query        = "SELECT $select_column FROM $table_name";
        $where_or_and = ' WHERE';
        foreach( $where as $where_key => $where_single ) {
            $variables[] = $where_single;
            $query       .= $where_or_and." $where_key";
            if( is_int( $where_single ) ) {
                $query .= " = %d";
            } else if( is_float( $where_single ) ) {
                $query .= " LIKE %f";
            } else {
                $query .= " LIKE %s";
            }
            $where_or_and = ' AND';
        }
        
        return $wpdb->prepare( $query, $variables );
    }
    
    /**
     * @param $table_name
     *
     * @return string
     */
    public static function prepare_table_name( $table_name = '' ) {
        global $wpdb;
        $prefix = $wpdb->prefix;
        if( strpos( $table_name, $prefix) === false ) $table_name = $prefix.$table_name;
        return $table_name;
    }
    
    /**
     * @param        $table_name
     * @param        $where
     * @param string $select_column
     *
     * @return array|object|null
     */
    public static function get_results( $table_name, $where, $select_column = '*' ) {
        global $wpdb;
        
        return $wpdb->get_results( static::prepare_get_query( static::prepare_table_name($table_name), $where, $select_column ), ARRAY_A );
    }
    
    /**
     * @param        $table_name
     * @param        $where
     * @param string $select_column
     *
     * @return false|mixed
     */
    public static function get_result( $table_name, $where, $select_column = '*' ) {
        $results = self::get_results( $table_name, $where, $select_column );
        if( !is_iterable( $results ) || empty( $results ) ) return false;
        return $results[0];
    }
    
    /**
     * @param        $table_name
     * @param        $where
     * @param string $select_column
     *
     * @return array|object|void|null
     */
    public static function get_row( $table_name, $where, $select_column = '*' ) {
        global $wpdb;
        
        return $wpdb->get_row( static::prepare_get_query( static::prepare_table_name($table_name), $where, $select_column ), ARRAY_A );
    }
    
    /**
     * @param        $table_name
     * @param        $where
     * @param string $select_column
     *
     * @return array
     */
    public static function get_col( $table_name, $where, $select_column = '*' ) {
        global $wpdb;
        
        return $wpdb->get_col( static::prepare_get_query( static::prepare_table_name($table_name), $where, $select_column ) );
    }
    
    /**
     * @param        $table_name
     * @param        $where
     * @param string $select_column
     *
     * @return string|null
     */
    public static function get_var( $table_name, $where, $select_column = '*' ) {
        global $wpdb;
        
        return $wpdb->get_var( static::prepare_get_query( self::prepare_table_name($table_name), $where, $select_column ) );
    }
    
    /**
     * @param $data
     *
     * @return array
     */
    public static function prepare_format_array( $data ) {
        return MC2_Vars::prepare_format_array( $data );
    }
    
    /**
     * @param        $table_name
     * @param        $data
     *
     * @return bool|int
     */
    public static function insert( $table_name, $data ) {
        global $wpdb;
        $format = static::prepare_format_array( $data );
        $wpdb->insert( $table_name, $data, $format );
        
        return $wpdb->insert_id ?? false;
    }
    
    /**
     * @param $table_name
     * @param $data
     * @param $where
     *
     * @return bool|int
     */
    public static function update( $table_name, $data, $where ) {
        global $wpdb;
        $format       = static::prepare_format_array( $data );
        $where_format = static::prepare_format_array( $where );
        
        return $wpdb->update( $table_name, $data, $where, $format, $where_format );
    }
    
    /**
     * @param $table_name
     * @param $where
     *
     * @return bool|int
     */
    public static function delete( $table_name, $where ) {
        global $wpdb;
        $where_format = static::prepare_format_array( $where );
        
        return $wpdb->delete( $table_name, $where, $where_format );
    }
    
    /**
     * @param $table_name
     * @param $primary_key
     * @param $value
     *
     * @return bool
     */
    public static function exists( $table_name, $primary_key, $value ) : bool {
        $results = self::get_var( $table_name, [ $primary_key => $value ] );
        if( empty( $results ) ) return false;
        return true;
    }
    
}