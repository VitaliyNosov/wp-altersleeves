<?php

namespace Mythic_Core\Ajax;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Alter_Functions;

class getCropType extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response               = [ 'status' => 0 ];
        $response['alter_id']   = $alter_id = $_POST['alter_id'] ?? 0;
        $response['crop_types'] = MC_Alter_Functions::get_alter_types( $alter_id );
        $response['status']     = 1;
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'getCropType';
    }
    
}
