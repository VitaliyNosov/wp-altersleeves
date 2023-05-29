<?php

namespace Mythic_Core\Functions;

use Mythic_Core\Abstracts\MC_Transaction_Type_Functions;

/**
 * Class MC_Transaction_Order_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Transaction_Order_Functions extends MC_Transaction_Type_Functions {
    
    public static $transaction_key = 'order';
    
    public function __construct( &$args ) {
        $this->prepareAssociatedOrder( $args );
        parent::__construct( $args );
    }
    
    public function checkIfTransactionNecessary( &$args ) {
        return !empty( $args['order_id'] );
    }
    
    public function prepareValue( &$args ) : float {
        return MC_Woo_Order_Functions::orderTotal( $this->associated_order );
    }
    
    public function prepareDate( &$args ) : string {
        $date = $this->associated_order->get_date_paid();
        return !empty($date) ? $date : date( 'Y-m-d H:i:s' );
    }
    
    public function prepareOrderId( &$args ) : int {
        return $args['order_id'];
    }
    
    public function prepareUserId( &$args ) : int {
        return $this->associated_order->get_user_id() ?? 0;
    }
    
    public function prepareCurrency( &$args ) : string {
        return $this->associated_order->get_currency();
    }
    
}