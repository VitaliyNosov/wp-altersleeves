<?php

namespace Mythic_Core\Ajax\Fulfillment;

use Mythic_Core\Abstracts\MC_Ajax;

/**
 * Class MC_Invoice_Print
 *
 * @package Mythic_Core\Ajax\Fulfillment
 */
class MC_Invoice_Print extends MC_Ajax {
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-order-print-invoice';
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $order_id = $_POST['order_id'];
        
        if( !empty( $_POST['gift'] ) ) {
            update_post_meta( $order_id, '_invoice_drop_ship', 1 );
        } else {
            delete_post_meta( $order_id, '_invoice_drop_ship' );
        }
        
        global $woocommerce_ext_printorders;
        $order = wc_get_order( $order_id );
        add_action( 'woocommerce_order_action_google-cloud-print-84e8f730-9e36-0e62-d53f-a1d730307bdf___pdf_invoices_invoice', [
            $woocommerce_ext_printorders,
            'woocommerce_order_action_google_cloud_print',
        ],          10, 1 );
        do_action( 'woocommerce_order_action_google-cloud-print-84e8f730-9e36-0e62-d53f-a1d730307bdf___pdf_invoices_invoice', $order );
        
        $this->success( [
                            'success' => 1,
                        ] );
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'as-order-data';
    }
    
}