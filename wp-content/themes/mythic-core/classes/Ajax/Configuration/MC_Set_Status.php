<?php

namespace Mythic_Core\Ajax\Configuration;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Objects\MC_Mtg_Set;

/**
 * Class MC_Set_Status
 *
 * @package Mythic_Core\Ajax\Configuration
 */
class MC_Set_Status extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response = [
            'success' => 0,
        ];
        if( !isset( $_REQUEST['available'] ) || empty( $_REQUEST['id'] ) ) $this->success( $response );
        $available             = $_REQUEST['available'];
        $id                    = $_REQUEST['id'];
        $parent                = $available ? MC_Mtg_Set::availableId() : MC_Mtg_Set::unavailableId();
        $update                = wp_update_term( $id, 'mtg_set', [
            'parent' => $parent,
        ] );
        $response['available'] = $available;
        $response['parent']    = $parent;
        $response['update']    = json_encode( $update );
        $response['set']       = json_encode( get_term_by( 'term_id', $id, 'mtg_set' ) );
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-set-status';
    }
    
}
