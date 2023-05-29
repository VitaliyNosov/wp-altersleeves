<?php
/**
 * Review order table
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
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

use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;
use Mythic_Core\Users\MC_Affiliates;

defined( 'ABSPATH' ) || exit;

?>

<table class="shop_table woocommerce-checkout-review-order-table">
    <thead>
    <tr>
        <th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
        <th class="product-total"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
    </tr>
    <tr>
        <td colspan="2"><small class="text-danger"><em>Alter Sleeves are <strong>not</strong> proxies and do not come with the cards shown on the
                    product pages; <a href="https://www.altersleeves.com/introducing-alter-sleeves" target="_blank"
                                      title="See Alter Sleeves in action">they are alters printed on perfect fit inner sleeves</a> designed to be put
                    on cards <strong>you already own</strong></em></small></td>
    </tr>
    </thead>
    <tbody>
    <?php
    do_action( 'woocommerce_review_order_before_cart_contents' );
    
    foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
        
        if( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true,
                                                                                             $cart_item, $cart_item_key ) ) {
            ?>
            <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                <td class="product-name">
                    
                    <?= apply_filters( 'woocommerce_checkout_cart_item_quantity',
                                       ' <strong class="product-quantity">'.sprintf( ' %s &times;', $cart_item['quantity'] ).'</strong>', $cart_item,
                                       $cart_item_key ); // phpcs:ignore Wordpress.Security.EscapeOutput.OutputNotEscaped            ?>
                    
                    <?php
                    if( MC_Product_Functions::isAlter( $_product->get_id() ) ) {
                        echo MC_Alter_Functions::nameOrderReview( $_product->get_id(), $cart_item_key );
                    } else {
                        if( isset( $cart_item['charity']['name'] ) && !empty( $cart_item['charity']['name'] ) && isset( $cart_item['charity']['url'] ) && !empty( $cart_item['charity']['url'] ) ) {
                            echo '<a href="'.$cart_item['charity']['link'].'">'.$cart_item['charity']['name'].'</a>';
                        } else {
                            echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item,
                                                $cart_item_key ).'&nbsp;'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }
                    }
                    
                    ?>

                </td>
                <td class="product-total">
                    <?php
                    
                    if( MC_Backer_Functions::remainingSingleCredits() > 0 ) {
                        echo 'Credit(s)';
                    } else {
                        echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ),
                                            $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    }
                    
                    ?>
                </td>
            </tr>
            <?php
        }
    }
    
    do_action( 'woocommerce_review_order_after_cart_contents' );
    ?>
    </tbody>
    <tfoot>

    <?php foreach( WC()->cart->get_coupons() as $code => $coupon ) : ?>
        <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
            <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
            <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
        </tr>
    <?php endforeach; ?>
    
    <?php if( WC()->cart->needs_shipping() && WC()->cart->show_shipping() && empty( MC_Backer_Functions::remainingSingleCredits() ) ) : ?>
        
        <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
        
        <?php wc_cart_totals_shipping_html(); ?>
        
        <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
    
    <?php endif; ?>
    
    <?php foreach( WC()->cart->get_fees() as $fee ) : ?>
        <tr class="fee">
            <th><?php echo esc_html( $fee->name ); ?></th>
            <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
        </tr>
    <?php endforeach; ?>
    
    <?php if( wc_tax_enabled() && !WC()->cart->display_prices_including_tax() ) : ?><?php if( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?><?php
        foreach( WC()->cart->get_tax_totals() as $code => $tax ) :
            // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
            ?>
            <tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                <th><?php echo esc_html( $tax->label ); ?></th>
                <td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
            </tr>
        <?php endforeach; ?><?php else : ?>
        <tr class="tax-total">
            <th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
            <td><?php wc_cart_totals_taxes_total_html(); ?></td>
        </tr>
    <?php endif; ?><?php endif; ?>
    
    <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>
    
    <?php if( empty( MC_Backer_Functions::remainingSingleCredits() ) ) : ?>
        <tr class="order-total">
            <th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
            <td>
                <?php
                wc_cart_totals_order_total_html();
                ?>
            </td>
        </tr>
    <?php endif; ?>
    <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

    </tfoot>
</table>
