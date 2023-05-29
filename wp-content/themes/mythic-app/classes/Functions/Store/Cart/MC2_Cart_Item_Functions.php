<?php

namespace Mythic\Functions\Store\Cart;

use Exception;
use Mythic\Objects\Store\MC2_Affiliate_Coupon_Functions;

/**
 * Class MC2_Cart_Item_Functions
 *
 * @package Mythic\Functions
 */
class MC2_Cart_Item_Functions {

    public const AJAX_REMOVE_BY_PRODUCT_ID    = 'mc-remove-cart-item-by-product-id';
    public const AJAX_ADD_PROMOTIONAL_PRODUCT = 'mc-add-promotional-item-to-cart';

    /**
     * @param int $product_id
     * @param int $quantity
     *
     * @return bool|string
     * @throws Exception
     */
    public static function addToCart( $product_id, $quantity = 1 ) {
        if( !WOO_ACTIVE ) return '';
        if( empty( $product_id ) ) return '';

        return WC()->cart->add_to_cart( $product_id, $quantity );
    }

    /**
     * @param string $function
     * @param array  $args
     *
     * @return array
     */
    public static function ajax( $function = '', $args = [] ) : array {
        switch( $function ) {
            case self::AJAX_ADD_PROMOTIONAL_PRODUCT :
                MC2_Affiliate_Coupon_Functions::addPromotionalProductToCart( $args );
                break;
            case self::AJAX_REMOVE_BY_PRODUCT_ID :
                $product_id = $args['product_id'] ?? 0;
                if( !empty( $product_id ) ) MC2_Cart_Item_Functions::removeFromCart( $product_id );
                break;
        }

        return MC2_Cart_Functions::returnForFrontEnd();
    }

    /**
     * @param $product_id
     */
    public static function removeFromCart( $product_id ) {
        foreach( WC()->cart->get_cart() as $key => $item ) {
            if( $item['product_id'] == $product_id ) {
                WC()->cart->remove_cart_item( $key );
                break;
            }
        }
    }

    /**
     * @param string $key
     *
     * @return array|null
     */
    public static function getCartItem( $key = '' ) : array {
        if( !function_exists( 'WC' ) || empty( $key ) ) return [];

        return WC()->cart->get_cart_item( $key );
    }

    /**
     * @param string $key
     *
     * @return int
     */
    public static function getProductId( $key = '' ) : int {
        $item = self::getCartItem( $key );
        if( empty( $item ) || !is_array( $item ) ) return 0;

        return $item['product_id'];
    }

    /**
     * @param string $key
     *
     * @return int
     */
    public static function getQuantity( $key = '' ) : int {
        $item = self::getCartItem( $key );
        if( empty( $item ) || !is_array( $item ) ) return 0;

        return $item['quantity'];
    }

    /**
     * @param      $cart_item_key
     * @param null $key
     * @param null $default
     *
     * @return array|mixed|null
     */
    public static function getMeta( $cart_item_key, $key = null, $default = null ) {
        if( !function_exists( 'WC' ) ) return [];
        $data = (array) WC()->session->get( '_as_woo_product_data' );
        if( empty( $data[ $cart_item_key ] ) ) $data[ $cart_item_key ] = [];
        if( $key != null ) {
            return empty( $data[ $cart_item_key ][ $key ] ) ? $default : $data[ $cart_item_key ][ $key ];
        }

        return $data[ $cart_item_key ] ? $data[ $cart_item_key ] : $default;
    }

    /**
     * @param $cart_item_key
     * @param $key
     * @param $value
     */
    public static function setMeta( $cart_item_key, $key, $value ) {
        if( !function_exists( 'WC' ) ) return;
        $data = (array) WC()->session->get( '_as_woo_product_data' );
        if( empty( $data[ $cart_item_key ] ) ) $data[ $cart_item_key ] = [];
        $data[ $cart_item_key ][ $key ] = $value;
        WC()->session->set( '_as_woo_product_data', $data );
    }

    /**
     * @param null $cart_item_key
     * @param null $key
     */
    public static function removeMeta( $cart_item_key = null, $key = null ) {
        if( !function_exists( 'WC' ) ) return;
        $data = (array) WC()->session->get( '_as_woo_product_data' );
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
    }

}