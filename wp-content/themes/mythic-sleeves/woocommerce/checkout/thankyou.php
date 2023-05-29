<?php
/**
 * Thankyou page
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

if( empty( $order ) ) return;

if( is_user_logged_in() ) {
    $idUser      = wp_get_current_user()->ID;
    $backerEmail = wp_get_current_user()->user_email;
    $idBacker    = get_page_by_title( $backerEmail, OBJECT, 'backer' );
    if( $idBacker != null ) {
        $idBacker = $idBacker->ID;
        update_post_meta( $idBacker, 'mc_sets_redeemed', 1 );
        update_user_meta( $idUser, 'mc_sets_redeemed', 1 );
    }
}

if( isset( $_COOKIE['content_creator'] ) ) {
    $content_creators = $_COOKIE['content_creator'];
    if( !MC_User_Functions::isAdmin() && isset( $order ) ) {
        if( is_array( $content_creators ) && is_object( $order ) ) {
            $content_creators = array_values( $content_creators );
            $content_creators = array_unique( $content_creators );
            ?>
            <script>
                <?php foreach( $content_creators as $content_creator ) :
                $content_creator = get_user_by( 'ID', $content_creator );
                if( empty( $content_creator ) ) continue;
                $content_creator_name = $content_creator->user_nicename;
                ?>
                gtag('event', 'purchase', {
                    'event_label': <?= $content_creator_name ?>,
                    'event_category': 'content_creator',
                    'value': <?= $order->get_total() ?>,
                    'non_interaction': true
                });
                <?php endforeach; ?>
            </script>
            <?php
        }
    }
    unset( $_COOKIE['content_creator'] );
    setcookie( 'content_creator', null, -1, '/' );
}
$facebook_conversion = get_post_meta( $order->get_id(), 'mc_facebook_conversion', true );
if( empty( $facebook_conversion ) ) do_action( 'mc_facebook_trigger_conversion_tracking', $order->get_id() );
do_action( 'mc_retailer_activation', $order );
?>

<div class="woocommerce-order">
    
    <?php
    if( $order ) :
        
        do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>
        
        <?php
        if( $order->has_status( 'failed' ) ) : ?>

            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php
                esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.',
                            'woocommerce' ); ?></p>

            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
                <a href="<?= esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php
                    esc_html_e( 'Pay', 'woocommerce' ); ?></a>
                <?php
                if( is_user_logged_in() ) : ?>
                    <a href="<?= esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php
                        esc_html_e( 'My account', 'woocommerce' ); ?></a>
                <?php
                endif; ?>
            </p>
        
        <?php
        else :
            
            ?>

            <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?= apply_filters( 'woocommerce_thankyou_order_received_text',
                                                                                                                             esc_html__( 'Thank you. Your order has been received.',
                                                                                                                                         'woocommerce' ),
                                                                                                                             $order ); // phpcs:ignore Wordpress.Security.EscapeOutput.OutputNotEscaped
                
                $idUser = wp_get_current_user()->ID;
                update_user_meta( $idUser, 'preview_order', 0 ); ?></p>

            <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

                <li class="woocommerce-order-overview__order order">
                    <?php
                    esc_html_e( 'Order number:', 'woocommerce' ); ?>
                    <strong><?= $order->get_order_number(); // phpcs:ignore Wordpress.Security.EscapeOutput.OutputNotEscaped        ?></strong>
                </li>

                <li class="woocommerce-order-overview__date date">
                    <?php
                    esc_html_e( 'Date:', 'woocommerce' ); ?>
                    <strong><?= wc_format_datetime( $order->get_date_created() ); // phpcs:ignore Wordpress.Security.EscapeOutput.OutputNotEscaped        ?></strong>
                </li>
                
                <?php
                if( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
                    <li class="woocommerce-order-overview__email email">
                        <?php
                        esc_html_e( 'Email:', 'woocommerce' ); ?>
                        <strong><?= $order->get_billing_email(); // phpcs:ignore Wordpress.Security.EscapeOutput.OutputNotEscaped        ?></strong>
                    </li>
                <?php
                endif; ?>

                <li class="woocommerce-order-overview__total total">
                    <?php
                    esc_html_e( 'Total:', 'woocommerce' ); ?>
                    <strong><?= $order->get_formatted_order_total(); // phpcs:ignore Wordpress.Security.EscapeOutput.OutputNotEscaped        ?></strong>
                </li>
                
                <?php
                if( $order->get_payment_method_title() ) : ?>
                    <li class="woocommerce-order-overview__payment-method method">
                        <?php
                        esc_html_e( 'Payment method:', 'woocommerce' ); ?>
                        <strong><?= wp_kses_post( $order->get_payment_method_title() ); ?></strong>
                    </li>
                <?php
                endif; ?>

            </ul>
        
        <?php
        endif; ?>
        
        <?php
        do_action( 'woocommerce_thankyou_'.$order->get_payment_method(), $order->get_id() ); ?><?php
        do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
    
    <?php
    else : ?>

        <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?= apply_filters( 'woocommerce_thankyou_order_received_text',
                                                                                                                         esc_html__( 'Thank you. Your order has been received.',
                                                                                                                                     'woocommerce' ),
                                                                                                                         null ); // phpcs:ignore Wordpress.Security.EscapeOutput.OutputNotEscaped        ?></p>
    
    <?php
    endif; ?>

</div>