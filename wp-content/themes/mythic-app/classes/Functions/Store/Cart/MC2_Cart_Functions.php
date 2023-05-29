<?php

namespace Mythic\Functions\Store\Cart;

use Exception;
use Mythic\Functions\Creator\MC2_Affiliate_Coupon_Functions;

/**
 * Class MC2_Cart_Functions
 *
 * @package Mythic\Functions
 */
class MC2_Cart_Functions {
    
    const DEFAULT_COUNTRY = 'US';
    const DEFAULT_CURRENCY = 'USD';
    
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
    public static function caclulationQuantities( $cart ) {
        if( is_admin() && !defined( 'DOING_AJAX' ) ) return;
        // Loop through cart items, add discount
        
        $cart_items         = $cart->get_cart();
        $coupons            = $cart->get_applied_coupons();
        $promotion_products = MC2_Affiliate_Coupon_Functions::getCurrentFreeAffiliateProducts();
        
        $mics         = [ 179257, 179253, 179250 ];
        $mic_coupons  = MC2_Affiliate_Functions::micCoupons();
        $mic_redeemed = false;
        foreach( $cart_items as $key => $cart_item ) {
            $product_id = $cart_item['product_id'];
            if( in_array( $product_id, [ 158991, 159064, 159065, 159066, 159067, 159068 ] ) ) {
                continue;
            }
            
            if( in_array( $product_id, $promotion_products ) ) {
                $cart_item['data']->set_price( 0 );
                continue;
            }
            
            $product = wc_get_product( $product_id );
            if( empty( $product ) ) continue;
            if( !has_term( 'alter', 'product_cat', $product_id ) && !has_term( 'giftcard', 'product_cat', $product_id ) ) {
                continue;
            }
            
            if( has_term( 'alter', 'product_cat', $product_id ) ) {
                $price = 6;
            } else {
                if( has_term( 'giftcard', 'product_cat', $product_id ) ) {
                    $price   = $product->get_price();
                    $digital = MC2_Gift_Card_Functions::digital( $product_id );
                    if( !$digital ) $price = $price + 1;
                }
            }
            if( is_user_logged_in() ) {
                $idCreator = MC2_WP::authorId( $product_id );
                if( $idCreator == wp_get_current_user()->ID && MC2_Product_Functions::isAlter( $product_id ) ) $price = 2.5;
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
                if( !empty( $promotion_id = MC2_Affiliate_Coupon_Functions::checkIfPromotionExistByWCCode( $coupon ) ) ) {
                    if( empty( $product_ids = MC2_Affiliate_Coupon_Functions::getAffiliatePromotionFreeProductsById( $promotion_id ) ) ) continue;
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
    
    /**
     * Gets the number of items in the cart
     *
     * @return int
     */
    public static function get_cart_item_count() : int {
		global $woocommerce;
        if( !WOO_ACTIVE || empty($woocommerce->cart) ) return 0;


        return $woocommerce->cart->get_cart_contents_count();
    }
    
    /**
     * Gets the total value of the cart
     *
     * @return float
     */
    public static function get_cart_total() : float {
		global $woocommerce;
		if( !WOO_ACTIVE || empty($woocommerce->cart) ) return 0;
        $total = $woocommerce->cart->get_cart_total();
        $total = strip_tags($total);
        return preg_replace('/[^0-9,.]+/', '', $total);
    }
    
    /**
     * Gets the currency of the cart
     *
     * @return string
     */
    public static function get_cart_currency() : string {
        if( !WOO_ACTIVE ) return self::DEFAULT_CURRENCY;
        return get_woocommerce_currency();
    }
    
    /**
     * Gets the country of the cart (and user of)
     *
     * @param string $source
     *
     * @return string
     */
    public static function get_cart_country( string $source = 'billing' ) : string {
        if( !WOO_ACTIVE ) return self::DEFAULT_COUNTRY;
        $method = 'get_'.$source.'_country';
        global $woocommerce;

        if(empty($woocommerce->customer)) return self::DEFAULT_COUNTRY;

        return $woocommerce->customer->$method() ?? self::DEFAULT_COUNTRY;
    }
    
    /**
     * The header data array for the API
     *
     * @return array
     */
    public static function get_header_data() :array  {
        return
            [
                'currency' => self::get_cart_currency(),
                'location' => self::get_cart_country(),
                'item_count' => self::get_cart_item_count(), // @todo changed this to 'item_count' from 'quantity' to make more sense
                'value' => self::get_cart_total() // @todo added this too! Value of the cart
            ];
    }
    
}