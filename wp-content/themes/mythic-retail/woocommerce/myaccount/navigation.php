<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

if( !defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="w-100">
    <div class="container">
        <div class="row justify-content-between align-items-center">
            <ul class="col-sm">
                <li>
                    <a href="/dashboard">Dashboard</a>
                </li>
                <?php foreach( wc_get_account_menu_items() as $endpoint => $label ) : ?>
                    <li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
                                <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
                            </li>
                <?php endforeach; ?>
            </ul>
            <?php if( !empty( MC_WP::meta( 'business_details', MC_User_Functions::id(), 'user' ) ) ) : ?>
                <div class="col-auto">
                <a href="/cart">
                    <i class="fas fa-shopping-cart" style="font-size:24px;color:#fc6a29;"></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
