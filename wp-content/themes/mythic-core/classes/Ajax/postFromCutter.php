<?php

namespace Mythic_Core\Ajax;

use Exception;
use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\System\MC_WP;
use Mythic_Core\Utils\MC_Images;
use Mythic_Core\Utils\MC_Url;
use Mythic_Core\Utils\MC_Vars;

class postFromCutter extends MC_Ajax {
    
    /**
     * Handles POST request
     *
     * @throws Exception
     */
    public function execute() {
        $response         = [ 'status' => 0, 'path' => '' ];
        $response['data'] = $data = $_REQUEST['data'] ?? '';
        if( empty( $data ) ) $this->success( $response );
        $filename = MC_Vars::generate( 10 );
        MC_Images::base64ToImage( $data, $path = MC_WP::uploadDir().'/'.$filename.'.png' );
        $uri              = MC_Url::pathToUrl( $path );
        $response['path'] = !empty( $path ) ? $path : 'failed';
        $response['uri']  = !empty( $path ) ? $uri : 'failed';
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'postFromCutter';
    }
    
}
