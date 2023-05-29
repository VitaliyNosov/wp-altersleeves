<?php

namespace Mythic_Core\Functions;

use Mythic_Core\Abstracts\MC_Transaction_Type_Functions;

/**
 * Class MC_Transaction_Refund_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Transaction_Refund_Functions extends MC_Transaction_Type_Functions {
    
    public static $transaction_key = 'refund';
    
    public function __construct( &$args ) {
        $this->prepareAssociatedOrder( $args );
        parent::__construct( $args );
    }
    
    public function checkIfTransactionNecessary( &$args ) {
        return !empty( $args['order_id'] );
    }
    
    public function prepareValue( &$args ) : float {
        return number_format( $this->associated_order->get_total_refunded(), 2 );
    }
    
    public function prepareDate( &$args ) : string {
        return $this->associated_order->get_date_paid() ?? $this->associated_order->get_date_created();
    }
    
    public function prepareUserId( &$args ) : int {
        return $this->associated_order->get_user_id() ?? 0;
    }
    
}