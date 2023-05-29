<?php

namespace Mythic_Core\System;

use MC_Artist_Functions;
use MC_Scryfall;
use MC_Vars;
use Mythic_Core\Display\MC_Render;
use Mythic_Core\Display\MC_Template_Parts;
use Mythic_Core\Functions\MC_Creator_Functions;
use Mythic_Core\Functions\MC_Mask_Cutter_Functions;
use Mythic_Core\Functions\MC_Mythic_Frames_Functions;
use Mythic_Core\Functions\MC_Transaction_Functions;
use Mythic_Core\Functions\MC_User_Functions;
use Mythic_Core\Functions\MC_Withdrawal_Functions;
use Mythic_Core\Functions\MC_Woo_Cart_Functions;
use Mythic_Core\Functions\MC_Woo_Order_Functions;
use Mythic_Core\Functions\MC_Woo_Order_Item_Functions;
use Mythic_Core\Functions\MC_Royalty_Functions;
use Mythic_Core\Objects\MC_User;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;
use Mythic_Core\Settings\MC_Data_Settings;
use Mythic_Core\Settings\MC_Site_Settings;
use Mythic_Core\Users\MC_Affiliates;
use Mythic_Core\Utils\MC_Facebook;
use Mythic_Core\Utils\MC_File_Locations;
use Mythic_Core\Utils\MC_Sendinblue;
use Mythic_Core\Utils\MC_Server;
use Mythic_Core\Utils\MC_Url;
use WC_Order;

/**
 * Class MC_Actions
 *
 * @package Mythic_Core\System
 */
class MC_Actions {
    
    /**
     * MC_Actions constructor.
     */
    public function __construct() {
        // Init Functions Classes for their actions
        new MC_User_Functions();
        
        // Misc Actions
        add_action( 'wp_before_admin_bar_render', [ MC_Admin_Bar::class, 'init' ] );
        add_action( 'wp_loaded', [ MC_Woo_Cart_Functions::class, 'addProductsToCartFromUrl' ], 15 );
        remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
        add_action( 'mc_head_open', [ self::class, 'googleEcommerceTracking' ] );
        
        /** Wordpress Hooks */
        add_action( 'restrict_manage_posts', [ self::class, 'filterByAuthor' ] );
        add_action( 'admin_action_mark_promotional_completed', [
            MC_Woo_Order_Functions::class,
            'markPromotionalCompleted',
        ] );
        add_action( 'admin_notices', [ MC_Woo_Order_Functions::class, 'noticePromotionalCompleted' ] );
        add_action( 'wp_head', [ MC_Facebook::class, 'pixel' ] );
        add_action( 'wp_before_admin_bar_render', [ MC_Admin_Bar::class, 'clean' ], 99999 );
        add_action( 'wp_before_admin_bar_render', [ MC_Admin_Bar::class, 'addElements' ] );
        
        /** Vendor Hooks */
        add_action( 'woocommerce_new_order_item', [
            MC_Woo_Order_Item_Functions::class,
            'transferMetaFromCartItemToOrderItem',
        ],          10, 3 );
        add_action( 'woocommerce_checkout_order_created', [
            MC_Affiliate_Coupon::class,
            'checkAffiliateFunctionality',
        ],          99999 );
        add_action( 'woocommerce_checkout_order_created', [
            self::class, 'removeFromBalance'
        ],          99999 );
        add_action( 'woocommerce_login_form_start', [ MC_User_Functions::class, 'renderLoginStart' ] );
        
        add_action( 'gform_after_submission_35', [ MC_User::class, 'discordUsername' ], 10, 2 );
        add_action( 'gform_after_submission_36', [ MC_User_Functions::class, 'discordApproval' ], 10, 2 );
        remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );
        //add_action( 'mc_sync_users', [ MC_User_Functions::class, 'syncSites' ] );
        
        /** Custom Hooks */
        add_action( 'init', [ self::class, 'api' ] );
        add_action( 'mc_favicon', [ self::class, 'favicon' ] );
        add_action( 'mc_open_graphs', [ self::class, 'openGraphs' ] );
        add_action( 'mc_body_open', [ self::class, 'bodyGtm' ] );
        add_action( 'mc_cart_notice', [ self::class, 'cartNotice' ] );
        add_action( 'mc_class_cart_has_items', [ MC_Woo_Cart_Functions::class, 'classHasItems' ] );
        add_action( 'mc_copyright', [ self::class, 'copyright' ] );
        add_action( 'mc_content', [ self::class, 'content' ] );
        add_action( 'mc_disclaimer', [ self::class, 'disclaimer' ] );
        add_action( 'mc_facebook_trigger_conversion_tracking', [
            MC_Facebook::class,
            'triggerConversionTracking',
        ],          10, 1 );
        add_action( 'mc_facebook_conversion_tracking', [ MC_Facebook::class, 'conversionTracking' ], 10, 1 );
        add_action( 'mc_footer', [ self::class, 'footer' ] );
        add_action( 'mc_retailer_activation', 'activate_retailer_account_from_order', 10, 1 );
        
