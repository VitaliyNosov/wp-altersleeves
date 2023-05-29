<?php

if( empty( $orders ) ) return;
?>
<div class="dashboard-user-orders mb-3">
    <?php
    
    foreach( $orders as $order ) {
        $order_id = $order->get_id();
        
        $pdf_url = wp_nonce_url( admin_url( 'admin-ajax.php?action=generate_wpo_wcpdf&template_type=invoice&order_ids='.$order_id.'&my-account' ),
                                 'generate_wpo_wcpdf' );
        
        include 'order.php';
    }
    ?>
</div>