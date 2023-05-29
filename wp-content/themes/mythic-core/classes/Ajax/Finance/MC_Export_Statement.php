<?php

namespace Mythic_Core\Ajax\Finance;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Transaction_Functions;
use Mythic_Core\Functions\MC_Woo_Order_Functions;

class MC_Export_Statement extends MC_Ajax {
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'export-statement';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'export-statement';
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $user_id = $_POST['user_id'];
        
        $entries = MC_Transaction_Functions::getForAffiliateLedger( $user_id );
        $data    = 'action_id,date,value,type,message,store,cleared';
        foreach( $entries as $entry ) {
            $action_id = $entry->action_id;
            $date      = date( 'Y-m-d H:i:s', strtotime( $entry->date ) );
            $type      = $entry->type;
            $value     = $entry->value;
            $store     = $entry->site_id == 2 ? 'Mythic Gaming' : 'Alter Sleeves';
            $message   = '';
            
            $date_as_string = strtotime( $date );
            $cleared        = 1;
            if( $type == 'royalty' && $entry->site_id == 1 ) {
                $cleared = $date_as_string < ( time() - strtotime( '30 days', 0 ) );
                $cleared = empty( $cleared ) ? 0 : 1;
            }
            switch( $type ) {
                case 'referral_fee' :
                    $order_id = $entry->order_id;
                    $message  = 'Associated with order ';
                    $message  .= $order_id;
                    $type     = 'referral';
                    $message  .= ' worth $'.MC_Woo_Order_Functions::orderTotal( $order_id, true, false );
                    break;
                case 'withdrawal' :
                    if( empty( $value ) || $value == 0 ) continue 2;
                    $currency = $entry->currency;
                    $type     = 'withdrawal';
                    $message  = 'Withdrawal of $'.$value.' in "'.$currency.'"';
                    $value    = -$value;
                    break;
                case 'contracted_fee' :
                    $type    = 'partner fee';
                    $message = 'Recurring fee of $'.$value;
                    break;
                case 'royalty' :
                    $type       = $value < 1 ? 'promotional' : 'royalty';
                    $product_id = $entry->product_id;
                    if( empty( $product_id ) ) {
                        $message = $entry->message;
                    } else {
                        $message = 'Royalty for sale of product <a href="'.get_blog_permalink( $entry->site_id ,$product_id ).'">'.$entry->product_id.'</a>';
                    }
                    if( $value < 1 ) $message = str_replace( 'Royalty', 'Promotional sale', $message );
                    break;
            }
            
            $data .= '
            ';
            $data .= $action_id.',';
            $data .= $date.',';
            $data .= $value.',';
            $data .= $type.',';
            $data .= $message.',';
            $data .= $store.',';
            $data .= $cleared.',';
        }
        
        $this->success( [
                            'success' => 1,
                            'data'    => $data,
                        ] );
    }
    
}