<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

if( empty( $user = wp_get_current_user() ) ) return;

do_action( 'woocommerce_before_edit_account_form' ); ?>

<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

    <?php do_action( 'woocommerce_edit_account_form_start' ); ?>

    <h1>Account Details</h1>

    <fieldset>

        <legend><?php esc_html_e( 'Personal Details', 'woocommerce' ); ?></legend>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-field form-floating">
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text form-control" id="account_first_name"
                           name="account_first_name" autocomplete="off" required value="<?php echo esc_attr( $user->first_name ); ?>">
                    <label for="account_first_name">First Name</label>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-field form-floating">
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text form-control" id="account_last_name"
                           name="account_last_name"
                           autocomplete="off" required value="<?php echo esc_attr( $user->last_name ); ?>">
                    <label for="account_last_name">Last Name</label>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-field form-floating">
                    <input type="email" class="woocommerce-Input woocommerce-Input--text input-text form-control" id="account_email"
                           name="account_email" autocomplete="off" required value="<?php echo esc_attr( $user->user_email ); ?>">
                    <label for="account_email">Email Address</label>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-field form-floating">
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text form-control" id="account_display_name"
                           name="account_display_name" autocomplete="off" required value="<?php echo esc_attr( $user->display_name ); ?>">
                    <label for="account_display_name">Display Name</label>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset>

        <legend><?php esc_html_e( 'Change Password', 'woocommerce' ); ?></legend>
        <div class="row">
            <div class="col-sm-6  me-auto">
                <div class="form-field form-floating">
                    <input type="password" class="form-control" id="password_current"
                           name="password_current" autocomplete="off">
                    <label for="password_current">Current Password</label>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-sm-6">
                <div class="form-field form-floating">
                    <input type="password" class="input-text form-control" id="password_1"
                           name="password_1" autocomplete="off">
                    <label for="password_1">New password </label>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-field form-floating">
                    <input type="password" class="input-text form-control" id="password_2"
                           name="password_2" autocomplete="off">
                    <label for="password_2">Confirm new password</label>
                </div>
            </div>

        </div>
    </fieldset>
    <div class="clear"></div>

    <?php do_action( 'woocommerce_edit_account_form' ); ?>

    <p>
        <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
        <button type="submit" class="woocommerce-Button button" name="save_account_details"
                value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
        <input type="hidden" name="action" value="save_account_details" />
    </p>

    <?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
