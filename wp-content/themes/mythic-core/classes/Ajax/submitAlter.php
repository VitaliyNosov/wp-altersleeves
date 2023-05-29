<?php

namespace Mythic_Core\Ajax;

use Mythic_Core\Abstracts\MC_Ajax;

/**
 * Class submitAlter
 *
 * @package Mythic_Core\Ajax\Creator\Management\Alter
 */
class submitAlter extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        if( !is_user_logged_in() ) $this->success( [ 'success' => 0 ] );
        $args      = [];
        $printings = !empty( $_POST['printings'] ) ? json_decode( $_POST['printings'] ) : [];
        //if( empty( $printings ) ) $this->success( [ 'printings' => $_POST['printings'] ] );
        $args['printings']     = is_array( $printings ) ? $printings : [];
        $args['creator']       = !empty( $_POST['creator'] ) ? $_POST['creator'] : wp_get_current_user()->ID;
        $args['alter_id']      = !empty( $_POST['alter_id'] ) ? $_POST['alter_id'] : 0;
        $args['design_id']     = !empty( $_POST['design_id'] ) ? $_POST['design_id'] : 0;
        $args['design_name']   = !empty( $_POST['design_name'] ) ? $_POST['design_name'] : '';
        $args['path_alter']    = !empty( $_POST['path_alter'] ) ? $_POST['path_alter'] : '';
        $args['frame']         = !empty( $_POST['frame'] ) ? $_POST['frame'] : 0;
        $args['alter_type']    = !empty( $_POST['alter_type'] ) ? $_POST['alter_type'] : 0;
        $args['product_tags']  = !empty( $_POST['product_tags'] ) ? $_POST['product_tags'] : 0;
        $args['bounty']        = !empty( $_POST['bounty'] ) ? $_POST['bounty'] : 0;
        $args['commission_id'] = !empty( $_POST['commission_id'] ) ? $_POST['commission_id'] : 0;
        $args['availability']  = !empty( $_POST['availability'] ) ? $_POST['availability'] : 'available';
        $args['product_type']  = !empty( $_POST['product_type'] ) ? $_POST['product_type'] : '';
        $args['generic']       = !empty( $_POST['generic'] ) ? $_POST['generic'] : '';
        
        wp_schedule_single_event( time(), 'mc_alter_management', [ $args ] );
        
        $this->success( [ 'success' => 1, 'args' => $args ] );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-alter-submit';
    }
    
}
