<?php

if( MC_Url::contains( 'recover-password' ) || !isset( $_GET['key'] ) ) return;

$order_id   = wc_get_order_id_by_order_key( $_GET['key'] );
$order      = wc_get_order( $order_id );
$orderItems = $order->get_items();
if( empty( $orderItems ) ) return;

$orderTotal = $order->get_total() ?? 0;
$currency   = $order->get_currency();
$shipping   = !empty( $order->get_shipping_total() ) ? $order->get_shipping_total() : 0;
$tax        = $currency == 'EUR' ? $orderTotal * 0.2 : 0;

$items         = [];
$itemsEnhanced = [];
foreach( $orderItems as $itemKey => $orderItem ) :
    $idProduct = $orderItem->get_product_id();
    $product   = wc_get_product( $idProduct );
    $name      = get_the_title( $idProduct );
    $category  = has_term( 'Playmat', 'product_category', $idProduct ) ? 'Playmat' : 'Mythic Frames';
    $price     = $product->get_price();
    $quantity  = $orderItem->get_quantity();

    $items[] = [
        'sku'      => $idProduct,
        'name'     => $name,
        'category' => $category,
        'price'    => number_format( $price, 2 ),
        'quantity' => $quantity,
    ];

    $itemsEnhanced[] = [
        'id'        => $idProduct,
        'name'      => $name,
        'brand'     => 'Mythic Gaming',
        'category'  => $category,
        'price'     => (float) number_format( $price, 2 ),
        'quantity'  => $quantity,
        'variant'   => $idProduct,
    ];
endforeach;

$coupons = $order->get_coupon_codes();
$coupons = is_array( $coupons ) ? implode( '|', $coupons ) : '';

?>
<script>
    window.dataLayer = window.dataLayer || [];
    dataLayer.push({
        'transactionId': <?= $order_id; ?>,
        'transactionTotal': <?= $orderTotal; ?>,
        'transactionTax': 0,
        'transactionProducts': <?= json_encode( $items ) ?>
    });

    gtag('event', 'purchase', {
        "coupon": "<?= $coupons ?>",
        "transaction_id": <?= $order_id; ?>,
        "affiliation": "Mythic Gaming",
        "value": <?= (float) $orderTotal; ?>,
        "currency": "<?= $currency ?>",
        "tax": <?= $tax ?>,
        "shipping": <?= $shipping ?>,
        "items": <?= json_encode( $itemsEnhanced ) ?>
    });
</script>