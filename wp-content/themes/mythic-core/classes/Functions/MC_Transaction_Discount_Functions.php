<?php

namespace Mythic_Core\Functions;

use Mythic_Core\Abstracts\MC_Transaction_Type_Functions;

/**
 * Class MC_Transaction_Discount_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Transaction_Discount_Functions extends MC_Transaction_Type_Functions {
    
    public static $transaction_key = 'discount';
    
    public function __construct( &$args ) {
        $this->prepareAssociatedOrder( $args );
        parent::__construct( $args );
    }
    
    public function checkIfTransactionNecessary( &$args ) {
        return !empty( $args['order_id'] );
    }
    
    public function prepareValue( &$args ) : float {
        $credits = $this->associated_order->get_payment_method() == 'wdc_woo_credits';
        if( $credits ) {
            $discount = MC_Woo_Order_Functions::orderTotal( $this->associated_order );
        } else {
            $discount = $this->associated_order->get_discount_total();
        }
        
        return $discount;
    }
    
    public function prepareDate( &$args ) : string {
        return $this->associated_order->get_date_paid();
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