<?php

namespace Mythic\Abstracts;

use Mythic\Helpers\MC2_DB;

abstract class MC2_DB_Table extends MC2_Class {
    
    protected static $table_name = '';
    protected static $object_class = '';
    protected static $prefix = '';
    
    public function __construct() {
        parent::__construct();
        if( !is_admin() ) return;
        $this->init_table();
        $this->init_meta_tables();
        $this->set_prefix(MC2_DB::prefix());
    }
    
    /**
     * Creates the table if needed
     */
    public function init_table() {
        $table_name = $this->get_table_name();
        require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
        maybe_create_table( $table_name, $this->get_create_table_query() );
    }
    
    /**
     * Creates the table if needed
     */
    public function init_meta_tables() {
        $table_names = $this->get_meta_tables();
        if( empty( $table_names ) ) return;
        require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
        $queries = $this->get_create_meta_table_queries();
        if( !is_iterable( $queries ) ) return;
        foreach( $queries as $table_name => $query ) {
            maybe_create_table( $table_name, $query );
        }
    }
    
    /**
     * Gets main table name
     *
     * @return string
     */
    public function get_table_name() : string {
        return $this->get_prefix().static::$table_name;
    }
    
    /**
     * @return string[]
     */
    public function meta_tables() : array {
        return [];
    }
    
    /**
     * Gets relationship tables
     *
     * @return array
     */
    public function get_meta_tables() : array {
        return $this->meta_tables();
    }
    
    /**
     * @return string
     */
    public function get_create_table_query() : string {
        $table_name = $this->get_table_name();
        return str_replace( '`table_name`', "`$table_name`", $this->create_table_query() );
    }
    
    /**
     *
     * The SQL query to create the required table for this class
     *
     * @return string
     */
    abstract function create_table_query() : string;
    
    /**
     * @return array
     */
    public function get_create_meta_table_queries() : array {
        $table_names = $this->get_meta_tables();
        foreach( $table_names as $key => $table_name ) {
            if( empty( $query = $this->create_meta_table_queries()[ $table_name ] ) ) {
                unset( $table_names[ $key ] );
                continue;
            }
            $table_name                 = $this->get_prefix().$table_name;
            $table_names[ $table_name ] = str_replace( '`table_name`', "`$table_name`", $query );
        }
        return $table_names;
    }
    
    /**
     *
     * The SQL queries to create the relationship tables for this class (if applicable) - make sure to use table_name as key in array
     *
     * @return array
     */
    protected function create_meta_table_queries() : array {
        return [];
    }
    
    /**
     * @return array|object|null
     */
    public static function get_all() {
        return static::get_results();
    }
    
    /**
     * @param        $where
     * @param string $select_column
     *
     * @return array|object|null
     */
    public static function get_results( $where = '', $select_column = '*' ) {
        $results = MC2_DB::get_results( static::$table_name, $where, $select_column );
        if( !is_iterable( $results ) || empty( $object = static::$object_class ) ) return $results;
        foreach( $results as $key => $result ) {
            $results[ $key ] = new $object();
        }
        return $results;
    }
    
    /**
     * @param        $where
     * @param string $select_column
     *
     * @return array|object|void|null
     */
    public static function get_row( $where, $select_column = '*' ) {
        return MC2_DB::get_row( static::$table_name, $where, $select_column );
    }
    
    /**
     * @param        $where
     * @param string $select_column
     *
     * @return array
     */
    public static function get_col( $where, $select_column = '*' ) {
        return MC2_DB::get_col( static::$table_name, $where, $select_column );
    }
    
    /**
     * @param        $where
     * @param string $select_column
     *
     * @return string|null
     */
    public static function get_var( $where, $select_column = '*' ) {
        return MC2_DB::get_var( static::$table_name, $where, $select_column );
    }
    
    /**
     * @param $data
     *
     * @return false|int
     */
    public static function insert( $data ) {
        return MC2_DB::insert( static::$table_name, $data );
    }
    
    /**
     * @param $data
     * @param $where
     *
     * @return false|int
     */
    public static function update( $data, $where ) {
        return MC2_DB::update( static::$table_name, $data, $where );
    }
    
    /**
     * @param $where
     *
     * @return false|int
     */
    public static function delete( $where ) {
        return MC2_DB::delete( static::$table_name, $where );
    }
    
    /**
     * @return string
     */
    public static function get_prefix() : string {
        return self::$prefix;
    }
    
    /**
     * @param string $prefix
     */
    public static function set_prefix( string $prefix ) {
        self::$prefix = $prefix;
    }
    
}