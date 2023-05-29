<?php

namespace Mythic_Core\Functions;

use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;
use Mythic_Core\Users\MC_Affiliates;
use WC_Cart;

class MC_Creator_Functions {
    
    /**
     * @return int
     */
    public static function get_current_creator_id() : int {
        if( !is_user_logged_in() ) return 0;
        $content_creator_id = wp_get_current_user()->ID;
        if( MC_User_Functions::isAdmin() ) {
            $default_id         = 9;
            $content_creator_id = $_GET['artist_id'] ?? $default_id;
            $content_creator_id = $_GET['affiliate_id'] ?? $content_creator_id;
        }
        return $content_creator_id;
    }
    
    /**
     * @param int $user_id
     *
     * @return float
     */
    public static function getAffiliateBalance( int $user_id = 0 ) : float {
        if( empty( $user_id ) ) $user_id = self:: get_current_creator_id();
        global $wpdb;
        $table_name = MC_Transaction_Functions::$table_name;
        $query      = "SELECT COALESCE(SUM(value), 0)";
        $query      .= " - COALESCE(( SELECT SUM(value) FROM mc_transactions WHERE type = 'withdrawal' AND user_id = $user_id ), 0 )";
        $query      .= " + COALESCE(( SELECT SUM(value) FROM mc_transactions WHERE type = 'royalty' AND user_id = $user_id AND site_id = 2 AND date > '2021-07-31'), 0 )";
        $query      .= " + COALESCE(( SELECT SUM(value) FROM mc_transactions WHERE type = 'topup' AND user_id = $user_id ), 0 )";
        $query      .= " + COALESCE(( SELECT SUM(value) FROM wp_as_royalties WHERE alterist_id = $user_id AND cleared = 1), 0 )";
        $query      .= " FROM $table_name WHERE type IN ('referral_fee', 'contracted_fee' ) AND user_id = $user_id ";
        if( in_array( $user_id, [ 5184, 6447, 3128, 2947 ] ) ) $query .= "and date > '2021-02-28'";
        $query = $wpdb->get_var( $query );
        return !empty( $query ) ? $query : 0;
    }
    
    /**
     * @param int $user_id
     *
     * @return float
     */
    public static function getEarnedByUser( int $user_id = 0 ) : float {
        global $wpdb;
        $table_name = MC_Transaction_Functions::$table_name;
        $query      = "SELECT COALESCE(SUM(value), 0)";
        $query      .= " + COALESCE(( SELECT SUM(value) FROM mc_transactions WHERE type = 'royalty' AND user_id = $user_id  AND site_id = 2 AND date > '2021-07-31'), 0 )";
        $query      .= " + COALESCE(( SELECT SUM(value) FROM wp_as_royalties WHERE alterist_id = $user_id AND cleared = 1), 0 )";
        $query      .= " + COALESCE(( SELECT SUM(value) FROM mc_transactions WHERE type = 'topup' AND user_id = $user_id ), 0 )";
        $query      .= " FROM $table_name WHERE type IN ('referral_fee', 'contracted_fee' ) AND user_id = $user_id ";
        if( in_array( $user_id, [ 5184, 6447, 3128, 2947 ] ) ) $query .= "and date > '2021-02-28'";
        $query = $wpdb->get_var( $query );
        return !empty( $query ) ? $query : 0;
    }
    
    /**
     * @param int $user_id
     *
     * @return bool
     */
    public static function has_sales( int $user_id = 0 ) : bool {
        if( empty( $user_id ) ) $user_id = self:: get_current_creator_id();
        global $wpdb;
        $as_sales = "SELECT
COUNT(wp_as_royalties.id) as count,
wp_as_royalties.product_id,
wp_posts.post_title as title
FROM wp_as_royalties LEFT JOIN wp_posts ON product_id = wp_posts.ID WHERE alterist_id = $user_id and value > 0 and cleared = 1 GROUP BY product_id ORDER BY COUNT(wp_as_royalties.id) DESC";
        
        $as_sales = $wpdb->get_results( $as_sales );
        
        $mg_sales = "SELECT
COUNT(mc_transactions.id) as count,
mc_transactions.product_id,
wp_2_posts.post_title as title
FROM mc_transactions LEFT JOIN wp_2_posts ON product_id = wp_2_posts.ID WHERE user_id = $user_id and type = 'royalty' and site_id = 2 GROUP BY product_id ORDER BY COUNT(mc_transactions.id) DESC";
        $mg_sales = $wpdb->get_results( $mg_sales );
        
        return !empty( $as_sales ) || !empty( $mg_sales );
    }
    
