<?php

namespace Mythic_Core\Ajax;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Mtg_Card_Functions;

class getPrintingsForCard extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response = [
            'status'    => 0,
            'printings' => [],
        ];
        $card_id  = $_REQUEST['card'] ?? 0;
        $generic  = !empty( $_REQUEST['generic'] );
        if( empty( $card_id ) ) $this->success( $response );
        $response['printings'] = MC_Mtg_Card_Functions::printings_for_submission( $card_id, $generic );
        $response['status']    = 1;
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'getPrintingsForCard';
    }
    
}
