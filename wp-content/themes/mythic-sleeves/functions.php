<?php

use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;

if( !function_exists( 'alter_sleeves' ) ) {
    function alter_sleeves() {
        require_once 'classes/Loader/AS_Global_Loader.php';
        
        new Alter_Sleeves\Loader\AS_Global_Loader();
    }
    
    add_action( 'after_setup_theme', 'alter_sleeves', 3 );
}

/** BADDDDDD - Retail partner stuff to update! */

add_filter( 'gform_confirmation_43', 'custom_confirmation', 10, 4 );
function custom_confirmation( $confirmation, $form, $entry, $ajax ) {
    $storename = rgar( $entry, '1' );
    if( empty( $storename ) ) return '<p>Invalid input. No store name provided</p>';
    $affiliate_code = rgar( $entry, '2' ) ?? '';
    if( empty( $affiliate_code ) ) $affiliate_code = $storename;
    $affiliate_code = MC_Vars::alphanumericOnly( strtolower( $affiliate_code ), false );
    $affiliate_code = str_replace( ' ', '', $affiliate_code );
    
    $data_exists    = get_option( 'retailer_'.$affiliate_code, false );
    $sleeves_coupon = MC_Vars::generate( 10 );
    if( !empty( $data_exists ) ) {
        $sleeves_coupon = $data_exists['sleeves_coupon'] ?? '';
    } else {
        $data                      = [
            'storename'      => $storename,
            'affiliate_code' => $affiliate_code,
            'sleeves_coupon' => $sleeves_coupon
        ];
        $data['affiliate_code_id'] = add_retailer_confirmation_coupon( $sleeves_coupon, $storename );
        add_retailer_coupon( $affiliate_code );
        
        update_option( 'retailer_'.$storename, $data );
        update_option( 'retailer_code_'.$sleeves_coupon, $affiliate_code );
    }
    
    ob_start();
    ?>
    <strong>Store Name:</strong> <?= $storename ?><br><br>
    <strong>Coupon/Affiliate Code:</strong> <?= $affiliate_code ?><br><br>
    <strong>Affiliate URL:</strong> https://www.altersleeves.com/<?= $affiliate_code ?><br><br>
    <strong>Activation code/Coupon for up 10 Alter Sleeves:</strong> <?= $sleeves_coupon ?>
    <p class="my-2"><strong><a href="/retail-partner-activation?code=<?= $sleeves_coupon ?>">Click here to activate store</a></strong></p>
    <?php
    
    return $confirmation.ob_get_clean();
}

function add_retailer_confirmation_coupon( $coupon_code, $storename ) {
    $pre = get_page_by_title( $coupon_code, OBJECT, 'shop_coupon' );
    if( !empty( $pre ) ) {
        return $pre->ID;
    }
    $coupon = new WC_Coupon();
    $coupon->set_code( $coupon_code );
    $coupon->set_discount_type( 'fixed_cart' );
    $coupon->set_amount( 72 );
    $coupon->set_usage_limit( 1 );
    $coupon->set_free_shipping( 1 );
    $coupon->set_meta_data( [ 'storename' => $storename ] );
    $coupon->save();
    return $coupon->get_id();
}

function add_retailer_coupon( $affiliate_code ) {
    $pre = get_page_by_title( $affiliate_code, OBJECT, 'shop_coupon' );
    if( !empty( $pre ) ) {
        return $pre->ID;
    }
    $coupon = new WC_Coupon();
    $coupon->set_code( $affiliate_code );
    $coupon->set_discount_type( 'percent' );
    $coupon->set_amount( 5 );
    $coupon->set_individual_use( 1 );
    $coupon->set_usage_limit( 1 );
    $coupon->set_free_shipping( 1 );
    $coupon->save();
    return $coupon->get_id();
}

