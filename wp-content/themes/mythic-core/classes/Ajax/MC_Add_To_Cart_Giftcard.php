<?php

namespace Mythic_Core\Ajax;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Giftcard_Functions;

/**
 * Class AddAlter
 *
 * @package Mythic_Core\Ajax\Store\Cart
 */
class MC_Add_To_Cart_Giftcard extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $product_id = $_REQUEST['product_id'] ?? 0;
        $quantity   = $_REQUEST['quantity'] ?? 1;
        MC_Giftcard_Functions::addToCart( $product_id, $quantity );
        $response = [
            'total' => WC()->cart->cart_contents_total,
            'count' => WC()->cart->get_cart_contents_count()
        ];
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-cart-add-gift-card';
    }
    
}