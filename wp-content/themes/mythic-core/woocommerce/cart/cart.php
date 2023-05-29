<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
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

use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;
use Mythic_Core\Users\MC_Affiliates;

defined( 'ABSPATH' ) || exit;

$cart_items          = WC()->cart->get_cart();
$total_cart_quantity = 0;
foreach( $cart_items as $key => $cart_item ) {
    if( !has_term( 'alter', 'product_group', $cart_item['product_id'] ) ) continue;
    $total_cart_quantity += $cart_item['quantity'];
}


do_action( 'woocommerce_before_cart' ); ?>

<div class="row mb-4 align-items-start my-4">
    <form class="woocommerce-cart-form col-md-8" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

        <div class=" block">

            <h1>My Cart</h1>
            <?php do_action( 'woocommerce_before_cart_table' ); ?>

            <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                <thead class="sr-only">
                <tr>
                    <th class="product-remove">&nbsp;</th>
                    <th class="product-thumbnail">&nbsp;</th>
                    <th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                    <th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
                    <th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
                    <th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php do_action( 'woocommerce_before_cart_contents' ); ?>
                
                <?php
                
                $has_donation = false;
                foreach( $cart_items as $cart_item_key => $cart_item ) {
                    $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                    $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                    $product_slug = $_product->get_slug();
                    
                    if( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true,
                                                                                                         $cart_item, $cart_item_key ) ) {
                        $product_permalink = apply_filters( 'woocommerce_cart_item_permalink',
                                                            $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item,
                                                            $cart_item_key );
                        
                        $is_donation = strpos( strtolower( $_product->get_name() ), 'additional donation' ) !== false;
                        if( !empty( $is_donation ) ) {
                            $has_donation = true;
                        }
                        ?>

                        <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item',
                                                                                                        $cart_item, $cart_item_key ) ); ?>">

                            <td class="product-remove">
                                <?php
                                echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    'woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                        esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                        esc_html__( 'Remove this item', 'woocommerce' ),
                                        esc_attr( $product_id ),
                                        esc_attr( $_product->get_sku() )
                                    ),
                                    $cart_item_key
                                );
                                ?>
                            </td>

                            <td class="product-thumbnail">
                                <?php
                                if( isset( $cart_item['charity']['image'] ) && $cart_item['charity']['image'] ) {
                                    echo '<img class="card-display" src="'.$cart_item['charity']['image'].'">';
                                } else {
                                    $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item,
                                                                $cart_item_key );
                                    
                                    if( !$product_permalink ) {
                                        echo $thumbnail; // PHPCS: XSS ok.
                                    } else {
                                        printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
                                    }
                                }
                                ?>
                            </td>

                            <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
                                <?php
                                if( isset( $cart_item['charity']['name'] ) && $cart_item['charity']['name'] ) {
                                    if( isset( $cart_item['charity']['url'] ) && $cart_item['charity']['url'] ) {
                                        echo '<a href="'.$cart_item['charity']['url'].'" target="_blank">'.$cart_item['charity']['name'].'</a>';
                                    } else {
                                        echo $cart_item['charity']['name'];
                                    }
                                } else {
                                    if( !$product_permalink ) {
                                        echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item,
                                                                          $cart_item_key ).'&nbsp;' );
                                    } else {
                                        echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name',
                                                                          sprintf( '<a href="%s">%s</a>',
                                                                                   esc_url( $product_permalink ),
                                                                                   $_product->get_name() ),
                                                                          $cart_item, $cart_item_key ) );
                                    }
                                    
                                    do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
                                    
                                    // Meta data.
                                    echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.
                                }
                                ?>
                            </td>

                            <td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
                                <?php
                                if( !$is_donation ) {
                                    if( $_product->is_sold_individually() ) {
                                        $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                                    } else {
                                        $product_quantity = woocommerce_quantity_input(
                                            [
                                                'input_name'   => "cart[{
                        $cart_item_key
                        }][qty]",
                                                'input_value'  => $cart_item['quantity'],
                                                'max_value'    => $_product->get_max_purchase_quantity(),
                                                'min_value'    => '0',
                                                'product_name' => $_product->get_name(),
                                            ],
                                            $_product,
                                            false
                                        );
                                    }
                                    echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key,
                                                        $cart_item ); // PHPCS: XSS ok.
                                }
                                ?>
                            </td>

                            <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
                                <?php
                                if( !$is_donation ) {
                                    echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item,
                                                        $cart_item_key );
                                }
                                ?>
                            </td>

                            <td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
                                <?php
                                echo apply_filters( 'woocommerce_cart_item_subtotal',
                                                    WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item,
                                                    $cart_item_key ); // PHPCS: XSS ok.
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                
                if( empty( $is_donation ) ) {
                    $has_donation = true;
                    echo $charity_output;
                }
                ?>
                
                <?php do_action( 'woocommerce_cart_contents' ); ?>

                <tr class="d-none">
                    <td colspan="6" class="actions">

                        <button type="submit" class="button" name="update_cart"
                                value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart',
                                                                                                               'woocommerce' ); ?></button>
                        
                        <?php do_action( 'woocommerce_cart_actions' ); ?>
                        
                        <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                    </td>
                </tr>
                
                <?php do_action( 'woocommerce_after_cart_contents' ); ?>

                </tbody>
            </table>
            <?php do_action( 'woocommerce_after_cart_table' ); ?>
        </div>
    </form>

    <div class="col-md-4">
        <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
        
        <?php
        /**
         * Cart collaterals hook.
         *
         * @hooked woocommerce_cross_sell_display
         * @hooked woocommerce_cart_totals - 10
         */
        do_action( 'woocommerce_cart_collaterals' );
        ?>

    </div>
</div>
<?php do_action( 'woocommerce_after_cart' ); ?>

<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

<?php MC_Render::templatePart( 'campaign/mythic-frames', 'team-products' ); ?>


<?php do_action( 'woocommerce_after_cart' ); ?>
