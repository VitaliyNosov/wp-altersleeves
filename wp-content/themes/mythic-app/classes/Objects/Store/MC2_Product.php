<?php

namespace Mythic\Objects\Store;

use Mythic\Abstracts\MC2_Object;
use Mythic\Helpers\MC2_DB;

class MC2_Product extends MC2_Object {
    
    protected static $table_name = 'products';
    
    protected $sku;
    protected $name;
    protected $searchable_name;
    protected $product_type;
    protected $price_id;
    protected $price;
    protected $price_usd;
    protected $price_eur;
    protected $last_edited;
    protected $images;
    
    /**
     * @param null $data
     */
    public function __construct( $data = null ) {
        parent::__construct( $data );
        $this->init_price();
        $this->init_images();
    }
    
    /**
     * @return string
     */
    public function get_sku() : string {
        return $this->sku;
    }
    
    /**
     * @param string $sku
     */
    public function set_sku( string $sku ) {
        $this->sku = $sku;
    }
    
    /**
     * @return string
     */
    public function get_name() : string {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function set_name( string $name ) {
        $this->name = $name;
    }
    
    /**
     * @return string
     */
    public function get_searchable_name() : string {
        return $this->searchable_name;
    }
    
    /**
     * @param string $searchable_name
     */
    public function set_searchable_name( string $searchable_name ) {
        $this->searchable_name = $searchable_name;
    }
    
    /**
     * @return int
     */
    public function get_product_type() : int {
        return $this->product_type;
    }
    
    /**
     * @param int $product_type
     */
    public function set_product_type( int $product_type ) {
        $this->product_type = $product_type;
    }
    
    /**
     * @return int
     */
    public function get_price_id() : int {
        return $this->price_id;
    }
    
    /**
     * @param int $price_id
     */
    public function set_price_id( int $price_id ) {
        $this->price_id = $price_id;
    }
    
    /**
     * @return mixed
     */
    public function get_price() {
        return $this->get_price_usd();
    }
    
    /**
     * @return mixed
     */
    public function get_price_formatted() {
        return $this->get_price_usd( true );
    }
    
    /**
     * @param mixed $price
     */
    public function set_price( $price ) {
        $this->price = $price;
    }
    
    /**
     * @return mixed
     */
    public function get_price_usd( bool $formatted = false ) {
        $price = $this->price_usd;
        return $formatted ? '$'.$price : $price;
    }
    
    /**
     * @return mixed
     */
    public function get_price_usd_formatted() {
        return $this->get_price_usd( true );
    }
    
    /**
     * @param mixed $price_usd
     */
    public function set_price_usd( $price_usd ) {
        $this->price_usd = $price_usd;
    }
    
    /**
     * @return mixed
     */
    public function get_price_eur( bool $formatted = false ) {
        $price = $this->price_eur;
        return $formatted ? '€'.$price : $price;
    }
    
    /**
     * @return mixed
     */
    public function get_price_eur_formatted() {
        return $this->get_price_eur( true );
    }
    
    /**
     * @param mixed $price_eur
     */
    public function set_price_eur( $price_eur ) {
        $this->price_eur = $price_eur;
    }
    
    /**
     * @return string
     */
    public function get_last_edited() : string {
        return $this->last_edited;
    }
    
    /**
     * @param string $last_edited
     */
    public function set_last_edited( string $last_edited ) {
        $this->last_edited = $last_edited;
    }
    
    /**
     * @return array
     */
    public function get_images() : array {
        return $this->images;
    }
    
    /**
     * @param array $images
     */
    public function set_images( array $images ) {
        $this->images = $images;
    }
    
    /**
     * Gets all the products images from the product_images table
     */
    public function init_price() {
        $price = MC2_DB::get_result(MC2_DB::prefix_table_name('product_prices'), [ 'id' => $this->get_price() ] );
        $this->set_price($price_usd = $price->usd);
        $this->set_price_usd($price_usd);
        $this->set_price_eur($price->eur);
    }
    
    /**
     * Gets all the products images from the product_images table
     */
    public function init_images() {
        $images = MC2_DB::get_results( $this->get_table_name(), [ 'product_id' => $this->get_id() ] );
        $this->set_images( $images );
    }
    
}