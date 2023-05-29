<?php
/**
 * Customer completed order email
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if( !defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php
/* translators: %s: Customer first name */ ?>
    <p><?php
        printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<?php
/* translators: %s: Site title */ ?>
    <p>We have great news: your order is on its way!</p>

    <p>
        <stsrong>When will I receive my order?</stsrong>
    </p>

<?php
if( $order->get_shipping_country() == 'US' ) : ?>
    <p>We are shipping your order from Madison, WI so orders often take up to 5 business days, <strong>but may take longer due to the pandemic and
            similar disruption.</p>

    <p>If your order is tracked you will receive an email shortly from the USPS.</p>

<?php
else : ?>
    <p>We are shipping your order from The Netherlands (Europe) so orders often take 5-10 business days, <strong>but may be up to 7 - 8 weeks for
            global delivery during the panedemic.</p>

    <p>If your order is tracked you will receive an email shortly from Deutsche Post with your details.</p>
<?php
endif; ?>

    <p>Please feel free to contact <?= EMAIL_SUPPORT ?> if you have any further queries regarding your order; we do not accept responsibility for
        undelivered untracked packages.</p>

    <h4>If you have ordered a gift card, the digital code has been attached to this email: this applies for both physical and digital.</h4>

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additonal content - this is set in each email's settings.
 */
if( $additional_content ) {
    echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
