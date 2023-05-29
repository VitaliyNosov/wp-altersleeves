<?php

namespace Alter_Sleeves\System;

use MC_Giftcard_Functions;
use MC_Mythic_Frames_Functions;
use MC_Production_Functions;
use MC_Render;
use MC_Woo_Order_Functions;
use MC_WP;
use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Royalty_Functions;
use Mythic_Core\Functions\MC_Search_Functions;
use Mythic_Core\Functions\MC_Woo_Cart_Functions;
use Mythic_Core\Functions\MC_User_Functions;
use Mythic_Core\Functions\MC_Product_Functions;
use Mythic_Core\Objects\MC_Ranked_Sale;
use Mythic_Core\Users\MC_Affiliates;
use Mythic_Core\Functions\MC_Retailer_Functions;

use Mythic_Core\Utils\MC_Server;

/**
 * Class MC_Actions
 *
 * @package Alter_Sleeves\System
 */
class AS_Actions {
    
    /**
     * MC_Actions constructor.
     */
    public function __construct() {
        add_action( 'init', [ AS_Crons::class, 'init' ] );
        
        add_action( 'mc_alter_management', [ MC_Alter_Functions::class, 'manage' ], 10, 11 );
        add_action( 'mc_alter_cards', [ MC_Alter_Functions::class, 'updateAssociatedCards' ] );
        add_action( 'mc_alters_design_groups', [ MC_Alter_Functions::class, 'arrangeAltersIntoDesignGroups' ] );
        add_action( 'mc_alter_names', [ MC_Alter_Functions::class, 'updateNames' ] );
        add_action( 'mc_alter_recent_results', [ MC_Alter_Functions::class, 'updateRecentResults' ] );
        add_action( 'mc_mythic_frames_printings', [ MC_Mythic_Frames_Functions::class, 'rand_printings' ] );
        add_action( 'mc_run_search_indexing_json', [ MC_Search_Functions::class, 'runGlobalIndexingJson' ] );
        add_action( 'mc_sleeves_production_report', [ MC_Production_Functions::class, 'dailyOrderReports' ] );
        add_action( 'mc_publishing_royalties', [ MC_Royalty_Functions::class, 'updateToPublisher' ] );
        
        add_action( 'mc_head_open', [ self::class, 'googleEcommerceTracking' ] );
        add_action( 'mc_header_cart', [ self::class, 'headerCart' ] );
        add_action( 'mc_header_cart', [ self::class, 'headerUser' ] );
        add_action( 'mc_header_logo', [ self::class, 'headerLogo' ] );
        add_action( 'mc_header_nav', [ self::class, 'headerNav' ] );
        add_action( 'mc_header_search', [ self::class, 'headerSearch' ] );
        add_action( 'init', [ self::class, 'addItems' ] );
        
        add_action( 'init', [ self::class, 'storewide_sale_activation' ] );
        
        /* Banners */
        
        add_action( 'admin_menu', [ self::class, 'menu_item' ] );
        add_action( 'admin_init', [ self::class, 'banner_settings' ] );
        add_action( 'admin_enqueue_scripts', [ self::class, 'load_admin_style' ] );
        
        /** To Sort */
        /** Add Actions */
        add_action( 'mc_update_product_sales', [ MC_Ranked_Sale::class, 'updateRanked_Sales' ] );
        add_action( 'import_royalties_from_orders', [ MC_Royalty_Functions::class, 'importRoyalties' ] );
        add_action( 'clear_royalties_from_cycle_orders', [ MC_Royalty_Functions::class, 'clearRoyalties' ] );
        add_action( 'update_balances_from_royalties', [ MC_Royalty_Functions::class, 'updateBalances' ] );
        add_filter( 'woocommerce_email_attachments', [ MC_Giftcard_Functions::class, 'attachGift_CardToEmail' ], 10, 4 );
        add_action( 'woocommerce_checkout_order_created', [ MC_Giftcard_Functions::class, 'assign_gift_cards_to_order' ] );
        add_action( 'woocommerce_checkout_order_created', [ MC_Giftcard_Functions::class, 'update_gift_card_balance' ] );
        
        add_action( 'mc_flagging_alter_email', [ self::class, 'as_email_creator' ], 10, 1 );
        add_action( 'gform_after_submission_24', [ $this, 'log_error' ], 10, 2 );
        $this->vendorActions();
    }
    
