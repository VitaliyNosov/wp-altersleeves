<?php

use Mythic_Core\Functions\MC_Creator_Functions;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;
use Mythic_Core\Users\MC_Affiliates;

if( !function_exists( 'mythic_core' ) ) {
    function mythic_core() {
        require_once 'classes/Abstracts/MC_Init.php';
        require_once 'classes/Loader/MC_Global_Loader.php';
        require_once 'classes/Loader/MC_Vendor_Loader.php';
        
        new Mythic_Core\Loader\MC_Vendor_Loader();
        new Mythic_Core\Loader\MC_Global_Loader();
    }
    
    add_action( 'after_setup_theme', 'mythic_core', 2 );
}

add_filter( 'woocommerce_coupon_get_discount_amount', 'filter_wc_coupon_get_discount_amount', 999999, 5 );
function filter_wc_coupon_get_discount_amount( $discount_amount, $discounting_amount, $cart_item, $single, WC_Coupon $coupon ) {
    if( $coupon->get_code() != 'accountcredit' ) return $discount_amount;
    return MC_Creator_Functions::getAffiliateBalance();
}

/**
 * Remove woocommerce hooked action (method woocommerce_template_loop_product_thumbnail on woocommerce_before_shop_loop_item_title
 * hook
 */
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
/**
 * Add our own action to the woocommerce_before_shop_loop_item_title hook with the same priority that woocommerce used
 */
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

/**
 * WooCommerce Loop Product Thumbs
 */
if( !function_exists( 'woocommerce_template_loop_product_thumbnail' ) ) {
    /**
     * echo thumbnail HTML
     */
    function woocommerce_template_loop_product_thumbnail() {
        echo woocommerce_get_product_thumbnail();
    }
}

/**
 * WooCommerce Product Thumbnail
 */
if( !function_exists( 'woocommerce_get_product_thumbnail' ) ) {
    /**
     * @param string $size
     * @param int    $placeholder_width
     * @param int    $placeholder_height
     *
     * @return string
     */
    function woocommerce_get_product_thumbnail( $size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0 ) {
        global $post, $woocommerce;
        
        //NOTE: those are PHP 7 ternary operators. Change to classic if/else if you need PHP 5.x support.
        $placeholder_width = !$placeholder_width ?
            wc_get_image_size( 'shop_catalog_image_width' )['width'] :
            $placeholder_width;
        
        $placeholder_height = !$placeholder_height ?
            wc_get_image_size( 'shop_catalog_image_height' )['height'] :
            $placeholder_height;
        
        /**
         * EDITED HERE: here I added a div around the <img> that will be generated
         */
        $output = '<div class="loop-thumbnail">';
        
        /**
         * This outputs the <img> or placeholder image.
         * it's a lot better to use get_the_post_thumbnail() that hardcoding a text <img> tag
         * as wordpress wil add many classes, srcset and stuff.
         */
        $output .= has_post_thumbnail() ?
            get_the_post_thumbnail( $post->ID, $size ) :
            '<img src="'.wc_placeholder_img_src().'" alt="Placeholder" width="'.$placeholder_width.'" height="'.$placeholder_height.'" />';
        
        /**
         * Close added div .my_new_wrapper
         */
        $output .= '</div>';
        
        return $output;
    }
}

/**
 * @snippet       Get Sales by State @ WooCommerce Admin
 * @how-to        Get CustomizeWoo.com FREE
 * @sourcecode    https://businessbloomer.com/?p=72853
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 3.1.2
 */

// -----------------------
// 1. Create extra tab under Reports / Orders

add_filter( 'woocommerce_admin_reports', 'bbloomer_admin_add_report_orders_tab' );

function bbloomer_admin_add_report_orders_tab( $reports ) {
    $array = [
        'sales_by_state' => [
            'title'       => 'Sales by state',
            'description' => '',
            'hide_title'  => 1,
            'callback'    => [ \Mythic_Core\Functions\MC_Woo_Order_Functions::class, 'yearlySalesByState' ],
        ],
    ];
    
    $reports['orders']['reports'] = array_merge( $reports['orders']['reports'] ?? [], $array );
    
    return $reports;
}

add_action( 'admin_head', function() {
    $currentPostType = get_post_type();
    
    if( $currentPostType != 'shop_order' ) {
        return;
    }
    ?>
    <script>
        ( function( $ ) {
            $(document).ready(function() {
                $('#refund_amount').removeAttr('readonly');
            });
        } )(jQuery);
    </script>
<?php } );

//assign user in guest order
add_action( 'woocommerce_new_order', 'action_woocommerce_new_order', 10, 1 );
function action_woocommerce_new_order( $order_id ) {
    $order = new WC_Order( $order_id );
    $user  = $order->get_user();
    
    if( !$user ) {
        //guest order
        $userdata = get_user_by( 'email', $order->get_billing_email() );
        if( isset( $userdata->ID ) ) {
            //registered
            update_post_meta( $order_id, '_customer_user', $userdata->ID );
        } else {
            //Guest
        }
    }
}

// define the woocommerce_applied_coupon callback
function custom_woocommerce_applied_coupon( $coupon_code ) {
    global $woocommerce;
    
    $promotion = MC_Affiliate_Coupon::getPromotionCodesForPromotionCount( $coupon_code );
    if( !empty( $promotion ) ) return;
    
    $cart    = $woocommerce->cart;
    $coupons = $cart->get_applied_coupons();
    
    if( !empty( MC_Affiliates::getAffiliateIdByCouponCode( $coupon_code ) ) ) {
        foreach( $coupons as $key => $coupon ) {
            if( $coupon_code == $coupon || empty( MC_Affiliates::getAffiliateIdByCouponCode( $coupon ) ) ) {
                unset( $coupons[ $key ] );
                continue;
            }
            $cart->remove_coupon( $coupon );
        }
    }
    $cart->add_discount( $coupon_code );
}

//add the action
add_action( 'woocommerce_applied_coupon', 'custom_woocommerce_applied_coupon', 10, 1 );

add_filter( 'wpseo_opengraph_author_facebook', '__return_false' );

function change_opengraph_title( $title ) {
    return str_replace( ', Author at Alter Sleeves', ' on Alter Sleeves', $title );
}

add_filter( 'wpseo_opengraph_title', 'change_opengraph_title', 10, 1 );

function change_opengraph_image( $image ) {
    if( !is_author() ) return $image;
    $user = get_queried_object();
    if( empty( $user ) ) return $image;
    $user_id = $user->ID;
    return \Mythic_Core\Functions\MC_User_Functions::avatar( $user_id );
}

add_filter( 'wpseo_opengraph_image', 'change_opengraph_image', 10, 1 );
