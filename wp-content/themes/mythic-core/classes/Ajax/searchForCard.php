<?php

namespace Mythic_Core\Ajax;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Mtg_Card_Functions;

class searchForCard extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response                = [ 'status' => 0 ];
        $search_term             = $_REQUEST['search_term'] ?? '';
        $search_term             = trim( $search_term );
        $response['search_term'] = $search_term;
        $response['results']     = MC_Mtg_Card_Functions::searchCards( $search_term );
        $response['status']      = 1;
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'searchForCard';
    }
    
}
