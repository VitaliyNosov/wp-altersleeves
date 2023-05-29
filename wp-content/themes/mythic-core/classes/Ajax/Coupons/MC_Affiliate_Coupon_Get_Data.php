<?php

namespace Mythic_Core\Ajax\Coupons;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;

/**
 * Class AffiliateCouponGetData
 *
 * @package Mythic_Core\Ajax\Coupons
 */
class MC_Affiliate_Coupon_Get_Data extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $result = [ 'status' => 0, 'message' => 'Something went wrong' ];
        if( !empty( $_POST['affiliateCouponId'] ) ) {
            $result['couponData'] = MC_Affiliate_Coupon::getAffiliatePromotionDataById( $_POST['affiliateCouponId'] );
        } else if( !empty( $_POST['affiliateId'] ) ) {
            $coupons_list      = [];
            $affiliate_coupons = MC_Affiliate_Coupon::generateCouponsSelectOptions( $_POST['affiliateId'] );
            if( empty( $affiliate_coupons ) ) {
                $coupons_list[] = [ 0 => 'You need create coupons first' ];
            } else {
                $single_coupon_added = 0;
                foreach( $affiliate_coupons as $affiliate_coupon_key => $affiliate_coupon ) {
                    if( empty( $single_coupon_added ) ) {
                        $result['couponData'] = MC_Affiliate_Coupon::getAffiliatePromotionDataById( $affiliate_coupon_key );
                        $single_coupon_added  = 1;
                    }
                    $coupons_list[ $affiliate_coupon_key ] = $affiliate_coupon;
                }
            }
            $result['couponsList'] = $coupons_list;
        } else {
            $this->success( $result );
        }
        
        $result['status'] = 1;
        
        $this->success( $result );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'mcGetAffiliateCouponData';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'mc_affiliate_coupon_data';
    }
    
}
