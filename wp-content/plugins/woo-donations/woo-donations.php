<?php
/*
Plugin Name: Woo Donations
Description: A plugin to add donation for campaign
Author: Geek Code Lab
Version: 2.5
WC tested up to: 5.8.0
Author URI: https://geekcodelab.com/
Text Domain : woo-donations
*/

if( !defined( 'ABSPATH' ) ) exit;

if( !defined( "wdgk_PLUGIN_DIR_PATH" ) )
    
    define( "wdgk_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ) );

if( !defined( "wdgk_PLUGIN_URL" ) )
    
    define( "wdgk_PLUGIN_URL", plugins_url().'/'.basename( dirname( __FILE__ ) ) );

define( "wdgk_BUILD", '2.5' );

require_once( wdgk_PLUGIN_DIR_PATH.'functions.php' );

add_action( 'admin_menu', 'wdgk_admin_menu_donation_setting_page' );

add_action( 'admin_print_styles', 'wdgk_admin_style' );

register_activation_hook( __FILE__, 'wdgk_plugin_active_woocommerce_donation' );

function wdgk_plugin_active_woocommerce_donation() {
    $error = 'required <b>woocommerce</b> plugin.';
    if( !class_exists( 'WooCommerce' ) ) {
        die( 'Plugin NOT activated: '.$error );
    }
    if( is_plugin_active( 'woo-donations-pro/woo-donations-pro.php' ) ) {
        deactivate_plugins( 'woo-donations-pro/woo-donations-pro.php' );
    }
    $btntext            = "Add Donation";
    $textcolor          = "#FFFFFF";
    $btncolor           = "#289dcc";
    $form_title         = "Donation";
    $amount_placeholder = "Ex.100";
    $note_placeholder   = "Note";
    $options            = [];
    $setting            = get_option( 'wdgk_donation_settings' );
    
    if( isset( $setting ) && !empty( $setting ) ) $options = $setting;
    
    // unset($options['Noteplaceholder']);
    
    if( !isset( $setting['Text'] ) ) $options['Text'] = $btntext;
    if( !isset( $setting['TextColor'] ) ) $options['TextColor'] = $textcolor;
    if( !isset( $setting['Color'] ) ) $options['Color'] = $btncolor;
    if( !isset( $setting['Formtitle'] ) ) $options['Formtitle'] = $form_title;
    if( !isset( $setting['AmtPlaceholder'] ) ) $options['AmtPlaceholder'] = $amount_placeholder;
    if( !isset( $setting['Noteplaceholder'] ) ) $options['Noteplaceholder'] = $note_placeholder;
    
    if( !isset( $setting['Product'] ) ) {
        $id  = wp_insert_post( [ 'post_title' => 'Donation', 'post_name' => 'donation', 'post_type' => 'product', 'post_status' => 'publish' ] );
        $sku = 'donation-'.$id;
        update_post_meta( $id, '_sku', $sku );
        update_post_meta( $id, '_tax_status', 'none' );
        update_post_meta( $id, '_tax_class', 'zero-rate' );
        update_post_meta( $id, '_visibility', 'hidden' );
        update_post_meta( $id, '_regular_price', 0 );
        update_post_meta( $id, '_price', 0 );
        update_post_meta( $id, '_sold_individually', 'yes' );
        $options['Product'] = $id;
        $taxonomy           = 'product_visibility';
        wp_set_object_terms( $id, 'exclude-from-catalog', $taxonomy );
        wdgk_generate_featured_image( wdgk_PLUGIN_URL.'/assets/images/donation_thumbnail.jpg', $id );
    }
    if( count( $options ) > 0 ) {
        update_option( 'wdgk_donation_settings', $options );
    }
}

add_action( 'wp_enqueue_scripts', 'wdgk_include_front_script' );
function wdgk_include_front_script() {
    wp_enqueue_style( "wdgk_front_style", wdgk_PLUGIN_URL."/assets/css/wdgk_front_style.css", '', wdgk_BUILD );
    
    wp_enqueue_script( 'wdgk_donation_script', wdgk_PLUGIN_URL.'/assets/js/wdgk_front_script.js', [ 'jquery' ], wdgk_BUILD );
}

function wdgk_admin_style() {
    if( is_admin() ) {
        $css = wdgk_PLUGIN_URL.'/assets/css/wdgk_admin_style.css';
        wp_enqueue_style( 'wdgk_admin_style', $css, '', wdgk_BUILD );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
    }
}

function wdgk_admin_menu_donation_setting_page() {
    add_submenu_page( 'woocommerce', 'Donation', 'Donation', 'manage_options', 'wdgk-donation-page', 'wdgk_donation_page_setting' );
}

function wdgk_donation_page_setting() {
    if( !current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    include( wdgk_PLUGIN_DIR_PATH.'options.php' );
}

function wdgk_plugin_add_settings_link( $links ) {
    $support_link = '<a href="https://geekcodelab.com/contact/"  target="_blank" >'.__( 'Support' ).'</a>';
    array_unshift( $links, $support_link );
    
    $pro_link = '<a href="https://geekcodelab.com/wordpress-plugins/woo-donation-pro/"  target="_blank" style="color:#46b450;font-weight: 600;">'.__( 'Premium Upgrade' ).'</a>';
    array_unshift( $links, $pro_link );
    
    $settings_link = '<a href="admin.php?page=wdgk-donation-page">'.__( 'Settings' ).'</a>';
    array_unshift( $links, $settings_link );
    return $links;
}

$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'wdgk_plugin_add_settings_link' );

$product  = "";
$cart     = "";
$checkout = "";
$options  = wdgk_get_wc_donation_setting();
if( isset( $options['Product'] ) ) {
    $product = $options['Product'];
}
if( isset( $options['Cart'] ) ) {
    $cart = $options['Cart'];
}
if( isset( $options['Checkout'] ) ) {
    $checkout = $options['Checkout'];
}
if( isset( $options['Note'] ) ) {
    $note = $options['Note'];
}
if( !empty( $product ) && $cart == 'on' ) {
    add_action( 'woocommerce_proceed_to_checkout', 'wdgk_donation_form_front_html' );
}
if( !empty( $product ) && $checkout == 'on' ) {
    add_action( 'woocommerce_before_checkout_form', 'wdgk_add_donation_on_checkout_page' );
}

add_shortcode( 'wdgk_donation', 'wdgk_donation_form_shortcode_html' );

function wdgk_add_donation_on_checkout_page() {
    global $woocommerce;
    $checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : $woocommerce->cart->get_checkout_url();
    wdgk_donation_form_front_html( $checkout_url );
}

function wdgk_donation_form_front_html( $redurl ) {
    if( is_checkout() || time() > 1638162982 ) return;
    global $woocommerce;
    $product            = "";
    $text               = "";
    $note               = "";
    $note_html          = "";
    $form_title         = "Donation";
    $amount_placeholder = "Ex.100";
    $note_placeholder   = "Note";
    
    $options = wdgk_get_wc_donation_setting();
    
    if( isset( $options['Product'] ) ) {
        $product = $options['Product'];
    }
    if( isset( $options['Text'] ) ) {
        $text = $options['Text'];
    }
    if( isset( $options['Note'] ) ) {
        $note = $options['Note'];
    }
    if( isset( $options['Formtitle'] ) ) {
        $form_title = $options['Formtitle'];
    }
    if( isset( $options['AmtPlaceholder'] ) ) {
        $amount_placeholder = $options['AmtPlaceholder'];
    }
    if( isset( $options['Noteplaceholder'] ) ) {
        $note_placeholder = $options['Noteplaceholder'];
    }
    if( !empty( $product ) && $note == 'on' ) {
        $note_html = '<textarea id="w3mission" rows="3" cols="20" placeholder="'.$note_placeholder.'" name="donation_note" class="donation_note"></textarea>';
    }
    
    if( !empty( $redurl ) && isset( $redurl ) ) {
        $cart_url = $redurl;
    } else {
        $cart_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : $woocommerce->cart->get_cart_url();
    }
    
    if( !empty( $product ) ) {
        $ajax_url     = admin_url( 'admin-ajax.php' );
        $current_cur  = get_woocommerce_currency();
        $cur_syambols = get_woocommerce_currency_symbols();
        
        $charity_select = '';
        
        $charities        = [];
        $content_creators = get_users( [ 'role' => 'content_creator', 'meta_key' => 'mc_charity_name', 'meta_compare' => '!=', 'meta_value' => '' ] );
        if( count( $content_creators ) ) {
            foreach( $content_creators as $user ) {
                $charity['status']          = time() < 1638162982;
                $charity['name']            = get_user_meta( $user->ID, 'mc_charity_name', true );
                $charity['charity_user_id'] = $user->ID;
                $charities[]                = $charity;
            }
        }
        $charity_select .= '<select class="form-select charity_select"><option data-charity_user_id=0>Split between all causes</option>';
        if( count( $charities ) ) {
            foreach( $charities as $charity ) {
                $cookie   = isset( $_COOKIE['charity_user_id'] ) ? $_COOKIE['charity_user_id'] : '';
                $selected = '';
                if(
                    $charity['charity_user_id'] == $cookie
                ) {
                    $selected = 'selected';
                }
                $name = $charity['name'];
                if( !empty( $charity['charity_user_id'] ) ) {
                    $display_name = MC_User_Functions::displayName( $charity['charity_user_id'] );
                    if( !empty( $display_name ) ) $name = $display_name.' - '.$name;
                }
                $charity_select .= '<option data-charity_user_id="'.$charity['charity_user_id'].'" '.$selected.'>'.$name.'</option>';
            }
        }
        $charity_select .= '</select><br>';
        if( empty( $selected ) && empty( $cookie ) ) {
            $charity_select = '<p>Our <a href="/content-creators">content creators</a> have helped us select charities to give to, so please select one below if you see a cause you particularly care about!</p>'.$charity_select;
        }
        
        printf( '<div class="wdgk_donation_content"><h3>'.esc_attr( $form_title,
                                                                    'woo-donations' ).'</h3>'.$charity_select.'<div class="wdgk_display_option"> <span>'.esc_attr( $cur_syambols[ $current_cur ] ).'</span><input type="text" name="donation-price" class="wdgk_donation" placeholder="'.esc_attr( $amount_placeholder,
                                                                                                                                                                                                                                                                                                   'woo-donations' ).'"></div>'.$note_html.'<a href="javascript:void(0)" class="button wdgk_add_donation" data-product-id="'.esc_attr( $product ).'" data-product-url="'.esc_attr( $cart_url ).'">'.esc_attr( $text,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              'woo-donations' ).'</a><input type="hidden" name="wdgk_product_id" value="" class="wdgk_product_id"><input type="hidden" name="wdgk_ajax_url" value="'.esc_attr( $ajax_url ).'" class="wdgk_ajax_url"><img src="'.wdgk_PLUGIN_URL.'/assets/images/ajax-loader.gif" class="wdgk_loader wdgk_loader_img"><div class="wdgk_error_front"></div></div>' );
    }
}

function wdgk_donation_form_shortcode_html( $redurl ) {
    return;
    global $woocommerce;
    $product            = "";
    $text               = "";
    $note               = "";
    $note_html          = "";
    $form_title         = "Donation";
    $amount_placeholder = "Ex.100";
    $note_placeholder   = "Note";
    
    $options = wdgk_get_wc_donation_setting();
    
    if( isset( $options['Product'] ) ) {
        $product = $options['Product'];
    }
    if( isset( $options['Text'] ) ) {
        $text = $options['Text'];
    }
    if( isset( $options['Note'] ) ) {
        $note = $options['Note'];
    }
    if( isset( $options['Formtitle'] ) ) {
        $form_title = $options['Formtitle'];
    }
    if( isset( $options['AmtPlaceholder'] ) ) {
        $amount_placeholder = $options['AmtPlaceholder'];
    }
    if( isset( $options['Noteplaceholder'] ) ) {
        $note_placeholder = $options['Noteplaceholder'];
    }
    if( !empty( $product ) && $note == 'on' ) {
        $note_html = '<textarea id="w3mission" rows="3" cols="20" placeholder="'.$note_placeholder.'" name="donation_note" class="donation_note"></textarea>';
    }
    
    if( !empty( $redurl ) && isset( $redurl ) ) {
        $cart_url = $redurl;
    } else {
        $cart_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : $woocommerce->cart->get_cart_url();
    }
    
    if( !empty( $product ) ) {
        $ajax_url     = admin_url( 'admin-ajax.php' );
        $current_cur  = get_woocommerce_currency();
        $cur_syambols = get_woocommerce_currency_symbols();
        
        return '<div class="wdgk_donation_content"><h3>'.esc_attr( $form_title,
                                                                   'woo-donations' ).'</h3><div class="wdgk_display_option"> <span>'.esc_attr( $cur_syambols[ $current_cur ] ).'</span><input type="text" name="donation-price" class="wdgk_donation" placeholder="'.esc_attr( $amount_placeholder,
                                                                                                                                                                                                                                                                               'woo-donations' ).'"></div>'.$note_html.'<a href="javascript:void(0)" class="button wdgk_add_donation" data-product-id="'.esc_attr( $product ).'" data-product-url="'.esc_attr( $cart_url ).'">'.esc_attr( $text,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          'woo-donations' ).'</a><input type="hidden" name="wdgk_product_id" value="" class="wdgk_product_id"><input type="hidden" name="wdgk_ajax_url" value="'.esc_attr( $ajax_url ).'" class="wdgk_ajax_url"><img src="'.wdgk_PLUGIN_URL.'/assets/images/ajax-loader.gif" class="wdgk_loader wdgk_loader_img"><div class="wdgk_error_front"></div></div>';
    }
}

add_action( 'wp_head', 'wdgk_set_button_text_color' );
function wdgk_set_button_text_color() { ?>
    <style>
        <?php $color = "";
        $textcolor = "";
        $options = wdgk_get_wc_donation_setting();

        if (isset($options['Color'])) {
            $color = $options['Color'];
            _e('.wdgk_donation_content a.button.wdgk_add_donation { background-color: ' . $color . ' !important; } ');
        }

        if (isset($options['TextColor'])) {
            $textcolor = $options['TextColor'];
            _e('.wdgk_donation_content a.button.wdgk_add_donation { color: ' . $textcolor . ' !important; }');
        }

        ?>
    </style>
    
    <?php
}

function wdgk_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
    $pid     = "";
    $options = wdgk_get_wc_donation_setting();
    if( isset( $options['Product'] ) ) {
        $pid = $options['Product'];
    }
    if( isset( $_COOKIE['wdgk_product_price'] ) ) {
        if( $product_id == $pid ) {
            $cart_item_data['donation_price'] = $_COOKIE['wdgk_product_price'];
            $cart_item_data['donation_note']  = $_COOKIE['wdgk_donation_note'] ?? '';
        }
    }
    return $cart_item_data;
}

