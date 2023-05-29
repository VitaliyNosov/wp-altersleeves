<?php

namespace Mythic_Core\Functions;

use Mythic_Core\Users\MC_Affiliates;

/**
 * Class MC_Woo_Coupon_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Woo_Coupon_Functions {
    
    /**
     * @return bool
     */
    public static function isFreeShipping() {
        global $woocommerce;
        
        $coupon_args = [
            'post_type'      => 'shop_coupon',
            'post_status'    => [ 'publish' ],
            'posts_per_page' => -1,
            'meta_query'     => [
                [
                    'key'   => 'free_shipping',
                    'value' => 'yes',
                ],
            ],
        ];
        $coupons     = get_posts( $coupon_args );
        $free_shipping_coupons = [];
        foreach( $coupons as $coupon ) {
            $free_shipping_coupons[] = $coupon->post_title;
        }
        $free_shipping_coupons = array_unique( $free_shipping_coupons );
        foreach( $free_shipping_coupons as $free_shipping_coupon ) {
            if( in_array( strtolower( $free_shipping_coupon ), $woocommerce->cart->get_applied_coupons() ) ) {
                return true;
            }
        }
        
        return false;
    }
    
}