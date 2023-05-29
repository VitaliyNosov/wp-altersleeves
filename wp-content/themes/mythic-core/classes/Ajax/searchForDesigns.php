<?php

namespace Mythic_Core\Ajax;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Design_Functions;

class searchForDesigns extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response                = [ 'status' => 0, 'results' => [] ];
        $response['search_term'] = $search_term = $_REQUEST['search_term'] ?? '';
        if( empty( $search_term ) ) $this->success( $response );
        
        $response['results'] = MC_Design_Functions::search_designs( $search_term );
        $response['status']  = 1;
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'searchForDesigns';
    }
    
}
