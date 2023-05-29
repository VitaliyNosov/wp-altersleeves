<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.9.0
 */


if ( empty($notices) || !is_array($notices) ) {
	return;
}

ob_start();
foreach ( $notices as $notice ) :
    if( $notice['notice'] == 'Coupon "" does not exist!' ) continue;
    if( $notice['notice'] == 'Coupon code already applied!' ) continue;
    if( strpos($notice['notice'], 'Donation' ) !== false ) continue;
    if( strpos($notice['notice'], ' does not exist' ) !== false ) continue;
    ?>
    <li<?php echo wc_get_notice_data_attr( $notice ); ?>>
			<?php echo wc_kses_notice( $notice['notice'] ); ?>
		</li>
<?php endforeach;
$output = ob_get_clean();
if( empty($output) ) return;
?>

<ul class="woocommerce-error" role="alert">
    <?php echo $output ?>
</ul>
