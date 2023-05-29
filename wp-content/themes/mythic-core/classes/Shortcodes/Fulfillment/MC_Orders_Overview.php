<?php

namespace Mythic_Core\Shortcodes\Fulfillment;

use Exception;
use MC_Woo_Order_Functions;
use Mythic_Core\Ajax\Fulfillment\MC_Labels_Csv;
use Mythic_Core\Ajax\Production\MC_Filter_Orders;
use Mythic_Core\Ajax\Production\MC_Send_Orders;
use Mythic_Core\Functions\MC_Product_Functions;
use WC_Order_Query;

/**
 * Class MC_Orders_Overview
 *
 * @package Mythic_Core\Shortcodes\Fulfillment
 */
class MC_Orders_Overview {
    
    public const SHORT_ADMIN_ORDERS = 'mc_orders_overview';
    
    /**
     * MC_Orders_Overview constructor.
     */
    public function __construct() {
        add_shortcode( self::SHORT_ADMIN_ORDERS, [ $this, 'generate' ] );
        add_shortcode( strtoupper( self::SHORT_ADMIN_ORDERS ), [ $this, 'generate' ] );
    }
    
    /**
     * @param array $args
     *
     * @return false|string
     * @throws Exception
     */
    public function generate( $args = [] ) {
        $location = $_GET['loc'] ?? 'us';
        
        if( $location != 'nl' ) {
            $query  = new WC_Order_Query( [
                                              'limit'        => 200,
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
        $order_ids = [];
        $count = 0;
        foreach( $orders as $order ) {
            $order_id = $order->get_id();
            if( $order_id <= 209638 ) continue;
            $order_ids[] = $order_id;
            $count++;
            if( $count == 50 ) break;
        }
        $order_ids = implode('x', $order_ids);
        
        ob_start(); ?>
        <div class="row">
            <?php
            $orderTotals               = MC_Woo_Order_Functions::getOrderTotals();
            $orderProcessingQueueCount = $orderTotals['to_process'];
            ?>
            <div class="col text-center">Total Orders to process: <?= count( $orders ) ?></div>
            
            <?php
            $numberOfSleeves = 0;
            foreach( $orders as $order ) {
                $shipping_country = $order->get_shipping_country();
                $shipping_country = strtolower( $shipping_country );
                if( $location == 'us' && $shipping_country != 'us' ) continue;
                if( !empty( get_post_meta( $order->get_id(), 'mc_order_printed', true ) ) ) continue;
                foreach( $order->get_items() as $itemId => $itemData ) {
                    $idProduct = $itemData->get_product_id();
                    if( !MC_Product_Functions::isAlter( $idProduct ) ) continue;
                    
                    $idProducts[]    = $idProduct;
                    $quantity        = $itemData->get_quantity(); // Get the item quantity
                    $numberOfSleeves = $numberOfSleeves + $quantity;
                }
            }

            
            
            
            ?>
            <div class="col text-center">Number of Sleeves: <?= $numberOfSleeves ?></div>
            <div class="col text-center">
                <button class="cas-button cas-button--red" id="orders-label-csv">Download Labels CSV</button>
                <?php MC_Labels_Csv::render_nonce(); ?>
            </div>
            <div class="col text-center">
                <a href="<?= wp_nonce_url( admin_url( "admin-ajax.php?action=generate_wpo_wcpdf&document_type=invoice&order_ids=".$order_ids ) ) ?>" class="cas-button cas-button--green p-2" target="_blank">View Invoices</a>
                
            </div>
            
            <div class="col text-center">
                <button class="cas-button cas-button--blue" id="orders-send-all">Send All Orders</button>
                <?php MC_Send_Orders::render_nonce(); ?>
            </div>
            <iframe id="iframe-orders-labels" style="display:none;"></iframe>
        </div>
        <!-- Filters -->
        <div class="orders-filter">
            <form onsubmit="return false;">
                <!-- Order ID -->
                <div class="mb-3">
                    <label class="form-label" for="order_id">ID, First name, Last Name:</label>
                    <input type="text" class="form-control" id="orders_id" placeholder="113113 or John">
                </div>
                <!-- Max number of Orders -->
                <div class="mb-3">
                    <label class="form-label" for="order_id">Date </label>
                    <input type="text" class="form-control" id="orders_date" placeholder="2019-10-25">
                </div>
                <!-- Status -->
                <div class="mb-3">
                    <label class="form-label" for="orders_status">Status</label>
                    <select class="form-control" id="orders_status">
                        <option value="processing" selected>To Process</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="orders-filter-button btn btn-primary">Submit</button>
                </div>
                <?php MC_Filter_Orders::render_nonce(); ?>
            </form>
        </div>
        <br><br>
        <?php
        
        $output = '';
        
        $ordersIdTop = [];
        foreach( $orders as $order ) {
            $order_id   = $order->get_id();
            $idUser     = $order->get_billing_email();
            $userExists = false;
            if( !empty( $ordersIdTop ) ) {
                foreach( $ordersIdTop as $key => $orderSorted ) {
                    $idUserCheck = $orderSorted[0]->get_billing_email();
                    if( strtolower( $idUser ) == strtolower( $idUserCheck ) ) {
                        $ordersIdTop[ $key ][] = $order;
                        $userExists            = true;
                    }
                }
            }
            if( $userExists ) continue;
            $ordersIdTop[ $order_id ] = [ $order ];
        }
        
        foreach( $ordersIdTop as $sortedOrder ) {
            foreach( $sortedOrder as $order ) {
                $order_id = $order->get_id();
                if( !empty( get_post_meta( $order_id, 'mc_order_printed', true ) ) ) continue;
                $shipping_country = $order->get_shipping_country();
                $shipping_country = strtolower( $shipping_country );
                if( $location == 'us' && $shipping_country != 'us' ) continue;
                ob_start();
                include( DIR_THEME_TEMPLATE_PARTS.'/fulfillment/order.php' );
                $output .= ob_get_clean();
            }
        }
        $output = '<div class="orders-results mt-3">'.$output.'</div>';
        ?>
        <div class="order2process__heading">
            <div class="row align-items-center">
                <div class="order2process__ID col-sm-1"><strong>ID</strong></div>
                <div class="order2process__name col-sm-2">Name</div>
                <div class="order2process__count col-sm-1">Sleeves</div>
            </div>
        </div>
        <?= $output; ?><?php
        return ob_get_clean();
    }
    
}
