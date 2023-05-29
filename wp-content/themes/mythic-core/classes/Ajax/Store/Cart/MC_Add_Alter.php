<?php

namespace Mythic_Core\Ajax\Store\Cart;

use MC_Product_Functions;
use MC_User_Functions;
use MC_WP;
use Mythic_Core\Abstracts\MC_Ajax;

/**
 * Class MC_Add_Alter
 *
 * @package Mythic_Core\Ajax\Store\Cart
 */
class MC_Add_Alter extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response = [];
        
        if( !isset( $_POST['alter_id'] ) ) die();
        $idPrinting = !empty( $_POST['printing_id'] ) ? $_POST['printing_id'] : 0;
        if( !MC_WP::exists( $idPrinting ) ) $idPrinting = 0;
        
        $idProduct = $_POST['alter_id'];
        $user_id   = MC_User_Functions::id();
        if( in_array( $idProduct, MC_Product_Functions::not_for_sale() ) ) die();
        if( get_post_status( $idProduct ) != 'publish' && get_post_status( $idProduct ) != 'internal_approved' && get_post_status( $idProduct ) != 'internal_verify' && !MC_User_Functions::isAdmin() && $user_id != MC_WP::authorId( $idProduct ) ) {
            die();
        }
        
        $quantity = isset( $_POST['quantity'] ) ? $_POST['quantity'] : 1;
        
        $itemKey = WC()->cart->add_to_cart( $idProduct, $quantity );
        
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-cart-add-alter';
    }
    
}