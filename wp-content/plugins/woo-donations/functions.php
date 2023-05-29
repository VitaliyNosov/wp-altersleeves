<?php

if( !defined( 'ABSPATH' ) ) exit;

if( !defined( "wdgk_PLUGIN_DIR_PATH" ) )
    
    define( "wdgk_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ) );

if( !defined( "wdgk_PLUGIN_URL" ) )
    
    define( "wdgk_PLUGIN_URL", plugins_url().'/'.basename( dirname( __FILE__ ) ) );

// Get form setting options

function wdgk_get_wc_donation_setting() {
    return get_option( 'wdgk_donation_settings' );
}

// Success message

function success_option_msg_wdgk( $msg ) {
    return ' <div class="notice notice-success wdgk-success-msg is-dismissible"><p>'.$msg.'</p></div>';
}

// Error message

function failure_option_msg_wdgk( $msg ) {
    return '<div class="notice notice-error wdgk-error-msg is-dismissible"><p>'.$msg.'</p></div>';
}

function wdgk_add_donation_product_to_cart( $id ) {
    if( sizeof( WC()->cart->get_cart() ) > 0 ) {
        foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
            $_product = $values['data'];
            
            if( $_product->get_id() == $id ) {
                $found = true;
                WC()->cart->remove_cart_item( $cart_item_key );
            }
        }
            WC()->cart->add_to_cart( $id, 1, null, null );
    } else {
        // if no products in cart, add it
        WC()->cart->add_to_cart( $id, 1, null, null );
    }
}

function wdgk_generate_featured_image( $image_url, $post_id ) {
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents( $image_url );
    $filename   = basename( $image_url );
    if( wp_mkdir_p( $upload_dir['path'] ) ) $file = $upload_dir['path'].'/'.$filename;
    else                                    $file = $upload_dir['basedir'].'/'.$filename;
    file_put_contents( $file, $image_data );
    
    $wp_filetype = wp_check_filetype( $filename, null );
    $attachment  = [
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     => sanitize_file_name( $filename ),
        'post_content'   => '',
        'post_status'    => 'inherit'
    ];
    $attach_id   = wp_insert_attachment( $attachment, $file, $post_id );
    require_once( ABSPATH.'wp-admin/includes/image.php' );
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    $res1        = wp_update_attachment_metadata( $attach_id, $attach_data );
    $res2        = set_post_thumbnail( $post_id, $attach_id );
}

/**
 * @param $valid
 * @param $product
 * @param $coupon
 * @param $values
 *
 * @return false|mixed
 */
function bbloomer_exclude_product_from_product_promotions_frontend( $valid, $product, $coupon, $values ) {
    $_product = get_page_by_title('Additional Donation', OBJECT, 'product' );
    if( empty($_product) ) return $valid;
    if ( $_product->ID == $product->get_id() ) {
        $valid = false;
    }
    return $valid;
}
add_filter( 'woocommerce_coupon_is_valid_for_product', 'bbloomer_exclude_product_from_product_promotions_frontend', 9999, 4 );