        add_action( 'mc_head_open', [ self::class, 'headGtm' ], 1 );
        add_action( 'mc_head_open', [ self::class, 'headGtag' ], 2 );
        add_action( 'mc_head_preconnects', [ self::class, 'headPreconnects' ] );
        add_action( 'mc_head_preloads', [ self::class, 'headPreloads' ] );
        add_action( 'mc_head_title', [ self::class, 'headTitle' ] );
        add_action( 'mc_header', [ self::class, 'header' ] );
        add_action( 'mc_header_element', [ self::class, 'headerElement' ], 10, 1 );
        add_filter( 'mc_header_visible', [ self::class, 'headerVisible' ] );
        add_action( 'mc_layout', [ self::class, 'layout' ] );
        add_action( 'mc_modals', [ self::class, 'promotionProductModal' ] );
        add_action( 'mc_modals', [ self::class, 'genConModal' ] );
        add_action( 'get_header', [ MC_Affiliate_Coupon::class, 'checkAndApplyAffiliatesCoupons' ] );
        add_action( 'after_setup_theme', [ MC_Admin_Bar::class, 'hide' ] );
        add_action( 'mc_social_icons', [ MC_Social::class, 'render' ], 10, 1 );
        add_action( 'mc_sib_newsletter_signup', [ MC_Sendinblue::class, 'createContactFromData' ], 10, 1 );
        add_action( 'mc_tools', [ self::class, 'tools' ] );
        add_action( 'wp_ajax_cutter_variation_file', [ MC_Mask_Cutter_Functions::class, 'upload' ] );
        add_action( 'wp_ajax_nopriv_cutter_variation_file', [ MC_Mask_Cutter_Functions::class, 'upload' ] );
        add_action( 'mc_transactions_import_remaining_orders', [ MC_Transaction_Functions::class, 'updateOrders' ] );
        add_action( 'mc_send_withdrawals', [ MC_Withdrawal_Functions::class, 'compileWithdrawals' ] );
        add_action( 'mc_orders_by_state', [ MC_Woo_Order_Functions::class, 'saveOrdersByStates' ] );
        add_action( 'woocommerce_after_calculate_totals', [ MC_Creator_Functions::class, 'woocommerce_coupons_pay_for_shipping' ] );
        add_action( 'woocommerce_after_calculate_totals', [ MC_Creator_Functions::class, 'mythic_frames_creator_discount' ] );
        add_action( 'mc_scryfall_card_import', [ $this, 'scryfall_card_import' ], 10, 3 );
        add_action( 'mc_alterist_index', [ MC_Artist_Functions::class, 'indexAlterists' ] );
        add_action( 'mc_mf_invoices', [ MC_Mythic_Frames_Functions::class, 'generate_packing_slips_and_csv' ] );
        //add_action( 'woocommerce_before_cart', [ $this, 'add_charity_to_cart' ] );
    }

    public function add_charity_to_cart() {
        
        global $woocommerce;
        
        $charity_creator_id = $_GET['charity_creator_id'] ?? $_COOKIE['charity_user_id'] ?? '';
        
        if( !empty($charity_creator_id) ) {

            $coupon_code = MC_Affiliates::userIdToCoupon($charity_creator_id);
            
             if (!$woocommerce->cart->add_discount( sanitize_text_field( $coupon_code )))
            //     $woocommerce->show_messages();

            $charity['name']   = get_user_meta( $charity_creator_id, 'mc_charity_name', true );
            $charity['url']    = get_user_meta( $charity_creator_id, 'mc_charity_url', true );
            $charity['image']  = get_user_meta( $charity_creator_id, 'mc_charity_image', true );
            $charity['reason'] = get_user_meta( $charity_creator_id, 'mc_charity_reason', true );

            $product = get_page_by_path( 'donation', OBJECT, 'product' );

            WC()->cart->add_to_cart( $product->ID, 1, null, null, [ 'charity'=>$charity ] );
        }
    }
    
    public function scryfall_card_import() {
        new MC_Scryfall();
    }
    
    public static function api() {
        if( MC_Server::primaryPath() != 'api' ) return;
        header( "Content-type: application/json; charset=utf-8" );
        $results = apply_filters( 'mc_api_filter', [] );
        if( empty( $results ) ) $results = [ 'error' => 'No parameters provided' ];
        echo json_encode( $results );
        die();
    }
    
    public static function bodyGtm() {
        $script = MC_Data_Settings::value( 'gtm_body' );
        if( empty( $script ) || !is_string( $script ) ) return;
        if( !MC_Vars::stringContains( $script, 'script>' ) ) return;
        echo $script;
    }
    
    public static function cartNotice() {
        ?>
        <div style="display: none;" class="notice notice-cart">
            <div class="container py-2">
                You have added a new item to your cart! <a href="/cart" title="Click here to go to the cart page">Click here to go the cart page</a>
            </div>
        </div>
        <?php
    }
    
    public static function content() {
        MC_Render::content();
    }
    
    public static function copyright() {
        $copyright = MC_Site_Settings::value( 'copyright', '' );
        $args      = [ 'copyright' => $copyright ];
        $output    = MC_Template_Parts::legal( 'copyright', '', $args );
        echo $output;
    }
    
    public static function disclaimer() {
        $disclaimer = MC_Site_Settings::value( 'disclaimer', '' );
        $args       = [ 'disclaimer' => $disclaimer ];
        $disclaimer = MC_Template_Parts::legal( 'disclaimer', '', $args );
        echo do_shortcode( $disclaimer );
    }
    
    public static function favicon() {
        MC_Render::templatePart( 'head/favicon' );
    }
    
    public static function footer() {
        MC_Render::templatePart( 'footer' );
    }
    
    public static function header() {
        MC_Render::templatePart( 'header' );
    }
    
    /**
     * @param string $element
     */
    public static function headerElement( $element = '' ) {
        MC_Render::templatePart( 'header', $element );
    }
    
    public static function headGtm() {
        $script = MC_Data_Settings::value( 'gtm_head' );
        if( empty( $script ) || !is_string( $script ) ) return;
        if( !MC_Vars::stringContains( $script, 'script>' ) ) return;
        echo $script;
    }
    
    public static function headGtag() {
        $script = MC_Data_Settings::value( 'gtag_head' );
        if( empty( $script ) || !is_string( $script ) ) return;
        if( !MC_Vars::stringContains( $script, 'script>' ) ) return;
        echo $script;
    }
    
    public static function headPreconnects() {
        $default_preconnections = [
            'ajax.googleapis.com',
            'www.gstatic.com',
            'www.google.com',
            'www.google-analytics.com',
            'www.googleadservices.com',
            'www.googletagmanager.com',
            //'www.cloudflare.com',
            'www.facebook.com',
            'fonts.google.com',
            // 'www.youtube.com'
        ];
        
        $preconnections = apply_filters( 'mc_head_preconnects_filter', $default_preconnections );
        if( !is_array( $preconnections ) ) return;
        foreach( $preconnections as $preconnection ) {
            echo '<link rel="preconnect" href="//'.$preconnection.'">';
        }
    }
    
    public static function headPreloads() {
        $default_preloads = [
            'font-awesome' => [
                'href' => MC_File_Locations::fontAwesomeUrl(),
                'as'   => 'style',
            ],
        ];
        
        $preloads = apply_filters( 'mc_head_preloads_filter', $default_preloads );
        if( !is_array( $preloads ) ) return;
        foreach( $preloads as $preload ) {
            $href = $preload['href'] ?? '';
            if( empty( $href ) ) continue;
            $as = $preload['style'] ?? 'style';
            echo '<link rel="preload" as="'.$as.'" href="'.$href.'" onload="this.rel=\'stylesheet\'" />';
        }
    }
    
    public static function headTitle() {
        $title = get_bloginfo( 'name' ) ?? '';
        echo apply_filters( 'mc_head_title_filter', $title );
    }
    
    /**
     * @return bool
     */
    public static function headerVisible() : bool {
        if( MC_Url::isLoginPage() ) return false;
        return true;
    }
    
    public static function layout() {
        MC_Render::layout( apply_filters( 'mc_layout_filter', 'default' ) );
    }
    
    public static function tools() {
        MC_Render::templatePart( 'tools' );
    }
    
    public static function filterByAuthor() {
        $post_type = $_GET['post_type'] ?? '';
        if( empty( $post_type ) || $post_type != 'product' ) return;
        $params = [
            'show_option_none'  => 'Select User',
            'option_none_value' => '',
            'name'              => 'author',
            'role__in'          => [ 'alterist' ],
            ''
        ];
        if( isset( $_GET['user'] ) ) {
            $params['selected'] = $_GET['user'];
        }
        wp_dropdown_users( $params );
    }
    
    public static function openGraphs() {
        MC_Render::templatePart( 'head/open-graphs' );
    }
    
    public static function promotionProductModal() {
        MC_Render::templatePart( 'partners', 'promotion-modal' );
    }
    
    public static function genConModal() {
        MC_Render::templatePart( 'partners', 'gen-con' );
    }
    
    public static function googleEcommerceTracking() {
        MC_Render::templatePart( 'scripts/google', 'impression' );
        MC_Render::templatePart( 'scripts/google', 'purchase' );
    }
    
    /**
     * @param WC_Order $order
     */
    public static function removeFromBalance( WC_Order $order ) {
        $coupons = $order->get_coupon_codes();
        if( !in_array( 'accountcredit', $coupons ) ) return;
        $user_id  = $order->get_user_id();
        $discount = $order->get_total_discount();
        $name     = $order->get_billing_first_name().' '.$order->get_billing_last_name();
        MC_Withdrawal_Functions::request( $user_id, $discount, $order->get_currency(), $name, $order->get_billing_email(), 'order' );
    }
    
}