add_filter( 'woocommerce_add_cart_item_data', 'wdgk_add_cart_item_data', 10, 3 );
add_action( 'woocommerce_before_calculate_totals', 'wdgk_before_calculate_totals', 10, 1 );

function wdgk_before_calculate_totals( $cart_obj ) {
    $pid     = "";
    $options = wdgk_get_wc_donation_setting();
    if( isset( $options['Product'] ) ) {
        $pid = $options['Product'];
    }
    if( is_admin() && !defined( 'DOING_AJAX' ) ) {
        return;
    }
    // Iterate through each cart item
    foreach( $cart_obj->get_cart() as $key => $value ) {
        $id = $value['data'];
        
        if( isset( $value['donation_price'] ) && $id->get_id() == $pid ) {
            $price = $value['donation_price'];
            $value['data']->set_price( ( $price ) );
        }
    }
}

// Mini cart: Display custom price
add_filter( 'woocommerce_cart_item_price', 'wdpgk_filter_cart_item_price', 10, 3 );
function wdpgk_filter_cart_item_price( $price_html, $cart_item, $cart_item_key ) {
    if( isset( $cart_item['donation_price'] ) ) {
        return wc_price( $cart_item['donation_price'] );
    }
    
    return $price_html;
}

// Mini cart: Display Custom subtotal price
add_filter( 'woocommerce_cart_item_subtotal', 'wdpgk_show_product_discount_order_summary', 10, 3 );

