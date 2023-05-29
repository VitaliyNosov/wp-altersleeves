<?php

namespace Mythic_Core\Ajax\ProductRightsSharing;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Licensing_Functions;

/**
 * Class MC_Product_Rights_Sharing_Update
 *
 * @package Mythic_Core\Ajax\ProductRightsSharing
 */
class MC_Product_Rights_Sharing_Update extends MC_Ajax {
    
    /**
     * @return array
     */
    public function required_values() : array {
        return [ 'currentShareId', 'newShareStatus', 'userType' ];
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $result = [ 'status' => 0, 'message' => 'Something went wrong' ];
        
        $update_status = MC_Licensing_Functions::checkAndUpdateShareStatus( $_POST['currentShareId'], $_POST['newShareStatus'], $_POST['userType'] );
        
        if( empty( $update_status ) ) $this->success( $result );
        
        $result = [
            'status'       => 1,
            'share_status' => MC_Licensing_Functions::prepareStatusLabel( $_POST['newShareStatus'] ),
            'actions'      => MC_Licensing_Functions::generateStatusActionForArtist( $_POST['currentShareId'], $_POST['newShareStatus'] ),
        ];
        
        $this->success( $result );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'mcUpdateProductRightsShare';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'mc_product_rights_sharing';
    }
    
}
