<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$allowed_html = [
    'a' => [
        'href' => [],
    ],
];

if( get_the_ID() == 1286 ) {
    MC_WP_Post_Functions::defaultLoop();
} else {
    ?>
    <h1>Dashboard</h1>
    <p>
        <?php
        
        /* translators: 1: Orders URL 2: Addresses URL 3: Account URL. */
        $dashboard_desc = __( 'Welcome to your Mythic Gaming account. From this dashboard you can manage your <a href="%1$s">shipping and billing addresses</a>, manage your <a href="%2$s">edit your password and account details</a>.',
                              'woocommerce' );
        printf(
            wp_kses( $dashboard_desc, $allowed_html ),
            esc_url( wc_get_endpoint_url( 'edit-address' ) ),
            esc_url( wc_get_endpoint_url( 'edit-account' ) )
        );
        ?>
    </p>
    <?php
}
?>

<p><strong>Please note: </strong>Alter Sleeves and Mythic Gaming accounts are the same; changing details on this site, will also change them on Alter
    Sleeves.</p>

<?php

if( MC_Mythic_Frames_Functions::getBackerId() || MC_User_Functions::isAdmin() ) {
    MG_Render::campaign( 'mythic-frames', 'notice' );
    MG_Render::campaign( 'mythic-frames', 'credits' );
}

/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_account_dashboard' );

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_before_my_account' );

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */

?>