    /**
     * @param int $user_id
     *
     * @return array
     */
    public static function get_sales( int $user_id = 0 ) : array {
        if( empty( $user_id ) ) $user_id = self:: get_current_creator_id();
        global $wpdb;
        
        $sales_counts = [];
        
        // Get Alter Sleeves Sales
        $as_sales = "SELECT
        COUNT(wp_as_royalties.id) as count,
        wp_as_royalties.product_id,
        wp_posts.post_title as title
        FROM wp_as_royalties LEFT JOIN wp_posts ON product_id = wp_posts.ID WHERE alterist_id = $user_id and value > 0 and cleared = 1 GROUP BY product_id ORDER BY COUNT(wp_as_royalties.id) DESC";
        
        $as_sales = $wpdb->get_results( $as_sales );
        if( !empty( $as_sales ) && is_iterable( $as_sales ) ) {
            foreach( $as_sales as $as_sale ) {
                $as_sale                             = (array) $as_sale;
                $as_sale['url']                      = get_blog_permalink( 1, $as_sale['product_id'] );
                $as_sale['site']                     = 'as';
                $sales_counts[ $as_sale['count'] ][] = $as_sale;
            }
        }
        
        // Mythic Gaming Sales
        $mg_sales = "SELECT
        COUNT(mc_transactions.id) as count,
        mc_transactions.product_id,
        wp_2_posts.post_excerpt as title
        FROM mc_transactions LEFT JOIN wp_2_posts ON product_id = wp_2_posts.ID WHERE user_id = $user_id and type = 'royalty' and site_id = 2 GROUP BY product_id ORDER BY COUNT(mc_transactions.id) DESC";
        $mg_sales = $wpdb->get_results( $mg_sales );
        if( !empty( $mg_sales ) && is_iterable( $mg_sales ) ) {
            foreach( $mg_sales as $mg_sale ) {
                $mg_sale                             = (array) $mg_sale;
                $mg_sale['url']                      = get_blog_permalink( 2, $mg_sale['product_id'] );
                $mg_sale['site']                     = 'mg';
                $sales_counts[ $mg_sale['count'] ][] = $mg_sale;
            }
        }
        krsort( $sales_counts );
        $sales = [];
        foreach( $sales_counts as $sales_count ) {
            foreach( $sales_count as $sale ) {
                if( $sale['product_id'] == 0 ) continue;
                $sales[] = $sale;
            }
        }
        return $sales;
    }
    
    /**
     * @return array
     */
    public static function get_all_sales( $limit = 200 ) : array {
        global $wpdb;
        
        $sales_counts = [];
        
        // Get Alter Sleeves Sales
        $as_sales = "SELECT
        COUNT(wp_as_royalties.id) as count,
        wp_as_royalties.product_id,
        wp_posts.post_title as title
        FROM wp_as_royalties LEFT JOIN wp_posts ON product_id = wp_posts.ID WHERE value > 0 and cleared = 1 GROUP BY product_id ORDER BY COUNT(wp_as_royalties.id) DESC";
        
        $as_sales = $wpdb->get_results( $as_sales );
        if( !empty( $as_sales ) && is_iterable( $as_sales ) ) {
            foreach( $as_sales as $as_sale ) {
                $as_sale                             = (array) $as_sale;
                $as_sale['url']                      = get_blog_permalink( 1, $as_sale['product_id'] );
                $as_sale['site']                     = 'as';
                $sales_counts[ $as_sale['count'] ][] = $as_sale;
            }
        }
        
        // Mythic Gaming Sales
        $mg_sales = "SELECT
        COUNT(mc_transactions.id) as count,
        mc_transactions.product_id,
        wp_2_posts.post_excerpt as title
        FROM mc_transactions LEFT JOIN wp_2_posts ON product_id = wp_2_posts.ID WHERE type = 'royalty' and site_id = 2 GROUP BY product_id ORDER BY COUNT(mc_transactions.id) DESC";
        $mg_sales = $wpdb->get_results( $mg_sales );
        if( !empty( $mg_sales ) && is_iterable( $mg_sales ) ) {
            foreach( $mg_sales as $mg_sale ) {
                $mg_sale                             = (array) $mg_sale;
                $mg_sale['url']                      = get_blog_permalink( 2, $mg_sale['product_id'] );
                $mg_sale['site']                     = 'mg';
                $sales_counts[ $mg_sale['count'] ][] = $mg_sale;
            }
        }
        krsort( $sales_counts );
        $sales = [];
        $count = 0;
        foreach( $sales_counts as $sales_count ) {
            foreach( $sales_count as $sale ) {
                if( $sale['product_id'] == 0 ) continue;
                $sales[] = $sale;
                $count++;
                if( $count > $limit ) break 2;
            }
        }
        return $sales;
    }
    
