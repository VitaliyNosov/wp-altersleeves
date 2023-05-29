<?php

namespace Mythic_Core\System;

use MC_Currency_Functions;
use MC_Invoice_Functions;
use MC_Woo_Product_Functions;
use Mythic_Core\Display\MC_Template_Parts;
use Mythic_Core\Functions\MC_User_Functions;
use Mythic_Core\Functions\MC_Woo_Cart_Item_Functions;
use Mythic_Core\Functions\MC_Woo_Order_Functions;
use Mythic_Core\Settings\MC_Site_Settings;
use Mythic_Core\Utils\MC_Url;
use Mythic_Core\Utils\MC_Woo;

/**
 * Class MC_Filters
 *
 * @package Mythic_Core\System
 */
class MC_Filters {
    
    /**
     * MC_Filters constructor.
     */
    public function __construct() {
        $this->addCustomFilters();
        $this->addWordpressFilters();
        $this->addVendorFilters();
        $this->woocommerce();
    }
    
    /**
     * @param string $content
     *
     * @return string
     */
    public function content( string $content = 'default' ) : string {
        if( function_exists( 'is_product' ) && is_product() ) $content = 'product';
        return $content;
    }
    
    /**
     * @param $template
     *
     * @return string
     */
    public static function allowProfilesForAuthorsWithoutPosts( $template ) : string {
        global $wp_query;
        if( !is_author() && get_query_var( 'author' ) && ( 0 == $wp_query->posts->post ) ) {
            return get_author_template();
        }
        return $template;
    }
    
    /**
     * @param string $class
     *
     * @return string
     */
    public function headerClass( $class = '' ) : string {
        $sticky = MC_Site_Settings::value( 'header_sticky' ) ?? '';
        if( !empty( $sticky ) ) $class = $class.' header-sticky';
        
        return $class;
    }
    
    /**
     * @param string $layout
     *
     * @return string
     */
    public function layout( string $layout = 'default' ) : string {
        if( is_home() || is_front_page() ) return 'home';
        if( is_tax() || is_category() ) return 'tax';
        if( MC_Url::isBrowse() ) return 'browse';
        if( function_exists( 'is_product' ) && is_product() ) return 'product';
        if( is_page() ) return 'page';
        if( is_404() ) return '404';
        $post_type = get_post_type( MC_WP::currentId() );
        if( $post_type != 'post' ) {
            if( file_exists( MC_Template_Parts::get( 'layouts/single-', $post_type ) ) ) return 'single-'.$post_type;
        }
        if( is_single( 'single' ) ) return 'single';
        
        return $layout;
    }
    
    public static function postClasses( $classes ) {
        $classes[] = 'container layout';
        
        return $classes;
    }
    
    public function addCustomFilters() {
        add_filter( 'mc_content_filter', [ $this, 'content' ] );
        add_filter( 'mc_header_class', [ $this, 'headerClass' ] );
        add_filter( 'mc_layout_filter', [ $this, 'layout' ] );
        add_filter( 'mc_icon_logo_src', [ $this, 'iconLogo' ], 1 );
        add_filter( 'mc_sidebar_filter', [ $this, 'sidebar' ], 1 );
        add_filter( 'send_password_change_email', '__return_false' );
    }
    
    public function addVendorFilters() {
        // Profile Builder
        add_filter( 'login_form_defaults', [ MC_User_Functions::class, 'loginArgs' ], 10, 1 );
        
        // Gravity Forms
        add_filter( 'gform_confirmation_anchor_5', '__return_false' );
        // WooCommerce
    }
    
    public function addWordpressFilters() {
        add_filter( '404_template', [ self::class, 'allowProfilesForAuthorsWithoutPosts' ] );
        add_filter( 'post_class', [ self::class, 'postClasses' ], 1, 1 );
        add_filter( 'init', [ MC_Woo_Order_Functions::class, 'registerCustomStatuses' ] );
        add_filter( 'validate_username', [ MC_User_Functions::class, 'validateUsername' ], 10, 2 );
        add_filter( 'upload_mimes', [ self::class, 'addUploadMimes' ], 10, 2 );
    }
    
    /**
     * @return string
     */
    public function iconLogo() : string {
        global $icon_logo;
        return $icon_logo ?? MC_URI_ICON_LOGO;
    }
    
    /**
     * @param $sidebar
     *
     * @return mixed|string
     */
    public function sidebar( $sidebar ) {
        if( !function_exists( 'is_shop' ) ) return $sidebar;
        if( is_woocommerce() ) return 'store-filter';
        return $sidebar;
    }
    
    /**
     * @param $is_editable
     * @param $order
     *
     * @return bool
     */
    public function wc_make_processing_orders_editable( $is_editable, $order ) : bool {
        if( $order->get_status() == 'processing' ) return true;
        return $is_editable;
    }
    
    /**
     * All Woocommerce filters
     */
    public function woocommerce() {
        add_filter( 'bulk_actions-edit-shop_order', [ MC_Woo_Order_Functions::class, 'addBulkActions' ] );
        add_filter( 'wc_aelia_currencyswitcher_country_currencies', [ MC_Currency_Functions::class, 'forceCountryCurrencies', ], 999, 1 );
        add_filter( 'wc_order_is_editable', [ $this, 'wc_make_processing_orders_editable' ], 10, 2 );
        add_filter( 'wc_order_statuses', [ MC_Woo_Order_Functions::class, 'wpblog_wc_add_order_statuses' ] );
        add_filter( 'woocommerce_account_menu_items', [ MC_User_Functions::class, 'dashboardNavItems' ] );
        // @TODO: check next action "remove_meta"
        //        add_action( 'woocommerce_new_order_item', [ MC_Woo_Order_Item_Functions::class, 'removeMeta', ], 10, 3 );
        add_filter( 'woocommerce_ajax_variation_threshold', [ MC_Woo_Product_Functions::class, 'setVariationThreshold' ] );
        add_filter( 'woocommerce_remove_cart_item', [ MC_Woo_Cart_Item_Functions::class, 'removeMeta', ], 10, 1 );
        add_filter( 'woocommerce_enqueue_styles', [ MC_Woo::class, 'enqueueStyle' ] );
        add_filter( 'woocommerce_cart_emptied', [ MC_Woo_Cart_Item_Functions::class, 'removeMeta', ], 10, 1 );
        add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
        add_filter( 'woocommerce_product_variation_title_include_attributes', '__return_false' );
        add_filter( 'woocommerce_form_field_args', [ MC_Woo::class, 'formClasses' ], 10, 3 );
        add_filter( 'woocommerce_is_purchasable', '__return_true' );
        add_filter( 'wpo_wcpdf_paper_format', [ MC_Invoice_Functions::class, 'paperFormat' ], 9999, 2 );
    }

	/**
	 * Add upload mimes
	 *
	 * @param $mimes
	 *
	 * @return mixed
	 */
	public static function addUploadMimes( $mimes ) {
		$mimes['csv'] = "text/csv";

		return $mimes;
    }
}