<?php
/**
 * Checkout login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

if( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
    return;
}

?>
<div class="woocommerce-form-login-toggle block mt-3">
    <?php wc_print_notice( apply_filters( 'woocommerce_checkout_login_message',
                                          'Returning <a href="https://www.altersleeves.com" target="_blank">Alter Sleeves</a> or Mythic Gaming customer?',
                                          'woocommerce' ).' <a href="#" class="showlogin">'.esc_html__( 'Click here to login',
                                                                                                        'woocommerce' ).'</a>',
                           'notice' ); ?>

    <form class="woocommerce-form woocommerce-form-login login" method="post" style="display:none;">

        <div class="row">
            <div class="col-sm-6">
                <label for="username">Username or email&nbsp;<span class="required">*</span></label>
                <input type="text" class="input-text" name="username" id="username" autocomplete="username">
            </div>
            <div class="col-sm-6">
                <label for="password">Password&nbsp;<span class="required">*</span></label>
                <input class="input-text" type="password" name="password" id="password" autocomplete="current-password">
            </div>
        </div>

        <p class="form-row">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme d-none">
                <input class="woocommerce-form__input woocommerce-form__input-checkbox  d-none" name="rememberme" type="checkbox" id="rememberme"
                       value="forever" checked> <span>Remember me</span>
            </label>
            <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>

            <input type="hidden"
                   name="_wp_http_referer"
                   value="<?= get_site_url() ?>/checkout/"> <input
                    type="hidden" name="redirect" value="<?= get_site_url() ?>/checkout/">
            <button type="submit" class="woocommerce-button button woocommerce-form-login__submit m-0" name="login" value="Login">Login</button>
        </p>

        <a href="<?= get_site_url() ?>dashboard/lost-password/">Lost your password?</a>

    </form>

</div>