    /**
     * @return array
     */
    public static function get_args_for_promotion_panels() {
        $is_admin  = MC_User_Functions::isAdmin();
        $tabs_info = [
            'mc_affiliates' => [
                'title'   => 'Affiliates',
                'content' => [ 'slug' => 'affiliates/panes/pane', 'name' => 'mc_affiliates' ],
                'active'  => 1,
            ],
            'mc_promotions' => [
                'title'   => 'Promotions',
                'content' => [ 'slug' => 'affiliates/panes/pane', 'name' => 'mc_promotions' ],
                'active'  => 0,
            ],
        ];
        $args      = [
            'existing_user_id'   => 0,
            'existing_user_data' => [],
            'existing_promotion' => [],
        ];
        if( !empty( $_GET['coupon_id'] ) ) {
            // coupon_id exists if current screen for coupon editing
            $tabs_info['mc_affiliates']['active'] = 0;
            $tabs_info['mc_promotions']['active'] = 1;
            $args['existing_promotion']           = MC_Affiliate_Coupon::getAffiliatePromotionDataById( $_GET['coupon_id'] );
            $args['existing_user_id']             = !empty( $args['existing_promotion']['userId'] ) ? $args['existing_promotion']['userId'] : 0;
        } else if( !empty( $_GET['coupons_affiliate_id'] ) ) {
            // coupons_affiliate_id exists if current screen for all affiliate coupons
            $tabs_info['mc_affiliates']['active'] = 0;
            $tabs_info['mc_promotions']['active'] = 1;
            $args['existing_user_id']             = $_GET['coupons_affiliate_id'];
            $affiliate_coupons                    = MC_Affiliate_Coupon::getAffiliatePromotions( $args['existing_user_id'] );
            if( !empty( $affiliate_coupons[0]['wc_coupon_id'] ) ) {
                $args['existing_promotion'] = MC_Affiliate_Coupon::getAffiliatePromotionDataById( $affiliate_coupons[0]['wc_coupon_id'] );
            } else {
                $args['existing_promotion']['promotionId'] = 0;
            }
        } else if( !empty( $_GET['affiliate_id'] ) ) {
            // affiliate_id exists if current screen for affiliate editing
            $args['existing_user_id'] = $_GET['affiliate_id'];
        }
        
        if( !$is_admin ) {
            $current_user_id = get_current_user_id();
            if( $args['existing_user_id'] != $current_user_id ) {
                $args['existing_user_id'] = $current_user_id;
                $affiliate_coupons        = MC_Affiliate_Coupon::getAffiliatePromotions( $args['existing_user_id'] );
                if( !empty( $affiliate_coupons[0]['wc_coupon_id'] ) ) {
                    $args['existing_promotion'] = MC_Affiliate_Coupon::getAffiliatePromotionDataById( $affiliate_coupons[0]['wc_coupon_id'] );
                } else {
                    $args['existing_promotion']['promotionId'] = 0;
                }
            }
        }
        
        if( !empty( $args['existing_user_id'] ) ) {
            $affiliate_data = MC_Affiliates::getAffiliateData( $args['existing_user_id'] );
            if( !empty( $affiliate_data['userData'] ) ) {
                $args['existing_user_data'] = $affiliate_data['userData'];
            }
        }
        return $args;
    }
    
    /**
     * @return mixed
     */
    public static function get_args_for_retailer_order_panels() {
        $args['user_id'] = get_current_user_id();
        
        return $args;
    }
    
