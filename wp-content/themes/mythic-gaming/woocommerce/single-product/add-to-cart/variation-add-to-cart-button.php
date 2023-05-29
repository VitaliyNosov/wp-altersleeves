<?php
/**
 * Single variation cart button
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

$has_credits = \Mythic_Core\Functions\MC_Mythic_Frames_Functions::hasRemainingCredits();


?>

<h3>Pre-Orders are now closed</h3>
<p>Mythic Gaming products, such as Mythic Frames, will be available again soon</p>

<?php
return;

global $product;
?>



<?php if( !empty( $has_credits ) ) : ?>
    <div class="text-center container">
        <h2 class="text-danger">Pre-order unavailable</h2>
        <p class="fw-bold"><a href="/campaign" title="Allocate pre-existing credits">You must allocate pre-existing credits before you can
                pre-order</a></p>
    </div>
<?php else : ?>
    <div class="woocommerce-variation-add-to-cart variations_button">
        <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

        <?php
        do_action( 'woocommerce_before_add_to_cart_quantity' );

        woocommerce_quantity_input(
            [
                'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.,
                'classes'     => 'form-control'
            ]
        );

        do_action( 'woocommerce_after_add_to_cart_quantity' );
        ?>

        <button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

        <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

        <input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
        <input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
        <input type="hidden" name="variation_id" class="variation_id" value="0" />
    </div>
<?php endif; ?>
