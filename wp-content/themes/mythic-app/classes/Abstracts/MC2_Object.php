<?php

namespace Mythic\Abstracts;

use Mythic\Helpers\MC2_DB;

class MC2_Object {
    
    protected static $primary_key = 'id';
    protected static $table_name;
    private $_data = [];
    protected $id;
    
    /**
     * @param null $data
     */
    public function __construct( $data = null ) {
        if( [ $data ] ) {
            $this->_data = $data;
            $this->allocate_values( $data );
        }
        if( is_numeric( $data ) ) $this->load_from_primary_key( $data );
    }
    
    /**
     * @param $data
     */
    public function allocate_values( $data ) {
        if( !is_iterable( $data ) ) $data = (array) $data;
        foreach( $data as $property => $value ) {
            $set = 'set_'.$property;
            if( method_exists( $this, $set ) ) $this->$set( $value );
        }
    }
    
    /**
     * @param $property
     *
     * @return mixed|null
     */
    public function __get( $property ) {
        return array_key_exists( $property, $this->_data )
            ? $this->_data[ $property ]
            : null;
    }
    
    /**
     * @param $property
     * @param $value
     *
     * @return mixed
     */
    public function __set( $property, $value ) {
        return $this->_data[ $property ] = $value;
    }
    
    /**
     * @return mixed
     */
    public function get_id() {
        return $this->id;
    }
    
    /**
     * @param mixed $id
     */
    public function set_id( $id ) {
        $this->id = $id;
    }
    
    /**
     * @return string
     */
    public function get_table_name() : string {
        $prefix     = MC2_DB_Table::prefix();
        $table_name = static::$table_name;
        if( strpos( $table_name, $prefix ) === false ) $table_name = $prefix.$table_name;
        return $table_name;
    }
    
    /**
     * @param $value
     *
     * @return bool
     */
    public function load_from_primary_key( $value ) : bool {
        $result = MC2_DB::get_result( $this->get_table_name(), [ static::$primary_key, $value ] );
        if( empty( $result ) ) return false;
        $result      = is_object( $result ) ? (array) $result : $result;
        $this->_data = $result;
        return true;
    }
    
    public function save() {
        $data  = $this->get_object_data();
        $where = $this->get_where();
        $id    = $this->get_id();
        if( !empty( $where ) || $this->exists( $id ) ) {
            if( empty( $where ) ) $where = [ self::get_primary_key() => $id ];
            return $this->update( $data, $where );
        } else {
            unset( $data['id'] );
            $result = $this->insert( $data );
            if( empty( $result ) ) return false;
            $this->set_id( $result );
            return true;
        }
    }
    
    /**
     * @return false|int
     */
    public function remove() {
        return $this->delete( [ self::get_primary_key() => $this->get_id() ] );
    }
    
    /**
     * @param $where
     *
     * @return false|int
     */
    public function delete( $where ) {
        return MC2_DB::delete( $this->get_table_name(), $where );
    }
    
    /**
     * @param $data
     *
     * @return false|int
     */
    public function insert( $data ) {
        return MC2_DB::insert( $this->get_table_name(), $data );
    }
    
    /**
     * @param $data
     * @param $where
     *
     * @return false|int
     */
    public function update( $data, $where ) {
        return MC2_DB::update( $this->get_table_name(), $data, $where );
    }
    
    /**
     * @param int $value
     *
     * @return bool
     */
    public function exists( $value = 0 ) : bool {
        return MC2_DB::exists( $this->get_table_name(), self::get_primary_key(), $value );
    }
    
    function reset() {
        foreach( $this as $key => $value ) {
            unset( $this->$key );
        }
    }
    
    /**
     * @return string
     */
    public static function get_primary_key() : string {
        return self::$primary_key;
    }
    
    /**
     * @return array
     */
    public function get_object_data() : array {
        return [];
    }
    
    /**
     * @return array
     */
    public function get_where() : array {
        return [];
    }
    
    /**
     * @param $condition
     *
     * @return mixed
     */
    public function get_id_where( $condition ) {
        return MC2_DB::get_var( $this->get_table_name(), $condition, 'id' );
    }
    
}