    /**
     * @param $extra_amount
     */
    public static function add_extra_charge_on_coupons( $extra_amount ) {
        $coupons = WC()->cart->get_coupons();
        foreach( $coupons as $coupon ) {
            if( $coupon->get_code() == 'accountcredit' ) $coupon->set_amount( MC_Creator_Functions::getAffiliateBalance() );
            $cart_discount_added_by_this_coupon = isset( WC()->cart->coupon_discount_amounts[ $coupon->get_code() ] ) ? round( WC()->cart->coupon_discount_amounts[ $coupon->get_code() ] ) : 0;
            if( $cart_discount_added_by_this_coupon < $coupon->get_amount() ) {
                $unused_coupon_amount = $coupon->get_amount() - $cart_discount_added_by_this_coupon;
                if( $extra_amount <= $unused_coupon_amount ) {
                    WC()->cart->coupon_discount_amounts[ $coupon->get_code() ] = ( isset( WC()->cart->coupon_discount_amounts[ $coupon->get_code() ] ) ? WC()->cart->coupon_discount_amounts[ $coupon->get_code() ] : 0 ) + $extra_amount;
                    break;
                } else {
                    $extra_amount = $extra_amount - $unused_coupon_amount;
                    
                    WC()->cart->coupon_discount_amounts[ $coupon->get_code() ] += $unused_coupon_amount;
                }
            }
            if( empty( $extra_amount ) ) break;
        }
    }
    
    /**
     * @param WC_Cart $cart
     */
    public static function mythic_frames_creator_discount( WC_Cart $cart ) {
        $cart_items = $cart->get_cart();
        $team_id    = \MC_Mythic_Frames_Functions::get_team_from_user_id();
        if( empty( $team_id ) ) return;
        foreach( $cart_items as $cart_item ) {
            $product_id   = $cart_item['product_id'];
            $product_team = \MC_Mythic_Frames_Functions::get_team_id_from_product_id( $product_id );
            if( $product_team != $team_id ) continue;
            $price = $cart_item['data']->get_price();
            $name  = get_the_title( $product_id );
            if( strpos( strtolower( $name ), 'playmat' ) !== false ) {
                $cart_item['data']->set_price( 17 );
            } else {
                $cart_item['data']->set_price( $price / 2 );
            }
        }
    }
    
    /**
     *
     * Main function which will allow coupons to pay for shipping
     *
     * @param $cart
     */
    public static function woocommerce_coupons_pay_for_shipping( $cart ) {
        $coupons  = $cart->get_coupons();
        $progress = false;
        foreach( $coupons as $coupon ) {
            if( strtolower( $coupon->get_code() ) != 'accountcredit' ) continue;
            $progress = true;
            break;
        }
        if( !$progress ) return;
        $discount_cart          = $cart->discount_cart;
        $total_coupons_amount   = self::get_total_coupons_amount( $coupons );
        $copouns_credit_remain  = $total_coupons_amount - $discount_cart;
        $shipping_including_tax = $cart->shipping_total + $cart->shipping_tax_total;
        
        if( $copouns_credit_remain > 0 && !empty( $cart->shipping_total ) ) {
            if( $copouns_credit_remain >= $shipping_including_tax ) {
                //$cart->discount_cart += $shipping_including_tax;
                self::add_extra_charge_on_coupons( $shipping_including_tax );
                //$cart->total -= $shipping_including_tax;
            } else if( $copouns_credit_remain < $shipping_including_tax ) {
                $cart->discount_cart += $copouns_credit_remain;
                self::add_extra_charge_on_coupons( $copouns_credit_remain );
                /** reset cart total **/
                $cart->total -= $copouns_credit_remain;
            }
        }
    }
    
    /**
     *
     * Return summation of all coupons applied in a cart
     *
     * @param $coupons
     *
     * @return int
     */
    public static function get_total_coupons_amount( $coupons ) {
        $total_coupons_amount = 0;
        foreach( $coupons as $coupon ) {
            $total_coupons_amount += $coupon->get_code() == 'accountcredit' ? MC_Creator_Functions::getAffiliateBalance() : $coupon->get_amount();
        }
        return $total_coupons_amount;
    }
    
}