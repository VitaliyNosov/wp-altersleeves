<?php

namespace Mythic_Core\Ajax;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Design_Functions;

/**
 * Class TrashDesign
 *
 * @package Mythic_Core\Ajax\Creator\Design
 */
class TrashDesign extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $idDesign = $_POST['product_id'] ?? '';
        if( $idDesign == '' ) die();
        if( get_post_type( $idDesign ) == 'product' ) {
            wp_update_post( [
                                'ID'          => $idDesign,
                                'post_status' => 'removed',
                            ] );
        } else {
            MC_Design_Functions::trashItem( $idDesign );
        }
        $this->success( [
                            'success' => 1,
                            'id'      => $idDesign,
                        ] );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'cas-trash-design';
    }
    
}
