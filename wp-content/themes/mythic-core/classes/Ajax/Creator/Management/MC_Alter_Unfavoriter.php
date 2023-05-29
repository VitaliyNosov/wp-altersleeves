<?php

namespace Mythic_Core\Ajax\Creator\Management;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Alter_Functions;

/**
 * Class MC_Alter_Unfavoriter
 *
 * @package Mythic_Core\Ajax\Creator\Management\Design
 */
class MC_Alter_Unfavoriter extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response   = [];
        $idAlter    = $_REQUEST['alter_id'];
        $idUser     = $_REQUEST['user_id'];
        $favDesigns = get_user_meta( $idUser, 'mc_fav_designs', true );
        $favDesigns = is_array( $favDesigns ) ? $favDesigns : [];
        foreach( $favDesigns as $key => $fav_design ) {
            if( $idAlter == $fav_design ) unset( $favDesigns[ $key ] );
        }
        $favDesigns = array_unique( $favDesigns );
        update_user_meta( $idUser, 'mc_fav_designs', $favDesigns );
        $response['designs'] = $favDesigns;
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-unfav-alter';
    }
    
}
