<?php

namespace Mythic_Core\Ajax;

use MC_Mask_Cutter_Functions;
use MC_Render;
use Mythic_Core\Abstracts\MC_Ajax;

class loadPrintingInCutter extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response                = [ 'status' => 0 ];
        $response['printing_id'] = $printing_id = $_REQUEST['printing_id'] ?? '';
        $response['alter_id']    = $alter_id = $_REQUEST['alter_id'] ?? 0;
        if( empty( $printing_id ) ) $this->success( $response );
        $response['data'] = [ 'maskMaps' => MC_Mask_Cutter_Functions::data( $printing_id, 0 ) ];
        ob_start();
        $design_file_name = !empty( $response['data']['maskMaps']['product_image_data']['file_name'] ) ? $response['data']['maskMaps']['product_image_data']['file_name'] : '';
        
        MC_Render::tool( 'cutter/tool-submit', [ 'alter_id' => $alter_id, 'design_file_name' => $design_file_name ] );
        $response['refresh'] = ob_get_clean();
        $response['status']  = 1;
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'loadPrintingInCutter';
    }
    
}
