<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

<h1 class="sr-only"><?php esc_html_e( 'Login', 'woocommerce' ); ?></h1>

<div class="p-4">

    <?php do_action( 'woocommerce_login_form_start' ); ?>

    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
        <button class="nav-link <?= empty($_GET['action']) ? 'active' : ''; ?>" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab"
                aria-controls="pills-login" aria-selected="true">Log In</button>
        </li>
        <li class="nav-item" role="presentation">
        <button class="nav-link <?= !empty($_GET['action']) ? 'active' : ''; ?>" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register" type="button" role="tab"
                aria-controls="pills-register" aria-selected="false">Register</button>
        </li>
    </ul>


    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show <?= empty($_GET['action']) ? 'active show' : ''; ?>" id="pills-login" role="tabpanel" aria-labelledby="pills-login-tab">

            <form class="woocommerce-form-login login" method="post">

            <div class="form-field form-floating">
                    <input type="text" class="form-control" id="username" name="username" placeholder="name@example.com" autocomplete="off" required>
                    <label for="username">Email or Username <span class="required">*</span></label>
                </div>
                <div class="form-field form-floating">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" autocomplete="off" required>
                    <label for="password">Password <span class="required">*</span></label>
                </div>

                        <?php do_action( 'woocommerce_login_form' ); ?>

                <div class="form-field form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="rememberme" name="rememberme" checked>
                    <label class="form-check-label" for="rememberme">Remember Me</label>
                </div>

                <div class="w-100">
                    <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                    <button type="submit" class="button" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
                </div>

                <p class="woocommerce-LostPassword lost_password">
                    <a href="/recover-password"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
                </p>

                <?php do_action( 'woocommerce_login_form_end' ); ?>


                </form>

        </div>

        <div class="tab-pane fade <?= !empty($_GET['action']) ? 'active show' : ''; ?>" id="pills-register" role="tabpanel" aria-labelledby="pills-register-tab">


            <?php echo do_shortcode('[wppb-register form_name="register"]' ); ?>


        </div>
    </div>

</div>


<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
