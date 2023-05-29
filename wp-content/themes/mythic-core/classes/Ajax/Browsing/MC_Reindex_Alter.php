<?php

namespace Mythic_Core\Ajax\Production;

use MC_Alter_Functions;
use Mythic_Core\Abstracts\MC_Ajax;

/**
 * Class MC_Regenerate_Files
 *
 * @package Mythic_Core\Ajax\Production
 */
class MC_Reindex_Alter extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $idAlter = $_POST['product_id'];
        
        MC_Alter_Functions::arrangeAltersIntoDesignGroups( $idAlter );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'reindex-alter';
    }
    
    /**
     * @return bool
     */
    protected function is_public() : bool {
        return false;
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'as-order-data';
    }
    
}