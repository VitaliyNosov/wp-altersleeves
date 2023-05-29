<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
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

if( !defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );



?>

<style>
    #order_review {
        background:#fff;
        border-radius:5px;
    }
</style>

<?php if( !empty($balance = \Mythic_Core\Functions\MC_Creator_Functions::getAffiliateBalance()) ) :?>
        <p>Your account has a balance of $<?= $balance ?>. <a href="/coupon/accountcredit/">Click here to use it during this order.</a> </p>
<?php endif;  ?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>"
      enctype="multipart/form-data">

    <div class="row align-items-start">

        <div class="col-sm-6 my-2">

            <?php if( $checkout->get_checkout_fields() ) : ?>

                <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

                <div id="customer_details">
                    <div class="my-2">
                        <?php do_action( 'woocommerce_checkout_billing' ); ?>
                    </div>

                    <div class="my-2">
                        <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                    </div>
                </div>

                <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

            <?php endif; ?>

        </div>

        <div class="col-sm-6 my-2">
            <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

            <h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>

            <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

            <div id="order_review" class="woocommerce-checkout-review-order">
                <?php do_action( 'woocommerce_checkout_order_review' ); ?>
            </div>

            <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

        </div>

    </div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
