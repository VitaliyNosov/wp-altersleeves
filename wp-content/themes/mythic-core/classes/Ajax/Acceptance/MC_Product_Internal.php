<?php

namespace Mythic_Core\Ajax\Acceptance;

use Mythic_Core\Abstracts\MC_Ajax;

/**
 * Class MC_Product_Internal
 *
 * @package Mythic_Core\Ajax\Acceptance
 */
class MC_Product_Internal extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $product_id = $_POST['id'] ?? 0;
        $internal   = $_POST['internal'] ?? 0;
        $status     = !empty( $internal ) ? 'internal_verify' : 'verify';
        wp_update_post( [
                            'ID'          => $product_id,
                            'post_status' => $status,
                        ] );
        $this->success( [ 'product_id' => $product_id ] );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'mc-product-internal';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'as-acceptance-data';
    }
    
}
