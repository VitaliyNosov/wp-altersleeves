<?php

namespace Mythic_Core\Ajax\Store;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Woo_Cart_Item_Functions;

/**
 * Class MC_Remove_Product
 *
 * @package Mythic_Core\Ajax\Store\Cart
 */
class MC_Remove_Product extends MC_Ajax {
    
    private const ACTION_NAME = MC_Woo_Cart_Item_Functions::AJAX_REMOVE_BY_PRODUCT_ID;
    
    /**
     * Handles POST request
     */
    public function execute() {
        $this->success( MC_Woo_Cart_Item_Functions::ajax( self::ACTION_NAME, $_POST ) );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return self::ACTION_NAME;
    }
    
}