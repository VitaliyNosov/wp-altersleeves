<?php

namespace Mythic_Core\Objects;

use MC_Woo_Order_Functions;
use MC_WP;
use Mythic_Core\Abstracts\MC_Post_Type_Object;
use Mythic_Core\Functions\MC_User_Functions;
use Mythic_Core\Users\MC_Affiliates;

/**
 * Class MC_Woo_Order
 *
 * @package Mythic_Core\Objects
 */
class MC_Woo_Order extends MC_Post_Type_Object {
    
    public static $post_type = 'shop_order';
    
    public $order;
    public $invoice_url;
    public $referrers_by_cookie;
    public $referrers_by_coupon = [];
    public $promotion;
    
    public function setAdditional() {
        $order_id = $this->getId();
        $order    = wc_get_order( $order_id );
        $this->setOrder( $order );
        $this->setInvoiceUrl( MC_Woo_Order_Functions::invoiceUrl( $order_id ) );
        $promotion = false;
        $user_id   = $order->get_user_id();
        if( !empty( $user_id ) ) $promotion = MC_User_Functions::isAdmin( $user_id );
        if( empty( $order->get_payment_method() ) ) $promotion = true;
        $this->setPromotion( $promotion );
        
        // Set Referrals by cookie
        $referrers = MC_WP::meta( '_content_creator', $order_id );
        $referrers = !empty( $referrers ) ? json_decode( stripslashes( $referrers ), true ) : [];
        $this->setReferrersByCookie( $referrers );
        
        // Set Referrals by coupon
        $affiliate_coupons = MC_Affiliates::getAllAffiliateCoupons();
        
        $referral_coupons = [];
        $order_coupons    = $order->get_coupon_codes() ?? [];
        if( !empty( $order_coupons ) ) {
            foreach( $order_coupons as $order_coupon ) {
                if( in_array( $order_coupon, $affiliate_coupons ) ) {
                    $referral_coupons[] = MC_Affiliates::userCouponToId( $order_coupon );
                }
            }
        }
        $this->setReferrersByCoupon( $referral_coupons );
    }
    
    /**
     * @return mixed
     */
    public function getOrder() {
        return $this->order;
    }
    
    /**
     * @param mixed $order
     */
    public function setOrder( $order ) {
        $this->order = $order;
    }
    
    /**
     * @return mixed
     */
    public function getInvoiceUrl() {
        return $this->invoice_url;
    }
    
    /**
     * @param mixed $invoice_url
     */
    public function setInvoiceUrl( $invoice_url ) {
        $this->invoice_url = $invoice_url;
    }
    
    /**
     * @return mixed
     */
    public function getReferrersByCookie() {
        return $this->referrers_by_cookie;
    }
    
    /**
     * @param mixed $referrers_by_cookie
     */
    public function setReferrersByCookie( $referrers_by_cookie ) {
        $this->referrers_by_cookie = $referrers_by_cookie;
    }
    
    /**
     * @return mixed
     */
    public function getReferrersByCoupon() {
        return $this->referrers_by_coupon;
    }
    
    /**
     * @param mixed $referrers_by_coupon
     */
    public function setReferrersByCoupon( $referrers_by_coupon ) {
        $this->referrers_by_coupon = $referrers_by_coupon;
    }
    
    /**
     * @return mixed
     */
    public function getPromotion() {
        return $this->promotion;
    }
    
    /**
     * @param mixed $promotion
     */
    public function setPromotion( $promotion ) : void {
        $this->promotion = $promotion;
    }
    
}