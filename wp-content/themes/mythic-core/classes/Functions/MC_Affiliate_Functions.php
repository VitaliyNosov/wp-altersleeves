<?php

namespace Mythic_Core\Functions;

/**
 * Class MC_Retailer_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Affiliate_Functions {
    
    /**
     * @param int    $affiliate_id
     * @param string $affiliate_coupon
     *
     * @return array|null
     */
    public static function getOrdersWithAffiliateCoupon( $affiliate_id = 0, $affiliate_coupon = '' ) {
        if( empty( $affiliate_id ) ) $affiliate_id = get_current_user_id();
        if( empty( $affiliate_id ) ) return null;
        
        global $wpdb;
        $query = "SELECT post_id FROM wp_postmeta WHERE meta_key = '_mc_order_affiliate_user_id' AND meta_value = $affiliate_id";
        
        return $wpdb->get_col( $query );
    }
    
}