add_filter( 'gform_confirmation_45', 'retailer_partner_details', 10, 4 );
function retailer_partner_details( $confirmation, $form, $entry, $ajax ) {
    $first_name      = rgar( $entry, '1' );
    $last_name       = rgar( $entry, '2' );
    $activation_code = rgar( $entry, '7' );
    $email           = rgar( $entry, '6' );
    
    $retail_coupon = get_option( 'retailer_code_'.$activation_code, '' );
    if( empty( $retail_coupon ) ) return $confirmation;
    $store_data = get_option( 'retailer_'.$retail_coupon, false );
    if( empty( $store_data ) || isset( $store_data['user_id'] ) ) return $confirmation;
    $data = [
        'address_1' => rgar( $entry, "5.1" ),
        'city'      => rgar( $entry, "5.3" ),
        'state'     => rgar( $entry, "5.4" ),
        'country'   => rgar( $entry, "5.5" ),
        'post_code' => rgar( $entry, "5.6" ),
    ];
    
    $user = get_user_by( 'login', $retail_coupon );
    if( empty( $user ) ) {
        wp_create_user( $retail_coupon, $password = MC_Vars::generate( 15 ), $email );
        $user = get_user_by( 'email', $email );
        
        wp_mail( $email, 'Your new Alter Sleeves retail account',
                 "Hello $first_name,<br> <p>A retail account has been created for you on Alter Sleeves.<br>You can login using this email or your username which is $retail_coupon <a href='https://www.altersleeves.com/login'>here</a></p><p>Your affiliate link is https://www.altersleeves.com/$retail_coupon</p><p>Your password is $password</p><p>Please contact support@altersleeves.com if you have any issues</p>" );
    }
    $user->set_role( 'retailer' );
    $user->set_role( 'content_creator' );
    $user_id = $user->ID;
    update_user_meta( $user_id, 'first_name', $first_name );
    update_user_meta( $user_id, 'last_name', $last_name );
    update_user_meta( $user_id, 'store_address', $data );
    update_user_meta( $user_id, '_mc_affiliate_coupon', $retail_coupon );
    update_user_meta( $user_id, '_mc_affiliate_url', '/content-creator/'.$retail_coupon );
    
    $store_data['user_id'] = $user_id;
    update_option( 'retailer_'.$retail_coupon, $store_data );
    
    $promotion_data = [ 'userId' => $user_id, 'couponId' => $store_data['affiliate_code_id'], 'redirectLink' => $retail_coupon ];
    MC_Affiliate_Coupon::savePromotionData( $promotion_data, true );
    
    return $confirmation;
}