function wdpgk_show_product_discount_order_summary( $total, $cart_item, $cart_item_key ) {
    //Get product object
    if( isset( $cart_item['donation_price'] ) ) {
        $total = wc_price( $cart_item['donation_price'] * $cart_item['quantity'] );
    }
    // Return the html
    return $total;
}

add_action( 'wp_ajax_wdgk_donation_form', 'wdgk_donation_ajax_callback' );    // If called from admin panel
add_action( 'wp_ajax_nopriv_wdgk_donation_form', 'wdgk_donation_ajax_callback' );
function wdgk_donation_ajax_callback() {
    $product_id   = sanitize_text_field( $_POST['product_id'] );
    $price        = sanitize_text_field( $_POST['price'] );
    $redirect_url = sanitize_text_field( $_POST['redirect_url'] );
    wdgk_add_donation_product_to_cart( $product_id );
    $response        = [];
    $response['url'] = $redirect_url;
    if( !preg_match( "/^[0-9.]*$/", $price ) || $price < 0.01 ) {
        $response['error'] = "true";
    }
    $response = json_encode( $response );
    _e( $response, 'woo-donations' );
    die;
}

/**
 * Display custom item data in the cart
 */
function wdgk_plugin_republic_get_item_data( $item_data, $cart_item_data ) {
    if(
        isset( $cart_item_data['donation_note'] ) && isset( $cart_item_data['donation_price'] ) && !empty( $cart_item_data['donation_note'] ) &&
        !empty( $cart_item_data['donation_note'] )
    ) {
        $item_data[] = [
            'key'   => __( 'Description', 'plugin-republic' ),
            'value' => wc_clean( $cart_item_data['donation_note'] )
        ];
    }
    return $item_data;
}

