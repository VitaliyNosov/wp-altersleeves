<div class="row border-bottom mb-3 align-items-center">
    <div class="col">
        <?= $order->get_billing_first_name().' '.$order->get_billing_last_name(); ?>
    </div>
    <div class="col">
        <?= $order->get_currency().' '.$order->get_total(); ?>
    </div>
    <div class="col-3">
        <?= $order->get_date_created()->format( 'Y-m-d' ); ?>
    </div>
    <div class="col-auto">
        <?php $urlNonce = wp_nonce_url( "admin-ajax.php?action=generate_wpo_wcpdf&document_type=invoice&order_ids=".$order_id,
                                        'generate_wpo_wcpdf' ); ?>
        <a href="<?= esc_attr( $pdf_url ) ?>">
            <button class="orange--button">View Receipt</button>
        </a>
    </div>
    <hr>
</div>