// TODO: replace this function with Mythic_Core\Users\MC_Affiliates::updateAffiliateData()
function activate_retailer_account_from_order( WC_Order $order ) {
    $coupons       = $order->get_coupon_codes();
    $retail_coupon = '';
    foreach( $coupons as $coupon ) {
        $option = get_option( 'retailer_code_'.$coupon, '' );
        if( empty( $option ) ) continue;
        $retail_coupon = $option;
    }
    if( empty( $retail_coupon ) ) return;
    
    $store_data = get_option( 'retailer_'.$retail_coupon, false );
    if( empty( $store_data ) || isset( $store_data['user_id'] ) ) return;
    
    $data              = [];
    $email             = $order->get_billing_email() ?? '';
    $first_name        = $order->get_billing_first_name() ?? '';
    $last_name         = $order->get_billing_last_name() ?? '';
    $data['company']   = $order->get_shipping_company() ?? $order->get_billing_company() ?? '';
    $data['address_1'] = $order->get_shipping_address_1() ?? $order->get_billing_address_1() ?? '';
    $data['city']      = $order->get_shipping_city() ?? $order->get_billing_city() ?? '';
    $data['state']     = $order->get_shipping_state() ?? $order->get_billing_state() ?? '';
    $data['country']   = $order->get_shipping_country() ?? $order->get_billing_country() ?? '';
    $data['postcode']  = $order->get_shipping_postcode() ?? $order->get_billing_postcode() ?? '';
    
    $user = get_user_by( 'login', $retail_coupon );
    if( empty( $user ) ) {
        wp_create_user( $retail_coupon, $password = MC_Vars::generate( 15 ), $email );
        $user = get_user_by( 'email', $email );
        
        wp_mail( $email, 'Your new Alter Sleeves retail account',
                 "Hello $first_name,<br> <p>A retail account has been created for you on Alter Sleeves.<br>You can login using this email or your username which is $retail_coupon <a href='https://www.altersleeves.com/login'>here</a></p><p>Your affiliate link is https://www.altersleeves.com/$retail_coupon</p><p>Your password is $password</p><p>Please contact support@altersleeves.com if you have any issues</p>" );
    }
    $user->set_role( 'retailer' );
    $user->set_role( 'content_creator' );
    $user_id = $user->ID;
    update_user_meta( $user_id, 'first_name', $first_name );
    update_user_meta( $user_id, 'last_name', $last_name );
    update_user_meta( $user_id, 'store_address', $data );
    update_user_meta( $user_id, '_mc_affiliate_coupon', $retail_coupon );
    update_user_meta( $user_id, '_mc_affiliate_url', '/content-creator/'.$retail_coupon );
    
    $store_data['user_id'] = $user_id;
    update_option( 'retailer_'.$retail_coupon, $store_data );
    
    $promotion_data = [ 'userId' => $user_id, 'couponId' => $store_data['affiliate_code_id'], 'redirectLink' => $retail_coupon ];
    MC_Affiliate_Coupon::savePromotionData( $promotion_data, true );
}

add_filter( 'gform_confirmation_46', 'lgs_code', 10, 4 );
function lgs_code( $confirmation, $form, $entry, $ajax ) {
    
    $code = 'LGS-'.MC_Vars::generate(6);
    $coupon = new WC_Coupon();
    $coupon->set_code( $code  );
    $coupon->set_discount_type( 'percent' );
    $coupon->set_amount( 10 );
    $coupon->set_individual_use( 2 );
    $coupon->set_usage_limit( 2 );
    $coupon->set_free_shipping( 1 );
    $coupon->save();
    
    ob_clean();
    echo '<p>The coupon code is: <strong>'.$code.'</strong>';
    return ob_get_clean();
}


// Add a custom coupon field before checkout payment section
add_action( 'woocommerce_review_order_before_payment', 'woocommerce_checkout_coupon_form_custom' );
function woocommerce_checkout_coupon_form_custom() {
    echo '<div class="coupon-form" style="margin-bottom:20px;">
        <p>' . __("If you have a coupon code, please apply it below.") . '</p>
        <p class="form-row form-row-first woocommerce-validated">
            <input type="text" name="coupon_code" class="input-text" placeholder="' . __("Coupon code") . '" id="coupon_code" value="">
        </p>
        <p class="form-row form-row-last">
            <button type="button" class="button" name="apply_coupon" value="' . __("Apply coupon") . '">' . __("Apply coupon") . '</button>
        </p>
        <div class="clear"></div>
    </div>';
}

add_action( 'wp_footer', 'custom_checkout_jquery_script' );
function custom_checkout_jquery_script() {
    if ( is_checkout() && ! is_wc_endpoint_url() ) :
    ?>
    <script type="text/javascript">
    jQuery( function($){
  
        // Copy the inputed coupon code to WooCommerce hidden default coupon field
        $('.coupon-form input[name="coupon_code"]').on( 'input change', function(){
            $('form.checkout_coupon input[name="coupon_code"]').val($(this).val());
        });
        
        // On button click, submit WooCommerce hidden default coupon form
        $('.coupon-form button[name="apply_coupon"]').on( 'click', function(){
            $('form.checkout_coupon').submit();
        });
    });
    </script>
    <?php
    endif;
}