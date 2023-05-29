<?php

namespace Mythic_Core\Ajax\Store;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Woo_Cart_Item_Functions;

/**
 * Class MC_Add_Promotion_Product
 *
 * @package Mythic_Core\Ajax\Store\Cart
 */
class MC_Add_Promotion_Product extends MC_Ajax {
    
    private static $action_name = MC_Woo_Cart_Item_Functions::AJAX_ADD_PROMOTIONAL_PRODUCT;
    
    /**
     * Handles POST request
     */
    public function execute() {
        $this->success( MC_Woo_Cart_Item_Functions::ajax( self::$action_name, $_POST ) );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return self::$action_name;
    }
    
}