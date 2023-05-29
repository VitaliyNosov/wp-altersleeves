<?php

namespace Mythic_Core\Ajax\Coupons;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;

/**
 * Class AffiliateCouponUpdate
 *
 * @package Mythic_Core\Ajax\Coupons
 */
class MC_Affiliate_Coupon_Update extends MC_Ajax {
    
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
        $update_status = MC_Affiliate_Coupon::savePromotionData( $_POST['affiliateCouponData'] );
        if( !empty( $update_status['status'] ) ) {
            $result['couponData'] = MC_Affiliate_Coupon::getAffiliatePromotionDataById( $_POST['affiliateCouponData']['couponId'] );
            $result['status']     = 1;
        }
        
        $this->success( $result );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'mcUpdateAffiliateCoupon';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'mc_affiliate_coupon_data';
    }
    
}