    public static function googleEcommerceTracking() {
        MC_Render::templatePart( 'scripts/google', 'impression' );
        MC_Render::templatePart( 'scripts/google', 'purchase' );
    }
    
    public static function headerCart() {
        MC_Render::templatePart( 'header/cart' );
    }
    
    public static function headerLogo() {
        MC_Render::templatePart( 'header/logo' );
    }
    
    public static function headerNav() {
        MC_Render::templatePart( 'header/nav' );
    }
    
    public static function headerSearch() {
        MC_Render::templatePart( 'header/search' );
    }
    
    public static function addItems() {
        return;
        $url = MC_Server::primaryPath();
        if( $url == 'checkout' && !isset( $_GET['key'] ) ) {
            $affiliates = $_COOKIE['content_creator'] ?? '';
            $affiliates = json_decode( stripslashes( $affiliates ), true );
            
            if( !empty( $affiliates ) && is_array( $affiliates ) ) {
                $get_applied_coupons = WC()->cart->get_applied_coupons();
                foreach( $affiliates as $affiliate ) {
                    $coupon_code = MC_Affiliates::userIdToCoupon( $affiliate );
                    if( empty( $get_applied_coupons ) || !in_array( $coupon_code, $get_applied_coupons ) ) {
                        WC()->cart->apply_coupon( $coupon_code );
                    }
                }
            }
        }
    }
    
