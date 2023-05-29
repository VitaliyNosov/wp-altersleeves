<?php

namespace Mythic_Core\Objects\Store;

use WC_Coupon;

/**
 * Class MC_Coupon
 *
 * @package Mythic_Core\Objects\Store
 */
class MC_Coupon {
    
    /**
     * Creates new WC_Coupon
     *
     * @param $coupon_data
     *
     * @return array
     */
    public static function createCoupon( $coupon_data ) {
        $coupon = new WC_Coupon();
        
        if( !is_array( $coupon_data ) ) {
            $coupon->set_code( $coupon_data );
        } else if( !empty( $coupon_data['promotionTitle'] ) ) {
            $coupon->set_code( $coupon_data['promotionTitle'] );
            if( !empty( $coupon_data['couponType'] ) ) {
                $coupon->set_discount_type( $coupon_data['couponType'] );
            }
            if( !empty( $coupon_data['discountValue'] ) ) {
                $coupon->set_amount( $coupon_data['discountValue'] );
            }
            $free_shipping = !empty( $coupon_data['freeUntrackedShipping'] ) ? true : false;
            $coupon->set_free_shipping( $free_shipping );
        } else {
            return [ 'status' => 0, 'result' => false ];
        }
        
        $coupon->save();
        
        return [ 'status' => 1, 'result' => $coupon->get_id() ];
    }
    
}