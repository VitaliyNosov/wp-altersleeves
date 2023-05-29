<?php

namespace Mythic_Core\Functions;

use Exception;
use Mythic_Core\Abstracts\MC_Post_Type_Functions;

/**
 * Class MC_Woo_Order_Item_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Woo_Order_Item_Functions extends MC_Post_Type_Functions {
    
    /**
     * @param $item_id
     * @param $values
     * @param $cart_item_key
     *
     * @throws Exception
     */
    public static function transferMetaFromCartItemToOrderItem( $item_id, $values, $cart_item_key ) {
        $cart_item_data = MC_Woo_Cart_Item_Functions::getMeta( $cart_item_key );
        if( empty( $cart_item_data ) ) return;
        wc_add_order_item_meta( $item_id, '_as_woo_product_data', $cart_item_data );
        if( is_array( $cart_item_data ) ) {
            foreach( $cart_item_data as $key => $order_item_data ) {
                wc_add_order_item_meta( $item_id, $key, $order_item_data );
            }
        }
    }
    
    /**
     * @param int    $item_id
     * @param string $key
     * @param string $default
     *
     * @return string
     * @throws Exception
     */
    public static function meta( int $item_id = 0, string $key = '', $default = '' ) : string {
        $meta = wc_get_order_item_meta( $item_id, '_as_woo_product_data', true );
        if( !is_array( $meta ) ) return $default;
        
        return isset( $meta[ $key ] ) ? $meta[ $key ] : $default;
    }
    
}