<?php

namespace Mythic_Core\Ajax\Finance;

use Mythic_Core\Abstracts\MC_Ajax;

/**
 * Class MC_Withdrawal_Approval
 *
 * @package Mythic_Core\Ajax\Finance
 */
class MC_Withdrawal_Approval extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        // Didn't add execute with nonce checking because we don't use this action now
        
        $withdrawal_id = $_POST['withdrawal_id'];
        if( empty( $withdrawal_id ) ) $this->error( 'No withdrawal id provided' );
        global $wpdb;
        $approved = !empty( $_POST['approved'] ) ? 1 : 0;
        $wpdb->update( 'mc_withdrawals', [ 'approved' => $approved ], [ 'id' => $withdrawal_id ] );
        $response = [
            'withdrawal_id' => $withdrawal_id,
            'approved'      => $approved,
        ];
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'mc-withdrawal-approval';
    }
    
}
