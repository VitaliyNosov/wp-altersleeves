<?php

namespace Mythic_Core\Ajax\Fulfillment;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Production_Functions;

/**
 * Class MC_Reship
 *
 * @package Mythic_Core\Ajax\Fulfillment
 */
class MC_Reship extends MC_Ajax {
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-order-reship';
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        if( !isset( $_POST['order_id'] ) ) die();
        $order_id = $_POST['order_id'];
        $order    = wc_get_order( $order_id );
        $office   = MC_Production_Functions::user();
        delete_post_meta( $order_id, 'mc_order_printed' );
        delete_post_meta( $order_id, 'mc_label_printed' );
        delete_post_meta( $order_id, 'mc_order_shipped' );
        $order->add_order_note( 'Order '.$order_id.' was uncompleted for reship by the <strong>'.$office.'</strong> office' );
        $order->update_status( 'processing' );
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