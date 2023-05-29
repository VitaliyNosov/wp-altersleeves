<?php

namespace Mythic_Core\Ajax\Production;

use MC_Woo_Order_Functions;
use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Production_Functions;
use Mythic_Core\Utils\MC_Vars;
use WC_Order_Query;

/**
 * Class MC_Order_Files_To_Print
 *
 * @package Mythic_Core\Ajax\Production
 */
class MC_Send_Orders extends MC_Ajax {
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'send-orders-print';
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $location = MC_Production_Functions::user();
        $location = strtolower( $location );
        
        if( $location != 'nl' ) {
            $query  = new WC_Order_Query( [
                                              'limit'        => 400,
                                              'status'       => 'processing',
                                              'order'        => 'ASC',
                                              'meta_key'     => '_shipping_country',
                                              'meta_compare' => '=',
                                              'meta_value'   => 'US',
                                          ] );
            $orders = $query->get_orders();
        } else {
            $query  = new WC_Order_Query( [
                                              'limit'        => -1,
                                              'status'       => 'processing',
                                              'order'        => 'ASC',
                                              'meta_key'     => '_shipping_country',
                                              'meta_compare' => '!=',
                                              'meta_value'   => 'US',
                                          ] );
            $orders = $query->get_orders();
        }
        
        foreach( $orders as $order ) {
            $order_id = $order->get_id();
            $items    = MC_Woo_Order_Functions::getOrderItemsFromId( $order_id );
            
            $path   = ABSPATH.'files/orders-'.MC_Production_Functions::user();
            $prefix = MC_Vars::generate( 3 );
            if( !is_dir( $path ) ) mkdir( $path, 0755, true );
            $file = "$path/$order_id-$prefix.txt";
            $file = fopen( $file, "w" );
            
            $output = "$order_id-$prefix\n";
            fwrite( $file, $output );
            $output = '';
            foreach( $items as $item ) {
                $idProduct = $item->get_product_id();
                $quantity  = $item->get_quantity();
                for( $x = 1; $x <= $quantity; $x++ ) {
                    $output .= "$idProduct\n";
                }
            }
            fwrite( $file, $output );
            fclose( $file );
        }
        
        $this->success( [
                            'success' => 1,
                        ] );
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'send-orders-print';
    }
    
}