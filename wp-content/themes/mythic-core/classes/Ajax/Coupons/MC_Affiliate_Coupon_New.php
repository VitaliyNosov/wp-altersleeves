<?php

namespace Mythic_Core\Ajax\Coupons;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;

/**
 * Class AffiliateCouponNew
 *
 * @package Mythic_Core\Ajax\Coupons
 */
class MC_Affiliate_Coupon_New extends MC_Ajax {
    
    /**
     * @return array
     */
    public function required_values() : array {
        return [ 'affiliateCouponData' ];
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $result        = [ 'status' => 0, 'message' => 'Something went wrong' ];
        $create_result = MC_Affiliate_Coupon::registerNewAffiliatePromotion( $_POST['affiliateCouponData'] );
        if( empty( $create_result['status'] ) ) {
            if( !empty( $create_result['message'] ) ) $result['message'] = $create_result['message'];
            $this->success( $result );
        }
        
        $affiliate_coupons = MC_Affiliate_Coupon::generateCouponsSelectOptions( $_POST['affiliateCouponData']['userId'] );
        if( empty( $affiliate_coupons ) ) {
            $this->success( $result );
        }
        
        $result = [
            'status'      => 1,
            'couponsList' => $affiliate_coupons,
            'newCouponId' => $create_result['new_coupon_id'],
            'couponData'  => MC_Affiliate_Coupon::getAffiliatePromotionDataById( $create_result['new_coupon_id'] ),
            'message'     => '',
        ];
        
        $this->success( $result );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'mcRegisterNewAffiliateCoupon';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'mc_affiliate_coupon_data';
    }
    
}
