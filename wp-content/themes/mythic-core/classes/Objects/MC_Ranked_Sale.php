<?php

namespace Mythic_Core\Objects;

use Mythic_Core\Abstracts\MC_Onsite_Object;
use Mythic_Core\Functions\MC_Product_Functions;
use Mythic_Core\Functions\MC_Royalty_Functions;
use Mythic_Core\System\MC_Statuses;
use Mythic_Core\System\MC_WP;

/**
 * Class MC_Ranked_Sale
 *
 * @package Mythic_Core\Objects
 */
class MC_Ranked_Sale extends MC_Onsite_Object {
    
    const TABLE_NAME    = 'as_ranked_sales';
    const DB_ID         = 'id';
    const DB_CREATOR_ID = 'creator_id';
    const DB_PRODUCT_ID = 'product_id';
    const DB_TOTAL      = 'total';
    public $total;
    protected $idCreator;
    protected $idProduct;
    
    public static function updateRanked_Sales() {
        // Simple (Alters) only atm
        $argsProductsSimple = [
            'post_type'      => 'product',
            'post_status'    => MC_Statuses::keys(),
            'posts_per_page' => -1,
            'tax_query'      => [
                [
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'simple',
                ],
            ],
            'fields'         => 'ids',
        ];
        $productsSimple     = get_posts( $argsProductsSimple );
        foreach( $productsSimple as $product_idSimple ) {
            $rankedSale = new MC_Ranked_Sale();
            $rankedSale->setIdProduct( $product_idSimple );
            $sales = MC_Royalty_Functions::getByProductId( $product_idSimple );
            $total = 0;
            foreach( $sales as $sale ) {
                $total = $total + $sale->quantity;
            }
            if( empty( $total ) ) continue;
            $rankedSale->setTotal( $total );
            $rankedSale->save();
        }
    }
    
    /**
     * @return bool|false|int
     */
    public function save() {
        global $wpdb;
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        $product_id = $this->getIdProduct();
        $total      = $this->getTotal();
        if( empty( $product_id ) || empty( $total ) ) return 0;
        
        $idCreator = !empty( $this->getIdCreator() ) ? $this->getIdCreator() : MC_WP::authorId( $product_id );
        
        if( self::exists( $this->idProduct ) ) {
            $wpdb->update( $table_name, [
                'creator_id' => $idCreator,
                'total'      => $total,
            ],             [ 'product_id' => $product_id ] );
        } else {
            return $wpdb->insert( $table_name, [
                'creator_id' => $idCreator,
                'product_id' => $product_id,
                'total'      => $total,
            ] );
        }
        
        return 0;
    }
    
    /**
     * @return int
     */
    public function getIdProduct() {
        return (int) $this->idProduct;
    }
    
    /**
     * @param int $product_id
     */
    public function setIdProduct( $product_id ) {
        $this->idProduct = $product_id;
    }
    
    /**
     * @return int
     */
    public function getTotal() {
        return (int) $this->total;
    }
    
    /**
     * @param int $total
     */
    public function setTotal( $total ) {
        $this->total = $total;
    }
    
    /**
     * @return int
     */
    public function getIdCreator() {
        return (int) $this->idCreator;
    }
    
    /**
     * @param int $idCreator
     */
    public function setIdCreator( $idCreator ) {
        $this->idCreator = $idCreator;
    }
    
    /**
     * @param int $product_id
     *
     * @return bool
     */
    public static function exists( $product_id = 0 ) {
        if( empty( $product_id ) ) return false;
        global $wpdb;
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        $query      = "SELECT * FROM $table_name WHERE ".self::DB_PRODUCT_ID." = $product_id";
        $results    = $wpdb->get_results( $query, ARRAY_A );
        if( !empty( $results ) ) return true;
        
        return false;
    }
    
    /**
     * @param int $idCreator
     * @param int $limit
     *
     * @return array|mixed
     */
    public static function getSalesForCreator( $idCreator = 0, $limit = 50, $page = 1 ) {
        if( empty( $idCreator ) || !is_numeric( $idCreator ) ) return [];
        $offset = ( $page - 1 ) * $limit;
        global $wpdb;
        $table_name  = $wpdb->prefix.self::TABLE_NAME;
        $idsSnapbolt = MC_Product_Functions::snapboltIdsSql();
        $query       = 'SELECT * FROM '.$table_name.' WHERE '.self::DB_CREATOR_ID.'="'.$idCreator.'" AND product_id NOT IN '.$idsSnapbolt;
        $query       .= ' ORDER BY total DESC';
        if( $offset > 0 ) $query .= $query.'  OFFSET '.$offset;
        $query   .= ' LIMIT '.$limit;
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results;
    }
    
    /**
     * @param int $idCreator
     * @param int $limit
     *
     * @return array|mixed
     */
    public static function getSumForCreator( $idCreator = 0 ) {
        if( empty( $idCreator ) || !is_numeric( $idCreator ) ) return 0;
        global $wpdb;
        $table_name  = $wpdb->prefix.self::TABLE_NAME;
        $idsSnapbolt = MC_Product_Functions::snapboltIdsSql();
        $query       = 'SELECT sum(total) as total_sold FROM '.$table_name.' WHERE '.self::DB_CREATOR_ID.'="'.$idCreator.'" AND product_id NOT IN '.$idsSnapbolt;
        
        return $wpdb->get_results( $query )[0]->total_sold;
    }
    
    /**
     * @param int $limit
     *
     * @return array|object
     */
    public static function getAll( $limit = 50 ) {
        global $wpdb;
        $table_name  = $wpdb->prefix.self::TABLE_NAME;
        $idsSnapbolt = MC_Product_Functions::snapboltIdsSql();
        $query       = 'SELECT * FROM '.$table_name.' WHERE product_id NOT IN '.$idsSnapbolt.' AND product_id != 182195';
        $query       .= ' AND creator_id != 59';
        $query       .= ' ORDER BY RAND()';
        $query       .= ' LIMIT '.$limit;
        $results     = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results;
    }
    
    /**
     * @return array|object
     */
    public static function getForHome() {
        global $wpdb;
        $table_name  = $wpdb->prefix.self::TABLE_NAME;
        $idsSnapbolt = MC_Product_Functions::snapboltIdsSql();
        $query       = 'SELECT * FROM '.$table_name.' WHERE product_id NOT IN '.$idsSnapbolt.' AND product_id != 182195';
        $query       .= is_front_page() ? ' LIMIT 200' : ' ORDER BY total DESC LIMIT 200';
        $results     = $wpdb->get_results( $query );
        if( is_front_page() ) {
            shuffle( $results );
            $results = array_slice($results,0,5);
        }
        if( $results == null || empty( $results ) ) return [];
        
        return $results;
    }
    
    /**
     * @param $row
     *
     * @return bool
     */
    public function initSetters( $row = [] ) {
        if( !is_array( $row ) || empty( $row ) ) return false;
        if( !isset( $row[ self::DB_ID ] ) || !isset( $row[ self::DB_CREATOR_ID ] ) || !isset( $row[ self::DB_PRODUCT_ID ] ) || !isset( $row[ self::DB_TOTAL ] ) ) {
            return false;
        }
        $this->id        = $row[ self::DB_ID ];
        $this->idCreator = $row[ self::DB_CREATOR_ID ];
        $this->idProduct = $row[ self::DB_PRODUCT_ID ];
        $this->total     = $row[ self::DB_TOTAL ];
        
        return true;
    }
    
    /**
     * @return int
     */
    public function getId() {
        return (int) $this->id;
    }
    
    /**
     * @param int $id
     */
    public function setId( $id ) {
        $this->id = $id;
    }
    
}