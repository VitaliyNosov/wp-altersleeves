<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Product_Functions;

$url = $_SERVER['REQUEST_URI'];
if( $url !== '/checkout' ) return;

$items   = WC()->cart->get_cart();
$coupons = WC()->cart->get_applied_coupons();
$coupons = is_array( $coupons ) ? implode( '|', $coupons ) : '';
if( empty( $items ) ) return;

ob_start();
$last  = count( $items );
$count = 1;
foreach( $items as $itemKey => $item ) {
    $idProduct = $item['product_id'];
    $alter     = MC_Product_Functions::isAlter( $idProduct );
    if( !$alter ) continue;
    $nameProduct = MC_Alter_Functions::nameCart( $idProduct, $itemKey );
    $idDesign    = MC_Alter_Functions::design( $idProduct );
    $price       = wc_get_product( $idProduct );
    $price       = $price->get_price();
    ?>
    {"id": <?= $idProduct ?>,"name": "<?= $nameProduct ?>","list_name": "<?= MC_Alter_Functions::type( $idProduct,
                                                                                                       'name' ) ?>","brand": "<?= MC_Alter_Functions::getAlteristDisplayName( $idProduct ) ?>","category": "Alter","variant": <?= (int) $idDesign ?>,"quantity": <?= (int) $item['quantity'] ?>,"price": <?= (float) $price ?>
    }
    <?php
    if( $count != $last ) echo ',';
    $count++;
}
$items = ob_get_clean();

?>
<script type="text/javascript">
    $(function() {
        let coupons = "<?= $coupons ?>";

        gtag('event', 'begin_checkout', {
            "items": [
                <?= $items ?>
            ],
            "coupon": coupons
        });

        $("body").on('applied_coupon_in_checkout', function( event, coupon ) {
            coupons = coupons + '|' + coupon;
            gtag('event', 'checkout_progress', {
                "items": [
                    <?= $items ?>
                ],
                "coupon": coupons
            });
        });
        $("body").on('removed_coupon_in_checkout', function( event, coupon ) {
            coupons = coupons.replace('|' + coupon, "");
            coupons = coupons.replace(coupon, "");
            coupons = coupons.replace(coupon + '|', "");
            gtag('event', 'checkout_progress', {
                "items": [
                    <?= $items ?>
                ],
                "coupon": coupons
            });
        });
    });
</script>