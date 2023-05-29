<?php

if( !function_exists( 'mythic_retail' ) ) {
    function mythic_gaming() {
        require_once 'classes/Loader/MR_Global_Loader.php';

        new Mythic_Retail\Loader\MR_Global_Loader();
    }

    add_action( 'after_setup_theme', 'mythic_gaming', 3 );
}

add_filter( 'woocommerce_account_menu_items', 'misha_one_more_link' );
function misha_one_more_link( $menu_links ) {
    // we will hook "anyuniquetext123" later
    $new = [ 'business_details' => 'Business Details' ];

    // or in case you need 2 links
    // $new = array( 'link1' => 'Link 1', 'link2' => 'Link 2' );

    // array_slice() is good when you want to add an element between the other ones
    $menu_links = array_slice( $menu_links, 0, 1, true )
                  + $new
                  + array_slice( $menu_links, 1, null, true );

    return $menu_links;
}

add_filter( 'woocommerce_get_endpoint_url', 'misha_hook_endpoint', 10, 4 );
function misha_hook_endpoint( $url, $endpoint, $value, $permalink ) {
    if( $endpoint === 'business_details' ) {
        $url = '/dashboard/partner-application';
        if( !empty( $details = MC_WP::meta( 'business_details', MC_User_Functions::id(), 'user' ) ) ) {
            $url   .= '?';
            $count = 1;
            foreach( $details as $key => $detail ) {
                if( empty($detail) ) continue;
                $url .= $key.'='.$detail;
                if( $count == count( $details ) ) break;
                $url .= '&';
                $count++;
            }
        }
    }

    return $url;
}

add_filter( 'woocommerce_checkout_get_value', function( $input, $key ) {
    $details = MC_WP::meta( 'business_details', $user_id = MC_User_Functions::id(), 'user' );
    $user = get_user_by('ID', $user_id );

    switch( $key ) :
        case 'billing_first_name':
        case 'shipping_first_name':
            return $user->first_name ?? $input;
        case 'billing_last_name':
        case 'shipping_last_name':
            return $user->last_name ?? $input;
        case 'billing_email':
            return $details[1] ?? $input;
        case 'billing_phone':
            return $details[2] ?? $input;
        case 'billing_company':
        case 'shipping_company':
            return $details[4] ?? $input;
        case 'billing_address_1':
        case 'shipping_address_1':
            return $details['5.1'] ?? $input;
        case 'billing_address_2':
        case 'shipping_address_2':
            return $details['5.2'] ?? $input;
        case 'billing_city':
        case 'shipping_city':
            return $details['5.3'] ?? $input;
        case 'billing_state':
        case 'shipping_state':
            return $details['5.4'] ?? $input;
        case 'billing_country':
        case 'shipping_country':
            return MC_Geo::nameToCode($details['5.6']) ?? $input;
        case 'billing_postcode':
        case 'shipping_postcode':
            return $details['5.5'] ?? $input;
    endswitch;
    return $input;
}, 10, 2 );




add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');

function woocommerce_ajax_add_to_cart() {

    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
    $variation_id = absint($_POST['variation_id']);
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
    $product_status = get_post_status($product_id);

    if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {

        do_action('woocommerce_ajax_added_to_cart', $product_id);

        if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
            wc_add_to_cart_message(array($product_id => $quantity), true);
        }

        WC_AJAX :: get_refreshed_fragments();
    } else {

        $data = array(
            'error' => true,
            'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));

        echo wp_send_json($data);
    }

    wp_die();
}

add_filter( 'woocommerce_billing_fields', 'ts_unrequire_wc_phone_field');
function ts_unrequire_wc_phone_field( $fields ) {
    $fields['billing_company']['required'] = true;
    return $fields;
}

add_filter("woocommerce_checkout_fields", "custom_override_checkout_fields", 1);
function custom_override_checkout_fields($fields) {
    $fields['billing']['billing_first_name']['priority'] = 3;
    $fields['billing']['billing_last_name']['priority'] = 4;
    $fields['billing']['billing_company']['priority'] = 1;
    $fields['billing']['billing_country']['priority'] = 2;
    $fields['billing']['billing_state']['priority'] = 5;
    $fields['billing']['billing_address_1']['priority'] = 6;
    $fields['billing']['billing_address_2']['priority'] = 7;
    $fields['billing']['billing_city']['priority'] = 8;
    $fields['billing']['billing_postcode']['priority'] = 9;
    $fields['billing']['billing_email']['priority'] = 10;
    $fields['billing']['billing_phone']['priority'] = 11;
    return $fields;
}

add_action('woocommerce_order_status_pending', 'email_order_processing_status_for_pending', 10, 2 );
function email_order_processing_status_for_pending( $order_id, $order ) {
    WC()->mailer()->get_emails()['WC_Email_Customer_Processing_Order']->trigger( $order_id );
}