add_filter( 'woocommerce_get_item_data', 'wdgk_plugin_republic_get_item_data', 10, 2 );

/**
 * Add custom meta to order
 */
function wdgk_plugin_republic_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
    if( isset( $values['donation_note'] ) ) {
        $item->add_meta_data(
            __( 'Description', 'plugin-republic' ),
            $values['donation_note'],
            true
        );
    }
}

add_action( 'woocommerce_checkout_create_order_line_item', 'wdgk_plugin_republic_checkout_create_order_line_item', 10, 4 );

/**
 * Add custom cart item data to emails
 */
function wdgk_plugin_republic_order_item_name( $product_name, $item ) {
    if( isset( $item['donation_note'] ) && isset( $item['donation_price'] ) ) {
        $product_name .= sprintf(
            '<ul><li>%s: %s</li></ul>',
            __( 'Description', 'plugin_republic' ),
            esc_html( $item['donation_note'] )
        );
    }
    return $product_name;
}

add_filter( 'woocommerce_order_item_name', 'wdgk_plugin_republic_order_item_name', 10, 2 );

/* Add "Donation" column on admin side order list */

add_filter( 'manage_edit-shop_order_columns', 'misha_order_items_column' );
function misha_order_items_column( $order_columns ) {
    $order_columns['order_products'] = "Donation";
    return $order_columns;
}

add_action( 'manage_shop_order_posts_custom_column', 'wdgk_order_items_column_cnt' );
function wdgk_order_items_column_cnt( $colname ) {
    global $the_order; // the global order object
    
    if( $colname == 'order_products' ) {
        // get items from the order global object
        $order_items = $the_order->get_items();
        $product     = "";
        $options     = wdgk_get_wc_donation_setting();
        if( isset( $options['Product'] ) ) {
            $product = $options['Product'];
        }
        if( !is_wp_error( $order_items ) ) {
            $donation_flag = false;
            foreach( $order_items as $order_item ) {
                if( $product == $order_item['product_id'] ) {
                    $donation_flag = true;
                }
            }
            if( $donation_flag == true ) {
                _e( '<span class="dashicons dashicons-yes-alt wdgk_right_icon"></span>' );
            }
        }
    }
}
