<?php

namespace Mythic_Core\Abstracts;

/**
 * Class MC_Onsite_Object
 *
 * @package Mythic_Core\Abstracts
 */
abstract class MC_Onsite_Object {
    
    const TABLE_NAME = '';
    const DB_ID      = 'id';
    public $id;
    public $result;
    
    /**
     * MC_Onsite_Object constructor.
     *
     * @param int $id
     */
    public function __construct( $id = 0 ) {
        $this->createTable();
        $this->setId( $id );
        if( !empty( $id ) && is_numeric( $id ) ) $this->load();
    }
    
    public function createTable() {
        $query = $this->tableQuery();
        if( empty( $query ) ) return;
        global $wpdb;
        $table_name = $wpdb->prefix.static::TABLE_NAME;
        if( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) return;
        require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
        dbDelta( $query );
    }
    
    /**
     * @return string
     */
    public function tableQuery() : string {
        return '';
    }
    
    /**
     * @param int $id
     */
    public function load( $id = 0 ) {
        global $wpdb;
        $table_name = $wpdb->prefix.static::TABLE_NAME;
        $query      = "SELECT * FROM $table_name WHERE ".static::DB_ID." = $id";
        $results    = $wpdb->get_results( $query, ARRAY_A );
        if( empty( $results ) ) return;
        $result = $results[0];
        $this->setResult( $result );
    }
    
    /**
     * @return string
     */
    public function getTableName() : string {
        global $wpdb;
        
        return $table_name = $wpdb->prefix.static::TABLE_NAME;
    }
    
    public function create() {
    }
    
    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * @param int $id
     */
    public function setId( $id ) {
        $this->id = $id;
    }
    
    /**
     * @return mixed
     */
    public function getResult() {
        return $this->result;
    }
    
    /**
     * @param mixed $result
     */
    public function setResult( $result ) {
        $this->result = $result;
    }
    
}