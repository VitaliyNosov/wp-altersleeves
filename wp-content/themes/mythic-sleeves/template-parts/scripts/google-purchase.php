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
    $name      = MC_Alter_Functions::nameOrder( $idProduct, $itemKey );
    $type      = wp_get_object_terms( $idProduct, 'product_group' );
    $category  = is_array( $type ) && !empty( $type ) ? $type[0]->name : $product->get_type();
    $price     = $product->get_price();
    $quantity  = $orderItem->get_quantity();
    $idDesign  = MC_Alter_Functions::design( $idProduct );
    
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
        'list_name' => MC_Alter_Functions::type( $idProduct, 'name' ),
        'brand'     => MC_Alter_Functions::getAlteristDisplayName( $idProduct ),
        'category'  => $category,
        'price'     => (float) number_format( $price, 2 ),
        'quantity'  => $quantity,
        'variant'   => $idDesign,
    ];
endforeach;

$credits    = !empty( (int) $order->get_total() );
$orderTotal = $credits ? $order->get_total() : $orderTotal;
$orderTax   = $order->get_currency() == 'EUR' && !$credits ? 0.2 * $orderTotal : 0;
$coupons    = $order->get_coupon_codes();
$coupons    = is_array( $coupons ) ? implode( '|', $coupons ) : '';

?>
<script>
    window.dataLayer = window.dataLayer || [];
    dataLayer.push({
        'transactionId': <?= $order_id; ?>,
        'transactionTotal': <?= $orderTotal; ?>,
        'transactionTax': <?= $orderTax; ?>,
        'transactionProducts': <?= json_encode( $items ) ?>
    });

    gtag('event', 'purchase', {
        "coupon": "<?= $coupons ?>",
        "transaction_id": <?= (int) $order_id; ?>,
        "affiliation": "Alter Sleeves",
        "value": <?= (float) $orderTotal; ?>,
        "currency": "<?= $currency ?>",
        "tax": <?= $tax ?>,
        "shipping": <?= $shipping ?>,
        "items": <?= json_encode( $itemsEnhanced ) ?>
    });
</script>