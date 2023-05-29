<?php

use Mythic_Core\Functions\MC_Affiliate_Functions;

$orders = MC_Affiliate_Functions::getOrdersWithAffiliateCoupon( $args['user_id'] );
if( empty( $orders ) ) {
    echo "<h2>No one order found</h2>";
    return;
}
$orders_data     = [];
$pagination_data = [];
$header_data     = [ 'Order ID', 'Status' ];
$orders_count    = count( $orders );
$per_page        = 10;

foreach( $orders as $order_id ) {
    $order_object  = wc_get_order( $order_id );
    $orders_data[] = [
        'order_id' => $order_object->get_id(),
        'status'   => $order_object->get_status(),
    ];
}
if( $orders_count > $per_page ) {
    $pagination_data = [ $orders_count, $per_page, 'center', 1 ];
}
MC_Table_Flex::renderTableFlex( $orders_data, $header_data, 'mc-retailer-orders', $pagination_data );