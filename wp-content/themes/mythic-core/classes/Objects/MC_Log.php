<?php

namespace Mythic_Core\Objects;

/**
 * Class MC_Log
 *
 * @package Mythic_Core\Objects
 */
class MC_Log {
    
    const TABLE_NAME  = 'as_logs';
    const DB_ID       = 'id';
    const DB_ADMIN_ID = 'admin_id';
    const DB_USER_ID  = 'user_id';
    const DB_ITEM_ID  = 'item_id';
    const DB_DATE     = 'date';
    const DB_CATEGORY = 'category';
    const DB_MESSAGE  = 'message';
    protected $id;
    protected $idAdmin;
    protected $idUser;
    protected $item_id;
    protected $date;
    protected $category;
    protected $message;
    
    /**
     * Log constructor.
     *
     * @param int $id
     */
    public function __construct( $id = 0 ) {
        $this->createTable();
        
        if( !empty( $id ) && is_numeric( $id ) ) $this->load();
    }
    
    public function createTable() {
        global $wpdb;
        $tableName = $wpdb->prefix.self::TABLE_NAME;
        if( $wpdb->get_var( "SHOW TABLES LIKE '$tableName'" ) == $tableName ) return;
        require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
        $tableName = $wpdb->prefix.self::TABLE_NAME;
        $sql       = "CREATE TABLE $tableName (
                      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                      `admin_id` int(11) NOT NULL,
                      `user_id` int(11) NOT NULL,
                      `item_id` int(11) NOT NULL,
                      `date` datetime NOT NULL,
                      `category` int(11) NOT NULL,
                      `message` longtext NOT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        dbDelta( $sql );
    }
    
    /**
     * @param int $id
     *
     * @return array|mixed
     */
    public function load( $id = 0 ) {
        if( empty( $id ) ) return [];
        global $wpdb;
        $tableName = $wpdb->prefix.self::TABLE_NAME;
        $query     = "SELECT * FROM $tableName WHERE ".self::DB_ID." = $id";
        $results   = $wpdb->get_results( $query, ARRAY_A );
        if( $results == null ) return [];
        $result = $results[0];
        $load   = $this->loadByDbRow( $result );
        if( !$load ) return [];
        
        return $results[0];
    }
    
    /**
     * @param $row
     *
     * @return bool
     */
    public function loadByDbRow( $row = [] ) {
        if( !is_array( $row ) || empty( $row ) ) return false;
        if( !isset( $row[ self::DB_ID ] ) || !isset( $row[ self::DB_ADMIN_ID ] ) || !isset( $row[ self::DB_USER_ID ] ) || !isset( $row[ self::DB_DATE ] ) || !isset( $row[ self::DB_CATEGORY ] ) || !isset( $row[ self::DB_MESSAGE ] ) ) {
            return false;
        }
        $this->id       = $row[ self::DB_ID ];
        $this->idAdmin  = $row[ self::DB_ADMIN_ID ];
        $this->idUser   = $row[ self::DB_USER_ID ];
        $this->idItem   = $row[ self::DB_ITEM_ID ];
        $this->date     = $row[ self::DB_DATE ];
        $this->category = $row[ self::DB_CATEGORY ];
        $this->message  = $row[ self::DB_MESSAGE ];
        
        return true;
    }
    
    /**
     * @param array $args
     *
     * @return array|object|null
     */
    public static function getByDetails( $args = [] ) {
        if( empty( $args ) ) return [];
        global $wpdb;
        $tableName = $wpdb->prefix.self::TABLE_NAME;
        $query     = 'SELECT * FROM '.$tableName.' WHERE ';
        
        $count = count( $args );
        $x     = 0;
        foreach( $args as $key => $arg ) {
            $query .= $key.'="'.$arg.'"';
            $x++;
            if( $x == $count ) break;
            $query .= ' AND ';
        }
        
        return $wpdb->get_results( $query );
    }
    
    /**
     * @return bool|false|int
     */
    public function create() {
        global $wpdb;
        
        $idAdmin  = $this->getIdAdmin();
        $idUser   = !empty( $this->getIdUser() ) ? $this->getIdUser() : $idAdmin;
        $item_id  = !empty( $this->getIdItem() ) ? $this->getIdItem() : 0;
        $category = !empty( $this->getCategory() ) ? $this->getCategory() : 0;
        $message  = !empty( $this->getMessage() ) ? $this->getMessage() : self::defaultMessage( $category );
        $time     = time();
        $date     = date( 'Y-m-d H:i:s', $time );
        
        $tableName = $wpdb->prefix.self::TABLE_NAME;
        
        return $wpdb->insert( $tableName, [
            self::DB_ADMIN_ID => $idAdmin,
            self::DB_USER_ID  => $idUser,
            self::DB_ITEM_ID  => $item_id,
            self::DB_DATE     => $date,
            self::DB_CATEGORY => $category,
            self::DB_MESSAGE  => $message,
        ] );
    }
    
    /**
     * @return mixed
     */
    public function getIdAdmin() {
        return $this->idAdmin;
    }
    
    /**
     * @param mixed $idAdmin
     */
    public function setIdAdmin( $idAdmin ) {
        $this->idAdmin = $idAdmin;
    }
    
    /**
     * @return mixed
     */
    public function getIdUser() {
        return $this->idUser;
    }
    
    /**
     * @param mixed $idUser
     */
    public function setIdUser( $idUser ) {
        $this->idUser = $idUser;
    }
    
    /**
     * @return mixed
     */
    public function getIdItem() {
        return $this->idItem;
    }
    
    /**
     * @param mixed $item_id
     */
    public function setIdItem( $item_id ) {
        $this->idItem = $item_id;
    }
    
    /**
     * @return mixed
     */
    public function getCategory() {
        return $this->category;
    }
    
    /**
     * @param mixed $category
     */
    public function setCategory( $category ) {
        $this->category = $category;
    }
    
    /**
     * @return mixed
     */
    public function getMessage() {
        return $this->message;
    }
    
    /**
     * @param mixed $message
     */
    public function setMessage( $message ) {
        $this->message = $message;
    }
    
    /**
     * @param int $category
     *
     * @return false|string
     */
    public static function defaultMessage( $category = 0 ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/logs/'.$category.'.php' );
        
        return ob_get_clean();
    }
    
    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * @param mixed $id
     */
    public function setId( $id ) {
        $this->id = $id;
    }
    
}