<?php

namespace Mythic_Core\Ajax\Browsing;

use AS_Browse;
use MC_Render;
use Mythic_Core\Abstracts\MC_AJAX;

/**
 * Class MC_Browse_Results
 *
 * @package Mythic_Core\Ajax\Browsing
 */
class MC_Browse_Results extends MC_AJAX {
    
    /**
     * Handles POST request
     */
    public function execute() {
    
        ob_start();
        MC_Render::component( 'browse', 'results', [ 'params' => $_POST ] );
        $response = [
            'output' => $output = ob_get_clean() ?? '',
            'post'   => $_POST,
        ];
    
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'browse-results';
    }
    
}
