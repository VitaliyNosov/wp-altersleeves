<?php

namespace Mythic_Core\Ajax\Production;

use Mythic_Core\Abstracts\MC_Ajax;
use WP_Query;

/**
 * Class FilterOrders
 *
 * @package Mythic_Core\Ajax\Fulfillment
 */
class MC_Filter_Orders extends MC_Ajax {
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'cas-orders-filter';
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        if( !isset( $_POST['query'] ) ) die();
        $query = trim( $_POST['query'] );
        
        $args = [
            'numberposts' => -1, //to get all post, you can use Pagination
            'post_type'   => 'shop_order',
            'post_status' => array_keys( wc_get_order_statuses() ),
            'order'       => 'ASC',
            'fields'      => 'ids',
        ];
        
        if( is_numeric( $query ) ) {
            $args['p'] = $query;
        } else {
            $args['meta_query'] = [
                'relation' => 'OR',
                [
                    'key'     => '_billing_first_name',
                    'value'   => $query,
                    'compare' => 'LIKE',
                ],
                [
                    'key'     => '_billing_last_name',
                    'value'   => $query,
                    'compare' => 'LIKE',
                ],
                [
                    'key'     => '_billing_email',
                    'value'   => $query,
                    'compare' => 'LIKE',
                ],
            ];
        }
        $orders = new WP_Query( $args );
        
        if( !empty( $orders->posts ) ) {
            $output = '';
            foreach( $orders->posts as $order_id ) {
                $order = wc_get_order( $order_id ); //getting order Object
                foreach( $order->get_items() as $itemId => $itemData ) {
                    if( get_post_status( $itemData->get_product_id() ) != 'publish' ) {
                        break;
                    }
                }
                ob_start();
                include( DIR_THEME_TEMPLATE_PARTS.'/fulfillment/order.php' );
                $output .= ob_get_clean();
            }
            /* Restore original Post Data */
            wp_reset_postdata();
        } else {
            $output = '<p>Sorry no results found</p>';
        }
        
        $this->success( [
                            'html'    => $output,
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