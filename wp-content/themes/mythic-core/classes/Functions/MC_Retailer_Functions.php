<?php

namespace Mythic_Core\Functions;

use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;
use Mythic_Core\Users\MC_Affiliates;
use Mythic_Core\Users\MC_Retailer;

/**
 * Class MC_Retailer_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Retailer_Functions {
    
    /**
     * @param $input
     * @param $field
     *
     * @return mixed
     */
    public static function checkShippingValueForRetailerCoupon( $input, $field ) {
        $checkRetailerCouponInCart = static::checkRetailerCouponInCart();
        if( empty( $checkRetailerCouponInCart ) ) return $input;
        
        return static::updateShippingValue( $input, $field, $checkRetailerCouponInCart['retailer_id'] );
    }
    
    /**
     * @param $field
     * @param $retailer_id
     *
     * @return mixed
     */
    public static function updateShippingValue( $input, $field, $retailer_id ) {
        if( strpos( $field, 'shipping' ) === false ) return $input;
        $retailer_address = static::getRetailerAddressInGlobal( $retailer_id );
        
        if( !empty( $retailer_address ) && isset( $retailer_address[ $field ] ) ) return $retailer_address[ $field ];
        
        return $input;
    }
    
    /**
     * @param $retailer_id
     *
     * @return array|mixed|null
     */
    public static function getRetailerAddressInGlobal( $retailer_id ) {
        if(
            !empty( $GLOBALS['mc_retailer_data']['retailer_id'] ) &&
            $GLOBALS['mc_retailer_data']['retailer_id'] == $retailer_id &&
            !empty( $GLOBALS['mc_retailer_data']['retailer_address'] )
        ) return $GLOBALS['mc_retailer_data']['retailer_address'];
        
        $retailer = new MC_Retailer( $retailer_id );
        if( empty( $retailer ) ) return null;
        
        return $GLOBALS['mc_retailer_data']['retailer_address'] = $retailer->getRetailerAddress();
    }
    
    /**
     * @param string $retailer_coupon
     * @param int    $retailer_id
     *
     * @return array|bool|mixed|null
     */
    public static function checkRetailerCouponInCart( $retailer_coupon = '', $retailer_id = 0 ) {
        $getRetailerCouponInCartInGlobals = static::getRetailerCouponInCartInGlobals( $retailer_coupon, $retailer_id );
        if( $getRetailerCouponInCartInGlobals !== null ) return $getRetailerCouponInCartInGlobals;
        
        $cart_object     = $coupons = WC()->cart;
        $applied_coupons = $cart_object->get_applied_coupons();
        if( empty( $applied_coupons ) ) return false;
        
        if( !empty( $retailer_id ) ) return MC_Affiliates::checkAffiliateCouponInCart( '', $retailer_id, $applied_coupons );
        
        if( !empty( $retailer_coupon ) ) return in_array( $retailer_coupon, $applied_coupons );
        
        foreach( $applied_coupons as $applied_coupon ) {
            $affiliate_user_id = MC_Affiliates::getAffiliateIdByCouponCode( $applied_coupon );
            if( empty( $affiliate_user_id ) ) continue;
            
            if( MC_User_Functions::isRetailer( $affiliate_user_id ) ) {
                return static::setRetailerCouponInCartInGlobals( $applied_coupon, $affiliate_user_id );
            }
        }
        
        return false;
    }
    
    /**
     * @param string $retailer_coupon
     * @param int    $retailer_id
     *
     * @return bool|mixed|null
     */
    public static function getRetailerCouponInCartInGlobals( $retailer_coupon = '', $retailer_id = 0 ) {
        if( empty( $GLOBALS['mc_retailer_data'] ) ) return null;
        
        if(
            empty( $retailer_coupon ) && empty( $retailer_id ) ||
            !empty( $retailer_id ) && $GLOBALS['mc_retailer_data']['retailer_id'] == $retailer_id ||
            !empty( $retailer_coupon ) && $GLOBALS['mc_retailer_data']['retailer_coupon'] == $retailer_coupon
        ) return $GLOBALS['mc_retailer_data'];
        
        return false;
    }
    
    /**
     * @param string $retailer_coupon
     * @param int    $retailer_id
     *
     * @return array
     */
    public static function setRetailerCouponInCartInGlobals( $retailer_coupon = '', $retailer_id = 0 ) {
        return $GLOBALS['mc_retailer_data'] = [
            'retailer_id'     => $retailer_id,
            'retailer_coupon' => $retailer_coupon
        ];
    }
    
    /**
     * @param $fields
     *
     * @return mixed
     */
    public static function checkFieldsForRetailerCoupon( $fields ) {
        $checkRetailerCouponInCart = static::checkRetailerCouponInCart();
        if( empty( $checkRetailerCouponInCart ) ) return $fields;
        
        $retailer_address = static::getRetailerAddressInGlobal( $checkRetailerCouponInCart['retailer_id'] );
        
        if( empty( $retailer_address ) ) return $fields;
        
        foreach( $retailer_address as $retailer_address_key => $retailer_address_single ) {
            $fields[ $retailer_address_key ]['readonly'] = 1;
            $fields[ $retailer_address_key ]['disabled'] = 1;
            $fields[ $retailer_address_key ]['class'][]  = 'mc-disabled-input';
        }
        
        return $fields;
    }
    
}