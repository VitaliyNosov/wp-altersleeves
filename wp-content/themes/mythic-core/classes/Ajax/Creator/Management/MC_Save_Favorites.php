<?php

namespace Mythic_Core\Ajax\Creator\Management;

use Mythic_Core\Abstracts\MC_Ajax;

class MC_Save_Favorites extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response = [
            'id'     => 0,
            'output' => '',
        ];
        $designs  = !empty( $_POST['designs'] ) ? json_decode( $_POST['designs'] ) : [];
        $idUser   = isset( $_REQUEST['user_id'] ) ? $_REQUEST['user_id'] : 0;
        if( empty( $idUser ) ) $this->success( $response );
        $designs = is_array( $designs ) ? array_unique( $designs ) : [];
        $designs = array_unique( $designs );
        update_user_meta( $idUser, 'mc_fav_designs', $designs );
        $response['success'] = 1;
        $response['designs'] = json_encode( $designs );
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-save-fav-alters';
    }
    
}
