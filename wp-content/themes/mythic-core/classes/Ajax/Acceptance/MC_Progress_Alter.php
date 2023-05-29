<?php

namespace Mythic_Core\Ajax\Acceptance;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Alter_Functions;

/**
 * Class Progress
 *
 * @package Mythic_Core\Ajax\Acceptance\Approval
 */
class MC_Progress_Alter extends MC_Ajax {
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'alter-approve';
    }
    
    /**
     * @return array|string[]
     */
    public function required_values() : array {
        return [ 'alter_id' ];
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $idAlter = $_POST['alter_id'];
        $status  = MC_Alter_Functions::progress( $idAlter );
        $this->success( [ 'status' => $status, ] );
    }
    
    /**
     * @return bool
     */
    protected function is_public() : bool {
        return false;
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'as-acceptance-data';
    }
    
}