    public static function vendorActions() {
        // Woocommerce
        if( !MC_WOO_ACTIVE ) return;
        add_action( 'woocommerce_checkout_create_order', [
            MC_Woo_Order_Functions::class,
            'contentCreatorOrderMeta',
        ],          1, 2 );
        add_action( 'woocommerce_before_calculate_totals', [ MC_Woo_Cart_Functions::class, 'caclulationQuantities' ],
                    10, 1 );
        add_action( 'woocommerce_before_calculate_totals', [ MC_Woo_Cart_Functions::class, 'giving_discount' ],
            10, 1 );
        add_action( 'woocommerce_checkout_order_created', [
            MC_Giftcard_Functions::class,
            'assign_gift_cards_to_order',
        ] );
        add_action( 'woocommerce_checkout_order_created', [ MC_Giftcard_Functions::class, 'update_gift_card_balance' ] );
        add_action( 'woocommerce_checkout_order_created', [
            MC_Woo_Order_Functions::class,
            'clearAffiliateCookies',
        ],          99999 );
        remove_action( 'woocommerce_cart_item_price', [
            'Woo_Download_Credits_Platinum',
            'wdcp_woocommerce_cart_item_price',
        ],             999 );
        remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
        add_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 20 );
        add_filter( 'woocommerce_create_account_default_checked', '__return_true' );
        add_filter( 'woocommerce_checkout_get_value', [ MC_Retailer_Functions::class, 'checkShippingValueForRetailerCoupon' ], 10, 2 );
        add_filter( 'woocommerce_shipping_fields', [ MC_Retailer_Functions::class, 'checkFieldsForRetailerCoupon' ] );
    }
    
    /**
     * @param int $idUser
     *
     * @return false|string
     */
    public static function as_email_creator( $idUser = 0 ) {
        if( empty( $idUser ) ) return '';
        $content = get_user_meta( $idUser, 'mc_alter_flag_email', true );
        // $content = get_user_meta( $idUser, 'as_alter_flag_email', true );
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/emails/flagging.php' );
        $emailContent    = ob_get_clean();
        $creatorEmail    = get_userdata( $idUser )->user_email;
        $creatorUserName = get_the_author_meta( 'user_nicename', $idUser );
        wp_mail( $creatorEmail, 'We have flagged some of your content', $emailContent );
        wp_mail( EMAIL_SUPPORT, '[Flagged] Email sent to '.$creatorUserName, $emailContent );
        //wp_mail( EMAIL_DEJAN, '[Flagged] Email sent to '.$creatorUserName, $emailContent );
        delete_user_meta( $idUser, 'mc_alter_flag_email' );
        wp_clear_scheduled_hook( 'mc_flagging_alter_email', [ $idUser ] );
        // delete_user_meta( $idUser, 'as_alter_flag_email' );
        // wp_clear_scheduled_hook( 'as_flagging_alter_email', [ $idUser ] );
        
        return $emailContent;
    }
    
    public function log_error( $entry, $form ) {
        $order_id = strip_tags( rgar( $entry, '1' ) );
        $order    = wc_get_order( $order_id );
        $idUser   = $order->get_user_id();
        
        $idProduct = strip_tags( rgar( $entry, '2' ) );
        // Resolution
        $resolution = rgar( $entry, '4' );
        
        // Issues
        $potentialIssues = [
            'alignment'       => '3.1',
            'color'           => '3.2',
            'resolution'      => '3.3',
            'copyright'       => '3.4',
            'cropping-tight'  => '3.5',
            'cropping-missed' => '3.6',
            'dithering'       => '3.7',
            'cmyk-0000'       => '3.8',
            'unwanted-white'  => '3.9',
            'other'           => 5,
        ];
        foreach( $potentialIssues as $key => $potentialIssue ) {
            if( $key != 'other' && rgar( $entry, $potentialIssue ) != $key ) continue;
            if( $key == 'other' && rgar( $entry, $potentialIssue ) == '' ) continue;
            if( $key == 'other' ) {
                $key = rgar( $entry, $potentialIssue );
                update_post_meta( $idProduct, 'as_violation_text', rgar( $entry, $potentialIssue ) );
            }
            
            switch( $potentialIssue ) {
                case 'resolution' :
                    wp_set_post_terms( $idProduct, 3022, 'alter_status', true );
                    break;
                case 'copyright' :
                    wp_set_post_terms( $idProduct, 3036, 'alter_status', true );
                    break;
                case 'cropping-tight' :
                    wp_set_post_terms( $idProduct, 3026, 'alter_status', true );
                    break;
                case 'cropping-missed' :
                    wp_set_post_terms( $idProduct, 3023, 'alter_status', true );
                    break;
                case 'dithering' :
                    wp_set_post_terms( $idProduct, 3025, 'alter_status', true );
                    break;
                case 'cmyk-0000' :
                    wp_set_post_terms( $idProduct, 3024, 'alter_status', true );
                    break;
                case 'unwanted-white' :
                    wp_set_post_terms( $idProduct, 3039, 'alter_status', true );
                    break;
                default:
                    break;
            }
        }
        
        /** SUSPEND ALTER */
        if( rgar( $entry, '7.1' ) == 1 ) {
            wp_update_post( [
                                'ID'          => $idProduct,
                                'post_status' => 'action',
                            ] );
            
            $creator      = MC_WP::authorId( $idProduct );
            $creatorEmail = get_userdata( $creator )->user_email;
            $firstName    = get_userdata( $creator )->first_name;
            $nameDesign   = MC_WP::meta( 'as_design_name', $idProduct );
            
            $message = '';
            $message .= '<p>Hey '.$firstName.',</p>';
            $message .= '<p>Thanks for all your great work contributing to Alter Sleeves, however <a href="/'
                        .get_the_permalink( $idProduct ).'">'.$nameDesign.'</a> appears to have an issue, so please check your <a href="https://www.altersleeves.com/dashboard/alterist/manage-alters">dashboard</a> to resolve this. Let us know at '.EMAIL_SUPPORT.' if anything is unclear.</p>';
            $message .= '<p>Regards,<br>';
            $message .= '<strong>Team Alter Sleeves</strong></p>';
            
            wp_mail( $creatorEmail, $nameDesign.' requires further action', $message );
        }
        
        /** HANDLE THE USER **/
        switch( $resolution ) {
            case 'credit' :
                $currentCredits = MC_WP::meta( '_download_credits', $idUser, 'user' );
                if( $currentCredits == '' ) $currentCredits = 0;
                $newCredits = $currentCredits + 1;
                update_user_meta( $idUser, '_download_credits', floatval( $newCredits ) );
                break;
            case '1get1' :
                do_action( 'sa_create_coupon_buy_1_get_1_free', $order_id );
                break;
            case '1get2' :
                do_action( 'sa_create_coupon_buy_1_get_2_free', $order_id );
                break;
            case 'credits' :
                $currentCredits = MC_WP::meta( '_download_credits', $idUser, 'user' );
                if( $currentCredits == '' ) $currentCredits = 0;
                $newCredits = $currentCredits + rgar( $entry, '6' );
                update_user_meta( $idUser, '_download_credits', floatval( $newCredits ) );
                break;
            case 'reship' :
                $address = [
                    'first_name' => $order->get_billing_first_name(),
                    'last_name'  => $order->get_billing_last_name(),
                    'email'      => $order->get_billing_email(),
                    'address_1'  => $order->get_shipping_address_1(),
                    'address_2'  => $order->get_shipping_address_2(),
                    'city'       => $order->get_shipping_city(),
                    'state'      => $order->get_shipping_state(),
                    'postcode'   => $order->get_shipping_postcode(),
                    'country'    => $order->get_shipping_country(),
                ];
                
                // Now we create the order
                $order = wc_create_order();
                $order->update_status( 'processing' );
                // The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
                $order->add_product( get_product( $idProduct ) ); // This is an existing SIMPLE product
                $order->set_address( $address, 'billing' );
                $order->set_address( $address, 'shipping' );
                //
                $order->calculate_totals();
                
                $order->update_meta_data( 'as_reship', $order_id );
                break;
        }
        
        /** RESCIND ROYALTY **/
        $quantity = 1;
        foreach( $order->get_items() as $itemId => $itemData ) {
            if( $idProduct != $itemData->get_product_id() ) continue;
            $quantity = $itemData->get_quantity();
        }
        $relinquishedRoyalties = MC_WP::meta( 'as_relinquished_royalties', $idProduct );
        if( ![ $relinquishedRoyalties ] || empty( $relinquishedRoyalties ) ) $relinquishedRoyalties = [];
        $errorMeta               = [
            'order_id' => $order_id,
            'quantity' => $quantity,
        ];
        $relinquishedRoyalties[] = $errorMeta;
        update_post_meta( $idProduct, 'as_relinquished_royalties', $relinquishedRoyalties );
    }
    
    /** Banners **/
    
    public static function menu_item() {
        add_menu_page( 'Discount Banner Settings', 'Banner', 'administrator', 'discount-banner', [ self::class, 'discount_banner_settings_page' ],
                       'dashicons-format-image', 57 );
    }
    
    public static function banner_settings() {
        //Regular banner
        add_settings_section( "regular_banner_section", "Regular Banner files", null, "regular-banner" );
        
        add_settings_field( "regular-banner-url", "Regular Banner URL", [ self::class, "regular_banner_url_display" ], "regular-banner",
                            "regular_banner_section" );
        register_setting( "regular_banner_section", "regular-banner-url" );
        
        add_settings_field( "desktop-regular-banner-file", "Desktop Banner File", [ self::class, "desktop_regular_banner_file_display" ],
                            "regular-banner", "regular_banner_section" );
        register_setting( "regular_banner_section", "desktop-regular-banner", [ self::class, "handle_desktop_regular_banner_file_upload" ] );
        
        add_settings_field( "mobile-regular-banner-file", "Mobile Banner File", [ self::class, "mobile_regular_banner_file_display" ],
                            "regular-banner", "regular_banner_section" );
        register_setting( "regular_banner_section", "mobile-regular-banner", [ self::class, "handle_mobile_regular_banner_file_upload" ] );
        
        //Discount banner
        add_settings_section( "discount_banner_section", "Discount Banner files (if 'Storewide sale' plugin enabled)", null, "discount-banner" );
        
        add_settings_field( "discount-banner-url", "Discount Banner URL", [ self::class, "discount_banner_url_display" ], "discount-banner",
                            "discount_banner_section" );
        register_setting( "discount_banner_section", "discount-banner-url" );
        
        add_settings_field( "desktop-discount-banner-file", "Desktop Banner File", [ self::class, "desktop_discount_banner_file_display" ],
                            "discount-banner", "discount_banner_section" );
        register_setting( "discount_banner_section", "desktop-discount-banner", [ self::class, "handle_desktop_discount_banner_file_upload" ] );
        
        add_settings_field( "mobile-discount-banner-file", "Mobile Banner File", [ self::class, "mobile_discount_banner_file_display" ],
                            "discount-banner", "discount_banner_section" );
        register_setting( "discount_banner_section", "mobile-discount-banner", [ self::class, "handle_mobile_discount_banner_file_upload" ] );
    }
    
    public function handle_desktop_regular_banner_file_upload( $option ) {
        if( !empty( $_FILES["desktop-regular-banner"]["tmp_name"] ) ) {
            $urls = wp_handle_upload( $_FILES["desktop-regular-banner"], [ 'test_form' => false ] );
            $temp = $urls["url"];
            
            return $temp;
        }
        return $option;
    }
    
    public function handle_mobile_regular_banner_file_upload( $option ) {
        if( !empty( $_FILES["mobile-regular-banner"]["tmp_name"] ) ) {
            $urls = wp_handle_upload( $_FILES["mobile-regular-banner"], [ 'test_form' => false ] );
            $temp = $urls["url"];
            
            return $temp;
        }
        return $option;
    }
    
    public function handle_desktop_discount_banner_file_upload( $option ) {
        if( !empty( $_FILES["desktop-discount-banner"]["tmp_name"] ) ) {
            $urls = wp_handle_upload( $_FILES["desktop-discount-banner"], [ 'test_form' => false ] );
            $temp = $urls["url"];
            
            return $temp;
        }
        return $option;
    }
    
    public function handle_mobile_discount_banner_file_upload( $option ) {
        if( !empty( $_FILES["mobile-discount-banner"]["tmp_name"] ) ) {
            $urls = wp_handle_upload( $_FILES["mobile-discount-banner"], [ 'test_form' => false ] );
            $temp = $urls["url"];
            
            return $temp;
        }
        return $option;
    }
    
    public function regular_banner_url_display() {
        ?>
        <input type="text" name="regular-banner-url" value="<?php echo get_option( 'regular-banner-url' ); ?>" style="width: 100%" />
        <?php
    }
    
    public function desktop_regular_banner_file_display() {
        $banner_img = get_option( 'desktop-regular-banner' );
        ?>
        <div class="banner_admin">
            <div>
                <input type="file" name="desktop-regular-banner" />
            </div>
            <?php if( $banner_img ): ?>
                <div class="banner_admin_preview">
                    <img src="<?php echo $banner_img; ?>">
                </div>
            <?php endif ?>
        </div>
        <?php
    }
    
    public function mobile_regular_banner_file_display() {
        $banner_img = get_option( 'mobile-regular-banner' );
        ?>
        <div class="banner_admin">
            <div>
                <input type="file" name="mobile-regular-banner" />
            </div>
            <?php if( $banner_img ): ?>
                <div class="banner_admin_preview">
                    <img src="<?php echo $banner_img ?>">
                </div>
            <?php endif ?>
        </div>
        <?php
    }
    
    public function discount_banner_url_display() {
        ?>
        <input type="text" name="discount-banner-url" value="<?php echo get_option( 'discount-banner-url' ); ?>" style="width: 100%" />
        <?php
    }
    
    public function desktop_discount_banner_file_display() {
        $banner_img = get_option( 'desktop-discount-banner' );
        ?>
        <div class="banner_admin">
            <div>
                <input type="file" name="desktop-discount-banner" />
            </div>
            <?php if( $banner_img ): ?>
                <div class="banner_admin_preview">
                    <img src="<?php echo $banner_img; ?>">
                </div>
            <?php endif ?>
        </div>
        <?php
    }
    
    public function mobile_discount_banner_file_display() {
        $banner_img = get_option( 'mobile-discount-banner' );
        ?>
        <div class="banner_admin">
            <div>
                <input type="file" name="mobile-discount-banner" />
            </div>
            <?php if( $banner_img ): ?>
                <div class="banner_admin_preview">
                    <img src="<?php echo $banner_img ?>">
                </div>
            <?php endif ?>
        </div>
        <?php
    }
    
    public function discount_banner_settings_page() {
        ?>
        <div class="wrap">
            <h1>Banner Settings</h1>

            <form method="post" action="options.php" enctype="multipart/form-data">
                <?php
                settings_fields( "regular_banner_section" );
                do_settings_sections( "regular-banner" );
                
                submit_button();
                ?>
            </form>

            <form method="post" action="options.php" enctype="multipart/form-data">
                <?php
                settings_fields( "discount_banner_section" );
                do_settings_sections( "discount-banner" );
                
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    /* END Banners */
    
    public static function load_admin_style() {
        wp_enqueue_style( 'admin_css', get_theme_file_uri().'/src/css/admin-style.css', false, '1.0.0' );
    }
    
    public static function storewide_sale_activation() {
        if( empty( $_GET['set_global_sale_on'] ) ) return;
        if( is_admin() ) {
            $sale_category = get_term_by( 'name', 'sale', 'product_cat' );
            if( !$sale_category ) {
                echo 'Please create category with name - "sale"';
                return false;
            }
            MC_User_Functions::set_users_sale_on_as_default( $sale_category );
            MC_Product_Functions::set_products_sale_on_as_default( $sale_category );
            
            exit;
        }
    }
    
}