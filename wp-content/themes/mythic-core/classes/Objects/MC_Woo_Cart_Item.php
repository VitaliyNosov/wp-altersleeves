<?php

namespace Mythic_Core\Objects;

/**
 * Class MC_Woo_Cart_Item
 *
 * @package Mythic_Core\Objects
 */
class MC_Woo_Cart_Item {
    
    protected $data = '';
    protected $data_hash = '';
    protected $product_id = 0;
    protected $quantity = 0;
    protected $variation = '';
    protected $variation_id = 0;
    protected $key = '';
    protected $meta = [];
    
    /**
     * MC_Woo_Cart_Item constructor.
     *
     * @param string $key
     */
    public function __construct( $key = '' ) {
        if( !MC_WOO_ACTIVE || empty( $key ) ) return;
        $cart_item = WC()->cart->get_cart_item( $key );
        if( empty( $cart_item ) ) return;
        $this->setData( $cart_item['data'] );
        $this->setDataHash( $cart_item['data_hash'] );
        $this->setProductId( $cart_item['product_id'] );
        $this->setQuantity( $cart_item['quantity'] );
        $this->setVariation( $cart_item['variation'] );
        $this->setVariationId( $cart_item['variation_id'] );
        $this->setKey( $cart_item['key'] );
        $meta = $this->getSessionItemMeta( $key );
        $this->setMeta( $meta );
    }
    
    /**
     * @return string
     */
    public function getData() : string {
        return $this->data;
    }
    
    /**
     * @param string $data
     */
    public function setData( string $data ) {
        $this->data = $data;
    }
    
    /**
     * @return string
     */
    public function getDataHash() : string {
        return $this->data_hash;
    }
    
    /**
     * @param string $data_hash
     */
    public function setDataHash( string $data_hash ) {
        $this->data_hash = $data_hash;
    }
    
    /**
     * @return int
     */
    public function getProductId() : int {
        return $this->product_id;
    }
    
    /**
     * @param int $product_id
     */
    public function setProductId( int $product_id ) {
        $this->product_id = $product_id;
    }
    
    /**
     * @return int
     */
    public function getQuantity() : int {
        return $this->quantity;
    }
    
    /**
     * @param int $quantity
     */
    public function setQuantity( int $quantity ) {
        $this->quantity = $quantity;
    }
    
    /**
     * @return string
     */
    public function getVariation() : string {
        return $this->variation;
    }
    
    /**
     * @param string $variation
     */
    public function setVariation( string $variation ) {
        $this->variation = $variation;
    }
    
    /**
     * @return int
     */
    public function getVariationId() : int {
        return $this->variation_id;
    }
    
    /**
     * @param int $variation_id
     */
    public function setVariationId( int $variation_id ) {
        $this->variation_id = $variation_id;
    }
    
    /**
     * @return string
     */
    public function getKey() : string {
        return $this->key;
    }
    
    /**
     * @param string $key
     */
    public function setKey( string $key ) {
        $this->key = $key;
    }
    
    /**
     * @return array
     */
    public function getMeta() : array {
        return $this->meta;
    }
    
    /**
     * @param array $meta
     */
    public function setMeta( array $meta ) {
        $this->meta = $meta;
    }
    
    /**
     * @param      $cart_item_key
     * @param null $key
     * @param null $default
     *
     * @return array|mixed|null
     */
    public function getSessionItemMeta( $cart_item_key, $key = 'null', $default = null ) {
        $data = (array) WC()->session->get( '_as_woo_product_data' );
        if( empty( $data[ $cart_item_key ] ) ) $data[ $cart_item_key ] = [];
        if( $key != null ) {
            return empty( $data[ $cart_item_key ][ $key ] ) ? $default : $data[ $cart_item_key ][ $key ];
        }
        
        return $data[ $cart_item_key ] ? $data[ $cart_item_key ] : $default;
    }
    
    /**
     * @param $key
     * @param $value
     */
    function setMetaByKey( $key, $value ) {
        $cart_item_key = $this->getKey();
        $data          = (array) WC()->session->get( '_as_woo_product_data' );
        if( empty( $data[ $cart_item_key ] ) ) $data[ $cart_item_key ] = [];
        $data[ $cart_item_key ][ $key ] = $value;
        WC()->session->set( '_as_woo_product_data', $data );
        $this->setMeta( $data );
    }
    
    /**
     * @param null $key
     */
    function removeMeta( $key = null ) {
        $cart_item_key = $this->getKey();
        $data          = (array) WC()->session->get( '_as_woo_product_data' );
        if( $cart_item_key == null ) {
            WC()->session->set( '_as_woo_product_data', [] );
            
            return;
        }
        if( !isset( $data[ $cart_item_key ] ) ) return;
        
        if( $key == null ) {
            unset( $data[ $cart_item_key ] );
        } else {
            if( isset( $data[ $cart_item_key ][ $key ] ) ) {
                unset( $data[ $cart_item_key ][ $key ] );
            }
        }
        
        WC()->session->set( '_as_woo_product_data', $data );
        $this->setMeta( $data );
    }
    
}