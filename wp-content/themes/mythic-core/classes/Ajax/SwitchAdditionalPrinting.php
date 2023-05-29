<?php

namespace Mythic_Core\Ajax;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Objects\MC_Mtg_Printing;

/**
 * Class SwitchAdditionalPrinting
 *
 * @package Mythic_Core\Ajax\Store\Products\Design\Card
 */
class SwitchAdditionalPrinting extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response = [];
        if( empty( $_POST['alter_id'] ) || empty( $_POST['printing_id'] ) ) $this->success( $response );
        $idAlter                    = $_POST['alter_id'];
        $response['image_alter']    = MC_Alter_Functions::image( $idAlter, 'hi' );
        $idPrinting                 = $_POST['printing_id'];
        $printing                   = new MC_Mtg_Printing( $idPrinting );
        $response['image_printing'] = $printing->imgJpgNormal;
        $response['set_name']       = $printing->set_name;
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'cas-product-switch-additional-printing';
    }
    
}