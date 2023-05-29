<?php

namespace Mythic_Core\Ajax\Reporting;

use MC_Withdrawal_Functions;
use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Users\MC_Affiliates;
use WP_User;

/**
 * Class MC_Withdrawals_Csv
 *
 * @package Mythic_Core\Ajax\Reporting
 */
class MC_Withdrawals_Csv extends MC_Ajax {
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-withdrawals-csv';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'as-withdrawals-data';
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $data = 'Date;Email;Withdrawal Name;Amount;Currency;Role;';
        
        $withdrawals = MC_Withdrawal_Functions::getAllOrderByDate();
        
        foreach( $withdrawals as $withdrawal ) {
            $user_id         = $withdrawal->creator_id;
            $user            = new WP_User( $user_id );
            $email           = get_user_meta( $user_id, 'mc_transferwise_email', true );
            $email           = !empty( $email ) ? $email : $user->user_email;
            $withdrawal_name = get_user_meta( $user_id, 'mc_transferwise_name', true );
            $withdrawal_name = !empty( $withdrawal_name ) ? $withdrawal_name : $user->first_name.' '.$user->last_name;
            $role            = MC_Affiliates::is( $user_id ) ? 'Content Creator' : 'Artist';
            
            $data .= '
            ';
            $data .= date( 'Y-m-d H:i:s', strtotime( $withdrawal->date ) ).';';
            $data .= $email.';';
            $data .= utf8_encode( $withdrawal_name ).';';
            $data .= $withdrawal->paid_init.';';
            $data .= $withdrawal->currency.';';
            $data .= $role.';';
        }
        
        $this->success( [
                            'success' => 1,
                            'data'    => $data,
                        ] );
    }
    
}