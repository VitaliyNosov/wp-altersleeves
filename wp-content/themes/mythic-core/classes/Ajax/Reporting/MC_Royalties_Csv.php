<?php

namespace Mythic_Core\Ajax\Reporting;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Royalty_Functions;
use WP_User;

/**
 * Class MC_Royalties_Csv
 *
 * @package Mythic_Core\Ajax\Reporting
 */
class MC_Royalties_Csv extends MC_Ajax {
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-royalties-csv';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'as-royalties-csv';
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $data      = 'Date,Email,Name,Display Name,Order ID,Product ID,Type,Quantity,Value,';
        $royalties = MC_Royalty_Functions::getAllWithValueByDate();
        
        foreach( $royalties as $royalty ) {
            $user_id = $royalty->alterist_id;
            $user    = new WP_User( $user_id );
            if( empty( $user ) ) continue;
            
            $data .= '
            ';
            $data .= date( 'Y-m-d H:i:s', strtotime( $royalty->date ) ).',';
            $data .= $user->user_email.',';
            $data .= utf8_encode( $user->first_name.' '.$user->last_name ).',';
            $data .= utf8_encode( $user->display_name ).',';
            $data .= $royalty->order_id.',';
            $data .= $royalty->product_id.',';
            $data .= $royalty->type.',';
            $data .= $royalty->quantity.',';
            $data .= $royalty->value.',';
        }
        
        $this->success( [
                            'success' => 1,
                            'data'    => $data,
                        ] );
    }
    
}