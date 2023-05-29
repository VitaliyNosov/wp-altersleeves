<?php

namespace Mythic_Core\Shortcodes\User;

use Mythic_Core\Functions\MC_User_Functions;

class MC_Customer_Orders {
    
    const SHORTCODE = 'mc_my_orders';
    
    /**
     * MyOrders constructor.
     */
    public function __construct() {
        add_shortcode( self::SHORTCODE, [ $this, 'parse_shortcode' ] );
        add_shortcode( strtolower( self::SHORTCODE ), [ $this, 'parse_shortcode' ] );
    }
    
    /**
     * @return false|string
     */
    public function parse_shortcode() {
        $idUser = wp_get_current_user()->ID;
        if( MC_User_Functions::isAdmin() && isset( $_GET['user_id'] ) ) $idUser = $_GET['user_id'];
        $userOrders = wc_get_customer_order_count( $idUser );
        
        $args = [
            'customer_id' => $idUser,
            'status'      => [ 'completed', 'processing' ],
            'limit'       => 0,
        ];
        if( empty( $orders ) ) $orders = wc_get_orders( $args );
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/store/my-orders.php' );
        
        return ob_get_clean();
    }
    
}