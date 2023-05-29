<?php

namespace Mythic_Core\Objects;

use Mythic_Core\Abstracts\MC_Onsite_Object;
use Mythic_Core\Functions\MC_Mtg_Card_Functions;

/**
 * Class MC_Shortlink
 *
 * @package Mythic_Core\Objects
 */
abstract class MC_Shortlink extends MC_Onsite_Object {
    
    const TABLE_NAME     = 'wp_as_shortened_links';
    const DB_ID          = 'id';
    const DB_SLUG        = 'slug';
    const DB_DESTINATION = 'destination';
    const DB_URL         = 'url';
    public $slug;
    public $destination;
    public $url;
    
    /**
     * MC_Shortlink constructor.
     *
     * @param int $id
     */
    public function __construct( $id = 0 ) {
        $this->createTable();
        
        if( !empty( $id ) && is_numeric( $id ) ) $this->load();
    }
    
    /**
     * @param string $slug
     */
    public static function redirectFromSlug( $slug = '' ) {
        if( is_home() || is_front_page() ) return;
        $urls = self::getBySlug( $slug );
        if( empty( $urls ) ) return;
        $url = $urls[0];
        $url = $url->destination;
        wp_redirect( $url );
        exit();
    }
    
    /**
     * @param string $slug
     *
     * @return array|object|null
     */
    public static function getBySlug( $slug = '' ) {
        if( empty( $slug ) ) return [];
        global $wpdb;
        $table_name = self::TABLE_NAME;
        $query      = 'SELECT * FROM '.$table_name.' WHERE slug = "'.$slug.'";';
        return $wpdb->get_results( $query );
    }
    
    /**
     * @return mixed
     */
    public function getDestination() {
        return $this->destination;
    }
    
    /**
     * @param mixed $destination
     */
    public function setDestination( $destination ) {
        $this->destination = $destination;
    }
    
    /**
     * @return mixed
     */
    public function getSlug() {
        return $this->slug;
    }
    
    /**
     * @param mixed $slug
     */
    public function setSlug( $slug ) {
        $this->slug = $slug;
    }
    
    /**
     * @return mixed
     */
    public function getUrl() {
        return $this->url;
    }
    
    /**
     * @param mixed $url
     */
    public function setUrl( $url ) {
        $this->url = $url;
    }
    
    public function initSetters() {
        $result = $this->getResult();
        if( !is_array( $result ) || empty( $result ) ) return false;
        if( !isset( $result[ self::DB_ID ] ) || !isset( $result[ self::DB_SLUG ] ) || !isset( $result[ self::DB_DESTINATION ] ) || !isset( $result[ self::DB_URL ] ) ) {
            return false;
        }
        $this->id          = $result[ self::DB_ID ];
        $this->slug        = $result[ self::DB_SLUG ];
        $this->destination = $result[ self::DB_DESTINATION ];
        $this->url         = $result[ self::DB_URL ];
        
        return true;
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
    
    /**
     * @return bool|false|int
     */
    public function create() {
        global $wpdb;
        
        $destination = !empty( $this->getDestination() ) ? $this->getDestination() : 0;
        
        if( empty( $destination ) ) return false;
        
        $this->generateSlug();
        $slug = $this->getSlug();
        $url  = 'https://altrslv.co/'.$slug;
        $this->setUrl( $url );
        
        $tableName = self::TABLE_NAME;
        
        return $wpdb->insert( $tableName, [
            self::DB_SLUG        => $slug,
            self::DB_DESTINATION => $destination,
            self::DB_URL         => $url,
        ] );
    }
    
    public function generateSlug() {
        $phrase = MC_Mtg_Card_Functions::generatePassPhrase();
        $this->setSlug( $phrase );
    }
    
}