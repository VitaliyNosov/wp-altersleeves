<?php

namespace Mythic_Core\Ajax\ProductRightsSharing;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Licensing_Functions;

/**
 * Class MC_Product_Rights_Sharing_New
 *
 * @package Mythic_Core\Ajax\ProductRightsSharing
 */
class MC_Product_Rights_Sharing_New extends MC_Ajax {
    
    /**
     * @return array
     */
    public function required_values() : array {
        return [ 'productRightsShareData' ];
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $this->success( MC_Licensing_Functions::registerNewProductRightsShares( $_POST['productRightsShareData'] ) );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'mcRegisterNewProductRightsShare';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'mc_product_rights_sharing';
    }
    
}
