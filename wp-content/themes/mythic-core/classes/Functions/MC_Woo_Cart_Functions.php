<?php

namespace Mythic_Core\Functions;

use Exception;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;
use Mythic_Core\System\MC_WP;
use Mythic_Core\Users\MC_Affiliates;
use WC_Cart;

/**
 * Class MC_Woo_Cart_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Woo_Cart_Functions {
    
    public static function classHasItems() {
        echo !self::empty() ? 'has-items' : '';
    }
    
    /**
     * @return bool
     */
    public static function empty() : bool {
        return !function_exists( 'WC' ) || empty( WC()->cart->get_cart_contents_count() );
    }
    
    /**
     * @param int $product_id
     *
     * @return bool
     */
    public static function productInCart( $product_id = 0 ) : bool {
        if( empty( $product_id ) ) return false;
        foreach( WC()->cart->get_cart() as $val ) {
            $_product = $val['data'];
            if( $product_id == $_product->get_id() ) return true;
        }
        return false;
    }
    
    /**
     * @return array
     */
    public static function returnForFrontEnd() : array {
        $cart = WC()->cart;
        $cart->calculate_totals();
        return [
            'total' => $cart->get_cart_contents_total(),
            'count' => $cart->get_cart_contents_count(),
        ];
    }
    
    /**
     * Allows adding of products from the url like: ?add-to-cart=123,456,678
     *
     * @throws Exception
     */
    public static function addProductsToCartFromUrl() {
        if(
            !class_exists( 'WC_Form_Handler' ) ||
            empty( $_REQUEST['add-to-cart'] ) ||
            false === strpos( $_REQUEST['add-to-cart'], ',' )
        ) return;
        
        remove_action( 'wp_loaded', [ 'WC_Form_Handler', 'add_to_cart_action' ], 20 );
        $product_ids = explode( ',', $_REQUEST['add-to-cart'] );
        foreach( $product_ids as $product_id ) {
            $product_id     = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );
            $adding_to_cart = wc_get_product( $product_id );
            if( !$adding_to_cart ) continue;
            $add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $adding_to_cart->get_type(), $adding_to_cart );
            
            /*
             * Sorry.. if you want non-simple products, you're on your own.
             *
             * Related: WooCommerce has set the following methods as private:
             * WC_Form_Handler::add_to_cart_handler_variable(),
             * WC_Form_Handler::add_to_cart_handler_grouped(),
             * WC_Form_Handler::add_to_cart_handler_simple()
             *
             * Why you gotta be like that WooCommerce?
             */
            if( 'simple' !== $add_to_cart_handler ) continue;
            $quantity = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
            WC()->cart->add_to_cart( $product_id, $quantity );
        }
    }
    
    /**
     * @param $cart
     */
    public static function caclulationQuantities( WC_Cart $cart ) {
        if( is_admin() && !defined( 'DOING_AJAX' ) ) return;
        // Loop through cart items, add discount
        
        $cart_items = $cart->get_cart();
        $coupons    = $cart->get_applied_coupons();
        
        /*
        foreach( $coupons as $coupon ) {
            $promotion = MC_Affiliate_Coupon::getPromotionCodesForPromotionCount($coupon);
            if( empty($promotion) || MC_User_Functions::isAdmin() ) continue;
            //$remove = true;
            $cookies = [ 'mc_af_code', 'coupon' ];
            foreach( $cookies as $cookie ) {
                if( empty($_COOKIE[$cookie]) ) continue;
                $promotion_code = $_COOKIE[$cookie];
                $coupon_id = MC_Affiliate_Coupon::getPromotionIdByCode($promotion_code);
                if( empty($coupon_id) ) continue;
                $coupon_name = get_the_title($coupon_id);
                if( empty($coupon_name) ) continue;
                $coupon_name = strtolower($coupon_name);
                if( $coupon_name == strtolower($coupon) ) {
                    $remove = false;
                    break;
                }
            }
            /*
            if( $remove ) {
                $cart->remove_coupon($coupon);
            }
        }
        */
        
        $promotion_products = MC_Affiliate_Coupon::getCurrentFreeAffiliateProducts();
        
        $mics         = [ 179257, 179253, 179250 ];
        $mic_coupons  = MC_Affiliates::micCoupons();
        $mic_redeemed = false;
        $mythicsleeve = false;
        $promo_used   = false;
        
        $total_cart_quantity = 0;
        foreach( $cart_items as $key => $cart_item ) {
            $total_cart_quantity += $cart_item['quantity'];
        }
        
        foreach( $cart_items as $key => $cart_item ) {
            $quantity = $cart_item['quantity'];
            
            $product_id = $cart_item['product_id'];
            if( in_array( $product_id, [ 158991, 159064, 159065, 159066, 159067, 159068 ] ) ) {
                continue;
            }
            
            if( !$mythicsleeve && in_array( 'mythicsleeve', $coupons ) ) {
                $price        = $cart_item['quantity'] > 1 ? 6 * ( $cart_item['quantity'] - 1 ) : 0;
                $mythicsleeve = true;
                $cart_item['data']->set_price( $price );
                continue;
            }
            
            if( in_array( $product_id, $promotion_products ) ) {
                $price = ( 6 * ( $quantity - 1 ) ) / $quantity;
                $cart_item['data']->set_price( $price );
                if( $promo_used ) $cart_item['data']->set_price( 6 );
                $promo_used = true;
                continue;
            }
            
            $product = wc_get_product( $product_id );
            if( empty( $product ) ) continue;
            if( !has_term( 'alter', 'product_group', $product_id ) && !has_term( 'giftcard', 'product_group', $product_id ) ) {
                continue;
            }
            
            if( has_term( 'alter', 'product_group', $product_id ) ) {
                $price = 6;
            } else {
                if( has_term( 'giftcard', 'product_group', $product_id ) ) {
                    $price   = $product->get_price();
                    $digital = MC_Giftcard_Functions::digital( $product_id );
                    if( !$digital ) $price = $price + 1;
                }
            }
            if( is_user_logged_in() ) {
                $idCreator = MC_WP::authorId( $product_id );
                if( $idCreator == wp_get_current_user()->ID && MC_Product_Functions::isAlter( $product_id ) ) $price = 2.5;
            }
            if( $product_id == 188830 || $product_id == 184268 || $product_id == 187732 ) {
                $price = 0;
            }
            
            // @Todo remove
            if( in_array( $product_id, $mics ) && !$mic_redeemed ) {
                foreach( $mic_coupons as $mic_coupon ) {
                    $mic_coupon = strtolower( $mic_coupon );
                    if( !in_array( $mic_coupon, $coupons ) ) continue;
                    $price        = $cart_item['quantity'] > 1 ? 6 / $cart_item['quantity'] : 0;
                    $mic_redeemed = true;
                    break;
                }
            }
            
            // @Todo remove
            $redeemed_products = [];
            foreach( $coupons as $coupon ) {
                if( !empty( $promotion_id = MC_Affiliate_Coupon::checkIfPromotionExistByWCCode( $coupon ) ) ) {
                    if( empty( $product_ids = MC_Affiliate_Coupon::getAffiliatePromotionFreeProductsById( $promotion_id ) ) ) continue;
                    if( in_array( $product_id, $product_ids ) ) {
                        $price = $cart_item['quantity'] > 1 ? 6 / $cart_item['quantity'] : 0;
                    }
                    continue;
                }
                $coupon = get_page_by_title( $coupon, OBJECT, 'shop_coupon' );
                if( empty( $coupon ) ) continue;
                $coupon_id         = $coupon->ID;
                $coupon_product_id = get_post_meta( $coupon_id, 'affiliate_product', true );
                if( empty( $coupon_product_id ) || in_array( $coupon_product_id, $redeemed_products ) ) continue;
                if( $coupon_product_id != $product_id ) continue;
                $price               = $cart_item['quantity'] > 1 ? 6 / $cart_item['quantity'] : 0;
                $redeemed_products[] = $coupon_product_id;
            }
            
            if( isset( $price ) ) $cart_item['data']->set_price( $price );
        }
    }
    
    public static function giving_discount() {
        if( is_admin() && !defined( 'DOING_AJAX' ) ) return;
        if( is_cart() ) {
            $cart = WC()->cart;
            if( $cart->has_discount( 'giveagift' ) ) $cart->remove_coupon( 'giveagift' );
        }
        if( !is_checkout() ) return;
        global $woocommerce;
        $billing  = $woocommerce->customer->get_billing_postcode();
        $shipping = $woocommerce->customer->get_shipping_postcode();
        $billing  = is_string( $billing ) ? trim( strtolower( $billing ) ) : '';
        $shipping = is_string( $shipping ) ? trim( strtolower( $shipping ) ) : '';
        
        $cart = WC()->cart;
        if( !empty( $shipping ) && $billing != $shipping ) {
            if( !$cart->has_discount( 'giveagift' ) ) $cart->add_discount( 'giveagift' );
        } else {
            $cart->remove_coupon( 'giveagift' );
        }
    }
    
}