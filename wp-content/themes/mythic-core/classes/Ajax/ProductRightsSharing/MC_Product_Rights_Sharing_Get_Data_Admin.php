<?php

namespace Mythic_Core\Ajax\ProductRightsSharing;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Display\MC_Template_Parts;
use Mythic_Core\Functions\MC_User_Functions;

/**
 * Class MC_Product_Rights_Sharing_Get_Data_Admin
 *
 * @package Mythic_Core\Ajax\ProductRightsSharing
 */
class MC_Product_Rights_Sharing_Get_Data_Admin extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $result = [ 'status' => 0, 'message' => 'Something went wrong' ];
        if(
            !MC_User_Functions::isAdmin() ||
            empty( $_POST['userId'] ) ||
            empty( $_POST['userType'] ) ||
            $_POST['userType'] != 'artist'
            && $_POST['userType'] != 'publisher'
        ) {
            $this->success( $result );
        }
        
        $result = [
            'status' => 1,
            'html'   => MC_Template_Parts::get(
                'rights-sharing-control',
                'rights-sharing-control-'.$_POST['userType'],
                [ 'user_id' => $_POST['userId'] ]
            ),
        ];
        
        $this->success( $result );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'mcGetAffiliatePublishingDataAdmin';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'mc_product_rights_sharing';
    }
    
}
