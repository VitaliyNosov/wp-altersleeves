<?php

namespace Mythic_Core\Ajax\Finance;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Withdrawal_Functions;

/**
 * Class MC_Withdrawal_Request
 *
 * @package Mythic_Core\Ajax\Creator\Finance
 */
class MC_Withdrawal_Request extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        // Didn't add execute with nonce checking because we don't use this action now
        
        $response = [ 'error' => 0, 'success' => 0 ];
        
        $response['user_id'] = $idCreator = $_REQUEST['user_id'];
        
        $response['name']     = $name = $_REQUEST['name'];
        $response['email']    = $email = $_REQUEST['email'];
        $response['currency'] = $currency = $_REQUEST['currency'];
        $response['amount']   = $amount = $_REQUEST['amount'];
        if( empty( $amount ) || $amount < 0.01 || empty( $idCreator ) ) {
            $response['error'] = 0;
            $this->success( $response );
        }
        $result = MC_Withdrawal_Functions::request( $idCreator, $amount, $currency, $name, $email );
        if( !$result ) {
            $response['error'] = 3;
            $this->success( $response );
        }
        $response['success'] = 1;
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-request-withdrawal';
    }
    
}
