<?php

namespace Mythic_Core\Ajax\Marketing;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Objects\MC_Shortlink;

/**
 * Class MC_Shorten_Link
 *
 * @package Mythic_Core\Ajax\Marketing
 */
class MC_Shorten_Link extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response = [
            'success' => 0,
            'link'    => '',
        ];
        if( empty( $_POST['destination'] ) ) $this->success( $response );
        $url = $_POST['destination'];
        
        $link = new MC_Shortlink();
        $link->setDestination( $url );
        $link->create();
        
        $response['link'] = $link->getUrl();
        
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-shorten-link';
    }
    
}