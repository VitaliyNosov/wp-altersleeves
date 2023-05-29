<?php

namespace Mythic_Core\Ajax\Fulfillment;

use MC_Woo_Order_Functions;
use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Production_Functions;

/**
 * Class MC_Complete_Order
 *
 * @package Mythic_Core\Ajax\Fulfillment
 */
class MC_Complete_Order extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        if( !isset( $_POST['order_id'] ) ) die();
        $order_id = $_POST['order_id'];
        $order    = wc_get_order( $order_id );
        $user     = $order->get_user_id();
        if( !empty( $user ) && user_can( $user, 'administrator' ) ) $order->update_status( 'promotional' );
        $office = MC_Production_Functions::user();
        $code   = $office == 'US' ? 2 : 1;
        update_post_meta( $order_id, 'mc_order_printed', time() );
        $order->add_order_note( 'Order '.$order_id.' was marked <strong>Printed</strong> by the <strong>'.$office.'</strong> office' );
        MC_Woo_Order_Functions::complete( $order_id );
        
        $this->success( [
                            'success' => 1,
                        ] );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-order-complete';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'as-order-data';
    }
    
}